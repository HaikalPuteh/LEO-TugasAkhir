// Earth2Dsimulation.js

// Import necessary modules and constants
import * as THREE from "three"; // Needed for THREE.Vector3 and its methods for position manipulation
import * as d3 from "d3"; // Import D3.js
import { SCENE_EARTH_RADIUS } from "./parametersimulation.js"; // Earth's radius in scene units

// Import astronomical calculation functions from the shared utility file
import { 
    degToRad,        // Convert degrees to radians
    radToDeg,        // Convert radians to degrees
    getSubsolarPoint // Calculate subsolar point for day/night rendering
} from "./sunCalculations.js";

// --- Canvas Setup ---
const canvas = document.getElementById('map-2D-canvas');

// Critical check: Ensure the canvas element exists before proceeding
if (!canvas) {
    console.error("CRITICAL ERROR: 'map-2D-canvas' element not found in the HTML. 2D simulation cannot initialize.");
    window.draw2D = function() {};
    window.resizeCanvas2D = function() { console.warn("2D canvas not available, resizeCanvas2D is a no-op."); };
    throw new Error("2D canvas element not found, terminating Earth2Dsimulation.js initialization.");
}

const ctx = canvas.getContext('2d');

// --- D3.js Projection and Path Generator ---
let projection; // D3 geographic projection
let pathGenerator; // D3 path generator for Canvas

// --- Texture Loading ---
const earthTexture = new Image();
earthTexture.src = '/textures/Earth_DayMap.jpg';
const nightLightsTexture = new Image();
nightLightsTexture.src = '/textures/Earth_NightMap.jpg';

