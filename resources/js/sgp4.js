// sgp4.js
import * as THREE from "three";
import * as satellite from "satellite.js";
import { SCENE_EARTH_RADIUS, EarthRadius } from "./parametersimulation.js";

const EARTH_RADIUS_KM = EarthRadius;

/** …parseTle… **/
export function parseTle(tleLine1, tleLine2) {
  const satrec = satellite.twoline2satrec(tleLine1, tleLine2);
  const jds = satrec.jdsatepoch + satrec.jdsatepochF;
  const epochTimestamp = (jds - 2440587.5) * 86400000.0;
  return { satrec, tleLine1, tleLine2, epochTimestamp };
}

/** …propagateSGP4… **/
export function propagateSGP4(satelliteData, utcDate) {
  try {
    const { satrec } = satelliteData;
    const pv = satellite.propagate(satrec, utcDate);
    if (!pv.position || !pv.velocity) return null;
    const scale = SCENE_EARTH_RADIUS / EARTH_RADIUS_KM;
    const { x: px, y: py, z: pz } = pv.position;
    const { x: vx, y: vy, z: vz } = pv.velocity;
    return {
      position: new THREE.Vector3(px * scale, pz * scale, py * scale),
      velocity: new THREE.Vector3(vx * scale, vz * scale, vy * scale),
    };
  } catch {
    return null;
  }
}
