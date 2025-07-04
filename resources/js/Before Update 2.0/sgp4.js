
// sgp4.js

import * as satellite from 'satellite.js';

// Constants for coordinate scaling and time calculations
const EARTH_RADIUS_KM = 6378.137; // Earth radius in kilometers
const SCENE_EARTH_RADIUS = 1; // Scene scaling factor (adjust as needed)

/**
 * Parses TLE lines and initializes a satellite record for SGP4 propagation using satellite.js.
 * Extracts the epoch from the TLE and sets it as the global reference time.
 *
 * @param {string} tleLine1 - First line of the Two-Line Element set.
 * @param {string} tleLine2 - Second line of the Two-Line Element set.
 * @returns {object} A satellite record object compatible with satellite.js propagation.
 * @throws {Error} If TLE parsing fails.
 */
export function parseTle(tleLine1, tleLine2) {
    try {
        // Initialize satellite record using satellite.js
        const satrec = satellite.twoline2satrec(tleLine1, tleLine2);

        // Extract epoch from TLE (Line 1, field 3, e.g., "25177.12345678")
        const epochYear = parseInt(tleLine1.substring(18, 20)); // e.g., "25" for 2025
        const epochDay = parseFloat(tleLine1.substring(20, 32)); // e.g., "177.12345678"
        const fullYear = epochYear < 57 ? 2000 + epochYear : 1900 + epochYear; // TLE epoch year handling
        const epochDate = new Date(Date.UTC(fullYear, 0, 1));
        epochDate.setUTCDate(epochDate.getUTCDate() + Math.floor(epochDay) - 1);
        epochDate.setUTCHours(0, 0, 0, (epochDay % 1) * 24 * 60 * 60 * 1000);

        // Set global epoch for application consistency
        window.currentEpochUTC = epochDate.getTime();

        // Return satellite record with additional metadata
        return {
            satrec,
            tleLine1,
            tleLine2,
            epoch: epochDate.toISOString(),
            epochTimestamp: epochDate.getTime()
        };
    } catch (error) {
        console.error('Error parsing TLE:', error);
        throw new Error(`Invalid TLE data: ${error.message}`);
    }
}

/**
 * Propagates a satellite's position and velocity using the SGP4 algorithm for a given UTC time.
 *
 * @param {object} satelliteData - The satellite record from parseTle, containing satrec and epoch.
 * @param {Date} utcDate - The UTC Date object for which to calculate the position.
 * @returns {object} An object containing:
 *   - position: THREE.Vector3 (ECI position in Three.js units, scaled by SCENE_EARTH_RADIUS)
 *   - velocity: THREE.Vector3 (ECI velocity in Three.js units)
 * Returns null if propagation fails.
 */
export function propagateSGP4(satelliteData, utcDate) {
    try {
        const { satrec } = satelliteData;

        // Propagate using satellite.js
        const positionAndVelocity = satellite.propagate(satrec, utcDate);

        // Check for propagation errors
        if (positionAndVelocity.position === false || positionAndVelocity.velocity === false) {
            throw new Error('SGP4 propagation failed');
        }

        // Extract ECI position and velocity (in kilometers and km/s)
        const positionEci = positionAndVelocity.position; // {x, y, z} in km
        const velocityEci = positionAndVelocity.velocity; // {x, y, z} in km/s

        // Convert to Three.js coordinates (X = ECI X, Y = ECI Z, Z = ECI Y)
        // Scale position by SCENE_EARTH_RADIUS / EARTH_RADIUS_KM
        const scaleFactor = SCENE_EARTH_RADIUS / EARTH_RADIUS_KM;
        const threeJsX = positionEci.x * scaleFactor;
        const threeJsY = positionEci.z * scaleFactor;
        const threeJsZ = positionEci.y * scaleFactor;

        // Scale velocity similarly (optional, depending on application needs)
        const velocityX = velocityEci.x * scaleFactor;
        const velocityY = velocityEci.z * scaleFactor;
        const velocityZ = velocityEci.y * scaleFactor;

        return {
            position: new THREE.Vector3(threeJsX, threeJsY, threeJsZ),
            velocity: new THREE.Vector3(velocityX, velocityY, velocityZ)
        };
    } catch (error) {
        console.error('SGP4 propagation failed:', error);
        return null;
    }
}
