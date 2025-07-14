//orbitalCalculation.js
import * as THREE from "three";
import {
    J2,
    MU_EARTH,
    EarthRadius,
} from "./parametersimulation.js";

/**
 * Validates orbital parameters for physical realism
 * @param {object} params - Orbital parameters to validate
 * @throws {Error} If parameters are invalid
 */
export function validateOrbitalParameters(params) {
    if (params.eccentricity < 0 || params.eccentricity >= 1) {
        throw new Error(`Invalid eccentricity: ${params.eccentricity}. Must be 0 ≤ e < 1`);
    }
    
    const altitudeKm = (params.semiMajorAxis - 1) * EarthRadius; // Convert from scene units
    if (altitudeKm < 100) {
        throw new Error(`Altitude too low: ${altitudeKm.toFixed(1)} km. Minimum is 100 km`);
    }
    
    if (altitudeKm > 100000) {
        throw new Error(`Altitude too high: ${altitudeKm.toFixed(1)} km. Maximum is 100,000 km`);
    }
    
    if (params.inclinationRad < 0 || params.inclinationRad > Math.PI) {
        throw new Error(`Invalid inclination: ${(params.inclinationRad * 180/Math.PI).toFixed(1)}°. Must be 0-180°`);
    }
    
    if (params.beamwidth <= 0 || params.beamwidth > 180) {
        throw new Error(`Invalid beamwidth: ${params.beamwidth}°. Must be 0-180°`);
    }
}

/**
 * Solves Kepler's Equation (M = E - e*sin(E)) for the Eccentric Anomaly (E)
 * using the Newton-Raphson iterative method with improved convergence.
 * @param {number} M - Mean Anomaly in radians.
 * @param {number} e - Eccentricity (dimensionless).
 * @param {number} [epsilon=1e-8] - Desired accuracy for E.
 * @param {number} [maxIterations=50] - Maximum number of iterations to prevent infinite loops.
 * @returns {number} The Eccentric Anomaly in radians.
 */
export function solveKepler(M, e, epsilon = 1e-8, maxIterations = 50) {
    // Validate inputs
    if (e < 0 || e >= 1) {
        throw new Error(`Invalid eccentricity for Kepler solver: ${e}`);
    }
    
    // Normalize mean anomaly to [0, 2π]
    M = ((M % (2 * Math.PI)) + (2 * Math.PI)) % (2 * Math.PI);
    
    // Better initial guess based on eccentricity
    let E;
    if (e < 0.8) {
        E = M;
    } else {
        // For high eccentricity, use a better initial guess
        E = M + e * Math.sin(M) / (1 - Math.sin(M + e) + Math.sin(M));
    }
    
    for (let i = 0; i < maxIterations; i++) {
        const sinE = Math.sin(E);
        const cosE = Math.cos(E);
        const f = E - e * sinE - M;
        const fp = 1 - e * cosE;
        
        // Newton-Raphson step
        const dE = f / fp;
        E -= dE;
        
        if (Math.abs(dE) < epsilon) {
            return E;
        }
    }
    
    // Warn if not converged but still return best estimate
    console.warn(`Kepler equation did not converge for M=${M.toFixed(6)}, e=${e.toFixed(6)} after ${maxIterations} iterations. Final error: ${Math.abs(E - e * Math.sin(E) - M).toFixed(8)}`);
    return E;
}

/**
 * Converts Eccentric Anomaly (E) to True Anomaly (nu, or theta).
 * @param {number} E - Eccentric Anomaly in radians.
 * @param {number} e - Eccentricity.
 * @returns {number} The True Anomaly in radians.
 */
export function E_to_TrueAnomaly(E, e) {
    const tanHalfNu = Math.sqrt((1 + e) / (1 - e)) * Math.tan(E / 2);
    return 2 * Math.atan(tanHalfNu);
}

/**
 * Converts True Anomaly (nu, or theta) to Eccentric Anomaly (E).
 * @param {number} nu - True Anomaly in radians.
 * @param {number} e - Eccentricity.
 * @returns {number} The Eccentric Anomaly in radians.
 */
export function TrueAnomaly_to_E(nu, e) {
    const tanHalfE = Math.sqrt((1 - e) / (1 + e)) * Math.tan(nu / 2);
    return 2 * Math.atan(tanHalfE);
}

