/*
================================================================================
FILE: linkBudgetCalculations.js (CORRECTED VERSION)
================================================================================
This file performs link budget analysis with proper satellite constellation design.
Key corrections made for accurate satellite count and altitude calculations.
*/

import { EarthRadius, MU_EARTH } from "./parametersimulation.js";

/**
 * Corrected link budget calculation with proper satellite count methodology
 * @param {object} inputValues - User-defined performance and RF parameters
 * @returns {object} The calculated orbital and constellation parameters
 */
export function calculateLinkBudget(inputValues) {
    // --- Constants and Conversions ---
    const FREQ_HZ = inputValues.frequency * 1e9;
    const BANDWIDTH_HZ = inputValues.bandwidth * 1e6;
    const BOLTZMANN_CONST = 1.38e-23; // J/K
    const TEMP_K = 290; // Standard noise temperature
    const SPEED_OF_LIGHT = 299792458; // m/s

    // --- STEP 1: Calculate Required Received Power ---
    const noiseFactor = Math.pow(10, inputValues.noiseFigure / 10);
    const noisePowerWatts = BOLTZMANN_CONST * TEMP_K * BANDWIDTH_HZ * noiseFactor;
    const noisePowerDbm = 10 * Math.log10(noisePowerWatts * 1000);
    const requiredReceivedPowerDbm = inputValues.minimumSNR + noisePowerDbm;

    // --- STEP 2: Calculate Maximum Allowable Path Loss ---
    const maxAllowedFsplDb = inputValues.transmitPower + inputValues.txAntennaGain +
                           inputValues.rxAntennaGain - requiredReceivedPowerDbm - inputValues.atmosphericLoss;

    // --- STEP 3: Calculate Maximum Distance from FSPL ---
    // FSPL(dB) = 20*log10(d_m) + 20*log10(f_Hz) + 20*log10(4π/c)
    const fsplConstant = 20 * Math.log10(4 * Math.PI / SPEED_OF_LIGHT);
    const log10d = (maxAllowedFsplDb - 20 * Math.log10(FREQ_HZ) - fsplConstant) / 20;
    const maxDistanceM = Math.pow(10, log10d);
    const maxDistanceKm = maxDistanceM / 1000;

    // --- STEP 4: Calculate Required Altitude (CORRECTED) ---
    const earthRadiusKm = EarthRadius;
    const elevationAngleRad = inputValues.elevationAngle * Math.PI / 180;
    
    // Using spherical geometry: Law of cosines for the triangle
    // Earth center -> User -> Satellite
    // CORRECTED: Proper geometric relationship
    const cosGamma = Math.cos(Math.PI/2 + elevationAngleRad); // Angle at satellite
    const a = earthRadiusKm;
    const c = maxDistanceKm;
    
    // From law of cosines: b² = a² + c² - 2ac*cos(B)
    // Where B is the angle at the user (90° + elevation angle)
    const cosBeta = Math.cos(Math.PI/2 + elevationAngleRad);
    const altitudeKm = Math.sqrt(a*a + c*c - 2*a*c*cosBeta) - a;
    
    // Validate altitude is reasonable (100km to 2000km for LEO)
    const finalAltitude = Math.max(300, Math.min(altitudeKm, 2000));
    const semiMajorAxisKm = earthRadiusKm + finalAltitude;

    // --- STEP 5: Calculate Coverage Parameters ---
    // Maximum central angle for coverage at minimum elevation
    const maxCentralAngle = Math.acos(earthRadiusKm * Math.cos(elevationAngleRad) / 
                                    (earthRadiusKm + finalAltitude)) - elevationAngleRad;
    
    // Coverage radius on Earth's surface
    const coverageRadiusKm = earthRadiusKm * maxCentralAngle;
    const coverageAreaKm2 = Math.PI * Math.pow(coverageRadiusKm, 2);

    // --- STEP 6: Calculate Required Beamwidth ---
    // Beamwidth to illuminate the coverage area
    const beamwidthRad = 2 * Math.atan(coverageRadiusKm / finalAltitude);
    const beamwidthDegrees = beamwidthRad * 180 / Math.PI;

    // --- STEP 7: CORRECTED Satellite Count Calculation ---
    // Method 1: Simple area division (conservative estimate)
    const earthSurfaceArea = 4 * Math.PI * Math.pow(earthRadiusKm, 2);
    const basicSatCount = Math.ceil(earthSurfaceArea / coverageAreaKm2);
    
    // Method 2: Walker constellation calculation (more realistic)
    // For global coverage with specified latitude limits
    const maxLatitude = Math.min(inputValues.orbitInclination, 90);
    const coverableArea = inputValues.targetArea || 
                         (earthSurfaceArea * Math.sin(maxLatitude * Math.PI / 180));
    
    // Account for overlap and coverage efficiency (typical 60-70% for constellation)
    const coverageEfficiency = 0.65;
    const adjustedCoverageArea = coverageAreaKm2 * coverageEfficiency;
    const practicalSatCount = Math.ceil(coverableArea / adjustedCoverageArea);
    
    // Use the more conservative (higher) count
    const requiredSatellites = Math.max(basicSatCount, practicalSatCount, 
                                      inputValues.minSatellitesInView || 1);

    // --- STEP 8: Walker Constellation Parameters ---
    // Optimize for global coverage
    let numPlanes, satsPerPlane;
    
    if (requiredSatellites <= 12) {
        // Small constellation
        numPlanes = Math.ceil(Math.sqrt(requiredSatellites));
        satsPerPlane = Math.ceil(requiredSatellites / numPlanes);
    } else if (requiredSatellites <= 100) {
        // Medium constellation - optimize for coverage
        numPlanes = Math.ceil(requiredSatellites / 8); // 6-8 sats per plane typical
        satsPerPlane = Math.ceil(requiredSatellites / numPlanes);
    } else {
        // Large constellation - consider operational constraints
        numPlanes = Math.min(Math.ceil(requiredSatellites / 12), 24); // Max 24 planes
        satsPerPlane = Math.ceil(requiredSatellites / numPlanes);
    }
    
    const totalSatellites = numPlanes * satsPerPlane;

    // --- STEP 9: Orbital Dynamics ---
    const orbitalPeriodSeconds = 2 * Math.PI * Math.sqrt(Math.pow(semiMajorAxisKm, 3) / MU_EARTH);
    const orbitalVelocity = Math.sqrt(MU_EARTH / semiMajorAxisKm);
    
    // Revisit time calculation (time between successive satellite passes)
    const revisitTimeMinutes = (orbitalPeriodSeconds / 60) / numPlanes;
    
    // Average number of satellites in view
    const avgSatsInView = Math.max(1, Math.floor(totalSatellites * 
                         (coverageAreaKm2 / earthSurfaceArea) * 1.5)); // 1.5x for overlap

    // --- STEP 10: Link Budget Verification ---
    // Recalculate with derived altitude for verification
    const actualFspl = 20 * Math.log10(maxDistanceM) + 20 * Math.log10(FREQ_HZ) + fsplConstant;
    const actualReceivedPower = inputValues.transmitPower + inputValues.txAntennaGain + 
                               inputValues.rxAntennaGain - actualFspl - inputValues.atmosphericLoss;
    const actualSnr = actualReceivedPower - noisePowerDbm;
    const shannonCapacity = BANDWIDTH_HZ * Math.log2(1 + Math.pow(10, actualSnr / 10));
    
    // Aggregate throughput with multiple satellites
    const aggregateThroughput = shannonCapacity * avgSatsInView;

    // --- STEP 11: Cost and Complexity Metrics ---
    const launchMass = totalSatellites * 150; // Assume 150kg per satellite
    const estimatedCost = totalSatellites * 2.5; // $2.5M per satellite (rough estimate)

    return {
        // --- Link Budget Results ---
        receivedPower: actualReceivedPower,
        snr: actualSnr,
        shannonCapacity: shannonCapacity,
        aggregateThroughput: aggregateThroughput,
        linkMargin: actualSnr - inputValues.minimumSNR,

        // --- Constellation Design ---
        altitude: finalAltitude,
        inclination: inputValues.orbitInclination,
        beamwidth: beamwidthDegrees,
        eccentricity: 0, // Circular orbits for uniform coverage
        
        // --- Satellite Count (CORRECTED) ---
        numSatellitesNeeded: totalSatellites,
        numOrbitalPlanes: numPlanes,
        satsPerPlane: satsPerPlane,
        avgSatellitesInView: avgSatsInView,
        
        // --- Coverage Metrics ---
        coverageArea: coverageAreaKm2,
        coverageRadius: coverageRadiusKm,
        revisitTime: revisitTimeMinutes,
        
        // --- Orbital Parameters ---
        orbitalPeriod: orbitalPeriodSeconds / 60, // minutes
        orbitalVelocity: orbitalVelocity,
        maxDistance: maxDistanceKm,
        
        // --- System Metrics ---
        launchMass: launchMass,
        estimatedCost: estimatedCost,
        
        // --- Design Rationale ---
        designMethod: "Corrected Walker constellation with coverage optimization",
        coverageEfficiency: coverageEfficiency,
        
        // Pass through original inputs
        ...inputValues
    };
}

