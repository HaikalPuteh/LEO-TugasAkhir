// Earth3Dsimulation.js

import * as THREE from "three";
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import { CSS2DRenderer, CSS2DObject } from 'three/examples/jsm/renderers/CSS2DRenderer.js';

// Import utility functions
import getStarfield from "./getStarfield.js";
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


// ============= EARTH ROTATION PRECISION MANAGER (ADD THIS SECTION) =============
class EarthRotationManager {
    constructor() {
        this.baseEpochUTC = 0;           // Base epoch timestamp (ms)
        this.baseGMST = 0;               // GMST at base epoch (radians)
        this.lastCalculatedTime = 0;     // Last simulation time calculated
        this.rotationOffset = 0;         // Accumulated rotation offset
        this.maxAccumulationTime = 3600; // Reset accumulation every hour (seconds)
    }

    initialize(epochUTC) {
        this.baseEpochUTC = epochUTC;
        this.baseGMST = getGMST(new Date(epochUTC));
        this.lastCalculatedTime = 0;
        this.rotationOffset = 0;
        console.log(`Earth rotation precision manager initialized: epoch=${new Date(epochUTC).toISOString()}`);
    }

    getRotationAngle(simulatedTimeSeconds) {
        // Check if we need to reset accumulation to prevent precision loss
        if (simulatedTimeSeconds - this.lastCalculatedTime > this.maxAccumulationTime) {
            this.resetAccumulation(simulatedTimeSeconds);
        }

        // Calculate rotation using high-precision method
        const deltaTime = simulatedTimeSeconds - this.lastCalculatedTime;
        const deltaRotation = deltaTime * EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;
        
        // Accumulate rotation with modulo to prevent overflow
        this.rotationOffset = (this.rotationOffset + deltaRotation) % (2 * Math.PI);
        this.lastCalculatedTime = simulatedTimeSeconds;

        // Total rotation = base GMST + accumulated rotation
        const totalRotation = this.baseGMST + this.rotationOffset;
        
        // Normalize to [0, 2π) to prevent floating-point drift
        return ((totalRotation % (2 * Math.PI)) + (2 * Math.PI)) % (2 * Math.PI);
    }

    peekRotationAngle(simulatedTimeSeconds) {
        // Get rotation without updating internal state (for lookups)
        const totalRotation = this.baseGMST + (simulatedTimeSeconds * EARTH_ANGULAR_VELOCITY_RAD_PER_SEC);
        return ((totalRotation % (2 * Math.PI)) + (2 * Math.PI)) % (2 * Math.PI);
    }

    resetAccumulation(currentSimulatedTime) {
        // Calculate new base epoch
        const newBaseEpochUTC = this.baseEpochUTC + (currentSimulatedTime * 1000);
        
        // Recalculate base GMST for new epoch
        this.baseGMST = getGMST(new Date(newBaseEpochUTC));
        
        // Reset accumulation
        this.baseEpochUTC = newBaseEpochUTC;
        this.lastCalculatedTime = 0;
        this.rotationOffset = 0;
        
        console.log(`Earth rotation reset at t=${currentSimulatedTime}s to prevent precision loss`);
    }

    validateAccuracy(simulatedTime) {
        // Compare with direct calculation
        const directGMST = getGMST(new Date(this.baseEpochUTC + simulatedTime * 1000));
        const managerAngle = this.peekRotationAngle(simulatedTime);
        
        const error = Math.abs(directGMST - managerAngle);
        const errorDegrees = error * (180 / Math.PI);
        
        return {
            directGMST: directGMST,
            managerAngle: managerAngle,
            errorRadians: error,
            errorDegrees: errorDegrees,
            isAccurate: errorDegrees < 0.001 // 0.001 degree tolerance
        };
    }
}

// Create the rotation manager instance
const earthRotationManager = new EarthRotationManager();
window.earthRotationManager = earthRotationManager; // Expose globally for 2D simulation
// ============= END EARTH ROTATION PRECISION MANAGER =============



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
window.SCENE_EARTH_RADIUS = SCENE_EARTH_RADIUS; // EXPOSED GLOBALLY
window.propagateSGP4 = propagateSGP4; // EXPOSE GLOBALLY for validation functions
window.getGMST = getGMST; // EXPOSE GLOBALLY for TLE epoch handling

// Satellite model loading variables
let satelliteModelLoaded = false;
let globalSatelliteGLB = null;
let lastAnimationFrameTime = performance.now();

//Label

window.labelVisibilityEnabled = true;
window.proximityLabelsEnabled = false;
window.labelProximityDistance = 2.0; // Distance threshold in scene units
window.maxVisibleLabels = 10; // Maximum number of labels to show in proximity mode

