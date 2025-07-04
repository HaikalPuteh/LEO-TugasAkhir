// Earth3Dsimulation.js
import * as THREE from "three";
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import getStarfield from "../getStarfield.js";
import { glowmesh } from "../glowmesh.js";
import { gsap } from 'gsap';

// Import astronomical calculation functions from the new shared utility file
import { getSunCoords } from "../sunCalculations.js"; // Updated import path

import {
    solveKepler,
    E_to_TrueAnomaly,
    TrueAnomaly_to_E,
    E_to_M,
    updateOrbitalElements,
    calculateSatellitePositionECI,
    calculateDerivedOrbitalParameters,
} from "../orbitalCalculation.js";

import {
    DEG2RAD,
    EarthRadius, //Km
    EARTH_ANGULAR_VELOCITY_RAD_PER_SEC,
    SCENE_EARTH_RADIUS    
} from "../parametersimulation.js";


// Scene variables
let camera, scene, renderer, controls, earthGroup;
let earthMesh, lightsMesh, cloudsMesh, atmosphereGlowMesh, earthEdgeGlowMesh;
let sunLight;
let earthEdgeGlowMaterial;

// Global state variables
window.activeSatellites = new Map();
window.activeGroundStations = new Map();
window.selectedSatelliteId = null;
window.isAnimating = false;
window.closeViewEnabled = false;
window.totalSimulatedTime = 0;
window.currentSpeedMultiplier = 1;
window.currentEpochUTC = new Date('2025-01-01T00:00:00Z').getTime(); // Base epoch for simulation time
window.EARTH_ANGULAR_VELOCITY_RAD_PER_SEC = EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;
window.is2DViewActive = false; //New


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

    // Set up renderer
    renderer = new THREE.WebGLRenderer({ antialias: true, logarithmicDepthBuffer: true });
    renderer.setSize(earthContainer.offsetWidth, earthContainer.offsetHeight);
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    earthContainer.appendChild(renderer.domElement);

    // Initialize scene and camera
    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera(75, earthContainer.offsetWidth / earthContainer.offsetHeight, 0.0001, 1000);
    camera.position.z = 5;

    // Set up orbit controls
    controls = new OrbitControls(camera, renderer.domElement);
    controls.minDistance = 0.001;
    controls.maxDistance = 1000;

    // Create Earth group - this group will rotate to simulate Earth's rotation
    earthGroup = new THREE.Group();
    scene.add(earthGroup); // Earth group is added to the scene

    // Load textures and create Earth geometry
    const textureLoader = new THREE.TextureLoader();
    const earthGeometry = new THREE.IcosahedronGeometry(SCENE_EARTH_RADIUS, 25);

    // Earth mesh with textures
    earthMesh = new THREE.Mesh(earthGeometry, new THREE.MeshPhongMaterial({
        map: textureLoader.load("/textures/earthmap10k.jpg"),
        specularMap: textureLoader.load("/textures/earthspec10k.jpg"),
        bumpMap: textureLoader.load("/textures/earthbump10k.jpg"),
        bumpScale: 0.04,
    }));
    earthMesh.rotation.y = Math.PI; // Apply initial 180-degree rotation to align the texture's Prime Meridian
    earthGroup.add(earthMesh); // Add to the rotating group

    // Lights mesh for night lights
    lightsMesh = new THREE.Mesh(earthGeometry, new THREE.MeshBasicMaterial({
        map: textureLoader.load("/textures/earthlights10k.jpg"),
        blending: THREE.AdditiveBlending
    }));
    lightsMesh.scale.setScalar(SCENE_EARTH_RADIUS * 1.001);
    lightsMesh.rotation.y = Math.PI; // Apply same initial rotation
    earthGroup.add(lightsMesh); // Add to the rotating group

    // Clouds mesh
    cloudsMesh = new THREE.Mesh(earthGeometry, new THREE.MeshStandardMaterial({
        map: textureLoader.load("/textures/04_earthcloudmap.jpg"),
        transparent: true,
        opacity: 0.1,
        blending: THREE.AdditiveBlending,
        alphaMap: textureLoader.load("/textures/05_earthcloudmaptrans.jpg")
    }));
    cloudsMesh.scale.setScalar(SCENE_EARTH_RADIUS * 1.003);
    cloudsMesh.rotation.y = Math.PI; // Apply same initial rotation
    // cloudsMesh will be a child of earthGroup, and its rotation will be handled differentially
    earthGroup.add(cloudsMesh);

    // Atmosphere glow mesh
    atmosphereGlowMesh = new THREE.Mesh(earthGeometry, glowmesh());
    atmosphereGlowMesh.scale.setScalar(SCENE_EARTH_RADIUS * 1.01);
    atmosphereGlowMesh.rotation.y = Math.PI; // Apply same initial rotation
    earthGroup.add(atmosphereGlowMesh); // Add to the rotating group

    // Earth edge glow mesh with shader
    earthEdgeGlowMaterial = new THREE.ShaderMaterial({
        uniforms: {
            'c': { type: 'f', value: 0.1 },
            'p': { type: 'f', value: 2.0 },
            glowColor: { type: 'c', value: new THREE.Color(0x0088ff) },
            viewVector: { type: 'v3', value: new THREE.Vector3() }
        },
        vertexShader: `
            uniform vec3 viewVector;
            uniform float c;
            uniform float p;
            varying float intensity;
            void main() {
                vec3 vNormal = normalize( normalMatrix * normal );
                intensity = pow( c - dot(vNormal, viewVector), p );
                gl_Position = projectionMatrix * modelViewMatrix * vec4( position, 1.0 );
            }
        `,
        fragmentShader: `
            uniform vec3 glowColor;
            varying float intensity;
            void main() {
                gl_FragColor = vec4( glowColor * intensity, 1.0 );
            }
        `,
        side: THREE.BackSide,
        blending: THREE.AdditiveBlending,
        transparent: true
    });
    earthEdgeGlowMesh = new THREE.Mesh(earthGeometry, earthEdgeGlowMaterial);
    earthEdgeGlowMesh.scale.setScalar(SCENE_EARTH_RADIUS * 1.000000001);
    earthEdgeGlowMesh.rotation.y = Math.PI; // Apply same initial rotation
    earthGroup.add(earthEdgeGlowMesh); // Add to the rotating group

    // Add starfield
    scene.add(getStarfield({ numStars: 4000 }));

    // Sun light setup
    sunLight = new THREE.DirectionalLight(0xffffff, 1.0);
    sunLight.castShadow = true;
    sunLight.shadow.mapSize.width = 1024;
    sunLight.shadow.mapSize.height = 1024;
    sunLight.shadow.camera.near = 0.5;
    sunLight.shadow.camera.far = 500;
    scene.add(sunLight);
}

