//orbitalCalculation.js
import * as THREE from "three";
import {
    J2,
    MU_EARTH,
    EarthRadius,
} from "./parametersimulation.js";


/**
 * Solves Kepler's Equation (M = E - e*sin(E)) for the Eccentric Anomaly (E)
 * using the Newton-Raphson iterative method.
 * @param {number} M - Mean Anomaly in radians.
 * @param {number} e - Eccentricity (dimensionless).
 * @param {number} [epsilon=1e-6] - Desired accuracy for E.
 * @param {number} [maxIterations=10] - Maximum number of iterations to prevent infinite loops.
 * @returns {number} The Eccentric Anomaly in radians.
 */
export function solveKepler(M, e, epsilon = 1e-6, maxIterations = 10) {
    let E = M; // Initial guess for E is M
    for (let i = 0; i < maxIterations; i++) {
        const dE = (M - E + e * Math.sin(E)) / (1 - e * Math.cos(E));
        E += dE;
        if (Math.abs(dE) < epsilon) {
            return E;
        }
    }
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
export function calculateSatellitePositionECI(params, currentMeanAnomaly, currentRAAN, sceneEarthRadius = 1) { // Changed default name to avoid confusion with EarthRadius
    // Convert semiMajorAxis from scene units (relative to SCENE_EARTH_RADIUS) to actual kilometers
    const a_km_actual = params.semiMajorAxis * EarthRadius;
    const e = params.eccentricity;
    const i_rad = -params.inclinationRad; // Inclination is negative to match Three.js Y-up coordinate system
    const argPerigee_rad = params.argPerigeeRad; // This is omega (ω)

    // 1. Solve Kepler's Equation for Eccentric Anomaly (E)
    const E = solveKepler(currentMeanAnomaly, e);

    // 2. Convert Eccentric Anomaly to True Anomaly (nu)
    const nu = -E_to_TrueAnomaly(E, e); // True Anomaly is negative to match Three.js Y-up coordinate system

    // 3. Calculate radius (distance from central body) in km
    const r_km = a_km_actual * (1 - e * e) / (1 + e * Math.cos(nu));

    // 4. Calculate position in the perifocal frame (orbital plane, X-axis along periapsis)
    // In perifocal frame, Z is 0. X is along periapsis, Y is 90 deg from X in plane.
    const x_perifocal = r_km * Math.cos(nu);
    const y_perifocal = r_km * Math.sin(nu);
    const z_perifocal = 0; // Perifocal plane is XY

    // Create a Three.js Vector3 for the position in the perifocal frame
    const position_km = new THREE.Vector3(x_perifocal, y_perifocal, z_perifocal);

    // Apply orbital transformations using a transformation matrix:
    // Rotations (applied in order: ArgPerigee, Inclination, RAAN)
    // Order of matrix multiplication: RAAN * Inclination * ArgPerigee * vector
    // This order is crucial for accurate ECI conversion.
    // Three.js rotation matrices are right-handed.

    // Rotation around Z for Argument of Perigee
    const rotationMatrixArgPerigee = new THREE.Matrix4().makeRotationZ(argPerigee_rad);
    // Rotation around X for Inclination (line of nodes is along X after RAAN rotation)
    const rotationMatrixInclination = new THREE.Matrix4().makeRotationX(i_rad);
    // Rotation around Z for RAAN (Right Ascension of Ascending Node)
    const rotationMatrixRAAN = new THREE.Matrix4().makeRotationZ(currentRAAN);

    const totalRotationMatrix = new THREE.Matrix4();
    totalRotationMatrix.multiply(rotationMatrixRAAN);       // Apply RAAN last (around Z-axis of ECI)
    totalRotationMatrix.multiply(rotationMatrixInclination); // Apply Inclination (around X-axis of line of nodes)
    totalRotationMatrix.multiply(rotationMatrixArgPerigee); // Apply Argument of Perigee first (around orbital plane's Z-axis)

    // Apply the combined transformation to the perifocal position vector, result is in ECI km
    position_km.applyMatrix4(totalRotationMatrix);

    // Now, map the ECI coordinates (X_ECI, Y_ECI, Z_ECI) to Three.js scene coordinates (X_3JS, Y_3JS, Z_3JS)
    // Desired mapping (consistent with Three.js Y-up, Right-Handed, and accommodating 180-deg texture offset):
    // Three.js X = ECI X
    // Three.js Y = ECI Z (North Pole, up direction)
    // Three.js Z = ECI Y (completes right-handed system)
    const scenePosition = new THREE.Vector3(
        position_km.x / EarthRadius * sceneEarthRadius,    // ECI X maps to Three.js X
        position_km.z / EarthRadius * sceneEarthRadius,    // ECI Z maps to Three.js Y (up)
        position_km.y / EarthRadius * sceneEarthRadius     // ECI Y maps to Three.js Z (depth)
    );

    return { x: scenePosition.x, y: scenePosition.y, z: scenePosition.z };
}

/**
 * Calculates additional derived orbital parameters.
 * @param {number} altitude - Altitude in km.
 * @param {number} eccentricity - Eccentricity (dimensionless).
 * @returns {object} Object with orbitalPeriod (seconds), orbitalVelocity (km/s), semiMajorAxis (km).
 */
export function calculateDerivedOrbitalParameters(altitude, eccentricity) {
    // semiMajorAxis here is in km, as altitude is in km and EarthRadius is in km.
    const semiMajorAxis_km = EarthRadius + altitude;
    const orbitalPeriodSeconds = 2 * Math.PI * Math.sqrt(Math.pow(semiMajorAxis_km, 3) / MU_EARTH);
    const orbitalVelocity = Math.sqrt(MU_EARTH / semiMajorAxis_km); // Velocity for circular orbit, or average for elliptical.

    return {
        orbitalPeriod: orbitalPeriodSeconds,
        orbitalVelocity: orbitalVelocity,
        semiMajorAxis: semiMajorAxis_km // Include semi-major axis for convenience
    };
}



/**
 * Calculates a satellite's Mean Anomaly (M) and RAAN (Right Ascension of the Ascending Node)
 * based on the initial orbital elements and total elapsed simulated time,
 * accounting for J2 perturbation.
 *
 * This function updates the Mean Anomaly and RAAN in place within the provided satellite object.
 * It now takes `totalSimulatedTime` directly, making the calculations absolute from T=0.
 *
 * @param {object} satellite - The satellite object (must have params.semiMajorAxis, params.eccentricity, params.inclinationRad, initialMeanAnomaly, initialRAAN properties). Note: params.semiMajorAxis is in scene units.
 * @param {number} totalSimulatedTime - Total time elapsed in simulation in seconds.
 */
export function updateOrbitalElements(satellite, totalSimulatedTime) {
    // Convert semiMajorAxis from scene units (relative to SCENE_EARTH_RADIUS) to actual kilometers
    // satellite.params.semiMajorAxis is a dimensionless scale factor (e.g., 1 + altitude_ratio_to_earth_radius_scene_units)
    // To get it in KM, multiply by EarthRadius (which is in KM).
    const a_km_actual = satellite.params.semiMajorAxis * EarthRadius; // Correct semi-major axis in KM

    const e = satellite.params.eccentricity;
    const i_rad = satellite.params.inclinationRad;

    // Calculate Mean Motion (n) in radians per second
    // Use the actual semi-major axis in kilometers
    const n = Math.sqrt(MU_EARTH / Math.pow(a_km_actual, 3));

    // Calculate current Mean Anomaly (M) from its initial value and total simulated time
    let newMeanAnomaly = satellite.initialMeanAnomaly + n * totalSimulatedTime;
    newMeanAnomaly %= (2 * Math.PI);
    if (newMeanAnomaly < 0) newMeanAnomaly += 2 * Math.PI;
    satellite.currentMeanAnomaly = newMeanAnomaly;

    // Apply J2 perturbation for RAAN drift
    // dRAAN/dt = - (3/2) * J2 * (R_earth/a)^2 * n * cos(i)
    // Use the actual semi-major axis in kilometers (a_km_actual)
    const dRAAN_dt_J2 = - (3 / 2) * J2 * Math.pow(EarthRadius / a_km_actual, 2) * n * Math.cos(i_rad);

    // Calculate current RAAN from its initial value and total simulated time
    // satellite.initialRAAN should be stored when the satellite is created.
    let newRAAN = satellite.initialRAAN + dRAAN_dt_J2 * totalSimulatedTime;
    newRAAN %= (2 * Math.PI);
    if (newRAAN < 0) newRAAN += 2 * Math.PI;
    satellite.currentRAAN = newRAAN;
}
