// Earth3Dsimulation.js
import * as THREE from "three";
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import getStarfield from "./getStarfield.js";
import { glowmesh } from "./glowmesh.js";
import { gsap } from 'gsap';

// Import astronomical calculation functions
import { getSunCoords } from "./sunCalculations.js"; // Correctly imported now

// Import SGP4 propagation functions
import { parseTle, propagateSGP4 } from "./sgp4.js";

import {
    solveKepler,
    E_to_TrueAnomaly,
    TrueAnomaly_to_E,
    E_to_M,
    updateOrbitalElements,
    calculateSatellitePositionECI,
    calculateDerivedOrbitalParameters, // Imported from orbitalCalculation.js
} from "./orbitalCalculation.js";

import {
    DEG2RAD,
    EarthRadius, // Imported from parametersimulation.js
    EARTH_ANGULAR_VELOCITY_RAD_PER_SEC,
    SCENE_EARTH_RADIUS
} from "./parametersimulation.js";

// Scene variables
let camera, scene, renderer, controls, earthGroup;
let earthMesh, cloudsMesh, atmosphereGlowMesh;
let sunLight;
//let earthEdgeGlowMaterial;

// Global state variables
window.activeSatellites = new Map();
window.activeGroundStations = new Map();
window.selectedSatelliteId = null;
window.isAnimating = false;
window.closeViewEnabled = false;
window.totalSimulatedTime = 0;
// Set currentEpochUTC to the current real-world time for a dynamic start
window.currentEpochUTC = new Date().getTime(); // Changed: Dynamic start epoch
window.currentSpeedMultiplier = 1;
window.EARTH_ANGULAR_VELOCITY_RAD_PER_SEC = EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;
window.is2DViewActive = false;

// Expose these for use in simulation.blade.php
window.calculateDerivedOrbitalParameters = calculateDerivedOrbitalParameters; // EXPOSED GLOBALLY
window.EarthRadius = EarthRadius; // EXPOSED GLOBALLY
window.DEG2RAD = DEG2RAD; // EXPOSED GLOBALLY

// Satellite model loading variables
let satelliteModelLoaded = false;
let globalSatelliteGLB = null;
let lastAnimationFrameTime = performance.now();


/**
 * Initializes the 3D scene with Earth, lights, clouds, atmosphere, and starfield.
 */
function init3DScene() {
    const earthContainer = document.getElementById('earth-container');
    if (!earthContainer) {
        console.error("Critical: #earth-container not found.");
        return;
    }

    // Set up renderer with antialiasing and logarithmic depth buffer for precision
    renderer = new THREE.WebGLRenderer({ antialias: true, logarithmicDepthBuffer: true });
    renderer.setSize(earthContainer.offsetWidth, earthContainer.offsetHeight);
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.setPixelRatio(window.devicePixelRatio); // Improve rendering quality on high-DPI screens
    earthContainer.appendChild(renderer.domElement);

    // Initialize scene and camera
    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera(75, earthContainer.offsetWidth / earthContainer.offsetHeight, 0.0001, 1000);
    camera.position.z = 2.5; // Closer initial view for better detail

    // Set up orbit controls for user interaction
    controls = new OrbitControls(camera, renderer.domElement);
    controls.minDistance = 1.2; // Prevent camera from going inside Earth
    controls.maxDistance = 10;
    controls.enablePan = false; // Disable panning to keep Earth centered
    controls.enableDamping = true; // Smooth out rotations
    controls.dampingFactor = 0.05;

    // Create Earth group - this group will rotate to simulate Earth's rotation
    earthGroup = new THREE.Group();
    scene.add(earthGroup); // Earth group is added to the scene

    // Load textures using placeholder images. Replace with actual high-resolution textures for production.
    const textureLoader = new THREE.TextureLoader();
    // Load Earth textures: Day, Night, Specular, Bump, Clouds
    const earthDayMap = textureLoader.load("/textures/Earth_DayMap.jpg");
    const earthNightMap = textureLoader.load("/textures/Earth_NightMap.jpg");
    const earthSpecularMap = textureLoader.load("/textures/Earth_Specular.jpg");
    const earthBumpMap = textureLoader.load("/textures/earthbump10k.jpg");
    const cloudsMap = textureLoader.load("/textures/Earth_Clouds.jpg");
    //const cloudsAlphaMap = textureLoader.load("/textures/05_earthcloudmaptrans.jpg");


    // Earth geometry: Changed to SphereGeometry with high segments for realism
    const earthGeometry = new THREE.SphereGeometry(SCENE_EARTH_RADIUS, 256, 256); // Increased segments for smoother sphere

    // Earth mesh with Day/Night Shader for realistic illumination
    const earthShader = new THREE.ShaderMaterial({
        uniforms: {
            uEarthDayMap: { value: earthDayMap },
            uEarthNightMap: { value: earthNightMap },
            uEarthSpecularMap: { value: earthSpecularMap },
            uEarthBumpMap: { value: earthBumpMap },
            uSunDirection: { value: new THREE.Vector3(1, 0, 0) }, // Initial light direction, will be updated dynamically
            uTime: { value: 0.0 }, // Time uniform for dynamic effects
            uCameraPosition: { value: camera.position }, // Camera position for specular calculation
            bumpScale: { value: 0.04 }, // Control intensity of bump map
            shininess: { value: 1000.0 } // Control size/intensity of specular highlight
        },
        vertexShader: `
            varying vec2 vUv;
            varying vec3 vWorldNormal; // World-space normal
            varying vec3 vWorldPosition; // World-space position

            void main() {
                vUv = uv;
                // Calculate world-space normal
                vWorldNormal = normalize(mat3(modelMatrix) * normal);
                // Calculate world-space position
                vWorldPosition = (modelMatrix * vec4(position, 1.0)).xyz;

                gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
            }
        `,
        fragmentShader: `
            uniform sampler2D uEarthDayMap;
            uniform sampler2D uEarthNightMap;
            uniform sampler2D uEarthSpecularMap;
            uniform sampler2D uEarthBumpMap;
            uniform vec3      uSunDirection;  // world-space sun vector
            uniform float     uTime;
            uniform vec3      uCameraPosition; // Camera's world position
            uniform float     bumpScale;
            uniform float     shininess;

            varying vec2 vUv;
            varying vec3 vWorldNormal;
            varying vec3 vWorldPosition;

            void main() {
                // Fetch day/night textures
                vec4 dayColor   = texture2D(uEarthDayMap, vUv);
                vec4 nightColor = texture2D(uEarthNightMap, vUv);

                // Bump mapping: perturb the world normal
                vec3 mapN         = texture2D(uEarthBumpMap, vUv).rgb * 2.0 - 1.0;
                // Use the world normal for perturbation
                vec3 perturbedNormal = normalize(vWorldNormal + mapN * bumpScale);

                // Diffuse lighting term in world space
                float lightIntensity = dot(perturbedNormal, normalize(uSunDirection));

                // Smooth blend between day and night textures
                // Adjust smoothstep range for a softer/harder terminator line
                float blendFactor = smoothstep(-0.2, 0.2, lightIntensity);
                vec4 baseColor = mix(nightColor, dayColor, blendFactor);

                // Specular lighting in world space
                vec3 viewDir = normalize(uCameraPosition - vWorldPosition); // Vector from fragment to camera
                vec3 reflDir = reflect(-normalize(uSunDirection), perturbedNormal); // Reflected light direction
                float spec  = pow(max(dot(viewDir, reflDir), 0.0), shininess);
                vec3  specCol = texture2D(uEarthSpecularMap, vUv).rgb * spec;

                // Final color is base color + specular highlight
                gl_FragColor = baseColor + vec4(specCol, 1.0);
            }
        `,
        transparent: false,
    });
    earthMesh = new THREE.Mesh(earthGeometry, earthShader);
    earthGroup.add(earthMesh); // Add to the rotating group

    // Clouds mesh: Enhanced opacity and blending for better visualization
    cloudsMesh = new THREE.Mesh(earthGeometry, new THREE.MeshStandardMaterial({
        map: cloudsMap,
        transparent: true,
        opacity: 0.5, // Increased opacity for better visibility
        blending: THREE.AdditiveBlending, // Makes clouds appear light and airy
        //alphaMap: cloudsAlphaMap, // Controls cloud transparency based on a separate texture
    }));
    cloudsMesh.scale.setScalar(SCENE_EARTH_RADIUS * 1.002); // Slightly further out from Earth
    earthGroup.add(cloudsMesh);

    // Atmosphere glow mesh (inner glow): Uses custom glowmesh shader
    atmosphereGlowMesh = new THREE.Mesh(earthGeometry, glowmesh({
        rimHex: 0x0088ff, // Blue color for the rim of the glow
        facingHex: 0xE0F0FF, // Lighter color for the part facing the camera
    }));
    atmosphereGlowMesh.scale.setScalar(SCENE_EARTH_RADIUS * 1.01); // Larger scale for a more prominent glow
    earthGroup.add(atmosphereGlowMesh);

   
    // Add starfield to the scene
    scene.add(getStarfield({ numStars: 4000 })); // Pass numStars for higher density

    // Sun light setup: Directional light simulating the sun
    sunLight = new THREE.DirectionalLight(0xffffff, 1); // White light, full intensity
    sunLight.castShadow = true; // Enabled shadows, requires renderer.shadowMap.enabled = true;
    sunLight.shadow.mapSize.width = 1024;
    sunLight.shadow.mapSize.height = 1024;
    sunLight.shadow.camera.near = 0.5;
    sunLight.shadow.camera.far = 500;
    scene.add(sunLight);

    // Ambient light: Soft, general illumination to prevent completely dark areas
    const ambientLight = new THREE.AmbientLight(0x333333, 1.0); // Subtle grey light
    scene.add(ambientLight);

    // For shadows to work, you also need to enable shadow map on the renderer
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap; // default THREE.PCFShadowMap
}

