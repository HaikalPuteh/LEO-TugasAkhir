/*
================================================================================
FILE: linkBudgetCalculations.js (Complete & Revised)
================================================================================
This file performs a "goal-seeking" analysis. It takes performance
requirements (like Minimum SNR) and calculates the necessary altitude,
beamwidth, and constellation parameters to meet those requirements.
*/

import { EarthRadius, MU_EARTH } from "./parametersimulation.js";

/**
 * Designs an orbit and constellation based on link performance requirements.
 * @param {object} inputValues - User-defined performance and RF parameters.
 * @returns {object} The calculated orbital and constellation parameters.
 */
export function calculateLinkBudget(inputValues) {
    // --- Constants and Initial Conversions ---
    const FREQ_HZ = inputValues.frequency * 1e9;
    const BANDWIDTH_HZ = inputValues.bandwidth * 1e6;
    const BOLTZMANN_CONST = 1.38e-23; // J/K
    const TEMP_K = 290; // Standard noise temperature
    const SPEED_OF_LIGHT = 299792458; // m/s

    // --- GOAL-SEEKING LOGIC ---

    // 1. Calculate the required Received Power (Pr) to meet the minimum SNR.
    // SNR = Pr - Pn  =>  Pr = SNR + Pn
    const noiseFactor = Math.pow(10, inputValues.noiseFigure / 10);
    const noisePowerWatts = BOLTZMANN_CONST * TEMP_K * BANDWIDTH_HZ * noiseFactor;
    const noisePowerDbm = 10 * Math.log10(noisePowerWatts * 1000);
    const requiredReceivedPowerDbm = inputValues.minimumSNR + noisePowerDbm;

    // 2. Calculate the maximum allowable Free Space Path Loss (FSPL).
    // Pr = Pt + Gt + Gr - FSPL - L_atmospheric  =>  FSPL = Pt + Gt + Gr - Pr - L_atmospheric
    const maxAllowedFsplDb = inputValues.transmitPower + inputValues.txAntennaGain +
                           inputValues.rxAntennaGain - requiredReceivedPowerDbm - inputValues.atmosphericLoss;

    // 3. From FSPL, calculate the maximum slant range (distance) in meters.
    // FSPL = 20*log10(d) + 20*log10(f) + 20*log10(4π/c)
    // 20*log10(d) = FSPL - 20*log10(f) - 20*log10(4π/c)
    // log10(d) = (FSPL - 20*log10(f) - 20*log10(4π/c)) / 20
    const log10d = (maxAllowedFsplDb - 20 * Math.log10(FREQ_HZ) - 20 * Math.log10(4 * Math.PI / SPEED_OF_LIGHT)) / 20;
    const maxDistanceM = Math.pow(10, log10d);
    const maxDistanceKm = maxDistanceM / 1000;

    // 4. From the max distance and minimum elevation angle, calculate the required altitude.
    // Using the Law of Sines on the triangle formed by Earth's center, the user, and the satellite.
    const earthRadiusKm = EarthRadius;
    const elevationAngleRad = inputValues.elevationAngle * Math.PI / 180;
    // Angle at the satellite (gamma)
    const gamma = Math.asin((earthRadiusKm * Math.cos(elevationAngleRad)) / maxDistanceKm);
    // Angle at Earth's center (alpha)
    const alpha = Math.PI - elevationAngleRad - Math.PI / 2 - gamma;
    // Now use Law of Cosines to find altitude
    const semiMajorAxisKm = Math.sqrt(Math.pow(earthRadiusKm, 2) + Math.pow(maxDistanceKm, 2) - 2 * earthRadiusKm * maxDistanceKm * Math.cos(Math.PI / 2 + elevationAngleRad));
    const requiredAltitudeKm = semiMajorAxisKm - earthRadiusKm;

    // --- FORWARD CALCULATIONS (using the newly derived altitude) ---

    // 5. Calculate coverage radius and area for one satellite from the new altitude.
    const centralAngle = Math.acos((earthRadiusKm * Math.cos(elevationAngleRad)) / (earthRadiusKm + requiredAltitudeKm)) - elevationAngleRad;
    const coverageRadiusKm = earthRadiusKm * centralAngle;
    const coverageAreaOneSat = Math.PI * Math.pow(coverageRadiusKm, 2);

    // 6. Calculate the required satellite beamwidth to cover that area.
    const nadirAngle = Math.atan(coverageRadiusKm / requiredAltitudeKm);
    const beamwidthDegrees = 2 * nadirAngle * 180 / Math.PI;

    // 7. Calculate constellation size.
    const numSatellitesNeeded = Math.ceil(inputValues.targetArea / coverageAreaOneSat);
    const numOrbitalPlanes = Math.max(1, Math.ceil(Math.sqrt(numSatellitesNeeded / 2)));
    const satsPerPlane = Math.ceil(numSatellitesNeeded / numOrbitalPlanes);
    const totalCalculatedSatellites = numOrbitalPlanes * satsPerPlane;

    // 8. Calculate other performance metrics.
    const orbitalPeriodSeconds = 2 * Math.PI * Math.sqrt(Math.pow(semiMajorAxisKm, 3) / MU_EARTH);
    const orbitalPeriodMinutes = orbitalPeriodSeconds / 60;
    const revisitTime = orbitalPeriodMinutes / numOrbitalPlanes;

    // Recalculate the final link budget with the derived altitude for verification.
    const finalFspl = 20 * Math.log10(maxDistanceM) + 20 * Math.log10(FREQ_HZ) + 20 * Math.log10(4 * Math.PI / SPEED_OF_LIGHT);
    const finalReceivedPower = inputValues.transmitPower + inputValues.txAntennaGain + inputValues.rxAntennaGain - finalFspl - inputValues.atmosphericLoss;
    const finalSnr = finalReceivedPower - noisePowerDbm;
    const finalShannonCapacity = BANDWIDTH_HZ * Math.log2(1 + Math.pow(10, finalSnr / 10));
    const peakThroughputPerUser = finalShannonCapacity / Math.max(1, inputValues.minSatellitesInView);

    return {
        // --- Final Verified Link Budget Results ---
        receivedPower: finalReceivedPower,
        snr: finalSnr,
        shannonCapacity: finalShannonCapacity,

        // --- Derived Constellation Parameters ---
        numSatellitesNeeded: totalCalculatedSatellites,
        numOrbitalPlanes: numOrbitalPlanes,
        satsPerPlane: satsPerPlane,
        revisitTime: revisitTime,
        peakThroughputPerUser: peakThroughputPerUser,

        // --- DERIVED Orbital Parameters for Simulation ---
        altitude: requiredAltitudeKm,
        inclination: inputValues.orbitInclination, // User still defines this for latitude coverage
        beamwidth: beamwidthDegrees,
        eccentricity: 0,
        raan: 0,
        argumentOfPerigee: 0,
        trueAnomaly: 0,
        
        // Pass original inputs through for reference
        ...inputValues
    };
}
