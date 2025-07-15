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
    const cosBeta = Math.cos(Math.PI/2 + elevationAngleRad);
    const altitudeKm = Math.sqrt(earthRadiusKm*earthRadiusKm + maxDistanceKm*maxDistanceKm - 2*earthRadiusKm*maxDistanceKm*cosBeta) - earthRadiusKm;
    
    // Validate altitude is reasonable (300km to 2000km for LEO)
    const finalAltitude = Math.max(300, Math.min(altitudeKm, 2000));
    const semiMajorAxisKm = earthRadiusKm + finalAltitude;

    // --- STEP 5: Calculate Coverage Parameters ---
    const maxCentralAngle = Math.acos(earthRadiusKm * Math.cos(elevationAngleRad) / 
                                    (earthRadiusKm + finalAltitude)) - elevationAngleRad;
    
    const coverageRadiusKm = earthRadiusKm * maxCentralAngle;
    const coverageAreaKm2 = Math.PI * Math.pow(coverageRadiusKm, 2);

    // --- STEP 6: Calculate Required Beamwidth ---
    const beamwidthRad = 2 * Math.atan(coverageRadiusKm / finalAltitude);
    const beamwidthDegrees = beamwidthRad * 180 / Math.PI;

    // --- STEP 7: CORRECTED Satellite Count Calculation ---
    let requiredSatellites;
    const coverageEfficiency = 0.65; // Using the user's specified efficiency
    const adjustedCoverageArea = coverageAreaKm2 * coverageEfficiency;

    // *** START OF FIX ***
    // If user provides a specific target area, prioritize that calculation.
    // Otherwise, default to a global coverage calculation.
    if (inputValues.targetArea && inputValues.targetArea > 0) {
        // Calculation for REGIONAL coverage
        requiredSatellites = Math.ceil(inputValues.targetArea / adjustedCoverageArea);
    } else {
        // Calculation for GLOBAL coverage
        const earthSurfaceArea = 4 * Math.PI * Math.pow(earthRadiusKm, 2);
        requiredSatellites = Math.ceil(earthSurfaceArea / adjustedCoverageArea);
    }
    // *** END OF FIX ***

    // Ensure at least the minimum number of satellites in view is met
    requiredSatellites = Math.max(requiredSatellites, inputValues.minSatellitesInView || 1);

    // --- STEP 8: Walker Constellation Parameters ---
    let numPlanes, satsPerPlane;
    
    if (requiredSatellites <= 12) {
        numPlanes = Math.ceil(Math.sqrt(requiredSatellites));
        satsPerPlane = Math.ceil(requiredSatellites / numPlanes);
    } else if (requiredSatellites <= 100) {
        numPlanes = Math.ceil(requiredSatellites / 8);
        satsPerPlane = Math.ceil(requiredSatellites / numPlanes);
    } else {
        numPlanes = Math.min(Math.ceil(requiredSatellites / 12), 24);
        satsPerPlane = Math.ceil(requiredSatellites / numPlanes);
    }
    
    const totalSatellites = numPlanes * satsPerPlane;

    // --- STEP 9: Orbital Dynamics ---
    const orbitalPeriodSeconds = 2 * Math.PI * Math.sqrt(Math.pow(semiMajorAxisKm, 3) / MU_EARTH);
    const orbitalVelocity = Math.sqrt(MU_EARTH / semiMajorAxisKm);
    const revisitTimeMinutes = (orbitalPeriodSeconds / 60) / numPlanes;
    
    // --- STEP 10: Link Budget Verification ---
    const actualFspl = 20 * Math.log10(maxDistanceM) + 20 * Math.log10(FREQ_HZ) + fsplConstant;
    const actualReceivedPower = inputValues.transmitPower + inputValues.txAntennaGain + 
    inputValues.rxAntennaGain - actualFspl - inputValues.atmosphericLoss;
    const actualSnr = actualReceivedPower - noisePowerDbm;
    const shannonCapacity = BANDWIDTH_HZ * Math.log2(1 + Math.pow(10, actualSnr / 10));

    return {
        receivedPower: actualReceivedPower,
        snr: actualSnr,
        shannonCapacity: shannonCapacity,
        linkMargin: actualSnr - inputValues.minimumSNR,
        altitude: finalAltitude,
        inclination: inputValues.orbitInclination,
        beamwidth: beamwidthDegrees,
        numSatellitesNeeded: totalSatellites,
        numOrbitalPlanes: numPlanes,
        satsPerPlane: satsPerPlane,
        revisitTime: revisitTimeMinutes,
        ...inputValues
    };
}