/**
 * Updates the direction of the sunlight in the scene based on the current simulated time.
 * It now uses the `getSunCoords` function (imported from sunCalculations.js) to get the inertial RA and Dec of the sun,
 * and then maps them consistently to the Three.js coordinate system.
 * @param {number} simulatedTimeSeconds - The total simulated time in seconds.
 */
function updateSunDirection(simulatedTimeSeconds) {
    // Calculate the current absolute UTC time based on initial epoch and simulated time
    const currentAbsoluteTimeMs = window.currentEpochUTC + (simulatedTimeSeconds * 1000);
    const currentDateTime = new Date(currentAbsoluteTimeMs);

    // Get the sun's inertial coordinates (Right Ascension and Declination)
    // Uses the imported getSunCoords function, which contains the accurate Wikipedia formulas.
    const sunCoords = getSunCoords(currentDateTime);
    const ra = sunCoords.ra; // Right Ascension in radians
    const dec = sunCoords.dec; // Declination in radians (latitude in inertial frame)

    // Convert sun's Right Ascension and Declination to ECI Cartesian coordinates (unit sphere)
    // ECI X = cos(Dec) * cos(RA)
    // ECI Y = cos(Dec) * sin(RA)
    // ECI Z = sin(Dec) (along Earth's rotational axis)
    const sunX_eci = Math.cos(dec) * Math.cos(ra);
    const sunY_eci = Math.cos(dec) * Math.sin(ra);
    const sunZ_eci = Math.sin(dec);

    // Now, map these ECI coordinates to Three.js Y-up coordinates for the sun's "position"
    // Desired mapping (consistent with orbitalCalculation.js):
    // Three.js X = ECI X
    // Three.js Y = ECI Z (North Pole, up direction)
    // Three.js Z = ECI Y (completes right-handed system)
    const sunPos_threeJsX = sunX_eci; // To match Three.js X with ECI X
    // Note: In Three.js, the Y axis is up, so we need to swap
    const sunPos_threeJsY = sunZ_eci;  // To match Three.js Y with ECI Z (North Pole aligns with +Y in Three.js world)
    const sunPos_threeJsZ = sunY_eci; // To match Three.js Z with ECI Y (90E Longitude aligns with +Z in Three.js world)

    // For a THREE.DirectionalLight, its 'position' property defines the *direction*
    // of the light from that position *towards* the target (default: origin 0,0,0).
    // So, if the vector (sunPos_threeJsX, sunPos_threeJsY, sunPos_threeJsZ)
    // points from the origin to the sun, then setting the light's position to
    // this vector will correctly make the light shine from the sun towards the origin.
    sunLight.position.set(sunPos_threeJsX, sunPos_threeJsY, sunPos_threeJsZ).normalize().multiplyScalar(5);
}


/**
 * Draws the orbit path for a satellite.
 * @param {Satellite} satellite - The satellite object for which to draw the orbit.
 */
function drawOrbitPath(satellite) {
    const e = satellite.params.eccentricity;
    const points = [];
    const numPathPoints = 360;

    const tempRAAN = satellite.currentRAAN;
    const tempArgPerigee = satellite.params.argPerigeeRad;

    for (let i = 0; i <= numPathPoints; i++) {
        const trueAnomaly_path = (i / numPathPoints) * 2 * Math.PI;
        const tempParams = {
            semiMajorAxis: satellite.params.semiMajorAxis,
            eccentricity: satellite.params.eccentricity,
            inclinationRad: satellite.params.inclinationRad,
            argPerigeeRad: tempArgPerigee,
        };
        // Ensure calculateSatellitePositionECI correctly takes SCENE_EARTH_RADIUS as scaling factor
        const tempPosition = calculateSatellitePositionECI(tempParams, E_to_M(TrueAnomaly_to_E(trueAnomaly_path, e), e), tempRAAN, SCENE_EARTH_RADIUS);
        points.push(new THREE.Vector3(tempPosition.x, tempPosition.y, tempPosition.z));
    }

    // Store the calculated 3D orbital path points on the satellite object for potential 2D rendering later
    satellite.orbitalPath3DPoints = points;

    // Dispose of previous orbit line to avoid memory leaks
    if (satellite.orbitLine) {
        scene.remove(satellite.orbitLine); // Removed from scene
        satellite.orbitLine.geometry.dispose();
        satellite.orbitLine.material.dispose();
    }
    // Orbit line is added to scene, so it will not rotate with the Earth, consistent with ECI positions
    satellite.orbitLine = new THREE.Line(new THREE.BufferGeometry().setFromPoints(points), new THREE.LineBasicMaterial({ color: 0x00ff00 }));
    scene.add(satellite.orbitLine); // Added to scene
}

/**
 * Updates the coverage cone visualization for a satellite.
 * @param {Satellite} satellite - The satellite object for which to update the cone.
 */