// Expose texturesLoaded globally
window.texturesLoaded = false;
let texturesToLoad = 2; // Earth map and night lights

    // --- Canvas Resizing ---
    window.resizeCanvas = function() {
        const earth2DContainer = document.getElementById('earth2D-container');
        if (!earth2DContainer) {
            console.warn("WARNING: 'earth2D-container' not found for 2D canvas resizing.");
            return;
        }
        // Set canvas size to match the container's dimensions exactly
        const containerWidth = earth2DContainer.offsetWidth;
        const containerHeight = earth2DContainer.offsetHeight;

        // Check if container has valid dimensions before setting canvas size
        if (containerWidth === 0 || containerHeight === 0) {
            console.warn("D3 Setup: earth2D-container has zero dimensions. Skipping projection initialization and setting canvas size.");
            projection = null; // Invalidate projection to prevent D3 errors
            pathGenerator = null;
            return; // Exit early, try again on next resize or draw call
        }

        canvas.width = containerWidth;
        canvas.height = containerHeight;

        projection = d3.geoEquirectangular()
            .fitExtent([[0, 0], [canvas.width, canvas.height]], { type: "Sphere" });
        pathGenerator = d3.geoPath().projection(projection).context(ctx);

        // Trigger a redraw after resize if the 2D view is active and textures are loaded
        if (window.texturesLoaded && window.is2DViewActive) {
            draw();
        }
    }

    // --- Texture Load Handler ---
    function textureLoaded() {
        texturesToLoad--;
        console.log(`Texture loaded. Textures remaining: ${texturesToLoad}`);

        if (texturesToLoad === 0) {
            window.texturesLoaded = true; // Update global variable
            console.log("All 2D textures loaded! texturesLoaded = true");
            const loadingMessage = document.getElementById('loading-message');
            if (loadingMessage) loadingMessage.style.display = 'none';

            // If 2D view is active when textures load, draw the first frame
            if (window.is2DViewActive) {
                draw();
            }
        }
    }

    // --- Texture Event Handlers ---
    earthTexture.onload = textureLoaded;
    nightLightsTexture.onload = textureLoaded;

    earthTexture.onerror = (e) => {
        console.error("ERROR: Failed to load texture. Check path and file.", e);
        texturesToLoad--;
        if (texturesToLoad === 0) textureLoaded();
    };
    nightLightsTexture.onerror = (e) => {
        console.error("ERROR: Failed to load texture. Check path and file.", e);
        texturesToLoad--;
        if (texturesToLoad === 0) textureLoaded();
    };

    // --- Coordinate Transformations ---
    function positionToLatLon(position) {
        const eciPosition_in_ThreeJsCoords = new THREE.Vector3().copy(position);
        const earthRotationAngleFor2D = window.getEarthRotationY ? window.getEarthRotationY() : 0;

        const ecefPosition_in_ThreeJsCoords = eciPosition_in_ThreeJsCoords
            .applyAxisAngle(new THREE.Vector3(0, 1, 0), -earthRotationAngleFor2D);

        const r = ecefPosition_in_ThreeJsCoords.length();
        if (r < 1e-6) return { lat: 0, lon: 0 };

        const lat = Math.asin(ecefPosition_in_ThreeJsCoords.y / r) * (180 / Math.PI);
        let lon = -Math.atan2(ecefPosition_in_ThreeJsCoords.z, ecefPosition_in_ThreeJsCoords.x) * (180 / Math.PI);

     
        lon = ((lon % 360) + 360) % 360;
        if (lon > 180) lon -= 360;
        return { lat, lon };
    }

    // --- Drawing Functions ---
    function drawOrbitalPath2D(satellite, ctx) {
        if (!satellite.orbitalPath3DPoints || satellite.orbitalPath3DPoints.length < 2 || !pathGenerator) return;

        ctx.beginPath();
        ctx.strokeStyle = 'rgba(0, 255, 0, 0.7)';
        ctx.lineWidth = 1.5;

        const geoLine = {
            type: "LineString",
            coordinates: satellite.orbitalPath3DPoints.map(p => {
                const { lat, lon } = positionToLatLon(p);
                return [lon, lat];
            })
        };

        pathGenerator(geoLine);
        ctx.stroke();
    }

    function drawGroundTrack2D(satellite, ctx) {
        if (!satellite.groundTrackHistory || satellite.groundTrackHistory.length < 2 || !pathGenerator) return;

        ctx.lineWidth = 2;

        for (let i = 1; i < satellite.groundTrackHistory.length; i++) {
            const p1_geo = satellite.groundTrackHistory[i - 1];
            const p2_geo = satellite.groundTrackHistory[i];
            const opacity = 0.1 + 0.7 * (i / satellite.groundTrackHistory.length);
            ctx.strokeStyle = `rgba(255, 165, 0, ${opacity})`;

            const segment = {
                type: "LineString",
                coordinates: [[p1_geo.lon, p1_geo.lat], [p2_geo.lon, p2_geo.lat]]
            };

            ctx.beginPath();
            pathGenerator(segment);
            ctx.stroke();
        }
    }

    function drawCoverageArea2D(satellite, ctx) {
        if (!satellite.params || satellite.params.beamWidthDeg === undefined || !pathGenerator) return;

        const satPosition = satellite.mesh.position;
        const { lat, lon } = positionToLatLon(satPosition);
        const satDistanceToCenter = satPosition.length();
        const earthRadiusScene = SCENE_EARTH_RADIUS;

        if (satDistanceToCenter <= earthRadiusScene) return;

        const halfBeamAngleRad = degToRad(satellite.params.beamWidthDeg / 2);
        let coverageCentralAngleRad = Math.acos((satDistanceToCenter * Math.cos(halfBeamAngleRad)) / earthRadiusScene) - halfBeamAngleRad;
        const horizonCentralAngle = Math.acos(earthRadiusScene / satDistanceToCenter);
        coverageCentralAngleRad = Math.min(coverageCentralAngleRad, horizonCentralAngle);

        if (isNaN(coverageCentralAngleRad) || coverageCentralAngleRad <= 0) return;

        const coverageRadiusDegrees = radToDeg(coverageCentralAngleRad);
        const circle = d3.geoCircle().center([lon, lat]).radius(coverageRadiusDegrees);

        if (coverageRadiusDegrees > 0) {
            ctx.beginPath();
            pathGenerator(circle());
            ctx.fillStyle = 'rgba(0, 100, 255, 0.05)';
            ctx.fill();
            ctx.strokeStyle = 'rgba(0, 100, 255, 0.3)';
            ctx.lineWidth = 1;
            ctx.stroke();
        }
    }

    function drawGroundStation2D(groundStation, ctx) {
        const { lat, lon, minElevationAngle } = groundStation;
        if (!pathGenerator) return;

        const geoPoint = { type: "Point", coordinates: [lon, lat] };
        ctx.beginPath();
        pathGenerator(geoPoint);
        ctx.arc(projection([lon, lat])[0], projection([lon, lat])[1], 3, 0, 2 * Math.PI);
        ctx.fillStyle = 'yellow';
        ctx.fill();

        const minElevRad = degToRad(minElevationAngle);
        const centralAngleRad = Math.PI / 2 - minElevRad;
        const coverageRadiusDegrees = radToDeg(centralAngleRad);

        if (coverageRadiusDegrees > 0) {
            const circle = d3.geoCircle().center([lon, lat]).radius(coverageRadiusDegrees);
            ctx.beginPath();
            pathGenerator(circle());
            ctx.fillStyle = 'rgba(255, 0, 255, 0.05)';
            ctx.fill();
            ctx.strokeStyle = 'rgba(255, 0, 255, 0.3)';
            ctx.lineWidth = 1;
            ctx.stroke();
        }
    }

