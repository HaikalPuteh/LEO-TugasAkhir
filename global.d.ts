// global.d.ts
// Extend the Window interface to include your custom properties
interface Window {
    activeSatellites: Map<string, any>; // Map<string, Satellite> if Satellite type is defined
    activeGroundStations: Map<string, any>; // Map<string, GroundStation>
    selectedSatelliteId: string | null;
    isAnimating: boolean;
    closeViewEnabled: boolean;
    totalSimulatedTime: number;
    currentSpeedMultiplier: number;
    currentEpochUTC: number;

    // Functions exposed from Earth3Dsimulation.js
    clearSimulationScene: () => void;
    addOrUpdateSatelliteInScene: (data: any) => void; // Replace 'any' with actual types if available
    addOrUpdateGroundStationInScene: (data: any) => void;
    removeObjectFromScene: (id: string, type: string) => void;
    viewSimulation: (data: any) => void;
    getSimulationCoreObjects: () => {
        scene: import('three').Scene; // Assuming Three.js types are installed
        camera: import('three').PerspectiveCamera;
        renderer: import('three').WebGLRenderer;
        controls: import('three/examples/jsm/controls/OrbitControls').OrbitControls;
        earthGroup: import('three').Group;
        activeSatellites: Map<string, any>; // Consider creating a Satellite interface
        activeGroundStations: Map<string, any>; // Consider creating a GroundStation interface
        isAnimating: boolean;
        currentSpeedMultiplier: number;
        totalSimulatedTime: number;
        selectedSatelliteId: string | null;
        closeViewEnabled: boolean;
        currentEpochUTC: number;
        setTotalSimulatedTime: (time: number) => void;
        setIsAnimating: (state: boolean) => void;
        setCurrentSpeedMultiplier: (speed: number) => void;
        setSelectedSatelliteId: (id: string | null) => void;
        setCloseViewEnabled: (state: boolean) => void;
        setCurrentEpochUTC: (epoch: number) => void;
    };
    load3DSimulationState: () => void;
    updateSatelliteDataDisplay: () => void; // Exposed from simulation.blade.php

    // Functions exposed from simulation.blade.php's script
    // You'll need to add ALL functions that you assign to `window.` here:
    toggleEccentricityInput: (type: string) => void;
    toggleConstellationType: (type: string) => void;
    toggleTrainOffset: (show: boolean) => void;
    playAnimation: () => void;
    pauseAnimation: () => void;
    speedUpAnimation: () => void;
    slowDownAnimation: () => void;
    setActiveControlButton: (id: string) => void;
    updateAnimationDisplay: () => void;
    zoomIn: () => void;
    zoomOut: () => void;
    showCustomConfirmation: (message: string, title?: string, confirmText?: string, onConfirm?: () => void, showCancel?: boolean) => void;
    showCustomAlert: (message: string, title?: string) => void;
    toggleTab: (id: string, btn: HTMLElement) => void;
    closepopup: () => void;
    formatNumberInput: (value: string | number) => string;
    showInputError: (id: string, message: string) => void;
    clearInputError: (id: string) => void;
    NewSingleMenu: () => void;
    NewConstellationMenu: () => void;
    NewGroundStationMenu: () => void;
    NewLinkBudgetMenu: () => void;
    showLinkBudgetOutput: (data: any) => void;
    editSingleParameter: (fileName: string) => void;
    editConstellationParameter: (fileName: string) => void;
    editGroundStation: (name: string) => void;
    deleteFile: (fileName: string, fileType: string) => void;
    toggle2DView: () => void;
    resetView: () => void;
    toggleCloseView: () => void;
    undoOperation: () => void;
    redoOperation: () => void;
    handleLogout: () => void;
    showSavePopup: () => void;
    generateAndSaveSelectedScripts: () => void;
    Load: () => void;
    selectSatellite: (id: string | null) => void; // Add this one for the selectSatellite function

    // Imported constants/functions exposed to window from other modules
    DEG2RAD: number;
    EarthRadius: number;
    MU_EARTH: number;
    solveKepler: (M: number, e: number) => number;
    E_to_TrueAnomaly: (E: number, e: number) => number;
    TrueAnomaly_to_E: (nu: number, e: number) => number;
    E_to_M: (E: number, e: number) => number;
    updateOrbitalElements: (satellite: any, timeSinceEpoch: number) => void; // If exposed
    calculateSatellitePositionECI: (params: any, meanAnomaly: number, raan: number, sceneEarthRadius: number, realEarthRadiusKm: number) => { x: number; y: number; z: number }; // If exposed
    calculateDerivedOrbitalParameters: (altitude_km: number, eccentricity: number) => { orbitalPeriod: number, meanMotion: number, semiMajorAxis: number, periapsis: number, apoapsis: number };
    calculateLinkBudget: (params: any) => any; // Replace 'any' with actual LinkBudget output type
}

// Declare modules that might be imported to ensure TypeScript knows about them
// (This might be handled by Vite/tsconfig, but doesn't hurt)
declare module 'three';
declare module 'three/examples/jsm/controls/OrbitControls.js';
declare module 'three/examples/jsm/loaders/GLTFLoader.js';
declare module './getStarfield.js';
declare module './glowmesh.js';
declare module 'gsap';
declare module './orbitalCalculation.js';
declare module './parametersimulation.js';
declare module './linkBudgetCalculations.js';