// resources/js/linkBudgetCalculations.js

// Import necessary constants
import { EarthRadius, MU_EARTH } from "./parametersimulation.js";

/**
 * Performs a link budget analysis and calculates constellation needs.
 * @param {object} inputValues - An object containing all link budget input parameters.
 * @returns {object} An object with calculated results.
 */
export function calculateLinkBudget(inputValues) {
    const FREQ_HZ = inputValues.frequency * 1e9;
    const DISTANCE_M = inputValues.distance * 1000;
    const BANDWIDTH_HZ = inputValues.bandwidth * 1e6;
    const BOLTZMANN_CONST = 1.38e-23; // Boltzmann constant
    const TEMP_K = 290; // Standard Noise Temperature in Kelvin

    // 1. Free Space Path Loss (FSPL)
    // L_fs = 20 log10(d) + 20 log10(f) + 20 log10(4π/c)
    // where d in meters, f in Hz, c in m/s
    // 20 log10(4π/c) is approx -147.55 dB for d in meters and f in Hz
    const fspl = 20 * Math.log10(DISTANCE_M) + 20 * Math.log10(FREQ_HZ) - 147.55;

    // 2. Received Power (Pr) in dBm
    // Pr = P_tx + G_tx + G_rx - L_fs - L_atm
    const receivedPower = inputValues.transmitPower + inputValues.txAntennaGain + inputValues.rxAntennaGain - fspl - inputValues.atmosphericLoss;

    // 3. Noise Power (Pn) in dBm
    // Pn (Watts) = k * T * B * F (Noise Factor)
    // Noise Factor (F) = 10^(Noise Figure/10)
    const noiseFactor = Math.pow(10, inputValues.noiseFigure / 10);
    const noisePowerWatts = BOLTZMANN_CONST * TEMP_K * BANDWIDTH_HZ * noiseFactor;
    const noisePowerDbm = 10 * Math.log10(noisePowerWatts) + 30; // Convert Watts to dBm

    // 4. Signal-to-Noise Ratio (SNR) in dB
    // SNR = Pr - Pn
    const snr = receivedPower - noisePowerDbm;

    // 5. Shannon Capacity (bps)
    // C = B * log2(1 + SNR_linear)
    const snrLinear = Math.pow(10, snr / 10); // Convert SNR from dB to linear
    const shannonCapacity = BANDWIDTH_HZ * Math.log2(1 + snrLinear);

    // 6. Constellation Needs (Walker Delta by default)
    const orbitHeightKm = inputValues.orbitHeight;
    const earthRadiusKm = EarthRadius; // Using EarthRadius from parametersimulation.js

    // Calculate coverage angle and area for one satellite
    // Angle from Earth center to edge of coverage for a satellite at height H
    // cos(angle) = R / (R+H)
    const angleFromCenter = Math.acos(earthRadiusKm / (earthRadiusKm + orbitHeightKm));
    const coverageAreaOneSat = 2 * Math.PI * Math.pow(earthRadiusKm, 2) * (1 - Math.cos(angleFromCenter));

    // Number of satellites needed to cover target area
    const numSatellitesNeeded = inputValues.targetArea / coverageAreaOneSat;

    // Simplified Walker Delta calculations for planes and satellites per plane
    // This is a heuristic. Actual Walker constellation design is more complex.
    const numOrbitalPlanes = Math.max(1, Math.ceil(Math.sqrt(numSatellitesNeeded)));
    const satsPerPlane = Math.max(1, Math.ceil(numSatellitesNeeded / numOrbitalPlanes));
    const totalCalculatedSatellites = numOrbitalPlanes * satsPerPlane; // Total rounded up satellites

    // Revisit Time (Simplified: Period / Num Planes)
    const semiMajorAxisKm = earthRadiusKm + orbitHeightKm;
    const orbitalPeriodSeconds = 2 * Math.PI * Math.sqrt(Math.pow(semiMajorAxisKm, 3) / MU_EARTH);
    const orbitalPeriodMinutes = orbitalPeriodSeconds / 60;
    const revisitTime = orbitalPeriodMinutes / numOrbitalPlanes;

    // Peak Throughput Per User (Simplified: Total Capacity / Min Satellites in View)
    const peakThroughputPerUser = shannonCapacity / inputValues.minSatellitesInView;


    return {
        receivedPower: receivedPower,
        snr: snr,
        shannonCapacity: shannonCapacity,
        numSatellitesNeeded: totalCalculatedSatellites,
        numOrbitalPlanes: numOrbitalPlanes,
        revisitTime: revisitTime,
        peakThroughputPerUser: peakThroughputPerUser
    };
}