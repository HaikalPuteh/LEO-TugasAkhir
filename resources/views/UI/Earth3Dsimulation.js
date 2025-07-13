// Earth3Dsimulation.js

import * as THREE from "three";
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import { CSS2DRenderer, CSS2DObject } from 'three/examples/jsm/renderers/CSS2DRenderer.js';

// Import utility functions
import getStarfield from "./getStarfield.js";
import { glowmesh } from "./glowmesh.js";
import { gsap } from 'gsap';

// Import astronomical calculation functions
import { getSunCoords, getGMST} from "./sunCalculations.js"; // Correctly imported now

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
// Initialize sunLightDirection here so it's defined when earthShader is created
let sunLightDirection = new THREE.Vector3();

// Global state variables
window.activeSatellites = new Map();
window.activeGroundStations = new Map();
window.selectedSatelliteId = null;
window.selectedGroundStationId = null; // New: Track selected ground station
window.isAnimating = false;
window.totalSimulatedTime = 0;
// Initialize initialEarthRotationOffset to 0; it will be set dynamically based on epoch.
window.initialEarthRotationOffset = 0;
window.currentEpochUTC = new Date().getTime(); // Changed: Dynamic start epoch
window.currentSpeedMultiplier = 1;
window.EARTH_ANGULAR_VELOCITY_RAD_PER_SEC = EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;
window.is2DViewActive = false;


// Expose these for use in simulation.blade.php
window.calculateDerivedOrbitalParameters = calculateDerivedOrbitalParameters; // EXPOSED GLOBALLY
window.EarthRadius = EarthRadius; // EXPOSED GLOBALLY
window.DEG2RAD = DEG2RAD; // EXPOSED GLOBALLY
window.SCENE_EARTH_RADIUS        = SCENE_EARTH_RADIUS; // EXPOSED GLOBALLY

// Satellite model loading variables
let satelliteModelLoaded = false;
let globalSatelliteGLB = null;
let lastAnimationFrameTime = performance.now();


// Holds THREE.Line objects for every (gsId, satId) pair
window.gsSatLinkLines = new Map(); //For Connection Analysis


// Initialize the 3D scene
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



    // Initialize CSS2DRenderer for labels
    window.labelRenderer = new CSS2DRenderer();
    labelRenderer.setSize( earthContainer.clientWidth, earthContainer.clientHeight );
    labelRenderer.domElement.style.position = 'absolute';
    labelRenderer.domElement.style.top      = '0';
    labelRenderer.domElement.style.pointerEvents = 'none';

    earthContainer.style.position = 'relative'; 
    earthContainer.appendChild(renderer.domElement);
    earthContainer.appendChild( labelRenderer.domElement );


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
            uSunDirection: { value: sunLightDirection }, // Initial light direction, will be updated dynamically
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
                vec3 mapN       = texture2D(uEarthBumpMap, vUv).rgb * 2.0 - 1.0;
                // Use the world normal for perturbation
                vec3 perturbedNormal = normalize(vWorldNormal + mapN * bumpScale);

                // Diffuse lighting term in world space
                float lightIntensity = dot(perturbedNormal, normalize(uSunDirection));

                // Smooth blend between day and night textures
                // Adjust smoothstep range for a softer/harder terminator line
                float blendFactor = smoothstep(-0.1, 0.1, lightIntensity);
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
        //transparent: false, //Changed to true for opacity to work
        opacity: 0.2, // Increased opacity for better visibility
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


function updateSunDirection(simTime) {
    const now = new Date(window.currentEpochUTC + simTime * 1000);
    const { ra, dec } = getSunCoords(now); // ra and dec are J2000 ECI coordinates

    // Calculate J2000 ECI components directly from RA and Dec
    const xJ2000 = Math.cos(dec) * Math.cos(ra);
    const yJ2000 = Math.cos(dec) * Math.sin(ra);
    const zJ2000 = Math.sin(dec);

    // Map J2000 ECI (Z-up) to Three.js scene coordinates (Y-up)
    // X_3JS = X_J2000
    // Y_3JS = Z_J2000
    // Z_3JS = Y_J2000
    const x3 = xJ2000;
    const y3 = zJ2000;
    const z3 = yJ2000;

    sunLightDirection.set(x3, y3, z3);
    // Set sunLight position far away in the calculated direction
    sunLight.position.copy(sunLightDirection).normalize().multiplyScalar(5);

    // Update both shaders with the sun's direction in the fixed ECI scene frame
    earthMesh.material.uniforms.uSunDirection.value.copy(sunLight.position).normalize();
    earthMesh.material.uniforms.uCameraPosition.value.copy(camera.position);

    if (atmosphereGlowMesh.material.uniforms.uSunDirection) {
        atmosphereGlowMesh.material.uniforms.uSunDirection.value.copy(sunLight.position).normalize();
        atmosphereGlowMesh.material.uniforms.uCameraPosition.value.copy(camera.position);
    }
}