function updateCoverageCone(satellite) {
    // Dispose of the previous cone to avoid memory leaks and ghost objects
    if (satellite.coverageCone) {
        scene.remove(satellite.coverageCone); // Removed from scene
        satellite.coverageCone.geometry.dispose();
        satellite.coverageCone.material.dispose();
        satellite.coverageCone = null;
    }

    const beamWidthDeg = satellite.params.beamWidthDeg;
    // If beamwidth is invalid or zero, do not draw a cone
    if (beamWidthDeg <= 0 || beamWidthDeg >= 180) return;

    const earthRadiusScene = SCENE_EARTH_RADIUS;
    const satPosition = satellite.mesh.position; // Satellite's position is now in ECI (relative to scene origin)
    const satDistanceToCenter = satPosition.length(); // Distance from Earth's center (0,0,0) to satellite

    const horizonAngleRad = satDistanceToCenter > earthRadiusScene ? Math.acos(earthRadiusScene / satDistanceToCenter) : Math.PI / 2;
    const halfBeamAngleRad = DEG2RAD * (satellite.params.beamWidthDeg / 2); // Use DEG2RAD from parametersimulation.js


    // coneHeight is the actual altitude of the satellite above the Earth's surface in scene units
    const coneHeight = satDistanceToCenter - earthRadiusScene;

    // Add coverage angle calculation for 2D
    const coverageAngleRad = Math.acos(earthRadiusScene / satDistanceToCenter) + halfBeamAngleRad;
    satellite.coverageAngleRad = Math.min(coverageAngleRad, Math.PI / 2); // Cap at 90°

    // Prevent drawing infinitesimally small or non-existent cones
    if (coneHeight <= 0.0001) return;

    const coneRadius = Math.tan(halfBeamAngleRad) * coneHeight;
    if (coneRadius <= 0.0001) return;

    const coneGeometry = new THREE.ConeGeometry(coneRadius, coneHeight, 32);
    coneGeometry.translate(0, -coneHeight / 2, 0);

    // Create the material for the cone.
    const coneMaterial = new THREE.MeshBasicMaterial({
        color: 0x00ff00, // Green color for visibility
        transparent: true,
        opacity: 0.2,
        side: THREE.DoubleSide // Important: Render both sides to see it from any angle
    });

    satellite.coverageCone = new THREE.Mesh(coneGeometry, coneMaterial);
    satellite.coverageCone.position.copy(satPosition);
    // Nadir direction is towards the center of the Earth from the satellite
    const nadirPointOnEarth = satPosition.clone().normalize().multiplyScalar(earthRadiusScene);
    const satToNadirDirection = nadirPointOnEarth.clone().sub(satPosition).normalize();
    // Align the cone's Y-axis (which is usually its central axis) with the nadir direction
    const rotationQuaternion = new THREE.Quaternion().setFromUnitVectors(new THREE.Vector3(0, 1, 0), satToNadirDirection.negate()); // Negate because cone points up by default
    satellite.coverageCone.setRotationFromQuaternion(rotationQuaternion);
    scene.add(satellite.coverageCone); // Added to scene
}


/**
 * Updates the nadir line visualization for a satellite.
 * @param {Satellite} satellite - The satellite object for which to update the nadir line.
 */
function updateNadirLine(satellite) {
    if (satellite.nadirLine) {
        scene.remove(satellite.nadirLine);
        satellite.nadirLine.geometry.dispose();
        satellite.nadirLine.material.dispose();
        satellite.nadirLine = null;
    }
    const points = [];
    const satPositionECI = satellite.mesh.position; // Satellite position is already in Three.js ECI coordinates

    // Get the current Earth rotation angle from the earthGroup (around Three.js Y-axis)
    const earthRotationAngle = earthGroup.rotation.y;

    // 1. Transform satellite's ECI position (in Three.js coords) to ECEF frame (in Three.js coords)
    // This is equivalent to rotating the ECI frame *back* by Earth's rotation around its Y-axis (which is ECI Z)
    const satPositionECEF_in_ThreeJsCoords = satPositionECI.clone().applyAxisAngle(new THREE.Vector3(0, 1, 0), -earthRotationAngle);

    // 2. Calculate the nadir point on Earth's surface in ECEF frame (in Three.js coords)
    // This point is on the Earth's sphere, directly below the satellite in the Earth-fixed frame
    const nadirPointECEF_in_ThreeJsCoords = satPositionECEF_in_ThreeJsCoords.clone().normalize().multiplyScalar(SCENE_EARTH_RADIUS);

    // 3. Transform the nadir point from ECEF back to ECI frame to draw the line in the scene
    // This is applying the current Earth rotation to the ECEF nadir point
    const nadirPointECI = nadirPointECEF_in_ThreeJsCoords.clone().applyAxisAngle(new THREE.Vector3(0, 1, 0), earthRotationAngle);

    points.push(satPositionECI); // Start point: Satellite's ECI position
    points.push(nadirPointECI); // End point: Nadir point on Earth's surface in ECI

    satellite.nadirLine = new THREE.Line(new THREE.BufferGeometry().setFromPoints(points), new THREE.LineBasicMaterial({ color: 0x888888, linewidth: 2 }));
    scene.add(satellite.nadirLine);
}


/**
 * Satellite class to manage satellite objects in the simulation.
 */
class Satellite {
    constructor(id, name, params, initialMeanAnomaly, initialRAAN, initialEpochUTC, tleLine1 = null, tleLine2 = null) {
        this.id = id;
        this.name = name;
        this.params = { ...params };
        this.initialEpochUTC = initialEpochUTC;
        this.tleLine1 = tleLine1;
        this.tleLine2 = tleLine2;
        this.initialMeanAnomaly = initialMeanAnomaly;
        this.currentMeanAnomaly = initialMeanAnomaly;
        this.currentRAAN = initialRAAN;
        this.initialRAAN = initialRAAN;

        // Parse TLE if provided
        if (this.tleLine1 && this.tleLine2) {
            try {
                this.parsedTle = parseTle(this.tleLine1, this.tleLine2);
                this.initialEpochUTC = this.parsedTle.epochTimestamp; // Use TLE epoch
            } catch (error) {
                console.error(`Failed to parse TLE for satellite ${this.id}:`, error);
                this.parsedTle = null; // Fallback to Keplerian if TLE is invalid
            }
        } else {
            const epochOffsetSeconds = (window.currentEpochUTC - initialEpochUTC) / 1000;
            if (epochOffsetSeconds !== 0) {
                updateOrbitalElements(this, epochOffsetSeconds);
                this.initialMeanAnomaly = this.currentMeanAnomaly;
            }
        }

        this.currentTrueAnomaly = this.parsedTle ? 0 : E_to_TrueAnomaly(solveKepler(this.currentMeanAnomaly, this.params.eccentricity), this.params.eccentricity);

        this.sphereMesh = null;
        this.glbMesh = null;
        this.mesh = null;
        this.orbitLine = null;
        this.coverageCone = null;
        this.nadirLine = null;
        this.prevPosition = new THREE.Vector3();
        this.velocity = new THREE.Vector3();
        this.orbitalVelocityMagnitude = 0;
        this.orbitalPath3DPoints = [];
        this.groundTrackHistory = [];
        this.maxGroundTrackPoints = 300;
        this.isCloseView = false;

        this.createMeshes();
        this.updatePosition(window.totalSimulatedTime, 0);
    }

    /**
     * Creates the initial meshes for the satellite (sphere and GLB placeholder).
     */
    createMeshes() {
        const sphereGeometry = new THREE.SphereGeometry(0.005, 16, 16);
        const sphereMaterial = new THREE.MeshBasicMaterial({ color: 0x0000ff });
        this.sphereMesh = new THREE.Mesh(sphereGeometry, sphereMaterial);
        scene.add(this.sphereMesh); // Satellites added directly to the scene (ECI frame)

        if (satelliteModelLoaded && globalSatelliteGLB) {
            this.setGlbMesh(globalSatelliteGLB);
        }
        this.mesh = this.sphereMesh; // Start with sphere mesh
        this.mesh.visible = true;
    }

