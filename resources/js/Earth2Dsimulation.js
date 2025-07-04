// Earth2Dsimulation.js

// Import necessary modules and constants
import * as THREE from "three"; // Needed for Vector3 math
import * as d3 from "d3";
import { SCENE_EARTH_RADIUS } from "./parametersimulation.js";

// Import astronomical calculation functions
import {
  degToRad,
  radToDeg,
  getSubsolarPoint,
} from "./sunCalculations.js";

// --- Canvas Setup ---
const canvas = document.getElementById('map-2D-canvas');
if (!canvas) {
  console.error("CRITICAL: 'map-2D-canvas' element not found. 2D simulation disabled.");
  window.draw2D = () => {};
  window.resizeCanvas2D = () => {};
  throw new Error("2D canvas element not found, terminating Earth2Dsimulation.js");
}
const ctx = canvas.getContext('2d');

// --- D3 Projection & Path ---
let projection;
let pathGenerator;

// --- Texture Loading ---
const earthTexture = new Image();
const nightLightsTexture = new Image();
let texturesToLoad = 2;
window.texturesLoaded = false;

function textureLoaded() {
    if (--texturesToLoad === 0) {
        window.texturesLoaded = true;
        const loadingMessage = document.getElementById('loading-message');
        if (loadingMessage) loadingMessage.style.display = 'none';
        if (window.is2DViewActive) draw2D();
    }
}

earthTexture.onload = textureLoaded;
earthTexture.onerror = (e) => { console.error("Failed to load day map", e); textureLoaded(); };
nightLightsTexture.onload = textureLoaded;
nightLightsTexture.onerror = (e) => { console.error("Failed to load night lights map", e); textureLoaded(); };

earthTexture.src = '/textures/Earth_DayMap.jpg';
nightLightsTexture.src = '/textures/Earth_NightMap.jpg';

// --- Canvas Resizing ---
window.resizeCanvas2D = function() {
  const container = document.getElementById('earth2D-container');
  if (!container) return;
  const w = container.offsetWidth, h = container.offsetHeight;
  if (!w||!h) { projection = pathGenerator = null; return; }

  canvas.width = w; canvas.height = h;

 // apply only the _initial_ GMST offset—
   // don't re-fit on every frame:
   const initialOffsetDeg = (window.initialEarthRotationOffset * 180 / Math.PI) % 360 ;// Ensure it's in [0, 360) range
  // Create a new projection with the initial rotation offset
   projection = d3.geoEquirectangular()
    .rotate([initialOffsetDeg, 0])
    .fitExtent([[0,0],[w,h]], { type: 'Sphere' });
 
  pathGenerator = d3.geoPath()
    .projection(projection)
    .context(ctx);

  if (window.is2DViewActive && window.texturesLoaded) draw2D();
};


window.addEventListener('resize', window.resizeCanvas2D);

// --- Coordinate Transformation ---
function positionToLatLon(pos) {
  const v = pos.clone();
  // total Earth rotation since epoch:
  const θ = -window.initialEarthRotationOffset + window.totalSimulatedTime * window.EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;
  //const θ = window.getEarthRotationY ? window.getEarthRotationY() : 0

  // undo *all* of it, ECI→ECEF:
  v.applyAxisAngle(new THREE.Vector3(0,1,0), -θ);

  const r = v.length();
  if (r < 1e-6) return { lat: 0, lon: 0 };

  const lat = radToDeg(Math.asin(v.y / r));
  // flip lon sign to match 3D direction
  let lon = -radToDeg(Math.atan2(v.z, v.x));
    lon = ((lon % 360) + 360) % 360;
    if (lon > 180) lon -= 360;
  return { lat, lon };

}

// --- Drawing Helpers ---
function drawOrbitalPath2D(sat) {
    if (!pathGenerator || !sat.orbitalPath3DPoints?.length) return;
    const geo = {
        type: 'LineString',
        coordinates: sat.orbitalPath3DPoints.map(p => {
            const { lat, lon } = positionToLatLon(p);
            return [lon, lat];
        })
    };
    ctx.beginPath();
    ctx.strokeStyle = 'rgba(0,255,0,0.7)';
    ctx.lineWidth = 1.5;
    pathGenerator(geo);
    ctx.stroke();
}