// --- Main Drawing Function ---
    function draw() {
        if (!window.is2DViewActive || !window.texturesLoaded || !ctx || !projection || !pathGenerator) {
            if (canvas.width === 0 || canvas.height === 0) {
                window.resizeCanvas();
                if (canvas.width === 0 || canvas.height === 0) {
                    console.warn("2D Draw Status: Canvas still has zero dimensions after resize attempt. Skipping draw.");
                    return;
                }
            } else {
                return;
            }
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const currentAbsoluteTimeMs = window.currentEpochUTC + (window.totalSimulatedTime * 1000);
        const currentDateTime = new Date(currentAbsoluteTimeMs);
        const subsolarPoint = getSubsolarPoint(currentDateTime);

        ctx.drawImage(earthTexture, 0, 0, canvas.width, canvas.height);

        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = canvas.width;
        tempCanvas.height = canvas.height;
        const tempCtx = tempCanvas.getContext('2d');

        tempCtx.drawImage(nightLightsTexture, 0, 0, canvas.width, canvas.height);
        tempCtx.globalCompositeOperation = 'destination-out';

        tempCtx.beginPath();
        const δ = subsolarPoint.latitude;       // radians
        const λ0 = subsolarPoint.longitude;     // radians
        const terminatorLinePoints = [];
        for (let lonDeg = -180; lonDeg <= 180; lonDeg += 1) {
            const λ = degToRad(lonDeg);
            const φ = Math.atan2(
                -Math.cos(δ) * Math.cos(λ - λ0),
                Math.sin(δ)
            );
            const latDeg = radToDeg(φ);
            terminatorLinePoints.push([lonDeg, latDeg]);
        }

        const firstProjectedPoint = projection(terminatorLinePoints[0]);
        tempCtx.moveTo(firstProjectedPoint[0], firstProjectedPoint[1]);
        for (let i = 1; i < terminatorLinePoints.length; i++) {
            const [lon, lat] = terminatorLinePoints[i];
            const [px, py] = projection([lon, lat]);
            tempCtx.lineTo(px, py);
        }

        if (subsolarPoint.latitude >= 0) {
            tempCtx.lineTo(canvas.width, canvas.height);
            tempCtx.lineTo(0, canvas.height);
        } else {
            tempCtx.lineTo(canvas.width, 0);
            tempCtx.lineTo(0, 0);
        }
        tempCtx.closePath();
        tempCtx.fill();
        ctx.drawImage(tempCanvas, 0, 0);

        ctx.globalCompositeOperation = 'source-over'; 

        if (window.activeSatellites && window.activeSatellites.size > 0) {
            window.activeSatellites.forEach(sat => {
                drawOrbitalPath2D(sat, ctx);
                drawGroundTrack2D(sat, ctx);
                drawCoverageArea2D(sat, ctx);

                const { lat, lon } = positionToLatLon(sat.mesh.position);
                const [px, py] = projection([lon, lat]);
                ctx.beginPath();
                ctx.arc(px, py, 5, 0, 2 * Math.PI);
                ctx.fillStyle = 'red';
                ctx.fill();
            });
        }

        if (window.activeGroundStations && window.activeGroundStations.size > 0) {
            window.activeGroundStations.forEach(gs => {
                drawGroundStation2D(gs, ctx);
            });
        }
    }

// Expose functions globally
window.draw2D = draw;
window.resizeCanvas2D = window.resizeCanvas;

// --- Event Listeners ---
window.addEventListener('resize', window.resizeCanvas);

// --- Global Toggle Function ---
window.toggle2DSimulation = function(state) {
    window.is2DViewActive = state;
    if (state) {
        window.resizeCanvas();
        if (window.texturesLoaded) {
            draw();
        }
    } else {
        if (ctx) ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
};

// --- Epoch Update Listener ---
window.addEventListener('epochUpdated', () => {
    if (window.is2DViewActive && window.texturesLoaded) {
        draw();
    }
});