    updatePosition(totalSimulatedTimeFromSimulationStart, frameDeltaTime) {
        this.prevPosition.copy(this.mesh.position);

        let newPositionEciThreeJs; // Position in ECI, in Three.js units (scaled by SCENE_EARTH_RADIUS)
        let newVelocityEciThreeJs; // Velocity vector in Three.js units

        // Determine the current absolute UTC time for propagation
        const currentAbsoluteTimeMs = window.currentEpochUTC + (totalSimulatedTimeFromSimulationStart * 1000);
        const currentDateTime = new Date(currentAbsoluteTimeMs);

        if (this.parsedTle) {
            // Use SGP4 propagation for TLE-based satellites
            const sgp4Result = propagateSGP4(this.parsedTle, currentDateTime);
            if (sgp4Result && sgp4Result.position) {
                newPositionEciThreeJs = sgp4Result.position;
                newVelocityEciThreeJs = sgp4Result.velocity || new THREE.Vector3(0,0,0); // Ensure velocity is available
            } else {
                console.warn(`SGP4 propagation failed for satellite ${this.id}. Keeping last known position.`);
                // If propagation fails, keep the current position and velocity zero.
                newPositionEciThreeJs = this.mesh.position;
                newVelocityEciThreeJs = new THREE.Vector3(0,0,0);
            }
        } else {
            // Use traditional Keplerian and J2 perturbation for non-TLE satellites
            const timeSinceSatelliteEpoch = (currentAbsoluteTimeMs - this.initialEpochUTC) / 1000;
            updateOrbitalElements(this, timeSinceSatelliteEpoch);

            const E = solveKepler(this.currentMeanAnomaly, this.params.eccentricity);
            this.currentTrueAnomaly = E_to_TrueAnomaly(E, this.params.eccentricity);

            const { x, y, z } = calculateSatellitePositionECI(
                this.params,
                this.currentMeanAnomaly,
                this.currentRAAN,
                SCENE_EARTH_RADIUS // Pass SCENE_EARTH_RADIUS for scaling
            );
            newPositionEciThreeJs = new THREE.Vector3(x, y, z);
            newVelocityEciThreeJs = new THREE.Vector3(); // Placeholder for Keplerian velocity (can be calculated if needed)
        }

        this.mesh.position.copy(newPositionEciThreeJs);

        // Update velocity (used for camera logic in close view)
        if (frameDeltaTime > 0) {
            this.velocity.copy(this.mesh.position).sub(this.prevPosition).divideScalar(frameDeltaTime);
            // If SGP4 provided velocity, prefer it, otherwise use difference for estimation
            if (this.parsedTle && newVelocityEciThreeJs.length() > 0) {
                this.velocity.copy(newVelocityEciThreeJs); // Directly use SGP4 velocity if available and non-zero
            }
            this.orbitalVelocityMagnitude = this.velocity.length() * (EarthRadius / SCENE_EARTH_RADIUS); // Scale back to real-world units
        } else {
            this.orbitalVelocityMagnitude = 0;
        }

        // Convert current ECI position to latitude and longitude for ground track (2D)
        const eciPosition_in_ThreeJsCoords = this.mesh.position.clone();
        const earthRotationAngleAtTime = window.totalSimulatedTime * window.EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;

        const rotationMatrix = new THREE.Matrix4().makeRotationY(-earthRotationAngleAtTime);
        eciPosition_in_ThreeJsCoords.applyMatrix4(rotationMatrix);

        const latitudeRad = Math.atan2(eciPosition_in_ThreeJsCoords.y, Math.sqrt(eciPosition_in_ThreeJsCoords.x * eciPosition_in_ThreeJsCoords.x + eciPosition_in_ThreeJsCoords.z * eciPosition_in_ThreeJsCoords.z));
        const longitudeRad = Math.atan2(eciPosition_in_ThreeJsCoords.z, -eciPosition_in_ThreeJsCoords.x);

        const longitudeDeg = longitudeRad * (180 / Math.PI);
        const latitudeDeg = latitudeRad * (180 / Math.PI);

        this.groundTrackHistory.push({ lat: latitudeDeg, lon: longitudeDeg });

        if (this.groundTrackHistory.length > this.maxGroundTrackPoints) {
            this.groundTrackHistory.shift();
        }

        updateCoverageCone(this);
        updateNadirLine(this);
        drawOrbitPath(this);
    }

    /**
     * Sets the GLB mesh for the satellite if available.
     * @param {THREE.Object3D} glbModel - The loaded GLB scene object.
     */
    setGlbMesh(glbModel) {
        if (!this.glbMesh) {
            this.glbMesh = glbModel.clone();
            this.glbMesh.scale.set(0.000002, 0.000002, 0.000002); // Adjust scale as needed
            this.glbMesh.visible = false;
            scene.add(this.glbMesh);
        }
    }

    /**
     * Sets the active mesh for the satellite (sphere or GLB model) based on view mode.
     * @param {boolean} isCloseView - True if in close view mode, false otherwise.
     */
    setActiveMesh(isCloseView) {
        if (isCloseView && this.glbMesh) {
            this.mesh = this.glbMesh;
            this.sphereMesh.visible = false;
            this.glbMesh.visible = true;
        } else {
            this.mesh = this.sphereMesh;
            this.sphereMesh.visible = true;
            if (this.glbMesh) this.glbMesh.visible = false;
        }
    }

    /**
     * Disposes of the satellite's meshes and lines to free up memory.
     */
    dispose() {
        if (this.sphereMesh) { scene.remove(this.sphereMesh); this.sphereMesh.geometry.dispose(); this.sphereMesh.material.dispose(); }
        if (this.glbMesh) {
            scene.remove(this.glbMesh);
            this.glbMesh.traverse((child) => {
                if (child.isMesh) {
                    child.geometry.dispose();
                    if (child.material.isMaterial) child.material.dispose();
                    else if (Array.isArray(child.material)) child.material.forEach(mat => mat.dispose());
                }
            });
        }
        if (this.orbitLine) { scene.remove(this.orbitLine); this.orbitLine.geometry.dispose(); this.orbitLine.material.dispose(); }
        if (this.coverageCone) { scene.remove(this.coverageCone); this.coverageCone.geometry.dispose(); this.coverageCone.material.dispose(); }
        if (this.nadirLine) { scene.remove(this.nadirLine); this.nadirLine.geometry.dispose(); this.nadirLine.material.dispose(); }
    }


    updateParametersFromCurrentPosition(newParams, newEpochUTC) {
        if (this.tleLine1 && this.tleLine2) {
            console.warn(`Updating parameters for TLE satellite ${this.id}. Requires new TLE data.`);
            if (newParams.tleLine1 && newParams.tleLine2) {
                this.tleLine1 = newParams.tleLine1;
                this.tleLine2 = newParams.tleLine2;
                try {
                    this.parsedTle = parseTle(this.tleLine1, this.tleLine2);
                    this.initialEpochUTC = this.parsedTle.epochTimestamp;
                } catch (error) {
                    console.error(`Failed to update TLE for satellite ${this.id}:`, error);
                }
            }
        } else {
            const currentE = solveKepler(this.currentMeanAnomaly, this.params.eccentricity);
            const currentTrueAnomaly = E_to_TrueAnomaly(currentE, this.params.eccentricity);
            this.params = { ...newParams };
            this.initialEpochUTC = newEpochUTC;
            const E_new = TrueAnomaly_to_E(currentTrueAnomaly, this.params.eccentricity);
            this.initialMeanAnomaly = E_to_M(E_new, this.params.eccentricity);
            this.initialMeanAnomaly %= (2 * Math.PI);
            if (this.initialMeanAnomaly < 0) this.initialMeanAnomaly += 2 * Math.PI;
            this.initialRAAN = newParams.raanRad;
            this.currentRAAN = this.initialRAAN;
        }
        this.updatePosition(window.totalSimulatedTime, 0);
    }


    updateTrueAnomalyOnly(newTrueAnomalyRad) {
        if (this.parsedTle) {
            console.warn("Cannot update true anomaly directly for TLE satellites. Use TLE update if available.");
            return;
        }
        const E_new = TrueAnomaly_to_E(newTrueAnomalyRad, this.params.eccentricity);
        this.currentMeanAnomaly = E_to_M(E_new, this.params.eccentricity);
        this.currentMeanAnomaly %= (2 * Math.PI);
        if (this.currentMeanAnomaly < 0) this.currentMeanAnomaly += 2 * Math.PI;

        this.initialMeanAnomaly = this.currentMeanAnomaly;
        this.updatePosition(window.totalSimulatedTime, 0);
    }
}


/**
 * GroundStation class to manage ground station objects in the simulation.
 */
class GroundStation {
    /**
     * Constructor for a GroundStation.
     * @param {string} id - Unique ID for the ground station.
     * @param {string} name - Display name for the ground station.
     * @param {number} latitude - Latitude in degrees.
     * @param {number} longitude - Longitude in degrees.
     * @param {number} minElevationAngle - Minimum elevation angle in degrees for line-of-sight.
     */
    constructor(id, name, latitude, longitude, minElevationAngle) {
        this.id = id;
        this.name = name;
        this.latitude = latitude;
        this.longitude = longitude;
        this.minElevationAngle = minElevationAngle;

        this.mesh = null;
        this.coverageCone = null;
        this.createMesh();
    }