/**
 * Converts Eccentric Anomaly (E) to Mean Anomaly (M).
 * @param {number} E - Eccentric Anomaly in radians.
 * @param {number} e - Eccentricity.
 * @returns {number} The Mean Anomaly in radians.
 */
export function E_to_M(E, e) {
    return E - e * Math.sin(E);
}

/**
 * Calculates the Cartesian (x, y, z) position of a satellite in the Earth-centered inertial (ECI) frame.
 * This is based on its classical orbital elements.
 *
 * @param {object} params - Satellite orbital parameters (semiMajorAxis (in scene units), eccentricity, inclinationRad, argPerigeeRad).
 * @param {number} currentMeanAnomaly - Current Mean Anomaly in radians.
 * @param {number} currentRAAN - Current Right Ascension of the Ascending Node in radians.
 * @param {number} [sceneEarthRadius=1] - The Earth's radius in Three.js scene units (this is SCENE_EARTH_RADIUS).
 * @returns {object} An object with x, y, z properties in Three.js scene units.
 */
export function calculateSatellitePositionECI(params, currentMeanAnomaly, currentRAAN, sceneEarthRadius = 1) {
    // Validate parameters
    try {
        validateOrbitalParameters(params);
    } catch (error) {
        console.warn("Orbital parameter validation warning:", error.message);
    }
    
    // Convert semiMajorAxis from scene units (relative to SCENE_EARTH_RADIUS) to actual kilometers
    const a_km_actual = params.semiMajorAxis * EarthRadius;
    const e = params.eccentricity;
    const i_rad = params.inclinationRad;
    const argPerigee_rad = params.argPerigeeRad;

    const E = solveKepler(currentMeanAnomaly, e);
    const nu = E_to_TrueAnomaly(E, e);

    const r_km = a_km_actual * (1 - e * e) / (1 + e * Math.cos(nu));

    const x_perifocal = r_km * Math.cos(nu);
    const y_perifocal = r_km * Math.sin(nu);

    const position_km = new THREE.Vector3(x_perifocal, y_perifocal, 0);

    const rotationMatrix = new THREE.Matrix4();
    const R_argP = new THREE.Matrix4().makeRotationZ(argPerigee_rad);
    const R_inc = new THREE.Matrix4().makeRotationX(i_rad);
    const R_raan = new THREE.Matrix4().makeRotationZ(currentRAAN);

    rotationMatrix.multiply(R_raan).multiply(R_inc).multiply(R_argP);
    position_km.applyMatrix4(rotationMatrix);

    // This mapping is the key to fixing the orbital direction.
    // ECI X -> Three.js X
    // ECI Y -> Three.js -Z (to maintain a right-handed system)
    // ECI Z -> Three.js Y (up)
    const scenePosition = new THREE.Vector3(
        position_km.x / EarthRadius * sceneEarthRadius,
        position_km.z / EarthRadius * sceneEarthRadius,
        -position_km.y / EarthRadius * sceneEarthRadius
    );

    return { x: scenePosition.x, y: scenePosition.y, z: scenePosition.z };
}

/**
 * Calculates additional derived orbital parameters.
 * @param {number} altitude - Altitude in km.
 * @param {number} eccentricity - Eccentricity (dimensionless).
 * @param {number} [earthRadius=EarthRadius] - Earth radius in km (optional override).
 * @returns {object} Object with orbitalPeriod (seconds), orbitalVelocity (km/s), semiMajorAxis (km).
 */
export function calculateDerivedOrbitalParameters(altitude, eccentricity, earthRadius = EarthRadius) {
    // semiMajorAxis here is in km, as altitude is in km and EarthRadius is in km.
    const semiMajorAxis_km = earthRadius + altitude;
    
    // Validate inputs
    if (semiMajorAxis_km <= 0) {
        throw new Error(`Invalid semi-major axis: ${semiMajorAxis_km} km`);
    }
    if (eccentricity < 0 || eccentricity >= 1) {
        throw new Error(`Invalid eccentricity: ${eccentricity}`);
    }
    
    const orbitalPeriodSeconds = 2 * Math.PI * Math.sqrt(Math.pow(semiMajorAxis_km, 3) / MU_EARTH);
    const orbitalVelocity = Math.sqrt(MU_EARTH / semiMajorAxis_km); // Velocity for circular orbit, or average for elliptical.

    return {
        orbitalPeriod: orbitalPeriodSeconds,
        orbitalVelocity: orbitalVelocity,
        semiMajorAxis: semiMajorAxis_km // Include semi-major axis for convenience
    };
}

