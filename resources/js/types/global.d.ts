// resources/js/types/global.d.ts

// Import necessary types from Three.js and OrbitControls if you're using them directly in the Window interface
// (which you are for getSimulationCoreObjects)
import * as THREE from "three";
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';

declare global {
  interface Window {
    // Properties from Earth3Dsimulation.js and the inline script
    activeSatellites: Map<string, any>; // Consider replacing 'any' with a more specific Satellite class type if defined
    activeGroundStations: Map<string, any>; // Consider replacing 'any' with a more specific GroundStation class type
    selectedSatelliteId: string | null;
    isAnimating: boolean;
    closeViewEnabled: boolean;
    totalSimulatedTime: number;
    currentSpeedMultiplier: number;
    currentEpochUTC: number;
    referenceUTC: number; // Added from parametersimulation.js

    // Exposed functions from Earth3Dsimulation.js
    clearSimulationScene: () => void;
    addOrUpdateSatelliteInScene: (satelliteData: any) => void;
    addOrUpdateGroundStationInScene: (gsData: any) => void;
    removeObjectFromScene: (idToRemove: string, type: 'satellite' | 'groundStation') => void;
    viewSimulation: (data: any) => void;
    getSimulationCoreObjects: () => {
      scene: THREE.Scene;
      camera: THREE.PerspectiveCamera;
      renderer: THREE.WebGLRenderer;
      controls: OrbitControls;
      earthGroup: THREE.Group;
      activeSatellites: Map<string, any>;
      activeGroundStations: Map<string, any>;
      isAnimating: boolean;
      currentSpeedMultiplier: number;
      totalSimulatedTime: number;
      selectedSatelliteId: string | null;
      closeViewEnabled: boolean;
      currentEpochUTC: number;
      setTotalSimulatedTime: (time: number) => void;
      setIsAnimating: (state: boolean) => void;
      setCurrentSpeedMultiplier: (speed: number) => void;
      setCurrentEpochUTC: (epoch: number) => void;
      setSelectedSatelliteId: (id: string | null) => void;
      setCloseViewEnabled: (state: boolean) => void;
    };
    load3DSimulationState: () => void;

    // Exposed functions from the inline script in simulation.blade.php
    undoOperation: () => void;
    redoOperation: () => void;
    showCustomConfirmation: (message: string, title?: string, confirmButtonText?: string, onConfirmCallback?: () => void, showCancelButton?: boolean) => void;
    showCustomAlert: (message: string, title?: string) => void;
    toggleTab: (id: string, btn: HTMLElement) => void;
    closepopup: () => void;
    formatNumberInput: (value: string | number) => string;
    showInputError: (inputId: string, message: string) => void;
    clearInputError: (inputId: string) => void;
    NewSingleMenu: () => void;
    toggleEccentricityInput: (type: 'circular' | 'elliptical') => void;
    NewConstellationMenu: () => void;
    toggleConstellationType: (type: 'train' | 'walker') => void;
    toggleWalkerOffset: (show: boolean) => void;
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
    showSavePopup: () => void;
    generateAndSaveSelectedScripts: () => void;
    Load: () => void;
    playAnimation: () => void;
    pauseAnimation: () => void;
    speedUpAnimation: () => void;
    slowDownAnimation: () => void;
    zoomIn: () => void;
    zoomOut: () => void;
    updateSatelliteDataDisplay: () => void; // This is crucial for UI script interaction with 3D data
  }
}