    /**
     * Creates the 3D mesh representation of the ground station and adds it to the scene.
     */
    createMesh() {
        const earthRadiusScene = SCENE_EARTH_RADIUS;
        const latRad = this.latitude * DEG2RAD;
        const lonRad = this.longitude * DEG2RAD;

        // Position calculation for Three.js Y-up, matching ECEF interpretation
        // Three.js X = -ECEF X (0 Longitude aligns with -X in Three.js world)
        // Three.js Y = ECEF Z (North Pole)
        // Three.js Z = ECEF Y (90E Longitude aligns with +Z in Three.js world)
        const x = -earthRadiusScene * Math.cos(latRad) * Math.cos(lonRad);
        const y = earthRadiusScene * Math.sin(latRad); // This is ECEF Z
        const z = earthRadiusScene * Math.cos(latRad) * Math.sin(lonRad); // This is ECEF Y

        const sphereGeometry = new THREE.SphereGeometry(0.005, 16, 16); // Small sphere
        const gsMaterial = new THREE.MeshBasicMaterial({ color: 0xffff00 }); // Yellow color
        this.mesh = new THREE.Mesh(sphereGeometry, gsMaterial);
        this.mesh.name = `groundstation-${this.id}-${this.name}`; // Set a unique name
        this.mesh.position.set(x, y, z);

        // Add the ground station mesh to the earthGroup so it rotates with the Earth
        earthGroup.add(this.mesh);

        // Update coverage cone immediately after creating the mesh
        this.updateCoverageCone();
    }

    /**
     * Updates the coverage cone visualization for the ground station.
     */
    updateCoverageCone() {
        // Remove existing cone if it exists to avoid duplicates/memory leaks
        if (this.coverageCone) {
            earthGroup.remove(this.coverageCone);
            this.coverageCone.geometry.dispose();
            this.coverageCone.material.dispose();
            this.coverageCone = null;
        }

        const minElevRad = this.minElevationAngle * DEG2RAD;

        if (minElevRad >= Math.PI / 2) return;
        const GsConeHalfAngle = Math.PI / 2 - minElevRad;

        const visualConeHeight = 0.2; // Made it smaller for better visual integration with small GS sphere

        // Calculate the radius at the top of the cone based on the half angle and height
        const visualConeRadiusAtTop = Math.tan(GsConeHalfAngle) * visualConeHeight;

        // Basic validation for cone dimensions
        if (visualConeHeight <= 0 || visualConeRadiusAtTop <= 0) return;

        // Create a cone geometry.
        // By default, THREE.ConeGeometry creates a cone with its base centered at (0,0,0) and its apex at (0, height, 0).
        const coneGeometry = new THREE.ConeGeometry(visualConeRadiusAtTop, visualConeHeight, 32);

        // Translate the cone so that its apex is at the ground station's position.
        // It moves apex to (0,0,0) if original apex was at (0, height, 0) by translating -height/2.
        coneGeometry.translate(0, -visualConeHeight / 2, 0);

        // Define the material for the cone
        const coneMaterial = new THREE.MeshBasicMaterial({
            color: 0x00ffff, // Cyan color
            transparent: true,
            opacity: 0.1, // Semi-transparent
            side: THREE.DoubleSide // Render both sides of the cone faces
        });
        this.coverageCone = new THREE.Mesh(coneGeometry, coneMaterial);

        // Position the cone's apex at the ground station's mesh position
        this.coverageCone.position.copy(this.mesh.position);
        // Orient the cone to point away from the Earth's center
        // The mesh position is (X_3JS, Y_3JS, Z_3JS) where Y_3JS is ECEF Z (North pole up)
        const upVector = this.mesh.position.clone().normalize();
        this.coverageCone.quaternion.setFromUnitVectors(new THREE.Vector3(0, 1, 0), upVector);
        earthGroup.add(this.coverageCone);
    }

    /**
     * Disposes of the ground station's meshes and lines to free up memory.
     */
    dispose() {
        if (this.mesh) {
            earthGroup.remove(this.mesh);
            this.mesh.geometry.dispose();
            this.mesh.material.dispose();
        }
        if (this.coverageCone) {
            earthGroup.remove(this.coverageCone);
            this.coverageCone.geometry.dispose();
            this.coverageCone.material.dispose();
        }
    }
}


/**
 * Loads the global satellite GLB model.
 * @returns {Promise<THREE.Object3D>} A promise that resolves with the loaded GLB scene.
 */
function loadGlobalGLBModel() {
    if (globalSatelliteGLB) {
        return Promise.resolve(globalSatelliteGLB);
    }
    const gltfLoader = new GLTFLoader();
    const loadingMessageElement = document.getElementById('loading-message');
    if (loadingMessageElement) {
        loadingMessageElement.style.display = 'block';
    }

    return new Promise((resolve, reject) => {
        gltfLoader.load(
            '/Satellitemodel/CALIPSO.glb', // Verify this path and filename
            (gltf) => {
                globalSatelliteGLB = gltf.scene;
                satelliteModelLoaded = true;
                if (loadingMessageElement) {
                    loadingMessageElement.style.display = 'none';
                }
                // Apply GLB mesh to already active satellites
                window.activeSatellites.forEach(sat => sat.setGlbMesh(globalSatelliteGLB));
                // Update active mesh if close view is enabled and GLB model is preferred
                window.activeSatellites.forEach(sat => sat.setActiveMesh(window.closeViewEnabled));
                resolve(globalSatelliteGLB);
            },
            (xhr) => {
                if (xhr.total > 0 && loadingMessageElement) {
                    loadingMessageElement.innerText = `Loading satellite model... ${Math.round(xhr.loaded / xhr.total * 100)}%`;
                }
            },
            (error) => {
                console.error('Error loading GLB model:', error);
                if (loadingMessageElement) {
                    loadingMessageElement.innerText = 'Error loading satellite model. Using spheres.';
                }
                satelliteModelLoaded = false;
                reject(error);
            }
        );
    });
}


init3DScene();// Initialize the 3D scene
loadGlobalGLBModel().catch(() => console.warn("GLB model failed to load, proceeding with sphere models."));

// Window functions for simulation control
window.clearSimulationScene = function() {
    window.activeSatellites.forEach(sat => sat.dispose());
    window.activeSatellites.clear();
    window.activeGroundStations.forEach(gs => gs.dispose());
    window.activeGroundStations.clear();
    window.selectedSatelliteId = null;
    window.closeViewEnabled = false;
    controls.object.up.set(0, 1, 0); // Reset controls up direction
    controls.minDistance = 1.2; // Reset to default initial distance
    controls.maxDistance = 10; // Reset to default max distance
    controls.target.set(0, 0, 0); // Reset controls target to Earth center
    camera.position.set(0, 0, 2.5); // Reset camera position to initial 3D scene Z
    controls.update();
    window.isAnimating = false;
    window.totalSimulatedTime = 0;
    window.currentSpeedMultiplier = 1;
    if(cloudsMesh) cloudsMesh.rotation.y = 0; // Reset clouds differential rotation
    if (typeof window.updateAnimationDisplay === 'function') {
        window.updateAnimationDisplay(); // Update UI after clearing
    }
};

//--------------------------------Generate a new simulation view based on input----------------------------------------

