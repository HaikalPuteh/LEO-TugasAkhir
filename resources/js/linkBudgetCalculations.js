//FILE: linkBudgetCalculations.js 

import { EarthRadius, MU_EARTH } from "./parametersimulation.js";

/**
 * Performs a single link budget calculation for either uplink or downlink.
 * This helper function calculates the maximum supportable distance for a given set of RF parameters.
 * @param {object} params - The parameters for the specific link (e.g., uplinkParams or downlinkParams).
 * @returns {object} The calculated link budget results for one direction, including max distance.
 */
function calculateSingleLink(params) {
    // Destructure all potential parameters with defaults for safety
    const {
        frequency = 1,
        bandwidth = 1,
        transmitPower = 0,
        txAntennaGain = 0,
        txCableLoss = 0,
        txPointingLoss = 0,
        rxAntennaGain = 0,
        rxCableLoss = 0,
        rxPointingLoss = 0,
        noiseFigure = 0,
        systemTemp = 290, // Default to standard temperature
        atmosphericLoss = 0,
        rainFadeMargin = 0,
        polarizationLoss = 0,
        minimumSNR = 0
    } = params;

    // --- Constants and Conversions ---
    const FREQ_HZ = frequency * 1e9;
    const BANDWIDTH_HZ = bandwidth * 1e6;
    const BOLTZMANN_CONST = 1.38e-23; // J/K
    const TEMP_K = systemTemp;
    const SPEED_OF_LIGHT = 299792458; // m/s

    // --- STEP 1: Calculate Required Received Power ---
    const noiseFactor = Math.pow(10, noiseFigure / 10);
    const noisePowerWatts = BOLTZMANN_CONST * TEMP_K * BANDWIDTH_HZ * noiseFactor;
    const noisePowerDbm = 10 * Math.log10(noisePowerWatts * 1000);
    const requiredReceivedPowerDbm = minimumSNR + noisePowerDbm + 3;

    // --- STEP 2: Calculate Total System Gains and Losses ---
    const totalTransmitLosses = txCableLoss + txPointingLoss;
    const totalReceiveLosses = rxCableLoss + rxPointingLoss;
    const totalPathLosses = atmosphericLoss + rainFadeMargin + polarizationLoss;
    const totalSystemGains = txAntennaGain + rxAntennaGain;
    const totalSystemLosses = totalTransmitLosses + totalReceiveLosses + totalPathLosses;
    
    // --- STEP 3: Calculate Maximum Allowable Path Loss (FSPL) ---
    const maxAllowedFsplDb = transmitPower + totalSystemGains - totalSystemLosses - requiredReceivedPowerDbm;

    // --- STEP 4: Calculate Maximum Distance from FSPL ---
    const fsplConstant = 20 * Math.log10(4 * Math.PI / SPEED_OF_LIGHT);
    const log10d = (maxAllowedFsplDb - 20 * Math.log10(FREQ_HZ) - fsplConstant) / 20;
    const maxDistanceM = Math.pow(10, log10d);
    
    return {
        maxDistanceM,
        noisePowerDbm,
        fsplConstant,
        frequency,
        transmitPower,
        txAntennaGain,
        rxAntennaGain,
        totalSystemLosses,
        minimumSNR
    };
}


/**
 * Performs a full two-way link budget calculation and designs a constellation.
 * It determines the limiting link (uplink or downlink) and uses that to constrain the design.
 * @param {object} inputValues - A flat object containing all parameters from the detailed UI.
 * @returns {object} A comprehensive object with results for both links and the final constellation design.
 */