//Nadir
window.nadirLinesEnabled = true; // Default: nadir lines visible


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
    //renderer = new THREE.WebGLRenderer({ antialias: true, logarithmicDepthBuffer: true });
    renderer = new THREE.WebGLRenderer({ antialias: true});
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
    // In Earth3Dsimulation.js - Earth mesh creation with improved shader
    const earthShader = new THREE.ShaderMaterial({
        uniforms: {
            uEarthDayMap: { value: earthDayMap },
            uEarthNightMap: { value: earthNightMap },
            uEarthSpecularMap: { value: earthSpecularMap },
            uEarthBumpMap: { value: earthBumpMap },
            uSunDirection: { value: sunLightDirection },
            uTime: { value: 0.0 },
            uCameraPosition: { value: camera.position },
            bumpScale: { value: 0.02 },
            shininess: { value: 1500.0 }
        },
        vertexShader: `
            varying vec2 vUv;
            varying vec3 vWorldNormal;
            varying vec3 vWorldPosition;

            void main() {
                vUv = uv;
                vWorldNormal = normalize(mat3(modelMatrix) * normal);
                vWorldPosition = (modelMatrix * vec4(position, 1.0)).xyz;
                gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
            }
        `,
        fragmentShader: `
            uniform sampler2D uEarthDayMap;
            uniform sampler2D uEarthNightMap;
            uniform sampler2D uEarthSpecularMap;
            uniform sampler2D uEarthBumpMap;
            uniform vec3 uSunDirection;
            uniform vec3 uCameraPosition;
            uniform float bumpScale;
            uniform float shininess;

            varying vec2 vUv;
            varying vec3 vWorldNormal;
            varying vec3 vWorldPosition;

            void main() {
                // Sample all textures
                vec4 dayColor = texture2D(uEarthDayMap, vUv);
                vec4 nightColor = texture2D(uEarthNightMap, vUv);
                vec3 specularMap = texture2D(uEarthSpecularMap, vUv).rgb;
                vec3 normalMap = texture2D(uEarthBumpMap, vUv).rgb * 2.0 - 1.0;
                
                // Apply normal mapping
                vec3 perturbedNormal = normalize(vWorldNormal + normalMap * bumpScale);
                
                // Lighting calculation
                vec3 lightDir = normalize(uSunDirection);
                float NdotL = dot(perturbedNormal, lightDir);
                
                // Smooth day/night transition with wider terminator
                float dayNightFactor = smoothstep(-0.05, 0.05, NdotL);
                
                // FIXED: Proper day/night blending without transparency issues
                vec3 baseColor = mix(
                    nightColor.rgb,   // Dim night lights slightly
                    dayColor.rgb,           // Full day color
                    dayNightFactor
                );
                
                // Ocean specular highlights (only on day side)
                vec3 viewDir = normalize(uCameraPosition - vWorldPosition);
                vec3 reflectDir = reflect(-lightDir, perturbedNormal);
                float specular = pow(max(dot(viewDir, reflectDir), 0.0), shininess);
                vec3 specularColor = specularMap * specular * dayNightFactor * 0.4;
                
                // CRITICAL FIX: Ensure completely opaque output
                gl_FragColor = vec4(baseColor + specularColor, 1.0);
            }
        `,
        // CRITICAL FIXES for transparency issue:
        transparent: false,           // Earth should NOT be transparent
        side: THREE.FrontSide,       // Only render front faces
        depthWrite: true,            // Write to depth buffer
        depthTest: true,             // Test depth buffer
        alphaTest: 0,                // No alpha testing
        blending: THREE.AdditiveBlending, // Standard blending
    });
    earthMesh = new THREE.Mesh(earthGeometry, earthShader);
    earthGroup.add(earthMesh); // Add to the rotating group

    const cloudGeometry = new THREE.SphereGeometry(SCENE_EARTH_RADIUS * 1.004, 256, 256);
    cloudsMesh = new THREE.Mesh(cloudGeometry, new THREE.MeshStandardMaterial({
    map: cloudsMap,
    transparent: true,
    opacity: 0.25,
    blending: THREE.AdditiveBlending,
    side: THREE.FrontSide,
    depthWrite: false,  // Prevent z-fighting with Earth
    depthTest: true
    }));
    earthGroup.add(cloudsMesh);

    // In Earth3Dsimulation.js - Atmosphere glow mesh setup
    // Create and add atmosphere glow
    function createAtmosphereGlow() {
    const atmosphereGeometry = new THREE.SphereGeometry(SCENE_EARTH_RADIUS * 1.01, 256, 256);
    
    const atmosphereMaterial = new THREE.ShaderMaterial({
        uniforms: {
            uSunDirection: { value: sunLightDirection },
            uCameraPosition: { value: camera.position },
            uGlowColor: { value: new THREE.Color(0x4da6ff) },
            uRimColor: { value: new THREE.Color(0x87ceeb) }
        },
        vertexShader: `
            varying vec3 vWorldNormal;
            varying vec3 vWorldPosition;
            varying vec3 vViewDirection;

            void main() {
                vWorldNormal = normalize(mat3(modelMatrix) * normal);
                vWorldPosition = (modelMatrix * vec4(position, 1.0)).xyz;
                vViewDirection = normalize(cameraPosition - vWorldPosition);
                
                gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
            }
        `,
        fragmentShader: `
            uniform vec3 uSunDirection;
            uniform vec3 uCameraPosition;
            uniform vec3 uGlowColor;
            uniform vec3 uRimColor;

            varying vec3 vWorldNormal;
            varying vec3 vWorldPosition;
            varying vec3 vViewDirection;

            void main() {
                vec3 normal = normalize(vWorldNormal);
                vec3 viewDir = normalize(vViewDirection);
                vec3 sunDir = normalize(uSunDirection);
                
                // Fresnel effect for atmospheric rim
                float fresnel = 1.0 - abs(dot(viewDir, normal));
                fresnel = pow(fresnel, 2.5);
                
                // Sun illumination factor
                float sunDot = dot(normal, sunDir);
                float sunInfluence = smoothstep(-0.5, 0.5, sunDot);
                
                // Combine effects
                vec3 glowColor = mix(uGlowColor * 0.3, uRimColor, sunInfluence);
                float intensity = fresnel * (0.2 + 0.8 * sunInfluence);
                
                gl_FragColor = vec4(glowColor, intensity * 0.6);
            }
        `,
        transparent: true,
        side: THREE.BackSide,
        blending: THREE.AdditiveBlending,
        depthWrite: false,
        depthTest: true
    });

    return new THREE.Mesh(atmosphereGeometry, atmosphereMaterial);
    }
    atmosphereGlowMesh = createAtmosphereGlow();
    scene.add(atmosphereGlowMesh); // Add to scene, NOT earthGroup

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