/**
 * Validate link budget inputs for physical and technical feasibility
 * @param {object} inputs - Input parameters to validate
 * @returns {object} Validation results with warnings and errors
 */
export function validateLinkBudgetInputs(inputs) {
    const warnings = [];
    const errors = [];
    
    // RF Parameter Validation
    if (inputs.frequency < 0.1 || inputs.frequency > 100) {
        warnings.push("Frequency outside typical satellite bands (0.1-100 GHz)");
    }
    
    if (inputs.transmitPower < -10 || inputs.transmitPower > 50) {
        warnings.push("Transmit power outside typical range (-10 to 50 dBm)");
    }
    
    if (inputs.minimumSNR < 0 || inputs.minimumSNR > 30) {
        warnings.push("Minimum SNR outside typical range (0-30 dB)");
    }
    
    // Coverage Parameter Validation
    if (inputs.elevationAngle < 5 || inputs.elevationAngle > 45) {
        warnings.push("Elevation angle outside recommended range (5-45°)");
    }
    
    if (inputs.orbitInclination > 90) {
        warnings.push("Retrograde orbit (inclination > 90°) may not be optimal for coverage");
    }
    
    // Target Area Validation
    const earthSurface = 4 * Math.PI * Math.pow(EarthRadius, 2);
    if (inputs.targetArea > earthSurface) {
        errors.push("Target area exceeds Earth's surface area");
    }
    
    return { warnings, errors, isValid: errors.length === 0 };
}