function drawGroundTrack2D(sat) {
    if (!pathGenerator || !sat.groundTrackHistory?.length) return;
    const n = sat.groundTrackHistory.length;
    for (let i = 1; i < n; i++) {
        const p1 = sat.groundTrackHistory[i-1];
        const p2 = sat.groundTrackHistory[i];
        ctx.beginPath();
        ctx.strokeStyle = `rgba(255,165,0,${0.1 + 0.7*(i/n)})`;
        ctx.lineWidth = 2;
        pathGenerator({
            type: 'LineString',
            coordinates: [[p1.lon, p1.lat], [p2.lon, p2.lat]]
        });
        ctx.stroke();
    }
}

function drawCoverageArea2D(sat) {
    const { lat, lon } = positionToLatLon(sat.mesh.position);
    const φ = sat.coverageAngleRad;
    if (φ <= 0) return;
    const circle = d3.geoCircle().center([lon, lat]).radius(radToDeg(φ));
    ctx.beginPath();
    pathGenerator(circle());
    ctx.fillStyle = 'rgba(136,136,136,0.05)'; ctx.fill();
    ctx.strokeStyle = 'rgb(255,11,11)'; ctx.lineWidth = 1; ctx.stroke();
}

function drawGroundStation2D(gs) {
    if (!pathGenerator) return;
    const { latitude: lat, longitude: lon, minElevationAngle } = gs;
    const [x, y] = projection([lon, lat]);
    ctx.beginPath();
    ctx.arc(x, y, 3, 0, 2*Math.PI);
    ctx.fillStyle = 'yellow'; ctx.fill();
    const central = Math.PI/2 - degToRad(minElevationAngle);
    if (central > 0) {
        const circle = d3.geoCircle().center([lon, lat]).radius(radToDeg(central));
        ctx.beginPath();
        pathGenerator(circle());
        ctx.fillStyle = 'rgba(255,0,255,0.05)'; ctx.fill();
        ctx.strokeStyle = 'rgba(255,0,255,0.3)'; ctx.lineWidth = 1; ctx.stroke();
    }
}


// --- Main Draw ---
function draw2D() {
  if (!window.is2DViewActive || !window.texturesLoaded) return;
 
  ctx.clearRect(0,0,canvas.width,canvas.height);

  // 1) draw the day side:
  ctx.drawImage(earthTexture, 0,0,canvas.width,canvas.height);

  // 2) mask out the night layer via a temporary canvas:
  const tmp = document.createElement('canvas');
  tmp.width = canvas.width; tmp.height = canvas.height;
  const tctx = tmp.getContext('2d');
  tctx.drawImage(nightLightsTexture, 0,0,canvas.width,canvas.height);

  // — punch out the day side in tctx based on getSubsolarPoint —
  tctx.save();
  tctx.globalCompositeOperation = 'destination-out';

    // compute current subsolar point (in radians) at your sim time:
     // update projection rotation per current GMST
  const simDate = new Date(window.currentEpochUTC + window.totalSimulatedTime *1000);
  const sub = getSubsolarPoint(simDate);

  // convert to degrees:
  const centerLon = sub.Sun_ra;
  const centerLat = sub.Sun_dec;

  // build & draw a 90°-radius terminator circle
  d3.geoPath()
    .projection(projection)
    .context(tctx)
    ( d3.geoCircle()
        .center([centerLon, centerLat])
        .radius(90)
        .precision(0.5)()
    );

  tctx.fillStyle = 'black';
  tctx.fill();  // erases the day side from the night layer
  tctx.restore(); //

  // 3) composite back onto your main canvas:
  ctx.globalCompositeOperation = 'source-over';
  ctx.drawImage(tmp, 0,0);

  // 4) finally, overlay satellites & ground stations
  window.activeSatellites?.forEach(sat => {
    drawOrbitalPath2D(sat);
    drawGroundTrack2D(sat);
    drawCoverageArea2D(sat);
    const { lat, lon } = positionToLatLon(sat.mesh.position);
    const [x, y] = projection([lon, lat]);
    ctx.beginPath(); ctx.arc(x,y,5,0,2*Math.PI);
    ctx.fillStyle='red'; ctx.fill();
  });
  window.activeGroundStations?.forEach(gs => drawGroundStation2D(gs));
}

// Expose
window.draw2D = draw2D;

// Toggle
window.toggle2DSimulation = (on) => {
    window.is2DViewActive = on;
    if (on) {
        window.resizeCanvas2D();
        if (window.texturesLoaded) draw2D();
    } else {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
};

// Redraw on epoch update
window.addEventListener('epochUpdated', () => {
    if (window.is2DViewActive && window.texturesLoaded) draw2D();
});
