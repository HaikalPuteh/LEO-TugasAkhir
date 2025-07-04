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
};