// Removed: All local degToRad, toJulian, getSunCoords, siderealTime functions.
// They are now imported from sunCalculations.js.

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
    // Note: The previous logic of (-sunX_eci, sunZ_eci, sunY_eci) for sunPos_threeJsX/Y/Z 
    // combined with a negated light direction was causing a 180-degree longitude offset.
    // By setting sunPos_threeJsX to positive ECI X, and rotating the Earth meshes by Math.PI,
    // we achieve the correct visual alignment and light direction.
    const sunPos_threeJsX = sunX_eci; 
    const sunPos_threeJsY = sunZ_eci;  
    const sunPos_threeJsZ = sunY_eci;  

    // For a THREE.DirectionalLight, its 'position' property defines the *direction*
    // of the light from that position *towards* the target (default: origin 0,0,0).
    // So, if the vector (sunPos_threeJsX, sunPos_threeJsY, sunPos_threeJsZ)
    // points from the origin to the sun, then setting the light's position to
    // this vector will correctly make the light shine from the sun towards the origin.
    sunLight.position.set(sunPos_threeJsX, sunPos_threeJsY, sunPos_threeJsZ).normalize().multiplyScalar(5);

    // Update the Earth edge glow shader's view vector
    if (earthEdgeGlowMaterial) {
        const viewVector = new THREE.Vector3();
        earthEdgeGlowMesh.worldToLocal(viewVector.copy(camera.position));
        earthEdgeGlowMaterial.uniforms.viewVector.value.copy(viewVector.normalize());
    }
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
    /**
     * Constructor initializes the satellite with its parameters and initial state.
     * @param {string} id - Unique identifier for the satellite.
     * @param {string} name - Display name for the satellite.
     * @param {object} params - Orbital parameters (semiMajorAxis, eccentricity, inclinationRad, argPerigeeRad, beamWidthDeg).
     * @param {number} initialMeanAnomaly - Initial Mean Anomaly in radians.
     * @param {number} initialRAAN - Initial Right Ascension of the Ascending Node in radians.
     * @param {number} initialEpochUTC - Initial epoch of the satellite in UTC milliseconds.
     */
    constructor(id, name, params, initialMeanAnomaly, initialRAAN, initialEpochUTC) {
        this.id = id;
        this.name = name;
        this.params = { ...params };
        this.initialEpochUTC = initialEpochUTC;

        const epochOffsetSeconds = (window.currentEpochUTC - initialEpochUTC) / 1000;
        this.initialMeanAnomaly = initialMeanAnomaly;
        this.currentMeanAnomaly = initialMeanAnomaly;
        this.currentRAAN = initialRAAN;
        this.initialRAAN = initialRAAN;

        if (epochOffsetSeconds !== 0) {
            updateOrbitalElements(this, epochOffsetSeconds);
            this.initialMeanAnomaly = this.currentMeanAnomaly; // Update initial MA to current
        }

        this.currentTrueAnomaly = E_to_TrueAnomaly(solveKepler(this.currentMeanAnomaly, this.params.eccentricity), this.params.eccentricity);

        this.sphereMesh = null;
        this.glbMesh = null;
        this.mesh = null; // Reference to the active mesh (sphere or GLB)

        this.orbitLine = null;
        this.coverageCone = null;
        this.nadirLine = null;

        this.prevPosition = new THREE.Vector3();
        this.velocity = new THREE.Vector3();
        this.orbitalVelocityMagnitude = 0;

        this.createMeshes();


        // NEW: Properties to store data for 2D rendering
        this.orbitalPath3DPoints = [];   // Stores the 3D points of the entire orbit (for 2D orbital path)
        this.groundTrackHistory = [];    // Stores {lat, lon} pairs for the ground track trail
        this.maxGroundTrackPoints = 300; // Limit for ground track history length to prevent performance issues (adjust as needed)
        this.isCloseView = false; // Flag to indicate if the satellite is in close view mode
        // Update position once to initialize
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


    /**
     * Updates the satellite's position based on the current simulation time and frame delta time.
     * @param {number} totalSimulatedTimeFromSimulationStart - Total elapsed simulation time in seconds.
     * @param {number} frameDeltaTime - Time elapsed since the last frame in seconds.
     */
    updatePosition(totalSimulatedTimeFromSimulationStart, frameDeltaTime) {
        const timeSinceSatelliteEpoch = (window.currentEpochUTC + totalSimulatedTimeFromSimulationStart * 1000 - this.initialEpochUTC) / 1000;
        updateOrbitalElements(this, timeSinceSatelliteEpoch);

        const E = solveKepler(this.currentMeanAnomaly, this.params.eccentricity);
        this.currentTrueAnomaly = E_to_TrueAnomaly(E, this.params.eccentricity);

        this.prevPosition.copy(this.mesh.position);

        // Calculate satellite position in ECI, scaled to scene units
        const { x, y, z } = calculateSatellitePositionECI(
            this.params,
            this.currentMeanAnomaly,
            this.currentRAAN,
            SCENE_EARTH_RADIUS // Pass SCENE_EARTH_RADIUS for scaling
        );
        this.mesh.position.set(x, y, z); // Position relative to scene origin (ECI)

        // Convert ECI position to latitude and longitude for ground track (2D)
        // satellite.mesh.position is already in Three.js ECI coordinates.
        // We need to transform it to Earth-Fixed (ECEF) coordinates for ground track.
        const eciPosition_in_ThreeJsCoords = this.mesh.position.clone();
        // The Earth's rotation angle in ECI frame at the current simulated time
        // This is `earthGroup.rotation.y`
        const earthRotationAngleAtTime = window.totalSimulatedTime * window.EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;

        // Rotate ECI position (in Three.js coords) by -earthRotationAngleAtTime around the Y-axis (Earth's axis) to get ECEF (in Three.js coords)
        const rotationMatrix = new THREE.Matrix4().makeRotationY(-earthRotationAngleAtTime);
        eciPosition_in_ThreeJsCoords.applyMatrix4(rotationMatrix); // Now this vector is in ECEF, still in Three.js coordinates.

        // Now convert ECEF (in Three.js coords) to Lat/Lon
        // Based on the coordinate system mapping:
        // Three.js X (ECEF) = -ECI X (unrotated)
        // Three.js Y (ECEF) = ECI Z (unrotated) = ECEF Z
        // Three.js Z (ECEF) = ECI Y (unrotated) = ECEF Y
        // So:
        // eciPosition_in_ThreeJsCoords.x is -ECEF_X_global (from ECI_X)
        // eciPosition_in_ThreeJsCoords.y is ECEF_Z_global (from ECI_Z)
        // eciPosition_in_ThreeJsCoords.z is ECEF_Y_global (from ECI_Y)

        // Latitude: asin(ECEF Z / r)
        const latitudeRad = Math.atan2(eciPosition_in_ThreeJsCoords.y, Math.sqrt(eciPosition_in_ThreeJsCoords.x * eciPosition_in_ThreeJsCoords.x + eciPosition_in_ThreeJsCoords.z * eciPosition_in_ThreeJsCoords.z));
        // Longitude: atan2(ECEF Y, ECEF X)
        // atan2(eciPosition_in_ThreeJsCoords.z, -eciPosition_in_ThreeJsCoords.x)
        const longitudeRad = Math.atan2(eciPosition_in_ThreeJsCoords.z, -eciPosition_in_ThreeJsCoords.x);

        const longitudeDeg = longitudeRad * (180 / Math.PI);
        const latitudeDeg = latitudeRad * (180 / Math.PI);

        this.groundTrackHistory.push({ lat: latitudeDeg, lon: longitudeDeg });

        // Limit the length of the ground track history for performance and fading effect (2D)
        if (this.groundTrackHistory.length > this.maxGroundTrackPoints) {
            this.groundTrackHistory.shift(); // Remove the oldest point
        }
        
        // Calculate the satellite's orbital velocity in scene units
        if (frameDeltaTime > 0) {
            this.velocity.copy(this.mesh.position).sub(this.prevPosition).divideScalar(frameDeltaTime);
            // Convert scene unit velocity back to km/s or m/s for display if EarthRadius is in km/m
            this.orbitalVelocityMagnitude = this.velocity.length() * (EarthRadius / SCENE_EARTH_RADIUS); // Scale back to real-world units
        } else {
            this.orbitalVelocityMagnitude = 0;
        }


        updateCoverageCone(this);
        updateNadirLine(this);
        drawOrbitPath(this); // Redraw orbit path (also child of scene now)
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
            scene.add(this.glbMesh); // Add to the scene (ECI frame)
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

    /**
     * Updates the satellite's parameters and recalculates its position based on the new parameters.
     * This is used when the user changes the satellite's parameters in the UI.
     * @param {object} newParams - New orbital parameters.
     * @param {number} newEpochUTC - New initial epoch in UTC milliseconds.
     */
    updateParametersFromCurrentPosition(newParams, newEpochUTC) {
        // Calculate current true anomaly from current mean anomaly
        const currentE = solveKepler(this.currentMeanAnomaly, this.params.eccentricity);
        const currentTrueAnomaly = E_to_TrueAnomaly(currentE, this.params.eccentricity);

        this.params = { ...newParams };
        this.initialEpochUTC = newEpochUTC;

        // Calculate the new initial mean anomaly that corresponds to the satellite's current true anomaly
        const E_new = TrueAnomaly_to_E(currentTrueAnomaly, this.params.eccentricity);
        this.initialMeanAnomaly = E_to_M(E_new, this.params.eccentricity);
        this.initialMeanAnomaly %= (2 * Math.PI);
        if (this.initialMeanAnomaly < 0) this.initialMeanAnomaly += 2 * Math.PI;

        this.initialRAAN = newParams.raanRad; // Update initial RAAN
        this.currentRAAN = this.initialRAAN; // Set current RAAN to initial

        // Recalculate position based on new parameters and current time
        this.updatePosition(window.totalSimulatedTime, 0);
    }
    /**
     * Updates the satellite's true anomaly and recalculates its position based on the new true anomaly.
     * This is used when the user changes the true anomaly in the UI.
     * @param {number} newTrueAnomalyRad - New true anomaly in radians.
     */
    updateTrueAnomalyOnly(newTrueAnomalyRad) {
        const E_new = TrueAnomaly_to_E(newTrueAnomalyRad, this.params.eccentricity);
        this.currentMeanAnomaly = E_to_M(E_new, this.params.eccentricity);
        this.currentMeanAnomaly %= (2 * Math.PI);
        if (this.currentMeanAnomaly < 0) this.currentMeanAnomaly += 2 * Math.PI;

        this.initialMeanAnomaly = this.currentMeanAnomaly; // Update initial MA for future calculations
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
            side: THREE.DoubleSide // Important: Render both sides to see it from any angle
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
            '/Satellitemodel/3DSatellite.glb',
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
    controls.minDistance = 0.001;
    controls.maxDistance = 1000;
    controls.target.set(0, 0, 0); // Reset controls target to Earth center
    camera.position.set(0, 0, 5); // Reset camera position
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

/**
 * Adds or updates a satellite in the scene based on user input.
 * @param {object} satelliteData - Object containing satellite parameters.
 */
window.addOrUpdateSatelliteInScene = function(satelliteData) {
    const uniqueId = satelliteData.id || satelliteData.fileName;
    if (!uniqueId) {
        console.error("Satellite data missing unique ID or fileName.");
        return;
    }

    let existingSat = window.activeSatellites.get(uniqueId);
    const initialEpochUTC = new Date(satelliteData.epoch).getTime();
    const params = {
        semiMajorAxis: SCENE_EARTH_RADIUS + (satelliteData.altitude / (EarthRadius / SCENE_EARTH_RADIUS)), // Convert altitude to scene units
        inclinationRad: satelliteData.inclination * DEG2RAD,
        eccentricity: satelliteData.eccentricity,
        raanRad: satelliteData.raan * DEG2RAD,
        argPerigeeRad: satelliteData.argumentOfPerigee * DEG2RAD,
        beamWidthDeg: satelliteData.beamwidth,
    };

    if (existingSat) {
        existingSat.updateParametersFromCurrentPosition(params, initialEpochUTC);
        existingSat.name = satelliteData.name || uniqueId;
        existingSat.initialEpochUTC = initialEpochUTC; // Ensure initial epoch is updated
    } else {
        const newSat = new Satellite(
            uniqueId,
            satelliteData.name || uniqueId,
            params,
            E_to_M(TrueAnomaly_to_E(satelliteData.trueAnomaly * DEG2RAD, satelliteData.eccentricity), satelliteData.eccentricity),
            satelliteData.raan * DEG2RAD,
            initialEpochUTC
        );
        if (satelliteModelLoaded && globalSatelliteGLB) {
            newSat.setGlbMesh(globalSatelliteGLB);
        }
        newSat.setActiveMesh(window.closeViewEnabled);
        window.activeSatellites.set(newSat.id, newSat);
    }
};


/**
 * Adds or updates a ground station in the scene based on provided data.
 * @param {object} gsData - Object containing ground station parameters.
 */
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


/**
 * Generates a new simulation view based on input data (single satellite, constellation, or ground station).
 * @param {object} data - Simulation configuration data.
 */
window.viewSimulation = function(data) {
    window.clearSimulationScene(); // Clear any existing objects
    // Set epoch for the simulation
    if (data.epoch) {
        window.currentEpochUTC = new Date(data.epoch).getTime();
    } else {
        window.currentEpochUTC = new Date().getTime(); // Default to current time if not provided
    }

    if (data.fileType === 'single') {
        const satelliteId = data.fileName; // Use filename as ID for single sat
        window.addOrUpdateSatelliteInScene({
            id: satelliteId,
            name: data.fileName,
            altitude: data.altitude,
            inclination: data.inclination,
            eccentricity: data.eccentricity,
            raan: data.raan,
            argumentOfPerigee: data.argumentOfPerigee,
            trueAnomaly: data.trueAnomaly,
            epoch: data.epoch,
            beamwidth: data.beamwidth
        });
        window.selectedSatelliteId = satelliteId; // Select the single satellite
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
                beamwidth: constellationParams.beamwidth
            };
        } else { // linkBudget file type with constellation parameters
            baseSatelliteParams = {
                altitude: constellationParams.orbitHeight,
                inclination: constellationParams.orbitInclination,
                eccentricity: 0.0, // Link budget often implies circular
                raan: 0, // Default RAAN for initial constellation generation
                argumentOfPerigee: 0, // Default ArgPerigee
                trueAnomaly: 0, // Default True Anomaly
                epoch: window.currentEpochUTC, // Use current simulation epoch
                beamwidth: 0 // Default beamwidth
            };
        }

        let satelliteCounter = 0;
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
                    id: `${constellationParams.fileName || constellationParams.name}-${Date.now()}-${satelliteCounter}`, // Ensure unique ID
                    name: satName,
                    altitude: baseSatelliteParams.altitude,
                    inclination: baseSatelliteParams.inclination,
                    eccentricity: baseSatelliteParams.eccentricity,
                    raan: baseSatelliteParams.raan,
                    argumentOfPerigee: baseSatelliteParams.argumentOfPerigee,
                    trueAnomaly: E_to_TrueAnomaly(solveKepler(currentSatelliteInitialMA, baseSatelliteParams.eccentricity), baseSatelliteParams.eccentricity) * (180 / Math.PI),
                    epoch: baseSatelliteParams.epoch,
                    beamwidth: baseSatelliteParams.beamwidth,
                    fileType: 'single'
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
                        fileType: 'single'
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


/**
 * Removes an object (satellite or ground station) from the scene by ID and type.
 * @param {string} idToRemove - The unique ID of the object to remove.
 * @param {'satellite'|'groundStation'} type - The type of object to remove.
 */
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
                controls.minDistance = 0.001;
                controls.maxDistance = 1000;
                controls.target.set(0, 0, 0);
                camera.position.set(0, 0, 5);
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
    // Iterate over current active satellites (assuming they were populated from a data source)
    const satellitesToRecreate = new Map(window.activeSatellites); // Make a copy
    window.activeSatellites.clear(); // Clear the map to rebuild
    satellitesToRecreate.forEach(satData => {
        window.addOrUpdateSatelliteInScene({
            id: satData.id,
            name: satData.name,
            altitude: (satData.params.semiMajorAxis - SCENE_EARTH_RADIUS) * (EarthRadius / SCENE_EARTH_RADIUS), // Convert scene unit altitude back to real-world
            inclination: satData.params.inclinationRad * (180 / Math.PI),
            eccentricity: satData.params.eccentricity,
            raan: satData.initialRAAN * (180 / Math.PI),
            // Need to calculate trueAnomaly from initialMeanAnomaly and epoch
            trueAnomaly: E_to_TrueAnomaly(solveKepler(satData.initialMeanAnomaly, satData.params.eccentricity), satData.params.eccentricity) * (180 / Math.PI),
            epoch: new Date(satData.initialEpochUTC).toISOString(),
            beamwidth: satData.params.beamWidthDeg
        });
    });

    const groundStationsToRecreate = new Map(window.activeGroundStations); // Make a copy
    window.activeGroundStations.clear(); // Clear the map to rebuild
    groundStationsToRecreate.forEach(gsData => {
        // Assuming `gsData` is like the `gsData` passed to `addOrUpdateGroundStationInScene`
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
    // Update UI after loading state
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
    if (window.isAnimating) {
        window.totalSimulatedTime += frameDeltaTime * window.currentSpeedMultiplier;
        const earthRotationAngle = (window.totalSimulatedTime * EARTH_ANGULAR_VELOCITY_RAD_PER_SEC);
        earthGroup.rotation.y = earthRotationAngle; // Re-enabled this line for 180-degree offset compensation
        if (cloudsMesh) {
            const cloudsDifferentialRotationSpeed = 0.15 * EARTH_ANGULAR_VELOCITY_RAD_PER_SEC; // 1.15 - 1.0 = 0.15
            cloudsMesh.rotation.y += cloudsDifferentialRotationSpeed * frameDeltaTime;
        }

        window.activeSatellites.forEach(satellite => {
            satellite.updatePosition(window.totalSimulatedTime, frameDeltaTime);
        });

        window.activeGroundStations.forEach(gs => {
            gs.updateCoverageCone();
        });
    }

    // Update Sun Direction using the integrated suncalc function
    updateSunDirection(window.totalSimulatedTime);

    if (window.is2DViewActive && typeof window.draw2D === 'function') {
        window.draw2D(); // Call without timestamp unless 2D specifically needs it, it gets totalSimulatedTime from window
    }
    // Render the 3D scene if not in 2D view
    if (!window.is2DViewActive) {
        renderer.render(scene, camera);
    }
    // Update UI displays for both 3D and 2D views by calling the function from blade file
    if (typeof window.updateAnimationDisplay === 'function') {
        window.updateAnimationDisplay();
    }
    if (window.selectedSatelliteId && typeof window.updateSatelliteDataDisplay === 'function') {
        window.updateSatelliteDataDisplay();
    }

    
    // Camera/controls logic for close view
    if (window.closeViewEnabled && window.selectedSatelliteId) {
        const selectedSat = window.activeSatellites.get(window.selectedSatelliteId);
        if (selectedSat) {
            const currentPos = selectedSat.mesh.position.clone(); // Satellite position is in ECI
            const forwardDir = selectedSat.velocity.length() > 0 ? selectedSat.velocity.clone().normalize() : new THREE.Vector3(0, 0, 1);
            
            // Calculate 'up' vector relative to the Earth's center from the satellite's current ECI position
            const upDir = currentPos.clone().normalize(); 

            // Adjust camera offset for a good view of the satellite
            const cameraOffset = forwardDir.clone().multiplyScalar(-0.08).add(upDir.clone().multiplyScalar(0.04));
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
                    if (window.closeViewEnabled) { // Re-enable controls only if still in close view mode
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
            controls.minDistance = 0.01; // Restrict zoom in close view
            controls.maxDistance = 0.2; // Restrict zoom out in close view
        } else {
            console.warn("Selected satellite not found for close view. Disabling close view.");
            window.closeViewEnabled = false;
            // Reset controls to default Earth view settings
            controls.object.up.set(0, 1, 0);
            controls.minDistance = 0.001;
            controls.maxDistance = 1000;
            controls.enabled = true; // Re-enable controls
        }
    } else if (!window.closeViewEnabled) {
        controls.enabled = true; // Ensure controls are enabled if not in close view
        // Reset controls limits and up vector if just exited close view
        if (controls.minDistance !== 0.001 || controls.maxDistance !== 1000 || controls.object.up.y !== 1) {
            controls.object.up.set(0, 1, 0);
            controls.minDistance = 0.001;
            controls.maxDistance = 1000;
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