function drawOrbitPath(satellite) {
    const e = satellite.params.eccentricity;
    const points = [];
    const numPathPoints = 360;

    const tempRAAN = satellite.currentRAAN;
    const tempArgPerigee = satellite.params.argPerigeeRad;
    //To Do Check : argPerigeeRad is in radians, from UI/UX

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
        const position = new THREE.Vector3(tempPosition.x, tempPosition.y, tempPosition.z)

        // —— apply the same initial GMST offset you applied to earthGroup & sats ——
            position.applyAxisAngle(
            new THREE.Vector3(0,1,0),
            window.initialEarthRotationOffset
            );
        points.push(position);
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


function updateCoverageCone(sat) {
  // ——— cleanup ———
  if (sat.coverageCone) {
    scene.remove(sat.coverageCone);
    sat.coverageCone.geometry.dispose();
    sat.coverageCone.material.dispose();
    sat.coverageCone = null;
  }
  if (sat.coverageRing) {
    scene.remove(sat.coverageRing);
    sat.coverageRing.geometry.dispose();
    sat.coverageRing.material.dispose();
    sat.coverageRing = null;
  }

  const beamDeg = sat.params.beamwidth;
  if (beamDeg <= 0 || beamDeg >= 180) return;

  const R = SCENE_EARTH_RADIUS;
  const P = sat.mesh.position.clone();
  const d = P.length();
  const β = THREE.MathUtils.degToRad(beamDeg / 2);

  // —— law of sines φ = arcsin((d/R)·sinβ) – β, clamped by horizon ——  
  let φ = Math.asin(Math.min(1, (d / R) * Math.sin(β))) - β;
  const φ_horizon = Math.acos(R / d);
  if (φ < 0 || φ > φ_horizon) {
    // either beam too narrow or aims past horizon → no coverage
    if (β < φ_horizon) return;
    φ = φ_horizon;
  }
  sat.coverageAngleRad = φ;

  // —— cone dims ——  
  const height     = d - R * Math.cos(φ);
  const coneRadius = R * Math.sin(φ);
  if (height <= 0 || coneRadius <= 0) return;

  // —— build the cone ——  
  const coneGeo = new THREE.ConeGeometry(coneRadius, height, 256, 1, true);
  // keep the apex at the sat by translating down half the height
  coneGeo.translate(0, -height/2, 0);

  const coneMat = new THREE.MeshBasicMaterial({
    color:       0x00ffff,
    transparent: true,
    opacity:     0.2,
    side:        THREE.DoubleSide
  });
  const cone = new THREE.Mesh(coneGeo, coneMat);
  cone.position.copy(P);

  // point +Y → nadir
  const nadir = P.clone().negate().normalize();
  const q     = new THREE.Quaternion()
    .setFromUnitVectors(new THREE.Vector3(0, -1, 0), nadir);
  cone.setRotationFromQuaternion(q);

  scene.add(cone);
  sat.coverageCone = cone;
}

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


function updateGsSatLinkLines() {
  // Remove any stale lines first
  window.gsSatLinkLines.forEach((line, key) => {
    scene.remove(line);
    line.geometry.dispose();
    line.material.dispose();
  });
  window.gsSatLinkLines.clear();

  // Temp vectors
  const gsPos = new THREE.Vector3();
  const satPos = new THREE.Vector3();
  const satToGs = new THREE.Vector3();
  const nadirDir = new THREE.Vector3();

  window.activeGroundStations.forEach(gs => {
    gs.mesh.getWorldPosition(gsPos);

    window.activeSatellites.forEach(sat => {
      sat.mesh.getWorldPosition(satPos);

      const key = `${gs.id}|${sat.id}`;
      const halfBeam = THREE.MathUtils.degToRad(sat.params.beamwidth / 2);

      // 1) is GS inside the beam cone?
      satToGs.copy(gsPos).sub(satPos).normalize();
      nadirDir.copy(satPos).negate().normalize();
      const coneOK = THREE.MathUtils.acosSafe(nadirDir.dot(satToGs)) <= halfBeam;

      // 2) is GS above the local horizon?
      const gsDir = gsPos.clone().normalize();
      const satDir = satPos.clone().normalize();
      const central = THREE.MathUtils.acosSafe(gsDir.dot(satDir));
      const horizonOK = central <= sat.coverageAngleRad;

      if (coneOK && horizonOK) {
        // create or update the line
        let line = window.gsSatLinkLines.get(key);
        if (!line) {
          line = new THREE.Line(
            new THREE.BufferGeometry().setFromPoints([gsPos, satPos]),
            new THREE.LineBasicMaterial({ color: 0xffff00, linewidth: 1 })
          );
          scene.add(line);
          window.gsSatLinkLines.set(key, line);
        } else {
          line.geometry.setFromPoints([gsPos, satPos]);
        }
      }
    });
  });
}

// Helper to clamp dot into [-1,1] before acos
THREE.MathUtils.acosSafe = function(x) {
  return Math.acos(THREE.MathUtils.clamp(x, -1, 1));
};


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

        // New properties for latitude and longitude
        this.latitudeDeg = 0;
        this.longitudeDeg = 0;

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
        // apply the same sidereal offset that we applied to the EarthGroup at t=0
        newPositionEciThreeJs.applyAxisAngle(
        new THREE.Vector3(0,1,0),
        window.initialEarthRotationOffset
        );
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
        // 1) Compute the total rotation (initial GMST + elapsed spin)
        const θ = window.initialEarthRotationOffset + window.totalSimulatedTime * window.EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;

        // 2) “Undo” it in one go (ECI→ECEF)
        const ecef = this.mesh.position.clone().applyAxisAngle(new THREE.Vector3(0,1,0), -θ);

        // 3) Spherical → lat/lon
        const latRad = Math.atan2(ecef.y, Math.hypot(ecef.x, ecef.z));
        let   lonRad = -Math.atan2(ecef.z, ecef.x);

        // 4) Convert to degrees and normalize longitude to [−180,180]
        const latDeg = latRad * (180/Math.PI);
        let   lonDeg = lonRad * (180/Math.PI);
        lonDeg = ((lonDeg + 540) % 360) - 180;

        // 5) Store for your 2D ground-track
        this.latitudeDeg  = latDeg;
        this.longitudeDeg = lonDeg;
        this.groundTrackHistory.push({ lat: this.latitudeDeg, lon: this.longitudeDeg });

        if (this.groundTrackHistory.length > this.maxGroundTrackPoints) {
            this.groundTrackHistory.shift();
        }

        // Update the satellite's mesh position and visibility
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
    if (this.sphereMesh) { 
      scene.remove(this.sphereMesh); 
      this.sphereMesh.geometry.dispose(); 
      this.sphereMesh.material.dispose(); 
    }
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
    if (this.orbitLine) { 
      scene.remove(this.orbitLine); 
      this.orbitLine.geometry.dispose(); 
      this.orbitLine.material.dispose(); 
    }
    if (this.coverageCone) { 
      scene.remove(this.coverageCone); 
      this.coverageCone.geometry.dispose(); 
      this.coverageCone.material.dispose(); 
    }
    if (this.nadirLine) { 
      scene.remove(this.nadirLine); 
      this.nadirLine.geometry.dispose(); 
      this.nadirLine.material.dispose(); 
    }

    // 5) Remove the CSS2D label object _and_ its DOM element
    if (this.labelObject) {
        this.mesh.remove(this.labelObject);    // remove the CSS2DObject
        this.labelObject = null;
    }

    if (this._labelElement) {
      this._labelElement.remove();
      this._labelElement = null;
    }
  }


    updateParametersFromCurrentPosition(newParams, newEpochUTC) {
        if (this.tleLine1 && this.tleLine2) {
            console.warn(`Updating parameters for TLE satellite ${this.id}. Requires new TLE data.`);
            if (newParams.tleLine1 && newParams.tleLine2) {
                this.tleLine1        = newParams.tleLine1;
                this.tleLine2        = newParams.tleLine2;
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

//--------------------------------------------- Start of the Label Creation---------------------------------
function createSatelliteLabel(sat) {
  const div = document.createElement('div');
  div.className = 'satellite-label';
  div.textContent = sat.name;
  div.style.color = 'white';
  div.style.fontSize = '12px';
  div.style.whiteSpace = 'nowrap';
  const label = new CSS2DObject(div);
  label.position.set(0, 0.02, 0);
  sat.labelObject = label;       // ← keep a reference
  sat.mesh.add(label);
  sat._labelElement = div;
}

/**
 * Highlights a specific satellite in the scene by changing its material emissive color and label color.
 * Resets other satellites to their default appearance.
 * @param {string|null} id - The ID of the satellite to highlight, or null to clear all highlights.
 */
window.highlightSatelliteInScene = function(id) {
    window.activeSatellites.forEach(sat => {
       if (sat.sphereMesh) {
        const mat = sat.sphereMesh.material;
        mat.color.setHex(0x0000ff);
            if (mat.emissive) {
            mat.emissive.setHex(0x000000);
            }
        }
        if (sat.glbMesh) {
            sat.glbMesh.traverse((child) => {
                if (child.isMesh && child.material) {
                    if (Array.isArray(child.material)) {
                        child.material.forEach(mat => {
                            if (mat.emissive) mat.emissive.setHex(0x000000);
                        });
                    } else {
                        if (child.material.emissive) child.material.emissive.setHex(0x000000);
                    }
                }
            });
        }
        if (sat._labelElement) {
            sat._labelElement.style.color = 'white'; // Default white
        }

        // Apply highlight if it's the selected ID
        if (sat.id === id) {
            if (sat.sphereMesh) {
                const mat = sat.sphereMesh.material;
                mat.color.setHex(0x00ff00);
                if (mat.emissive) {
                    mat.emissive.setHex(0x00ff00);
                }
            }
            // If GLB mesh exists, highlight it as well
            if (sat.glbMesh) {
                sat.glbMesh.traverse((child) => {
                    if (child.isMesh && child.material) {
                        if (Array.isArray(child.material)) {
                            child.material.forEach(mat => {
                                if (mat.emissive) mat.emissive.setHex(0x00ff00);
                            });
                        } else {
                            if (child.material.emissive) child.material.emissive.setHex(0x00ff00);
                        }
                    }
                });
            }
            if (sat._labelElement) {
                sat._labelElement.style.color = 'limegreen'; // Highlight label
            }
            // Also update camera target to the highlighted satellite
            controls.target.copy(sat.mesh.position);
            controls.update();
        }
    });
};

/**
 * Highlights a specific ground station in the scene by changing its material emissive color and label color.
 * Resets other ground stations to their default appearance.
 * @param {string|null} id - The ID of the ground station to highlight, or null to clear all highlights.
 */
window.highlightGroundStationInScene = function(id) {
    window.activeGroundStations.forEach(gs => {
        // Reset color first
        if (gs.mesh) {
            gs.mesh.material.color.setHex(0xffff00); // Default yellow
            gs.mesh.material.emissive.setHex(0x000000); // No glow
        }
        if (gs._labelElement) {
            gs._labelElement.style.color = 'white'; // Default white
        }

        // Apply highlight if it's the selected ID
        if (gs.id === id) {
            if (gs.mesh) {
                gs.mesh.material.color.setHex(0x00ffff); // Highlight cyan
                gs.mesh.material.emissive.setHex(0x00ffff); // Cyan glow
            }
            if (gs._labelElement) {
                gs._labelElement.style.color = 'cyan'; // Highlight label
            }
            // Also update camera target to the highlighted ground station
            controls.target.copy(gs.mesh.position);
            controls.update();
        }
    });
};

function createGroundStationLabel(gs) {
    const div = document.createElement('div');
    div.className = 'satellite-label'; // Re-use satellite-label class for styling
    div.textContent = gs.name;
    div.style.color = 'white';
    div.style.fontSize = '12px';
    div.style.whiteSpace = 'nowrap';
    const label = new CSS2DObject(div);
    label.position.set(0, 0.02, 0); // Offset slightly above the ground station
    gs.mesh.add(label);
    gs._labelElement = div;
}

//------------------------------------------- End Of Label Creation ---------------------------------

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
        this._labelElement = null; // Property to hold the label element
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
        const x = earthRadiusScene * Math.cos(latRad) * Math.cos(lonRad);
        const y = earthRadiusScene * Math.sin(latRad); // This is ECEF Z
        const z = earthRadiusScene * Math.cos(latRad) * Math.sin(lonRad); // This is ECEF Y

        const sphereGeometry = new THREE.SphereGeometry(0.005, 16, 16); // Small sphere
        const gsMaterial = new THREE.MeshBasicMaterial({ color: 0xffff00 }); // Yellow color
        this.mesh = new THREE.Mesh(sphereGeometry, gsMaterial);
        this.mesh.name = `groundstation-${this.id}-${this.name}`; // Set a unique name
        this.mesh.position.set(x, y, z);

        // Add the ground station mesh to the earthGroup so it rotates with the Earth
        earthGroup.add(this.mesh);
        createGroundStationLabel(this); // Create label for the ground station

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
        if (minElevRad <= 0) return;
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
        coneGeometry.translate(0, visualConeHeight / 2, 0);

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
    // 1) Remove the station mesh
    if (this.mesh) {
      earthGroup.remove(this.mesh);
      this.mesh.geometry.dispose();
      this.mesh.material.dispose();
      this.mesh = null;
    }

    // 2) Remove the coverage cone
    if (this.coverageCone) {
      earthGroup.remove(this.coverageCone);
      this.coverageCone.geometry.dispose();
      this.coverageCone.material.dispose();
      this.coverageCone = null;
    }

    // 3) Remove the CSS2DObject label
    if (this.labelObject) {
      this.mesh?.remove(this.labelObject);  // in case you kept a reference
      this.labelObject = null;
    }

    // 4) Remove its <div> from the DOM
    if (this._labelElement) {
      this._labelElement.remove();
      this._labelElement = null;
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
            //To do 
            '/Satellitemodel/CALIPSO.glb', // Path to the GLB model
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

// Set initial Earth rotation offset based on the current epoch at script load
window.initialEarthRotationOffset = getGMST(new Date(window.currentEpochUTC));

updateSunDirection(window.totalSimulatedTime);
renderer.render(scene, camera);

loadGlobalGLBModel().catch(() => console.warn("GLB model failed to load, proceeding with sphere models."));


// Window functions for simulation control
window.clearSimulationScene = function() {
    window.activeSatellites.forEach(sat => sat.dispose());
    window.activeSatellites.clear();
    window.activeGroundStations.forEach(gs => gs.dispose());
    window.activeGroundStations.clear();
    window.selectedSatelliteId = null;
    window.selectedGroundStationId = null; // Clear selected ground station
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
    
    // IMPORTANT: Recalculate initial Earth rotation offset for the current epoch after clearing
    window.initialEarthRotationOffset = getGMST(new Date(window.currentEpochUTC));

    // Update sun direction to current epoch at 0 simulated time after clearing
    updateSunDirection(0);
    renderer.render(scene, camera); // Re-render to show updated sun position immediately

    if (typeof window.updateAnimationDisplay === 'function') {
        window.updateAnimationDisplay(); // Update UI after clearing
    }

    window.gsSatLinkLines.forEach(line => {
        scene.remove(line);
        line.geometry.dispose();
        line.material.dispose();
    });
    window.gsSatLinkLines.clear();

    // Clear any highlights in the scene
    window.highlightSatelliteInScene(null);
    window.highlightGroundStationInScene(null);
};

//--------------------------------Generate a new simulation view based on input----------------------------------------

// Data Passed From New Single Satellite Form
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
        beamwidth: satelliteData.beamwidth, // Corrected to beamwidth
    };

      // If user supplied TLE lines, parse once
    if (satelliteData.tleLine1 && satelliteData.tleLine2) {
        try {
            parsedTle = parseTle(satelliteData.tleLine1, satelliteData.tleLine2);
            // If a TLE is provided, the simulation epoch should align with the TLE's epoch.
            // This is handled in window.viewSimulation, but also here for direct additions.
        } catch (err) {
            console.error("Invalid TLE:", err);
            satelliteData.tleLine1 = satelliteData.tleLine2 = null;
        }
    }

    if (existingSat) {
        if (parsedTle) {
            existingSat.tleLine1        = satelliteData.tleLine1;
            existingSat.tleLine2        = satelliteData.tleLine2;
            existingSat.parsedTle       = parsedTle;
            existingSat.initialEpochUTC = parsedTle.epochTimestamp;  // make sure to update its epoch too
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

    // Update the satellite label if it exists
    const satToUpdate = existingSat || window.activeSatellites.get(uniqueId);
    if (satToUpdate) {
        if (!satToUpdate._labelElement) {
            createSatelliteLabel(satToUpdate); // Create label if it doesn't exist
        } else {
            satToUpdate._labelElement.textContent = satToUpdate.name; // Update label text
        }
        // Ensure mesh visibility is correct based on closeViewEnabled
        satToUpdate.setActiveMesh(window.closeViewEnabled);
    }

    if (window.is2DViewActive && window.texturesLoaded) {
    // fire your redraw listener:
    window.dispatchEvent(new Event('epochUpdated'));
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
        existingGs.dispose(); // Dispose old meshes and label
        existingGs.createMesh(); // Create new meshes and label with updated properties
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

    if (window.is2DViewActive && window.texturesLoaded) {
    // fire your redraw listener:
    window.dispatchEvent(new Event('epochUpdated'));
    }
};



// Data Passed From New Constellation Satellite Form
window.viewSimulation = function(data) {
    // --- 1) Clear scene & reset epoch ---
    window.clearSimulationScene();//Clear the simulation called it in add it to addOrUpdateSatelliteInScene

    if (data.tleLine1 && data.tleLine2) {
        try {
            const parsedTle = parseTle(data.tleLine1, data.tleLine2);
            window.currentEpochUTC    = parsedTle.epochTimestamp;
            window.totalSimulatedTime = 0;
        } catch (err) {
            console.error("Invalid TLE, falling back to now:", err);
            window.currentEpochUTC    = Date.now();
            window.totalSimulatedTime = 0;
        }
    } else if (typeof data.utcTimestamp === 'number') {
        window.currentEpochUTC    = data.utcTimestamp;
        window.totalSimulatedTime = 0;
    } else {
        window.currentEpochUTC    = Date.now();
        window.totalSimulatedTime = 0;
    }

    // recalc Earth rotation, sun, initial render
    window.initialEarthRotationOffset = getGMST(new Date(window.currentEpochUTC));
    updateSunDirection(window.totalSimulatedTime);
    renderer.render(scene, camera);

    // --- 2) Single/TLE branch ---
    if (data.fileType === 'single' || data.fileType === 'tle') {
        window.addOrUpdateSatelliteInScene({
            id:               data.fileName,
            name:             data.fileName,
            altitude:         data.altitude,
            inclination:      data.inclination,
            eccentricity:     data.eccentricity,
            raan:             data.raan,
            argumentOfPerigee:data.argumentOfPerigee,
            trueAnomaly:      data.trueAnomaly,
            utcTimestamp:     window.currentEpochUTC,
            beamwidth:        data.beamwidth,
            tleLine1:         data.tleLine1,
            tleLine2:         data.tleLine2
        });
        window.isAnimating = false;

        // ensure fileOutputs and output tab know about it
        data.satellites = [ data.fileName ];
        window.fileOutputs.set(data.fileName, data);
        if (window.saveFilesToLocalStorage) window.saveFilesToLocalStorage();
        window.updateOutputTabForFile(data.fileName, data.fileType);

    // --- 3) Constellation / LinkBudget branch ---
    } else if (data.fileType === 'constellation' || data.fileType === 'linkBudget') {
        const params    = data;
        const satList   = [];
        let baseParams;

        if (data.fileType === 'constellation') {
            baseParams = {
                altitude:         params.altitude,
                inclination:      params.inclination,
                eccentricity:     params.eccentricity,
                raan:             params.raan,
                argumentOfPerigee:params.argumentOfPerigee,
                trueAnomaly:      params.trueAnomaly,
                utcTimestamp:     window.currentEpochUTC,
                beamwidth:        params.beamwidth,
                tleLine1:         params.tleLine1,
                tleLine2:         params.tleLine2
            };
        } else {
            // linkBudget uses different orbit fields
            baseParams = {
                altitude:         params.orbitHeight,
                inclination:      params.orbitInclination,
                eccentricity:     0,
                raan:             0,
                argumentOfPerigee:0,
                trueAnomaly:      0,
                utcTimestamp:     window.currentEpochUTC,
                beamwidth:        0,
                tleLine1:         params.tleLine1,
                tleLine2:         params.tleLine2
            };
        }

        // TRAIN-style constellation
        if (params.constellationType === 'train') {
            const N         = params.numSatellites;
            const sepType   = params.separationType;
            const sepValue  = params.separationValue;
            const backward  = (params.trainDirection === 'backward');
            const derived   = calculateDerivedOrbitalParameters(baseParams.altitude, baseParams.eccentricity, EarthRadius);
            const periodSec = derived.orbitalPeriod;
            let spacingRad = 0;

            if (sepType === 'meanAnomaly') {
                spacingRad = sepValue * DEG2RAD;
            } else {
                spacingRad = ((2*Math.PI)/periodSec)*sepValue;
            }
            if (backward) spacingRad *= -1;

            // base M
            let M0 = E_to_M(
                      TrueAnomaly_to_E(baseParams.trueAnomaly*DEG2RAD, baseParams.eccentricity),
                      baseParams.eccentricity
                     );

            for (let i = 0; i < N; i++) {
                const M_i = ((M0 + i*spacingRad) % (2*Math.PI) + 2*Math.PI) % (2*Math.PI);
                const TA  = E_to_TrueAnomaly(solveKepler(M_i, baseParams.eccentricity), baseParams.eccentricity) * (180/Math.PI);
                const satId   = `${data.fileName}-${Date.now()}-${i+1}`;
                const satName = `${data.fileName}_Sat${i+1}`;

                window.addOrUpdateSatelliteInScene({
                    id:               satId,
                    name:             satName,
                    altitude:         baseParams.altitude,
                    inclination:      baseParams.inclination,
                    eccentricity:     baseParams.eccentricity,
                    raan:             baseParams.raan,
                    argumentOfPerigee:baseParams.argumentOfPerigee,
                    trueAnomaly:      TA,
                    utcTimestamp:     window.currentEpochUTC,
                    beamwidth:        baseParams.beamwidth,
                    fileType:         data.fileType
                });
                satList.push(satId);
                window.isAnimating = false;
            }

        // WALKER-style constellation
        } else if (params.constellationType === 'walker') {
            const P     = parseInt(params.numPlanes,      10) || 1;
            const S     = parseInt(params.satellitesPerPlane,10) || 1;
            const F     = parseInt(params.phasingFactor,  10) || 0;
            const RAANdeg = parseFloat(params.raanSpread) || 360;
            const total = P*S;

            const RAANstep = (RAANdeg/P)*DEG2RAD;
            const MAstep   = (2*Math.PI)/S;
            const PHstep   = (F*(2*Math.PI))/total;

            let M0 = E_to_M(
                      TrueAnomaly_to_E(baseParams.trueAnomaly*DEG2RAD, baseParams.eccentricity),
                      baseParams.eccentricity
                     );

            let counter = 0;
            for (let p = 0; p < P; p++) {
                const RAANp = ((baseParams.raan*DEG2RAD + p*RAANstep)%(2*Math.PI)+2*Math.PI)%(2*Math.PI);

                for (let s = 0; s < S; s++) {
                    counter++;
                    let M_i = M0 + s*MAstep + p*PHstep;
                    M_i = ((M_i)%(2*Math.PI)+2*Math.PI)%(2*Math.PI);

                    const TA  = E_to_TrueAnomaly(solveKepler(M_i, baseParams.eccentricity), baseParams.eccentricity)*(180/Math.PI);
                    const satId   = `${data.fileName}-${Date.now()}-${p+1}-${s+1}`;
                    const satName = `${data.fileName}_Sat${p+1}_${s+1}`;

                    window.addOrUpdateSatelliteInScene({
                        id:               satId,
                        name:             satName,
                        altitude:         baseParams.altitude,
                        inclination:      baseParams.inclination,
                        eccentricity:     baseParams.eccentricity,
                        raan:             RAANp*(180/Math.PI),
                        argumentOfPerigee:baseParams.argumentOfPerigee,
                        trueAnomaly:      TA,
                        utcTimestamp:     window.currentEpochUTC,
                        beamwidth:        baseParams.beamwidth,
                        fileType:         data.fileType
                    });
                    satList.push(satId);
                    window.isAnimating = false;
                }
            }
        }
        // --- write back & refresh once ---
        params.satellites = satList;
        window.fileOutputs.set(data.fileName, params);
        if (window.saveFilesToLocalStorage) window.saveFilesToLocalStorage();
        window.updateOutputTabForFile(data.fileName, data.fileType);

    // --- 4) Ground station branch ---
    } else if (data.fileType === 'groundStation') {
        window.addOrUpdateGroundStationInScene({
            id:               data.name,
            name:             data.name,
            latitude:         data.latitude,
            longitude:        data.longitude,
            minElevationAngle:data.minElevationAngle
        });
        if (window.activeSatellites.size === 0) {
            const gs = window.activeGroundStations.get(data.name);
            camera.position.set(gs.mesh.position.x*2, gs.mesh.position.y*2, gs.mesh.position.z*2+1);
            controls.target.copy(gs.mesh.position);
            controls.update();
        }
    }
    // final render & UI update
    const core3D = window.getSimulationCoreObjects();
    if (core3D.renderer) core3D.renderer.render(core3D.scene, core3D.camera);
    if (window.updateAnimationDisplay) window.updateAnimationDisplay();
};

window.removeObjectFromScene = function(idToRemove, type) {
    if (type === 'satellite') {
        const sat = window.activeSatellites.get(idToRemove);
        if (sat) {
            sat.dispose();
            window.activeSatellites.delete(idToRemove);
            //— remove from our saved fileOutputs entry —
            window.fileOutputs.forEach((fileData, fileName) => {
            if (fileData.satellites && fileData.satellites.includes(idToRemove)) {
                fileData.satellites = fileData.satellites.filter(id => id !== idToRemove);
                window.fileOutputs.set(fileName, fileData);
                if (typeof window.saveFilesToLocalStorage === 'function') {
                window.saveFilesToLocalStorage();
                }
                window.updateOutputTabForFile(fileName, fileData.fileType);
            }
            });
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
            if (window.selectedGroundStationId === idToRemove) {
                window.selectedGroundStationId = null;
                // If selected GS removed, reset camera to Earth center view
                controls.object.up.set(0, 1, 0);
                controls.minDistance = 1.2; // Reset to default initial distance
                controls.maxDistance = 10; // Reset to default max distance
                controls.target.set(0, 0, 0);
                camera.position.set(0, 0, 2.5); // Reset camera position to initial 3D scene Z
                controls.update();
            }
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

//Reload the 3D simulation state from sidebar or saved state
window.load3DSimulationState = function() {
    const satellitesToRecreate = new Map(window.activeSatellites);
    window.activeSatellites.clear();
    satellitesToRecreate.forEach(satData => {
        // Re-add satellites using their original TLEs if available, or calculated Keplerian parameters
        window.addOrUpdateSatelliteInScene({
            id: satData.id,
            name: satData.name,
            // Only provide these if not using TLE
            altitude: satData.tleLine1 ? undefined : (satData.params.semiMajorAxis - SCENE_EARTH_RADIUS) * (EarthRadius / SCENE_EARTH_RADIUS),
            inclination: satData.tleLine1 ? undefined : satData.params.inclinationRad * (180 / Math.PI),
            eccentricity: satData.tleLine1 ? undefined : satData.params.eccentricity,
            raan: satData.tleLine1 ? undefined : satData.initialRAAN * (180 / Math.PI),
            trueAnomaly: satData.tleLine1 ? undefined : E_to_TrueAnomaly(solveKepler(satData.initialMeanAnomaly, satData.params.eccentricity), satData.eccentricity) * (180 / Math.PI),
            
            utcTimestamp: satData.initialEpochUTC, // Always re-apply the original epoch
            beamwidth: satData.params.beamwidth, // Corrected to beamwidth
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

    // After all objects are re-added, force a sun update and render
    updateSunDirection(window.totalSimulatedTime);
    renderer.render(scene, camera);

    if (typeof window.updateAnimationDisplay === 'function') {
        window.updateAnimationDisplay();
    }
};



// Utility functions for satellite data calculations Display
// Convert radians → formatted degrees
function toDeg(rad) {
  return (rad * 180/Math.PI).toFixed(2);
}

// Compute a satellite’s altitude (in km) from its scene‐unit radius
function computeAltitude(sat) {
  // EarthRadius (imported) is km per scene‐unit
  const kmPerUnit = EarthRadius;
  return ((sat.mesh.position.length() * kmPerUnit) - kmPerUnit).toFixed(2);
}

// If you still need these globally (e.g. your Blade inline code), expose them:
window.toDeg = toDeg;
window.computeAltitude = computeAltitude;


function updateSatellitePopup() {
    if (!window.activeSatellitePopup) return;
    const { element, satId } = window.activeSatellitePopup;
    const sat = window.activeSatellites.get(satId);
    if (!sat) {
        element.remove();
        window.activeSatellitePopup = null;
        return;
    }
    // Recompute derived parameters
    const { orbitalPeriod, orbitalVelocity } = calculateDerivedOrbitalParameters(
        sat.params.semiMajorAxis - SCENE_EARTH_RADIUS,
        sat.params.eccentricity
    );
    // Update each span with current data
    element.querySelector('.altitude').textContent = computeAltitude(sat);
    element.querySelector('.inclination').textContent = toDeg(sat.params.inclinationRad);
    element.querySelector('.latitude').textContent = sat.latitudeDeg.toFixed(2);
    element.querySelector('.longitude').textContent = sat.longitudeDeg.toFixed(2);
    element.querySelector('.raan').textContent = toDeg(sat.currentRAAN);
    element.querySelector('.orbitalPeriod').textContent = (orbitalPeriod / 60).toFixed(2);
    element.querySelector('.orbitalVelocity').textContent = orbitalVelocity.toFixed(2);
    element.querySelector('.beamwidth').textContent = sat.params.beamwidth;
    element.querySelector('.trueAnomaly').textContent = toDeg(sat.currentTrueAnomaly);
    element.querySelector('.eccentricity').textContent = sat.params.eccentricity.toFixed(4);
    element.querySelector('.argPerigee').textContent = toDeg(sat.params.argPerigeeRad);
}
window.updateSatellitePopup = updateSatellitePopup;

// Initialize the 3D scene and start the animation loop
function animate() { // timestamp is passed by requestAnimationFrame
    requestAnimationFrame(animate);
    const currentTime = performance.now();
    const frameDeltaTime = (currentTime - lastAnimationFrameTime) / 1000; // Seconds
    lastAnimationFrameTime = currentTime;
    const core3D = window.getSimulationCoreObjects();

    // --- advance simulation time & rotate Earth ---
    if (core3D.isAnimating) {
        core3D.setTotalSimulatedTime(
            core3D.totalSimulatedTime + frameDeltaTime * core3D.currentSpeedMultiplier
        );
        if (cloudsMesh) {
            const diffSpeed = 0.05 * EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;
            cloudsMesh.rotation.y += diffSpeed * frameDeltaTime;
        }

        core3D.activeSatellites.forEach(sat =>
            sat.updatePosition(core3D.totalSimulatedTime, frameDeltaTime)
        );
        core3D.activeGroundStations.forEach(gs => gs.updateCoverageCone());
    }

    // Earth's rotation angle includes the initial offset to align with GMST at epoch
    const earthRotationAngle = core3D.totalSimulatedTime * EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;
    earthGroup.rotation.y = earthRotationAngle + window.initialEarthRotationOffset;

    // --- update sun & shader time uniform ---
    updateSunDirection(core3D.totalSimulatedTime);
    earthMesh.material.uniforms.uTime.value = core3D.totalSimulatedTime;
    

    // --- render 3D every frame ---
    renderer.render(scene, camera);

    // --- render labels ---
    labelRenderer.render(scene, camera);

    // --- then overlay 2D if requested ---
    if (core3D.is2DViewActive && typeof window.draw2D === 'function') {
        window.draw2D();
    }
    
    // Update the satellite pop-up
    if (window.updateSatellitePopup) window.updateSatellitePopup();
    
    // --- UI callbacks ---
    if (typeof window.updateAnimationDisplay === 'function') {
        window.updateAnimationDisplay();
    }
    // if (core3D.selectedSatelliteId && typeof window.updateSatelliteDataDisplay === 'function') {
    //     window.updateSatelliteDataDisplay();
    // }

    updateGsSatLinkLines();// For Connection Analysis


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
        // …and in your resize listener:
        labelRenderer.setSize(newWidth, newHeight);
        camera.aspect = newWidth / newHeight;
        camera.updateProjectionMatrix();
        controls.update(); // Update controls after camera aspect changes
    }
});