export function calculateLinkBudget(inputValues) {
    // --- Separate parameters for each link from the main input object ---
    const uplinkParams = {
        frequency: inputValues.uplinkFrequency,
        bandwidth: inputValues.uplinkBandwidth,
        transmitPower: inputValues.uplinkTransmitPower,
        txAntennaGain: inputValues.uplinkTxAntennaGain,
        txCableLoss: inputValues.uplinkTxCableLoss,
        txPointingLoss: inputValues.uplinkTxPointingLoss,
        rxAntennaGain: inputValues.uplinkRxAntennaGain,
        rxCableLoss: inputValues.uplinkRxCableLoss,
        rxPointingLoss: inputValues.uplinkRxPointingLoss,
        noiseFigure: inputValues.uplinkNoiseFigure,
        systemTemp: inputValues.uplinkSystemTemp,
        atmosphericLoss: inputValues.uplinkAtmosphericLoss,
        rainFadeMargin: inputValues.uplinkRainFadeMargin,
        polarizationLoss: inputValues.uplinkPolarizationLoss,
        minimumSNR: inputValues.minimumSNR
    };

    const downlinkParams = {
        frequency: inputValues.downlinkFrequency,
        bandwidth: inputValues.downlinkBandwidth,
        transmitPower: inputValues.downlinkTransmitPower,
        txAntennaGain: inputValues.downlinkTxAntennaGain,
        txCableLoss: inputValues.downlinkTxCableLoss,
        txPointingLoss: inputValues.downlinkTxPointingLoss,
        rxAntennaGain: inputValues.downlinkRxAntennaGain,
        rxCableLoss: inputValues.downlinkRxCableLoss,
        rxPointingLoss: 0, // Assume user terminal points perfectly
        noiseFigure: inputValues.downlinkNoiseFigure,
        systemTemp: inputValues.downlinkSystemTemp,
        atmosphericLoss: inputValues.downlinkAtmosphericLoss,
        rainFadeMargin: inputValues.downlinkRainFadeMargin,
        polarizationLoss: inputValues.downlinkPolarizationLoss,
        minimumSNR: inputValues.minimumSNR
    };

    // --- Calculate max distance for both links ---
    const uplinkResult = calculateSingleLink(uplinkParams);
    const downlinkResult = calculateSingleLink(downlinkParams);

    // --- Determine the Limiting Link ---
    // The link that can only support the shorter distance is the bottleneck for the whole system.
    const limitingMaxDistanceM = Math.min(uplinkResult.maxDistanceM, downlinkResult.maxDistanceM);
    const maxDistanceKm = limitingMaxDistanceM / 1000;

    // --- Calculate Required Altitude from the Limiting Distance ---
    const earthRadiusKm = EarthRadius;
    const elevationAngleRad = inputValues.elevationAngle * Math.PI / 180;
    const altitudeKm = Math.sqrt(Math.pow(earthRadiusKm, 2) + Math.pow(maxDistanceKm, 2) + 2 * earthRadiusKm * maxDistanceKm * Math.sin(elevationAngleRad)) - earthRadiusKm;
    const finalAltitude = Math.max(100, Math.min(altitudeKm, 2000)); // Limit to LEO
    const semiMajorAxisKm = earthRadiusKm + finalAltitude;

    // --- Recalculate Final Link Margins for both links at the determined altitude ---
    // We need the actual slant range at the final altitude and elevation angle.
    const finalSlantRangeKm = Math.sqrt(Math.pow(finalAltitude + earthRadiusKm, 2) - Math.pow(earthRadiusKm * Math.cos(elevationAngleRad), 2)) - (earthRadiusKm * Math.sin(elevationAngleRad));
    const finalSlantRangeM = finalSlantRangeKm * 1000; // Convert km to meters

    // Uplink Margin Calculation
    const uplinkFspl = 20 * Math.log10(finalSlantRangeM) + 20 * Math.log10(uplinkResult.frequency * 1e9) + uplinkResult.fsplConstant;
    const uplinkReceivedPower = uplinkResult.transmitPower + uplinkResult.txAntennaGain + uplinkResult.rxAntennaGain - uplinkResult.totalSystemLosses - uplinkFspl;
    const uplinkSnr = uplinkReceivedPower - uplinkResult.noisePowerDbm;
    const uplinkLinkMargin = uplinkSnr - uplinkResult.minimumSNR;

    // Downlink Margin Calculation
    const downlinkFspl = 20 * Math.log10(finalSlantRangeM) + 20 * Math.log10(downlinkResult.frequency * 1e9) + downlinkResult.fsplConstant;
    const downlinkReceivedPower = downlinkResult.transmitPower + downlinkResult.txAntennaGain + downlinkResult.rxAntennaGain - downlinkResult.totalSystemLosses - downlinkFspl;
    const downlinkSnr = downlinkReceivedPower - downlinkResult.noisePowerDbm;
    const downlinkLinkMargin = downlinkSnr - downlinkResult.minimumSNR;
    
    // --- Constellation Design (based on the final altitude) ---
    const maxCentralAngle = Math.acos(earthRadiusKm * Math.cos(elevationAngleRad) / (earthRadiusKm + finalAltitude)) - elevationAngleRad;
    const coverageRadiusKm = earthRadiusKm * maxCentralAngle;
    const coverageAreaKm2 = Math.PI * Math.pow(coverageRadiusKm, 2);
    const beamwidthDegrees = 2 * Math.atan(coverageRadiusKm / finalAltitude) * 180 / Math.PI;

    const earthSurfaceArea = inputValues.targetArea || (4 * Math.PI * Math.pow(earthRadiusKm, 2));
    const SatCount = Math.ceil(earthSurfaceArea / coverageAreaKm2);
    const coverageOverlapFactor = inputValues.overlapFractionInput || 1; // Use input or default to 1
    const requiredSatellites = Math.ceil(SatCount * coverageOverlapFactor);

    const walkerConfig = optimizeWalkerConstellation(
        requiredSatellites, 
        inputValues.orbitInclination, 
        inputValues.targetArea || earthSurfaceArea,
        coverageAreaKm2
    );

    const { numPlanes, satsPerPlane, totalSatellites } = walkerConfig;
    const orbitalPeriodSeconds = 2 * Math.PI * Math.sqrt(Math.pow(semiMajorAxisKm, 3) / MU_EARTH);
    const orbitalVelocity = Math.sqrt(MU_EARTH / semiMajorAxisKm);
    const shannonCapacity = (downlinkParams.bandwidth * 1e6) * Math.log2(1 + Math.pow(10, downlinkSnr / 10));

    return {
        ...inputValues, // Pass through all original inputs for populating the edit form
        altitude: finalAltitude,
        maxDistance: maxDistanceKm,
        uplink: {
            receivedPower: uplinkReceivedPower,
            snr: uplinkSnr,
            linkMargin: uplinkLinkMargin,
            fspl: uplinkFspl,
        },
        downlink: {
            receivedPower: downlinkReceivedPower,
            snr: downlinkSnr,
            linkMargin: downlinkLinkMargin,
            fspl: downlinkFspl,
            shannonCapacity: shannonCapacity,
        },
        inclination: inputValues.orbitInclination,
        beamwidth: beamwidthDegrees,
        numSatellitesNeeded: totalSatellites,
        numOrbitalPlanes: numPlanes,
        satsPerPlane: satsPerPlane,
        ...walkerConfig,
        coverageArea: coverageAreaKm2,
        coverageRadius: coverageRadiusKm,
        orbitalPeriod: orbitalPeriodSeconds / 60,
        orbitalVelocity: orbitalVelocity,
    };
}