// In Earth3Dsimulation.js - Updated sun direction function with validation
function updateSunDirection(simTime) {
    const now = new Date(window.currentEpochUTC + simTime * 1000);
    const { ra, dec } = getSunCoords(now);

    // FIXED: Correct transformation from J2000 ECI to Three.js coordinates
    // J2000 ECI standard: X = vernal equinox, Y = 90° from X in equatorial plane, Z = north pole
    // Three.js: X = right, Y = up (north), Z = toward viewer
    
    const cosRA = Math.cos(ra);
    const sinRA = Math.sin(ra);
    const cosDec = Math.cos(dec);
    const sinDec = Math.sin(dec);
    
    // Standard J2000 ECI coordinates
    const xJ2000 = cosDec * cosRA;  // Toward vernal equinox
    const yJ2000 = cosDec * sinRA;  // 90° from vernal equinox in equatorial plane  
    const zJ2000 = sinDec;          // Toward north pole
    
    // Transform to Three.js coordinate system:
    // Three.js X = J2000 X (vernal equinox direction preserved)
    // Three.js Y = J2000 Z (north pole becomes "up")
    // Three.js Z = -J2000 Y (to maintain right-handed coordinate system)
    const x3 = xJ2000;   // Vernal equinox direction
    const y3 = zJ2000;   // North pole -> up
    const z3 = -yJ2000;  // Complete right-handed system

    sunLightDirection.set(x3, y3, z3).normalize();
    
    // Position sun light far away in the calculated direction
    const sunDistance = 10;
    sunLight.position.copy(sunLightDirection).multiplyScalar(sunDistance);
    
    // Update Earth shader uniforms
    if (earthMesh && earthMesh.material.uniforms) {
        earthMesh.material.uniforms.uSunDirection.value.copy(sunLightDirection);
        earthMesh.material.uniforms.uCameraPosition.value.copy(camera.position);
    }

    // Update atmosphere glow
    if (atmosphereGlowMesh && atmosphereGlowMesh.material.uniforms) {
        atmosphereGlowMesh.material.uniforms.uSunDirection.value.copy(sunLightDirection);
        atmosphereGlowMesh.material.uniforms.uCameraPosition.value.copy(camera.position);
    }
    
    
}


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
        const position = new THREE.Vector3(tempPosition.x, tempPosition.y, tempPosition.z);

        // FIXED: Remove double rotation - orbit paths are in ECI frame
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
    }
    
    const satPositionECI = satellite.mesh.position;
    const nadirPointECI = satPositionECI.clone().normalize().multiplyScalar(SCENE_EARTH_RADIUS);
    const points = [satPositionECI, nadirPointECI];

    satellite.nadirLine = new THREE.Line(
        new THREE.BufferGeometry().setFromPoints(points), 
        new THREE.LineBasicMaterial({ color: 0x888888, linewidth: 2 })
    );
    
    // SET INITIAL VISIBILITY BASED ON GLOBAL SETTING
    satellite.nadirLine.visible = window.nadirLinesEnabled;
    
    scene.add(satellite.nadirLine);
}


