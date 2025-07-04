// parametersimulation.js

// Constants (common to both simulations)
const DEG2RAD = Math.PI / 180;
const EarthRadius = 6378.137; // km (WGS84 equatorial radius for more precise calculations)
const EarthMass = 5.972e24; // kg
const GravitationalConstant = 6.67430e-11; // m³/kg/s²
const MU_EARTH = 398600.4418; // km^3/s^2 (Standard Gravitational Parameter for Earth)
const J2 = 1.08263e-3; // J2 perturbation constant for Earth (dimensionless)
const EARTH_SIDEREAL_ROTATION_PERIOD_SECONDS = 86164.090530833; // seconds (23h 56m 4.09053s)
const EARTH_ANGULAR_VELOCITY_RAD_PER_SEC = 2 * Math.PI / EARTH_SIDEREAL_ROTATION_PERIOD_SECONDS; // Earth's angular velocity in radians per second
const SCENE_EARTH_RADIUS = 1; // Earth radius in scene units (e.g., 1 unit = 6371 km if EarthRadius is 6371 km)

let _currentOrbitParams = null;
function setCurrentOrbitParameters(params) {
    _currentOrbitParams = params;
}

function getCurrentOrbitParameters() {
    return _currentOrbitParams;
}

// --- Orbit input parsing and conversion ---
function orbitparameters() {
    const getValidatedInput = (id, min, max) => {
        const element = document.getElementById(id);
        if (!element) {
            throw new Error(`Input element with ID '${id}' not found.`);
        }
        const value = parseFloat(element.value);
        if (isNaN(value)) throw new Error(`Invalid ${id} value. Must be a number.`);
        if (value < min || value > max) throw new Error(`${id} must be between ${min} and ${max}.`);
        return value;
    };

    try {
        // Retrieve and validate input values from the HTML DOM
        const altitude = getValidatedInput("altitudeInput", 100, 35786); // Altitude above Earth's surface in km
        const inclination = getValidatedInput("inclinationInput", 0, 180); // Orbital inclination in degrees
        const eccentricity = getValidatedInput("eccentricityInput", 0, 0.99); // Orbital eccentricity (0 for circular, <1 for elliptical)
        const raan = getValidatedInput("raanInput", 0, 360); // Right Ascension of the Ascending Node in degrees
        const argPerigee = getValidatedInput("argumentOfPerigeeInput", 0, 360); // Argument of Perigee in degrees
        const trueAnomaly = getValidatedInput("trueAnomalyInput", 0, 360); // True Anomaly in degrees (satellite's current position in orbit)
        const beamWidth = getValidatedInput("beamWidthInput", 0, 179); // Beamwidth of the satellite's coverage cone in degrees

        // Calculate derived orbital parameters
        const a_km = EarthRadius + altitude; // Semi-major axis in kilometers (distance from Earth's center to orbit's average radius)
        const a_m = a_km * 1000; // Semi-major axis in meters
        const mu_m3_s2 = GravitationalConstant * EarthMass; // Earth's standard gravitational parameter (G * M_earth) in m³/s²

        // Mean motion (average angular speed) and orbital period based on Kepler's Third Law
        const orbitalAngularVelocity = Math.sqrt(mu_m3_s2 / Math.pow(a_m, 3)); // radians per second
        const orbitalPeriod = 2 * Math.PI * Math.sqrt(Math.pow(a_m, 3) / mu_m3_s2); // seconds

        // Convert input angles from degrees to radians for mathematical calculations
        const raanRad = raan * DEG2RAD;
        const argPerigeeRad = argPerigee * DEG2RAD;
        const inclinationRad = inclination * DEG2RAD;
        const trueAnomalyRad = trueAnomaly * DEG2RAD;

        // Bundle all parameters into a single object
        const params = {
            semiMajorAxis: a_km,            // km
            inclinationRad,                // radians
            eccentricity,                  // dimensionless
            raanRad,                       // radians
            argPerigeeRad,                 // radians
            trueAnomalyRad,                // radians
            beamWidthDeg: beamWidth,       // degrees
            orbitalPeriod,                 // seconds
            orbitalAngularVelocity,        // rad/s
            gravitationalParameter: mu_m3_s2 // m³/s²
        };

        // Store these parameters in the shared state, primarily for constellation generation
        setCurrentOrbitParameters(params);

        return params; // Return the calculated parameters
    } catch (error) {
        console.error("Orbit parameter error:", error);
        throw error; // Re-throw the error so calling functions can handle it
    }
}

// Export functions and constants for other modules to import
export {
    DEG2RAD,
    EarthRadius,
    EarthMass,
    GravitationalConstant,
    MU_EARTH, // Export MU_EARTH
    J2,
    EARTH_SIDEREAL_ROTATION_PERIOD_SECONDS,
    EARTH_ANGULAR_VELOCITY_RAD_PER_SEC,
    SCENE_EARTH_RADIUS,
    getCurrentOrbitParameters,
    orbitparameters
};