// Function to add or update a satellite in the scene with a specific position.
// This function can be called from an HTML form submission or other UI interaction
window.addOrUpdateSatelliteInScene = function(satelliteData) {
        const uniqueId = satelliteData.id || satelliteData.fileName;
    if (!uniqueId) {
        console.error("Satellite data missing unique ID or fileName.");
        return;
    }
    let existingSat = window.activeSatellites.get(uniqueId);
    // capture any parsed TLE here
    let parsedTle = null;
    const initialEpochUTC = typeof satelliteData.utcTimestamp === 'number'? satelliteData.utcTimestamp: window.currentEpochUTC;

   //User input goes here from HTML form
    const params = {
        semiMajorAxis: SCENE_EARTH_RADIUS + (satelliteData.altitude / (EarthRadius / SCENE_EARTH_RADIUS)),
        inclinationRad: satelliteData.inclination * DEG2RAD,
        eccentricity: satelliteData.eccentricity,
        raan: satelliteData.raan * DEG2RAD,
        argPerigeeRad: satelliteData.argumentOfPerigee * DEG2RAD,
        beamWidthDeg: satelliteData.beamwidth,
    };

      // If user supplied TLE lines, parse once
  if (satelliteData.tleLine1 && satelliteData.tleLine2) {
    try {
      parsedTle = parseTle(satelliteData.tleLine1, satelliteData.tleLine2);
      // reset global clock to the TLE epoch
      window.currentEpochUTC = parsedTle.epochTimestamp;
      window.totalSimulatedTime = 0;
    } catch (err) {
      console.error("Invalid TLE:", err);
      satelliteData.tleLine1 = satelliteData.tleLine2 = null;
    }
  }

    if (existingSat) {
        if (parsedTle) {
            existingSat.tleLine1     = satelliteData.tleLine1;
            existingSat.tleLine2     = satelliteData.tleLine2;
            existingSat.parsedTle    = parsedTle;
            existingSat.initialEpochUTC = parsedTle.epochTimestamp;  // ← make sure to update its epoch too
        } else {
        existingSat.updateParametersFromCurrentPosition(params, initialEpochUTC);
        }
        existingSat.name = satelliteData.name || uniqueId;
    }else{
             const newSat = new Satellite(
             uniqueId,
            satelliteData.name || uniqueId,
            params,
            /* initialMeanAnomaly */ 
            satelliteData.trueAnomaly? E_to_M(TrueAnomaly_to_E(satelliteData.trueAnomaly * DEG2RAD, satelliteData.eccentricity), satelliteData.eccentricity): 0,
            satelliteData.raan * DEG2RAD,
            // if we parsed a TLE, use its epoch, otherwise fallback
            parsedTle? parsedTle.epochTimestamp: initialEpochUTC,
            satelliteData.tleLine1,
            satelliteData.tleLine2
        );
        if (satelliteModelLoaded && globalSatelliteGLB) {
            newSat.setGlbMesh(globalSatelliteGLB);
        }
        newSat.setActiveMesh(window.closeViewEnabled);
        window.activeSatellites.set(newSat.id, newSat);
    }

    if (typeof window.updateAnimationDisplay === 'function') {
        window.updateAnimationDisplay();
    }
};


window.addOrUpdateGroundStationInScene = function(gsData) {
    const uniqueId = gsData.id || gsData.name;
    if (!uniqueId) {
        console.error("Ground station data missing unique ID or name.");
        return;
    }

    let existingGs = window.activeGroundStations.get(uniqueId);
    if (existingGs) {
        // If updating an existing one, dispose and recreate to ensure mesh/cone updates correctly
        existingGs.name = gsData.name;
        existingGs.latitude = gsData.latitude;
        existingGs.longitude = gsData.longitude;
        existingGs.minElevationAngle = gsData.minElevationAngle;
        existingGs.dispose(); // Dispose old meshes
        existingGs.createMesh(); // Create new meshes with updated properties
    } else {
        const newGs = new GroundStation(
            uniqueId,
            gsData.name,
            gsData.latitude,
            gsData.longitude,
            gsData.minElevationAngle
        );
        window.activeGroundStations.set(newGs.id, newGs);
    }
};


