// sunCalculations.js

/**
 * Helper function to convert degrees to radians.
 * @param {number} degrees - Angle in degrees.
 * @returns {number} Angle in radians.
 */
export function degToRad(degrees) {
    return degrees * (Math.PI / 180);
}

/**
 * Converts radians to degrees.
 * @param {number} radians - Angle in radians.
 * @returns {number} Angle in degrees.
 */
export function radToDeg(radians) {
    return radians * (180 / Math.PI);
}

/**
 * Converts a JavaScript Date object (UTC) to Julian Day.
 * Julian Day 2451545.0 is J2000 epoch (January 1, 2000, 12:00 TT).
 * @param {Date} date - UTC Date object.
 * @returns {number} Julian Day.
 */
function toJulian(date) {
    // ValueOf returns milliseconds since Jan 1, 1970 UTC
    // 86400000 milliseconds in a day
    // 2440587.5 is Julian Day for Jan 1, 1970 00:00 UTC
    return date.valueOf() / 86400000 - 0.5 + 2440587.5;
}

/**
 * Calculates Greenwich Mean Sidereal Time (GMST) in radians.
 * This is used to relate celestial coordinates to Earth-fixed longitudes.
 *
 * @param {Date} date - UTC Date object.
 * @returns {number} GMST in radians.
 */
export function getGMST(date) {
    const d = toJulian(date) - 2451545.0; // Days since J2000 epoch
    // GMST in hours
    const gmst = (18.697374558 + 24.06570982441908 * d) % 24; 
    // Convert to degrees, then radians (lw is 0 for Greenwich)
    return degToRad((gmst * 15) % 360); 
}

//-------------------------------------------------3D-----------------------------------------------------
/**
 * Calculates the sun’s coordinates (Right Ascension and Declination) in radians
 * using formulas that incorporate the "equation of center" for higher accuracy,
 * based on methods found in astronomical references like Wikipedia's "Position of the Sun".
 *
 * @param {Date} date - UTC Date object representing the current time.
 * @returns {{ra: number, dec: number}} Object with Right Ascension (ra) and Declination (dec) in radians (J2000 ECI).
 */
export function getSunCoords(date) {
    const d = toJulian(date) - 2451545.0; // Days since J2000 epoch

    // Mean anomaly of the Sun (M) - in degrees
    const M_deg = (357.5291 + 0.98560028 * d) % 360;
    const M_rad = degToRad(M_deg); // Mean Anomaly in radians

    // Mean longitude of the Sun (L0) - in degrees
    // This L0 includes initial offset and daily motion.
    const L0_deg = (280.460 + 0.98564736 * d) % 360;
    
    // Ecliptic longitude (lambda) - in degrees, including equation of center
    // This term accounts for the elliptical nature of Earth's orbit.
    const lambda_deg = L0_deg + 1.915 * Math.sin(M_rad) + 0.020 * Math.sin(2 * M_rad);
    const lambda_rad = degToRad(lambda_deg); // True Ecliptic Longitude in radians

    // Obliquity of the ecliptic (epsilon) - Earth's axial tilt in degrees
    const epsilon_deg = 23.439 - 0.00000036 * d;
    const epsilon_rad = degToRad(epsilon_deg);

    // Right Ascension (RA) - in radians
    // Conversion from ecliptic coordinates (lambda, epsilon) to equatorial coordinates (RA, Dec)
    const ra = Math.atan2(Math.cos(epsilon_rad) * Math.sin(lambda_rad), Math.cos(lambda_rad));
    
    // Declination (Dec) - in radians
    const dec = Math.asin(Math.sin(epsilon_rad) * Math.sin(lambda_rad));

    // Normalize RA to be between 0 and 2*PI radians
    return {
        ra: (ra + 2 * Math.PI) % (2 * Math.PI),
        dec: dec
    };
}


/**
 * Calculates the subsolar point (latitude and longitude) for a given UTC Date object.
 * The subsolar point is where the sun's rays are directly overhead.
 *
 * @param {Date} utcDate - The Date object representing the UTC time.
 * @returns {{lat: number, lon: number, latitude: number, longitude: number}} An object containing
 * latitude and longitude in degrees (lat, lon) and radians (latitude, longitude).
 */

//-------------------------------------------------2D-------------------------------------------------------
export function getSubsolarPoint(utcDate) {
    
    // Get the sun's coordinates (Right Ascension and Declination)
    // using the accurate getSunCoords function
    const sunCoords = getSunCoords(utcDate); // Use the accurate getSunCoords
    const GST = getGMST(utcDate); // Greenwich Sidereal Time in radians

    // Subsolar longitude = GST - Right Ascension
    let lonRad = (GST - sunCoords.ra) % (2 * Math.PI);
    // Normalize longitude to [-π, π]
    if (lonRad > Math.PI) lonRad -= 2 * Math.PI;
    else if (lonRad < -Math.PI) lonRad += 2 * Math.PI;

    const latRad = sunCoords.dec; // Subsolar latitude is the declination

    return {
        lat: latRad * (180 / Math.PI),   // Latitude in degrees
        lon: lonRad * (180 / Math.PI),   // Longitude in degrees
        latitude: latRad,              // Latitude in radians
        longitude: lonRad              // Longitude in radians
    };
}