// FIXED updateGsSatLinkLines function
function updateGsSatLinkLines() {
  // Remove any stale lines first
  window.gsSatLinkLines.forEach((line, key) => {
    if (line && line.parent) {
      scene.remove(line);
      if (line.geometry) line.geometry.dispose();
      if (line.material) line.material.dispose();
    }
  });
  window.gsSatLinkLines.clear();

  // Early exit if no ground stations or satellites
  if (!window.activeGroundStations || window.activeGroundStations.size === 0 ||
      !window.activeSatellites || window.activeSatellites.size === 0) {
    return;
  }

  // Temp vectors
  const gsPos = new THREE.Vector3();
  const satPos = new THREE.Vector3();
  const satToGs = new THREE.Vector3();
  const nadirDir = new THREE.Vector3();

  window.activeGroundStations.forEach(gs => {
    // CRITICAL FIX: Check if ground station and its mesh exist
    if (!gs || !gs.mesh || !gs.mesh.position) {
      console.warn(`Ground station ${gs?.id || 'unknown'} has invalid mesh, skipping link line calculation`);
      return; // Skip this ground station
    }

    try {
      gs.mesh.getWorldPosition(gsPos);
    } catch (error) {
      console.warn(`Failed to get world position for ground station ${gs.id}:`, error);
      return; // Skip this ground station
    }

    window.activeSatellites.forEach(sat => {
      // CRITICAL FIX: Check if satellite and its mesh exist
      if (!sat || !sat.mesh || !sat.mesh.position) {
        console.warn(`Satellite ${sat?.id || 'unknown'} has invalid mesh, skipping link line calculation`);
        return; // Skip this satellite
      }

      // Additional check for satellite parameters
      if (!sat.params || typeof sat.params.beamwidth !== 'number') {
        console.warn(`Satellite ${sat.id} has invalid parameters, skipping link line calculation`);
        return; // Skip this satellite
      }

      try {
        sat.mesh.getWorldPosition(satPos);
      } catch (error) {
        console.warn(`Failed to get world position for satellite ${sat.id}:`, error);
        return; // Skip this satellite
      }

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
      // Additional safety check for coverageAngleRad
    //   const horizonOK = (typeof sat.coverageAngleRad === 'number') ? 
    //     central <= sat.coverageAngleRad : 
    //     central <= Math.PI / 2; // Fallback to 90 degrees

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

// Helper to clamp dot into [-1,1] before acos
THREE.MathUtils.acosSafe = function(x) {
  return Math.acos(THREE.MathUtils.clamp(x, -1, 1));
};

// Satellite class definition
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
                // IMPORTANT: Sync simulation epoch with TLE epoch
                this.initialEpochUTC = this.parsedTle.epochTimestamp;
                
                // Update global simulation epoch if this is the first/only satellite
                if (window.activeSatellites.size === 0) {
                    window.currentEpochUTC = this.parsedTle.epochTimestamp;
                    window.totalSimulatedTime = 0;
                    window.initialEarthRotationOffset = getGMST(new Date(this.parsedTle.epochTimestamp));
                }
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
            try {
                const sgp4Result = propagateSGP4(this.parsedTle, currentDateTime);
                if (sgp4Result && sgp4Result.position) {
                    newPositionEciThreeJs = sgp4Result.position;
                    newVelocityEciThreeJs = sgp4Result.velocity || new THREE.Vector3(0,0,0);
                } else {
                    console.warn(`SGP4 propagation failed for satellite ${this.id}. Using last known position.`);
                    newPositionEciThreeJs = this.mesh.position.clone();
                    newVelocityEciThreeJs = new THREE.Vector3(0,0,0);
                }
            } catch (error) {
                console.error(`SGP4 error for satellite ${this.id}:`, error);
                // Fallback to current position
                newPositionEciThreeJs = this.mesh.position.clone();
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
        
        // FIXED: Remove double rotation - Earth rotation is handled by earthGroup.rotation.y
        // Satellite position is already in correct ECI frame
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
         const θ = earthRotationManager.peekRotationAngle(window.totalSimulatedTime);

        // 2) "Undo" it in one go (ECI→ECEF)
        const ecef = this.mesh.position.clone().applyAxisAngle(new THREE.Vector3(0,1,0), -θ);

        // 3) Spherical → lat/lon with improved numerical stability
        const r = ecef.length();
        const latRad = Math.asin(ecef.y / r); // Use asin for better numerical stability
        let lonRad = Math.atan2(-ecef.z, ecef.x); // Standard atan2 without negation

        // 4) Convert to degrees 
        const latDeg = latRad * (180/Math.PI);
        const lonDeg = lonRad * (180/Math.PI); // atan2 already returns [-π, π], so this is correct

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
        
        // Only draw orbit path for Keplerian satellites (not TLE)
        if (!this.parsedTle) {
            drawOrbitPath(this);
        }
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
        // Remove any link lines associated with this satellite
        if (window.gsSatLinkLines) {
            const keysToRemove = [];
            window.gsSatLinkLines.forEach((line, key) => {
                if (key.startsWith(this.id + '|') || key.endsWith('|' + this.id)) {
                    scene.remove(line);
                    if (line.geometry) line.geometry.dispose();
                    if (line.material) line.material.dispose();
                    keysToRemove.push(key);
                }
            });
            keysToRemove.forEach(key => window.gsSatLinkLines.delete(key));
        }

        // Dispose satellite meshes (existing code with safety checks)
        if (this.sphereMesh) { 
            scene.remove(this.sphereMesh); 
            if (this.sphereMesh.geometry) this.sphereMesh.geometry.dispose(); 
            if (this.sphereMesh.material) this.sphereMesh.material.dispose(); 
            this.sphereMesh = null; // IMPORTANT: Set to null
        }
        
        if (this.glbMesh) {
            scene.remove(this.glbMesh);
            this.glbMesh.traverse((child) => {
                if (child.isMesh) {
                    if (child.geometry) child.geometry.dispose();
                    if (child.material) {
                        if (child.material.isMaterial) child.material.dispose();
                        else if (Array.isArray(child.material)) child.material.forEach(mat => mat.dispose());
                    }
                }
            });
            this.glbMesh = null; // IMPORTANT: Set to null
        }
        
        // Dispose other objects with safety checks
        if (this.orbitLine) { 
            scene.remove(this.orbitLine); 
            if (this.orbitLine.geometry) this.orbitLine.geometry.dispose(); 
            if (this.orbitLine.material) this.orbitLine.material.dispose(); 
            this.orbitLine = null; // IMPORTANT: Set to null
        }
        
        if (this.coverageCone) { 
            scene.remove(this.coverageCone); 
            if (this.coverageCone.geometry) this.coverageCone.geometry.dispose(); 
            if (this.coverageCone.material) this.coverageCone.material.dispose(); 
            this.coverageCone = null; // IMPORTANT: Set to null
        }
        
        if (this.nadirLine) { 
            scene.remove(this.nadirLine); 
            if (this.nadirLine.geometry) this.nadirLine.geometry.dispose(); 
            if (this.nadirLine.material) this.nadirLine.material.dispose(); 
            this.nadirLine = null; // IMPORTANT: Set to null
        }

        // Remove the CSS2D label object and its DOM element
        if (this.labelObject) {
            if (this.mesh) this.mesh.remove(this.labelObject);
            this.labelObject = null; // IMPORTANT: Set to null
        }

        if (this._labelElement) {
            this._labelElement.remove();
            this._labelElement = null; // IMPORTANT: Set to null
        }

        // Clear mesh reference
        this.mesh = null; // IMPORTANT: Set to null
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
    
    // Set initial visibility based on current settings
    label.visible = window.labelVisibilityEnabled;
    
    sat.labelObject = label;
    sat.mesh.add(label);
    sat._labelElement = div;
    
    // Update visibility immediately after creation
    if (window.labelVisibilityManager) {
        window.labelVisibilityManager.updateLabelVisibility();
    }
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

// ============= LABEL VISIBILITY MANAGER =============
class LabelVisibilityManager {
    constructor() {
        this.globalVisible = true;
        this.proximityMode = false;
        this.proximityDistance = 2.0;
        this.maxLabels = 10;
    }

    // ADD THIS NEW METHOD
    setNadirLinesVisibility(visible) {
        this.nadirLinesVisible = visible;
        window.nadirLinesEnabled = visible;
        this.updateNadirLinesVisibility();
    }

    // ADD THIS NEW METHOD
    updateNadirLinesVisibility() {
        window.activeSatellites.forEach(sat => {
            if (sat.nadirLine) {
                sat.nadirLine.visible = this.nadirLinesVisible;
            }
        });
    }

    updateLabelVisibility() {
        if (!this.globalVisible) {
            // Hide all labels
            this.hideAllLabels();
            return;
        }

        if (this.proximityMode) {
            this.updateProximityLabels();
        } else {
            // Show all labels
            this.showAllLabels();
        }
        // Update nadir lines visibility
        this.updateNadirLinesVisibility();
    }

    hideAllLabels() {
        window.activeSatellites.forEach(sat => {
            if (sat.labelObject) {
                sat.labelObject.visible = false;
            }
        });
        window.activeGroundStations.forEach(gs => {
            if (gs.labelObject) {
                gs.labelObject.visible = false;
            }
        });
    }

    showAllLabels() {
        window.activeSatellites.forEach(sat => {
            if (sat.labelObject) {
                sat.labelObject.visible = true;
            }
        });
        window.activeGroundStations.forEach(gs => {
            if (gs.labelObject) {
                gs.labelObject.visible = true;
            }
        });
    }

    updateProximityLabels() {
        const cameraPosition = camera.position;
        const satellites = Array.from(window.activeSatellites.values());
        
        // Calculate distances and sort
        const satellitesWithDistance = satellites.map(sat => ({
            satellite: sat,
            distance: cameraPosition.distanceTo(sat.mesh.position)
        })).sort((a, b) => a.distance - b.distance);

        // Hide all labels first
        this.hideAllLabels();

        // Show labels for closest satellites within distance threshold
        let visibleCount = 0;
        for (const item of satellitesWithDistance) {
            if (item.distance <= this.proximityDistance && visibleCount < this.maxLabels) {
                if (item.satellite.labelObject) {
                    item.satellite.labelObject.visible = true;
                    visibleCount++;
                }
            }
        }

        // Always show ground station labels (they're usually fewer)
        window.activeGroundStations.forEach(gs => {
            if (gs.labelObject) {
                gs.labelObject.visible = true;
            }
        });
    }

    setGlobalVisibility(visible) {
        this.globalVisible = visible;
        window.labelVisibilityEnabled = visible;
        this.updateLabelVisibility();
    }

    setProximityMode(enabled, distance = 2.0, maxLabels = 10) {
        this.proximityMode = enabled;
        this.proximityDistance = distance;
        this.maxLabels = maxLabels;
        window.proximityLabelsEnabled = enabled;
        window.labelProximityDistance = distance;
        window.maxVisibleLabels = maxLabels;
        this.updateLabelVisibility();
    }

    getStatus() {
        return {
            globalVisible: this.globalVisible,
            proximityMode: this.proximityMode,
            proximityDistance: this.proximityDistance,
            maxLabels: this.maxLabels,
            nadirLinesVisible: this.nadirLinesVisible // ADD THIS LINE
        };
    }
}

// Create the label manager instance
const labelVisibilityManager = new LabelVisibilityManager();
window.labelVisibilityManager = labelVisibilityManager;



// Expose label control functions globally
window.toggleLabels = function() {
    const newState = !window.labelVisibilityEnabled;
    labelVisibilityManager.setGlobalVisibility(newState);
    
    // Update UI button text
    const toggleBtn = document.getElementById('toggleLabelsBtn');
    if (toggleBtn) {
        toggleBtn.textContent = newState ? 'Hide Labels' : 'Show Labels';
    }
};

window.toggleProximityLabels = function() {
    const newState = !window.proximityLabelsEnabled;
    labelVisibilityManager.setProximityMode(
        newState, 
        window.labelProximityDistance, 
        window.maxVisibleLabels
    );
    
    // Update UI button text
    const proximityBtn = document.getElementById('toggleProximityLabelsBtn');
    if (proximityBtn) {
        proximityBtn.textContent = newState ? 'Disable Smart Labels' : 'Enable Smart Labels';
    }
};

window.configureLabelProximity = function(distance, maxLabels) {
    window.labelProximityDistance = distance;
    window.maxVisibleLabels = maxLabels;
    if (window.proximityLabelsEnabled) {
        labelVisibilityManager.setProximityMode(true, distance, maxLabels);
    }
};


// 3. ADD GLOBAL TOGGLE FUNCTION (Earth3Dsimulation.js)
window.toggleNadirLines = function() {
    const newState = !window.nadirLinesEnabled;
    
    if (window.labelVisibilityManager) {
        window.labelVisibilityManager.setNadirLinesVisibility(newState);
    } else {
        // Fallback if manager not available
        window.nadirLinesEnabled = newState;
        window.activeSatellites.forEach(sat => {
            if (sat.nadirLine) {
                sat.nadirLine.visible = newState;
            }
        });
    }
    
    // Update UI button text and state
    updateNadirButtonStates();
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

// Modify the existing createGroundStationLabel function
function createGroundStationLabel(gs) {
    const div = document.createElement('div');
    div.className = 'satellite-label'; // Re-use satellite-label class for styling
    div.textContent = gs.name;
    div.style.color = 'white';
    div.style.fontSize = '12px';
    div.style.whiteSpace = 'nowrap';
    const label = new CSS2DObject(div);
    label.position.set(0, 0.02, 0); // Offset slightly above the ground station
    
    // Set initial visibility based on current settings
    label.visible = window.labelVisibilityEnabled;
    
    gs.labelObject = label; // Store reference for visibility management
    gs.mesh.add(label);
    gs._labelElement = div;
    
    // Update visibility immediately after creation
    if (window.labelVisibilityManager) {
        window.labelVisibilityManager.updateLabelVisibility();
    }
}

// Add this function to handle batch label updates when satellites are added/removed
window.refreshAllLabels = function() {
    if (window.labelVisibilityManager) {
        window.labelVisibilityManager.updateLabelVisibility();
    }
};

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
     */
    constructor(id, name, latitude, longitude) {
        this.id = id;
        this.name = name;
        this.latitude = latitude;
        this.longitude = longitude;

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
        const lonRad = -this.longitude * DEG2RAD;

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

    }

    

    /**
     * Disposes of the ground station's meshes and lines to free up memory.
     */
    dispose() {
        // Remove any link lines associated with this ground station
        if (window.gsSatLinkLines) {
            const keysToRemove = [];
            window.gsSatLinkLines.forEach((line, key) => {
                if (key.startsWith(this.id + '|') || key.endsWith('|' + this.id)) {
                    scene.remove(line);
                    if (line.geometry) line.geometry.dispose();
                    if (line.material) line.material.dispose();
                    keysToRemove.push(key);
                }
            });
            keysToRemove.forEach(key => window.gsSatLinkLines.delete(key));
        }

        // Remove the station mesh
        if (this.mesh) {
            if (earthGroup && earthGroup.children.includes(this.mesh)) {
                earthGroup.remove(this.mesh);
            }
            if (this.mesh.geometry) this.mesh.geometry.dispose();
            if (this.mesh.material) this.mesh.material.dispose();
            this.mesh = null; // IMPORTANT: Set to null after disposal
        }

        // Remove the CSS2DObject label
        if (this.labelObject) {
            if (this.mesh) this.mesh.remove(this.labelObject);
            this.labelObject = null; // IMPORTANT: Set to null after disposal
        }

        // Remove its <div> from the DOM
        if (this._labelElement) {
            this._labelElement.remove();
            this._labelElement = null; // IMPORTANT: Set to null after disposal
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
earthRotationManager.initialize(window.currentEpochUTC);

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
     earthRotationManager.initialize(window.currentEpochUTC);

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
// Function to add or update a ground station in the scene
// This function will handle both adding new ground stations and updating existing ones
window.addOrUpdateGroundStationInScene = function(groundStationData) {
    const uniqueId = groundStationData.id || groundStationData.name;
    if (!uniqueId) {
        console.error("Ground station data missing unique ID or name.");
        return;
    }

    // Check if ground station already exists
    let existingGs = window.activeGroundStations.get(uniqueId);
    
    if (existingGs) {
        // Update existing ground station
        console.log(`Updating existing ground station: ${uniqueId}`);
        
        // Remove the old one
        existingGs.dispose();
        window.activeGroundStations.delete(uniqueId);
    }

    // Create new ground station
    const newGs = new GroundStation(
        uniqueId,
        groundStationData.name || uniqueId,
        groundStationData.latitude,
        groundStationData.longitude,
        groundStationData.minElevationAngle || 5 // Default 5 degrees if not provided
    );

    // Add to active ground stations
    window.activeGroundStations.set(newGs.id, newGs);
    
    console.log(`Ground station added: ${newGs.name} at (${newGs.latitude}°, ${newGs.longitude}°)`);

    // Create label for the ground station if it doesn't exist
    if (!newGs._labelElement) {
        createGroundStationLabel(newGs);
    }

    // If this is the only object in the scene, focus camera on it
    if (window.activeSatellites.size === 0 && window.activeGroundStations.size === 1) {
        const gsPosition = newGs.mesh.position;
        camera.position.set(
            gsPosition.x * 2, 
            gsPosition.y * 2, 
            gsPosition.z * 2 + 1
        );
        controls.target.copy(gsPosition);
        controls.update();
    }

    // Update UI if function exists
    if (typeof window.updateAnimationDisplay === 'function') {
        window.updateAnimationDisplay();
    }

    // Render the scene to show the new ground station
    renderer.render(scene, camera);
};

// Also add this helper function for debugging
window.listActiveGroundStations = function() {
    console.log("Active Ground Stations:");
    window.activeGroundStations.forEach((gs, id) => {
        console.log(`- ${id}: ${gs.name} at (${gs.latitude}°, ${gs.longitude}°)`);
    });
    return window.activeGroundStations;
};

// And this function to check ground station visibility
window.checkGroundStationVisibility = function() {
    window.activeGroundStations.forEach(gs => {
        console.log(`Ground Station ${gs.name}:`);
        console.log(`- Position: (${gs.mesh.position.x.toFixed(3)}, ${gs.mesh.position.y.toFixed(3)}, ${gs.mesh.position.z.toFixed(3)})`);
        console.log(`- Visible: ${gs.mesh.visible}`);
        console.log(`- In earthGroup: ${earthGroup.children.includes(gs.mesh)}`);
        if (gs.coverageCone) {
            console.log(`- Coverage cone visible: ${gs.coverageCone.visible}`);
        }
    });
};

// Data Passed From New Constellation Satellite Form
window.viewSimulation = function(data) {
    // --- 1) Clear scene & reset epoch ---
    window.clearSimulationScene();

    // ENHANCED: Handle TLE epoch synchronization
    if (data.tleLine1 && data.tleLine2) {
        try {
            const parsedTle = parseTle(data.tleLine1, data.tleLine2);
            window.currentEpochUTC = parsedTle.epochTimestamp;
            window.totalSimulatedTime = 0;
            console.log(`Synchronized simulation epoch to TLE epoch: ${new Date(parsedTle.epochTimestamp).toUTCString()}`);
        } catch (err) {
            console.error("Invalid TLE, falling back to current time:", err);
            window.currentEpochUTC = Date.now();
            window.totalSimulatedTime = 0;
        }
    } else if (typeof data.utcTimestamp === 'number') {
        window.currentEpochUTC = data.utcTimestamp;
        window.totalSimulatedTime = 0;
    } else {
        window.currentEpochUTC = Date.now();
        window.totalSimulatedTime = 0;
    }

    // recalc Earth rotation, sun, initial render
    earthRotationManager.initialize(window.currentEpochUTC);
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
        is2DViewActive:  window.is2DViewActive,
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
            trueAnomaly: satData.tleLine1 ? undefined : E_to_TrueAnomaly(solveKepler(satData.initialMeanAnomaly, satData.params.eccentricity), satData.params.eccentricity) * (180 / Math.PI),
            
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

// Compute a satellite's altitude (in km) from its scene‐unit radius
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

// Add constellation generation support for link budget
window.generateConstellationFromLinkBudget = function(linkBudgetData) {
    const constellationName = `${linkBudgetData.name}_Constellation`;
    
    // Create a standard 'constellation' data object from the link budget results.
    // This allows us to reuse the existing and robust constellation creation logic.
    const constellationParams = {
        fileName: constellationName,
        fileType: 'constellation',
        constellationType: 'walker', // Link budget defaults to a Walker constellation
        
        // --- Orbital parameters derived from the link budget ---
        altitude: linkBudgetData.altitude,
        inclination: linkBudgetData.inclination,
        beamwidth: linkBudgetData.beamwidth,
        eccentricity: 0, // Assume circular orbits for optimal coverage
        raan: 0,         // Base RAAN; will be spread across planes
        argumentOfPerigee: 0,
        trueAnomaly: 0,
        
        // --- Walker constellation parameters from the link budget ---
        numPlanes: linkBudgetData.numOrbitalPlanes,
        satellitesPerPlane: linkBudgetData.satsPerPlane,
        raanSpread: 360, // Spread planes evenly around the Earth
        phasingFactor: 1, // Standard phasing for Walker Delta patterns
        
        // --- Timing and metadata ---
        epoch: new Date().toISOString().slice(0, 16),
        utcTimestamp: Date.now(),
        satellites: [] // This will be populated by viewSimulation
    };
    
    // Now, call the main simulation function with the newly created constellation data.
    window.viewSimulation(constellationParams);
    
    // Save this new constellation object to local storage so it persists
    window.fileOutputs.set(constellationName, constellationParams);
    window.addFileToResourceSidebar(constellationName, constellationParams, 'constellation');
    if (window.saveFilesToLocalStorage) {
        window.saveFilesToLocalStorage();
    }

    showCustomAlert(`Generated and visualized '${constellationName}' from link budget analysis.`);
};

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
        
    }

    // Earth's rotation angle includes the initial offset to align with GMST at epoch
    const earthRotationAngle = earthRotationManager.getRotationAngle(core3D.totalSimulatedTime);
    earthGroup.rotation.y = earthRotationAngle;

    // --- update sun & shader time uniform ---
    updateSunDirection(core3D.totalSimulatedTime);
    earthMesh.material.uniforms.uTime.value = core3D.totalSimulatedTime;
    
    // --- render 3D every frame ---
    renderer.render(scene, camera);

    // --- render labels ---
    labelRenderer.render(scene, camera);

    // Update label visibility based on proximity (add this near the end of animate function)
    if (window.proximityLabelsEnabled) {
        labelVisibilityManager.updateProximityLabels();
    }
    
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

    // SAFE CALL: Wrap updateGsSatLinkLines in try-catch
    try {
        updateGsSatLinkLines(); // For Connection Analysis
    } catch (error) {
        console.error('Error in updateGsSatLinkLines:', error);
        // Clear problematic link lines
        if (window.gsSatLinkLines) {
            window.gsSatLinkLines.forEach((line, key) => {
                try {
                    scene.remove(line);
                    if (line.geometry) line.geometry.dispose();
                    if (line.material) line.material.dispose();
                } catch (e) {
                    console.warn('Error disposing link line:', e);
                }
            });
            window.gsSatLinkLines.clear();
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
        labelRenderer.setSize(newWidth, newHeight);
        camera.aspect = newWidth / newHeight;
        camera.updateProjectionMatrix();
        controls.update(); // Update controls after camera aspect changes
    }
});