//Function to view/Render a simulation based on provided data
window.viewSimulation = function(data) {
    window.clearSimulationScene(); // Clear any existing objects
    // Set epoch for the simulation
    if (typeof data.utcTimestamp === 'number' && !(data.tleLine1 && data.tleLine2)) {
        window.currentEpochUTC = data.utcTimestamp;
    }
        if (data.fileType === 'single') {
        window.addOrUpdateSatelliteInScene({
            id: data.fileName,
            name: data.fileName,
            altitude: data.altitude,
            inclination: data.inclination,
            eccentricity: data.eccentricity,
            raan: data.raan,
            argumentOfPerigee: data.argumentOfPerigee,
            trueAnomaly: data.trueAnomaly,
            epoch: data.epoch,
            beamwidth: data.beamwidth,
            tleLine1: data.tleLine1,
            tleLine2: data.tleLine2
        });
        window.selectedSatelliteId = data.fileName;
        window.isAnimating = false;
    } else if (data.fileType === 'constellation' || data.fileType === 'linkBudget') {
        const constellationParams = data;
        let baseSatelliteParams;
        if (data.fileType === 'constellation') {
            baseSatelliteParams = {
                altitude: constellationParams.altitude,
                inclination: constellationParams.inclination,
                eccentricity: constellationParams.eccentricity,
                raan: constellationParams.raan,
                argumentOfPerigee: constellationParams.argumentOfPerigee,
                trueAnomaly: constellationParams.trueAnomaly,
                epoch: constellationParams.epoch,
                beamwidth: constellationParams.beamwidth,
                tleLine1: constellationParams.tleLine1,
                tleLine2: constellationParams.tleLine2
            };
        } else {
            baseSatelliteParams = {
                altitude: constellationParams.orbitHeight,
                inclination: constellationParams.orbitInclination,
                eccentricity: 0.0,
                raan: 0,
                argumentOfPerigee: 0,
                trueAnomaly: 0,
                epoch: window.currentEpochUTC,
                beamwidth: 0,
                tleLine1: constellationParams.tleLine1,
                tleLine2: constellationParams.tleLine2
            };
        }


        let satelliteCounter = 0; // Counter for satellite names
        window.selectedSatelliteId = null; // Reset selected satellite (Added for clarity/Remove if not needed)

        if (constellationParams.constellationType === 'train') {
            const numSatellites = constellationParams.numSatellites;
            const separationType = constellationParams.separationType;
            const separationValue = constellationParams.separationValue;
            const separationDirection = constellationParams.separationDirection || 'Forward';

            let initialMeanAnomalyBase = E_to_M(TrueAnomaly_to_E(baseSatelliteParams.trueAnomaly * DEG2RAD, baseSatelliteParams.eccentricity), baseSatelliteParams.eccentricity);
            const derivedOrbitalParams = calculateDerivedOrbitalParameters(baseSatelliteParams.altitude, baseSatelliteParams.eccentricity, EarthRadius);
            const orbitalPeriodSeconds = derivedOrbitalParams.orbitalPeriod;

            let meanAnomalySpacing = 0;
            if (separationType === "meanAnomaly") {
                meanAnomalySpacing = separationValue * DEG2RAD;
            } else if (separationType === "time") {
                const meanMotionRadPerSec = (2 * Math.PI) / orbitalPeriodSeconds;
                meanAnomalySpacing = meanMotionRadPerSec * separationValue;
            }

            if (separationDirection === "Backward") {
                meanAnomalySpacing *= -1;
            }

            for (let i = 0; i < numSatellites; i++) {
                satelliteCounter++;
                const satName = `${constellationParams.fileName || constellationParams.name}_Sat${satelliteCounter}`;
                let currentSatelliteInitialMA = (initialMeanAnomalyBase + (i * meanAnomalySpacing));
                currentSatelliteInitialMA = currentSatelliteInitialMA % (2 * Math.PI);
                if (currentSatelliteInitialMA < 0) currentSatelliteInitialMA += 2 * Math.PI;

                const satData = {
                    id: `${constellationParams.fileName || constellationParams.name}-${Date.now()}-${satelliteCounter}`,
                    name: satName,
                    altitude: baseSatelliteParams.altitude,
                    inclination: baseSatelliteParams.inclination,
                    eccentricity: baseSatelliteParams.eccentricity,
                    raan: baseSatelliteParams.raan,
                    argumentOfPerigee: baseSatelliteParams.argumentOfPerigee,
                    trueAnomaly: E_to_TrueAnomaly(solveKepler(currentSatelliteInitialMA, baseSatelliteParams.eccentricity), baseSatelliteParams.eccentricity) * (180 / Math.PI),
                    epoch: baseSatelliteParams.epoch,
                    beamwidth: baseSatelliteParams.beamwidth,
                    fileType: 'single',
                    tleLine1: null, // Do not propagate TLEs to train constellation satellites
                    tleLine2: null
                };
                window.addOrUpdateSatelliteInScene(satData);
                window.isAnimating = false;
            }
        } else if (constellationParams.constellationType === 'walker') {
            // Ensure parameters are integers for counts and floats for angles
            const P = parseInt(constellationParams.numPlanes) || 1; // Number of planes
            const S = parseInt(constellationParams.satellitesPerPlane) || 1; // Satellites per plane
            const F = parseInt(constellationParams.phasingFactor) || 0; // Phasing factor (relative spacing between satellites in adjacent planes)
            const RAAN_spread_deg = parseFloat(constellationParams.raanSpread) || 360; // Total RAAN spread

            const totalSatellites = P * S;
            if (totalSatellites === 0) {
                console.warn("No satellites to create for Walker constellation (P*S=0).");
                // Ensure scene is rendered even if no satellites
                const core3D = window.getSimulationCoreObjects();
                if (core3D.renderer) core3D.renderer.render(core3D.scene, core3D.camera);
                if (typeof window.updateAnimationDisplay === 'function') {
                    window.updateAnimationDisplay();
                }
                return;
            }

            const RAAN_spacing_per_plane_rad = (RAAN_spread_deg / P) * DEG2RAD;
            const MA_spacing_in_plane_rad = (2 * Math.PI) / S;
            const MA_phase_shift_between_planes_rad = (F * (2 * Math.PI)) / totalSatellites;

            const initialMeanAnomaly_seed_rad = E_to_M(TrueAnomaly_to_E(baseSatelliteParams.trueAnomaly * DEG2RAD, baseSatelliteParams.eccentricity), baseSatelliteParams.eccentricity);

            for (let p = 0; p < P; p++) {
                const currentPlaneRAAN_rad = (baseSatelliteParams.raan * DEG2RAD + (p * RAAN_spacing_per_plane_rad));
                // Normalize RAAN to 0 to 2*PI
                const normalizedRAAN_rad = currentPlaneRAAN_rad % (2 * Math.PI);
                const finalRAAN_rad = normalizedRAAN_rad < 0 ? normalizedRAAN_rad + 2 * Math.PI : normalizedRAAN_rad;

                for (let s = 0; s < S; s++) {
                    satelliteCounter++;
                    const satName = `${constellationParams.fileName || constellationParams.name}_Sat${satelliteCounter}`;

                    let currentSatelliteInitialMA = initialMeanAnomaly_seed_rad;
                    // Spacing within plane
                    currentSatelliteInitialMA = (currentSatelliteInitialMA + (s * MA_spacing_in_plane_rad));
                    // Phase shift between planes
                    currentSatelliteInitialMA = (currentSatelliteInitialMA + (p * MA_phase_shift_between_planes_rad));

                    // Normalize MA to 0 to 2*PI
                    const normalizedMA_rad = currentSatelliteInitialMA % (2 * Math.PI);
                    const finalMA_rad = normalizedMA_rad < 0 ? normalizedMA_rad + 2 * Math.PI : normalizedMA_rad;

                    const satData = {
                        id: `${constellationParams.fileName || constellationParams.name}-${Date.now()}-${satelliteCounter}`,
                        name: satName,
                        altitude: baseSatelliteParams.altitude,
                        inclination: baseSatelliteParams.inclination,
                        eccentricity: baseSatelliteParams.eccentricity,
                        raan: finalRAAN_rad * (180 / Math.PI), // Convert back to degrees for satData
                        argumentOfPerigee: baseSatelliteParams.argumentOfPerigee,
                        trueAnomaly: E_to_TrueAnomaly(solveKepler(finalMA_rad, baseSatelliteParams.eccentricity), baseSatelliteParams.eccentricity) * (180 / Math.PI), // Convert back to degrees
                        epoch: baseSatelliteParams.epoch,
                        beamwidth: baseSatelliteParams.beamwidth,
                        fileType: 'single',
                        // Pass TLEs to individual satellites if present in base params
                        tleLine1: null, // Do not propagate TLEs to walker constellation satellites
                        tleLine2: null
                    };
                    window.addOrUpdateSatelliteInScene(satData);
                    window.isAnimating = false;
                }
            }
        }
        // Select the first satellite in the constellation for initial close view if applicable
        window.selectedSatelliteId = window.activeSatellites.keys().next().value;
    } else if (data.fileType === 'groundStation') {
        window.addOrUpdateGroundStationInScene({
            id: data.name, // Using name as ID for ground stations
            name: data.name,
            latitude: data.latitude,
            longitude: data.longitude,
            minElevationAngle: data.minElevationAngle
        });
        // If only a ground station is loaded and no satellites, center camera on it
        if (window.activeSatellites.size === 0) {
            const gs = window.activeGroundStations.get(data.name);
            if (gs) {
                camera.position.set(gs.mesh.position.x * 2, gs.mesh.position.y * 2, gs.mesh.position.z * 2 + 1);
                controls.target.copy(gs.mesh.position);
                controls.update();
            }
        }
    }
    // Ensure the scene is rendered after loading new objects
    const core3D = window.getSimulationCoreObjects();
    if (core3D.renderer) {
        core3D.renderer.render(core3D.scene, core3D.camera);
    }
    // Update UI after adding objects
    if (typeof window.updateAnimationDisplay === 'function') {
        window.updateAnimationDisplay();
    }
};


window.removeObjectFromScene = function(idToRemove, type) {
    if (type === 'satellite') {
        const sat = window.activeSatellites.get(idToRemove);
        if (sat) {
            sat.dispose();
            window.activeSatellites.delete(idToRemove);
            if (window.selectedSatelliteId === idToRemove) {
                window.selectedSatelliteId = null;
                // If selected sat removed, reset camera to Earth center view
                controls.object.up.set(0, 1, 0);
                controls.minDistance = 1.2; // Reset to default initial distance
                controls.maxDistance = 10; // Reset to default max distance
                controls.target.set(0, 0, 0);
                camera.position.set(0, 0, 2.5); // Reset camera position to initial 3D scene Z
                controls.update();
                window.closeViewEnabled = false; // Disable close view
            }
        }
    } else if (type === 'groundStation') {
        const gs = window.activeGroundStations.get(idToRemove);
        if (gs) {
            gs.dispose();
            window.activeGroundStations.delete(idToRemove);
        }
    }
    // Update UI after removing objects
    if (typeof window.updateAnimationDisplay === 'function') {
        window.updateAnimationDisplay();
    }
};


/**
 * Function to get the core 3D simulation objects for external access.
 * @returns {object} An object containing references to key 3D scene elements and state.
 */
window.getSimulationCoreObjects = function() {
    return {
        scene: scene,
        camera: camera,
        renderer: renderer,
        controls: controls,
        earthGroup: earthGroup,
        activeSatellites: window.activeSatellites,
        activeGroundStations: window.activeGroundStations,
        isAnimating: window.isAnimating,
        is2DViewActive:  window.is2DViewActive,    // ← add this
        currentSpeedMultiplier: window.currentSpeedMultiplier,
        totalSimulatedTime: window.totalSimulatedTime,
        selectedSatelliteId: window.selectedSatelliteId,
        closeViewEnabled: window.closeViewEnabled,
        currentEpochUTC: window.currentEpochUTC,
        setTotalSimulatedTime: (time) => { window.totalSimulatedTime = time; },
        setIsAnimating: (state) => { window.isAnimating = state; },
        setCurrentSpeedMultiplier: (speed) => { window.currentSpeedMultiplier = speed; },
        setSelectedSatelliteId: (id) => { window.selectedSatelliteId = id; },
        setCloseViewEnabled: (state) => {
        window.closeViewEnabled = state;
        window.activeSatellites.forEach(sat => sat.setActiveMesh(state)); // Update satellite mesh visibility
        },
        setCurrentEpochUTC: (epoch) => { window.currentEpochUTC = epoch; }
    };
};