/**
 * Enhanced J2 perturbation model that includes all major effects:
 * - RAAN precession (secular)
 * - Argument of perigee precession (secular) 
 * - Mean anomaly drift (secular)
 * - Semi-major axis variation (periodic)
 * - Eccentricity variation (periodic)
 */
/**
 * CORRECT J2 perturbation model with standard, validated formulas
 * Based on Vallado's "Fundamentals of Astrodynamics and Applications"
 * and other authoritative orbital mechanics sources
 */
export function updateOrbitalElements(satellite, totalSimulatedTime) {
  const a_km = satellite.params.semiMajorAxis * EarthRadius;
  const e = satellite.params.eccentricity;
  const i = satellite.params.inclinationRad;
  const n = Math.sqrt(MU_EARTH / (a_km * a_km * a_km));
  
  // Store initials once
  if (satellite.initialRAAN === undefined) satellite.initialRAAN = satellite.currentRAAN;
  if (satellite.initialArgPerigee === undefined) satellite.initialArgPerigee = satellite.params.argPerigeeRad;
  if (satellite.initialMeanAnomaly === undefined) satellite.initialMeanAnomaly = satellite.currentMeanAnomaly;
  if (satellite.initialSemiMajorAxis === undefined) {
    satellite.initialSemiMajorAxis = a_km;
    satellite.initialEccentricity = e;
  }
  
  // Base J2 factor: (3/2) * J2 * (Re/a)² * n
  const J2_base = 1.5 * J2 * Math.pow(EarthRadius / a_km, 2) * n;
  const cosI = Math.cos(i);
  const cos2I = cosI * cosI;
  const oneMinusE2 = 1 - e * e;
  
  // Secular rates (Vallado §4.3)
  const dRAAN_dt = -J2_base * cosI / (oneMinusE2 * oneMinusE2);
  const dArgPerigee_dt = (J2_base / 2) * (5 * cos2I - 1) / (oneMinusE2 * oneMinusE2);
  const dM_dt = (J2_base / 2) * (3 * cos2I - 1) / Math.pow(oneMinusE2, 1.5);
  
  // Apply secular drifts
  let Ω = satellite.initialRAAN + dRAAN_dt * totalSimulatedTime;
  let ω = satellite.initialArgPerigee + dArgPerigee_dt * totalSimulatedTime;
  let M = satellite.initialMeanAnomaly + n * totalSimulatedTime + dM_dt * totalSimulatedTime;
  
  // Normalize to [0, 2π)
  satellite.currentRAAN = ((Ω % (2*Math.PI)) + 2*Math.PI) % (2*Math.PI);
  satellite.params.argPerigeeRad = ((ω % (2*Math.PI)) + 2*Math.PI) % (2*Math.PI);
  satellite.currentMeanAnomaly = ((M % (2*Math.PI)) + 2*Math.PI) % (2*Math.PI);
  
  // Periodic effects (unchanged)
  const E = solveKepler(satellite.currentMeanAnomaly, e);
  const ν = E_to_TrueAnomaly(E, e);
  const u = satellite.params.argPerigeeRad + ν;
  
  const δa = satellite.initialSemiMajorAxis * (1.5 * J2 * Math.pow(EarthRadius / satellite.initialSemiMajorAxis, 2)) * Math.sqrt(1 - e*e) * Math.sin(2 * u);
  const δe = (0.75 * J2 * Math.pow(EarthRadius / satellite.initialSemiMajorAxis, 2)) * Math.sqrt(1 - e*e) * Math.sin(2 * u);
  
  satellite.params.semiMajorAxis = (satellite.initialSemiMajorAxis + δa) / EarthRadius;
  satellite.params.eccentricity = Math.min(0.99, Math.max(0, satellite.initialEccentricity + δe));
  satellite.currentTrueAnomaly = ν;
}