/**
 * The final, correct version of the Walker constellation optimizer.
 */
function optimizeWalkerConstellation(
  totalSats,
  inc,
  targetArea,
  perSatArea,
  options = {}
) {
  const regionMaxLat = options.regionMaxLatitude ?? inc;
  const overlapFraction = options.overlapFraction ?? 0.1;
  const latFactor = Math.cos((regionMaxLat * Math.PI) / 180);
  const effectiveFootprint = perSatArea * latFactor * (1 - overlapFraction);
  let best = { score: -Infinity };
  const earthSurfaceArea = 4 * Math.PI * Math.pow(EarthRadius, 2);
  const isGlobalCoverage = targetArea / earthSurfaceArea > 0.95;
  const raanSpreadsToTest = isGlobalCoverage ? [360] : [360, 270, 180, 120, 90];

  for (let P = 1; P <= totalSats; P++) {
    const S = Math.ceil(totalSats / P);
    if (P * S > totalSats * 1.2) continue;
    const uniformity = 1 - Math.abs(P / S - 1);
    for (const raanSpread of raanSpreadsToTest) {
      const spreadRatio = raanSpread / 360;
      const scaledArea = targetArea / spreadRatio;
      for (let F = 0; F < S; F++) {
        const totalFootprint = effectiveFootprint * P * S;
        const coverFrac = Math.min(1, totalFootprint / scaledArea);
        const score = coverFrac * uniformity;
        if (score > best.score) {
          best = { P, S, F, raanSpread, score, coverFrac, uniformity };
        }
      }
    }
  }

  if (best.score < 0 || best.score === -Infinity) {
    const P = Math.max(3, Math.ceil(Math.sqrt(totalSats)));
    const S = Math.ceil(totalSats / P);
    best = { P, S, F: 0, raanSpread: 360, score: 0 };
  }

  const { P, S, F, raanSpread } = best;
  return {
    numPlanes: P,
    satsPerPlane: S,
    totalSatellites: P * S,
    raanSpread,
    phasingFactor: F,
    inPlaneSpacing: 360 / S,
    walkerNotation: `W${inc}°:${P * S}/${P}/${F}`,
    coverageEfficiency: best.score
  };
}