/**
 * Function to load the 3D simulation state from serialized data.
 * It re-creates satellites and ground stations based on existing active data.
 */
window.load3DSimulationState = function() {
    const satellitesToRecreate = new Map(window.activeSatellites);
    window.activeSatellites.clear();
    satellitesToRecreate.forEach(satData => {
        window.addOrUpdateSatelliteInScene({
            id: satData.id,
            name: satData.name,
            // Revert scaling if necessary, ensure these are original values
            altitude: (satData.params.semiMajorAxis - SCENE_EARTH_RADIUS) * (EarthRadius / SCENE_EARTH_RADIUS),
            inclination: satData.params.inclinationRad * (180 / Math.PI),
            eccentricity: satData.params.eccentricity,
            raan: satData.initialRAAN * (180 / Math.PI),
            trueAnomaly: E_to_TrueAnomaly(solveKepler(satData.initialMeanAnomaly, satData.params.eccentricity), satData.params.eccentricity) * (180 / Math.PI),
            epoch: new Date(satData.initialEpochUTC).toISOString(),
            beamwidth: satData.params.beamWidthDeg,
            tleLine1: satData.tleLine1,
            tleLine2: satData.tleLine2
        });
    });

    const groundStationsToRecreate = new Map(window.activeGroundStations);
    window.activeGroundStations.clear();
    groundStationsToRecreate.forEach(gsData => {
        window.addOrUpdateGroundStationInScene({
            id: gsData.id,
            name: gsData.name,
            latitude: gsData.latitude,
            longitude: gsData.longitude,
            minElevationAngle: gsData.minElevationAngle
        });
    });

    const core3D = window.getSimulationCoreObjects();
    if (core3D.renderer) {
        core3D.renderer.render(core3D.scene, core3D.camera);
    }
    if (typeof window.updateAnimationDisplay === 'function') {
        window.updateAnimationDisplay();
    }
};


/**
 * Main animation loop for the simulation.
 * It updates Earth's rotation, satellite positions, and other visual elements.
 */
function animate() { // timestamp is passed by requestAnimationFrame
    requestAnimationFrame(animate);
    const currentTime = performance.now();
    const frameDeltaTime = (currentTime - lastAnimationFrameTime) / 1000; // Seconds
    lastAnimationFrameTime = currentTime;

    // --- update earth & glow uniforms ---
    earthMesh.material.uniforms.uSunDirection.value.copy(sunLight.position).normalize();
    earthMesh.material.uniforms.uCameraPosition.value.copy(camera.position);
    if (atmosphereGlowMesh?.material?.uniforms) {
        atmosphereGlowMesh.material.uniforms.uSunDirection.value.copy(sunLight.position).normalize();
        atmosphereGlowMesh.material.uniforms.uCameraPosition.value.copy(camera.position);
    }

    const core3D = window.getSimulationCoreObjects();

    // --- advance simulation time & rotate Earth ---
    if (core3D.isAnimating) {
        core3D.setTotalSimulatedTime(
            core3D.totalSimulatedTime + frameDeltaTime * core3D.currentSpeedMultiplier
        );
        const earthRotationAngle = core3D.totalSimulatedTime * EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;
        earthGroup.rotation.y = earthRotationAngle;

        if (cloudsMesh) {
            const diffSpeed = 0.05 * EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;
            cloudsMesh.rotation.y += diffSpeed * frameDeltaTime;
        }

        core3D.activeSatellites.forEach(sat =>
            sat.updatePosition(core3D.totalSimulatedTime, frameDeltaTime)
        );
        core3D.activeGroundStations.forEach(gs => gs.updateCoverageCone());
    }

    // --- update sun & shader time uniform ---
    updateSunDirection(core3D.totalSimulatedTime);
    earthMesh.material.uniforms.uTime.value = core3D.totalSimulatedTime;

    // --- render 3D every frame ---
    renderer.render(scene, camera);

    // --- then overlay 2D if requested ---
    if (core3D.is2DViewActive && typeof window.draw2D === 'function') {
        window.draw2D();
    }

    // --- UI callbacks ---
    if (typeof window.updateAnimationDisplay === 'function') {
        window.updateAnimationDisplay();
    }
    if (core3D.selectedSatelliteId && typeof window.updateSatelliteDataDisplay === 'function') {
        window.updateSatelliteDataDisplay();
    }

    // Camera/controls logic for close view
    if (core3D.closeViewEnabled && core3D.selectedSatelliteId) {
        const selectedSat = core3D.activeSatellites.get(core3D.selectedSatelliteId);
        if (selectedSat) {
            const currentPos = selectedSat.mesh.position.clone(); // Satellite position is in ECI
            const forwardDir = selectedSat.velocity.length() > 0 ? selectedSat.velocity.clone().normalize() : new THREE.Vector3(0, 0, 1);

            // Calculate 'up' vector relative to the Earth's center from the satellite's current ECI position
            const upDir = currentPos.clone().normalize();

            // Adjust camera offset for a good view of the satellite
            // These values (0.08, 0.04) are scale-dependent. They relate to SCENE_EARTH_RADIUS.
            const cameraOffset = forwardDir.clone().multiplyScalar(-SCENE_EARTH_RADIUS * 0.08).add(upDir.clone().multiplyScalar(SCENE_EARTH_RADIUS * 0.04));
            const desiredCameraPos = currentPos.clone().add(cameraOffset);

            controls.enabled = false; // Disable direct user control during close-view animation

            gsap.to(camera.position, {
                duration: 0.15,
                x: desiredCameraPos.x,
                y: desiredCameraPos.y,
                z: desiredCameraPos.z,
                ease: "none",
                onUpdate: () => controls.update(),
                onComplete: () => {
                    if (core3D.closeViewEnabled) { // Re-enable controls only if still in close view mode
                        controls.enabled = true;
                    }
                }
            });
            gsap.to(controls.target, {
                duration: 0.15,
                x: currentPos.x,
                y: currentPos.y,
                z: currentPos.z,
                ease: "none",
                onUpdate: () => controls.update()
            });

            // Make the camera's 'up' vector align with the satellite's 'up' relative to Earth
            controls.object.up.copy(upDir);
            controls.update(); // Update controls immediately after setting .up
            controls.minDistance = SCENE_EARTH_RADIUS * 0.01; // Restrict zoom in close view (e.g., 0.01 scene units from target)
            controls.maxDistance = SCENE_EARTH_RADIUS * 0.2; // Restrict zoom out in close view (e.g., 0.2 scene units from target)
        } else {
            console.warn("Selected satellite not found for close view. Disabling close view.");
            core3D.setCloseViewEnabled(false); // Use setter to update global state and UI
            // Reset controls to default Earth view settings
            controls.object.up.set(0, 1, 0);
            controls.minDistance = 1.2; // Restore default initial distance
            controls.maxDistance = 10; // Restore default max distance
            controls.enabled = true; // Re-enable controls
            controls.update(); // Ensure controls are updated after reset
        }
    } else if (!core3D.closeViewEnabled) {
        controls.enabled = true; // Ensure controls are enabled if not in close view
        // Reset controls limits and up vector if just exited close view or always in normal view
        // Only reset if they are not already at default values to avoid unnecessary updates
        if (controls.minDistance !== 1.2 || controls.maxDistance !== 10 || controls.object.up.y !== 1) {
            controls.object.up.set(0, 1, 0);
            controls.minDistance = 1.2;
            controls.maxDistance = 10;
            controls.update();
        }
    }
}

// Expose earthGroup's current Y-rotation globally for 2D simulation
window.getEarthRotationY = () => earthGroup.rotation.y;

// Start the animation loop
animate();

// Handle window resize
window.addEventListener('resize', () => {
    const earthContainer = document.getElementById('earth-container');
    if (earthContainer && camera && renderer) {
        const newWidth = earthContainer.offsetWidth;
        const newHeight = earthContainer.offsetHeight;
        renderer.setSize(newWidth, newHeight);
        camera.aspect = newWidth / newHeight;
        camera.updateProjectionMatrix();
        controls.update(); // Update controls after camera aspect changes
    }
});
