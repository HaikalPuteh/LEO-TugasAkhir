<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satellite UI/UX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- Assuming Vite setup is correct and points to resources/js/Earth3Dsimulation.js --}}
    @vite([
        'resources/js/Earth2Dsimulation.js', // Uncomment if you implement 2D view and related logic
        'resources/js/Earth3Dsimulation.js' // Your primary Three.js simulation logic
    ])
    <style>

        /*keseluruhan tapi bagian footer*/
        body {
            font-family: 'Rubik', sans-serif;
            background-color: #16214a;
            color: white;
            margin: 0;
            padding: 0;
        }

        /*bagian header*/
        header {
            background-color: #00274e !important;
        }

        /*bagian navigation menu*/
        .nav-link {
            font-size: 14px;
            padding: 6px 12px;
            color: White !important;
        }

        .nav-link:hover {
            background-color: #001f4d;
            border-radius: 4px;
        }

        /*bagian toolbar dan settings*/
        .contextmenu, .settings-contextmenu {
            display: none;
            position: absolute;
            background-color: #00274e;
            color: white;
            list-style: none;
            padding: 0.25rem 0;
            border-radius: 0.25rem;
            z-index: 1050;
            min-width: 160px;
            box-shadow: 0 2px 6px rgba(255, 255, 255, 0.15);
            font-size: 16px;
        }

        .settings-contextmenu {
            top: 100%;
            right: 0;
            transform: translateX(-1%);
        }
        /*Play,Pause,etc*/
        .btn-toolbar {
            background-color: #00274e;
            border-radius: 4px;
            padding: 4px;
        }

        .btn-toolbar .btn {
            border: none;
            color: white;
        }

        .btn-toolbar .btn:hover {
            background-color: #001f4d;
        }

        .btn-toolbar .btn.pressed {
            background-color: #001a33; /* A darker shade of #003366 */
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2); /* Optional: add an inset shadow for a pressed look */
        }

        /* Add this to your <style> section */
        .btn-group-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 10px; /* Provides spacing between groups */
        }

        .contextmenu li,
        .settings-contextmenu li {
            padding: 2px 10px;
            cursor: pointer;
            white-space: nowrap;
        }

        .menu-item:hover .contextmenu, .settings-icon:hover .settings-contextmenu {
            display: block;
        }

        .contextmenu li:hover, .settings-contextmenu li:hover {
            background-color:rgb(200, 200, 200);
        }

        .settings-icon button {
            background-color: transparent;
            border: 1px solid #003366;
            border-radius: 4px;
            padding: 6px 10px;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .settings-icon button:hover {
            background-color: #001f4d;
            border-color: #001f4d;
            color: white;
        }

        /*bagian sidebar*/
        .sidebar {
            width: 250px;
            background: #001b36;
            color: rgb(255, 255, 255);
            display: flex;
            flex-direction: column;
        }

        .menu-content {
            flex-grow: 1;
            overflow-y: auto;
        }

        /*bagian sidebar (resource menu)*/
        #resource-menu ul {
            padding-left: 0;
            margin-top: 0;
            margin-bottom: 0;
            list-style: none;
        }

        #resource-menu ul li {
            list-style: none;
            font-weight: normal;
            padding-left: 1rem;
            /* Flexbox removed, as edit button will not be next to it */
        }

        /* Khusus untuk "Satellite" */
        #satellite-resource-list {
            padding-left: 0.5rem;
        }


        #single-files-list,
        #constellation-files-list {
            list-style: disc;
            padding-left: 1rem;
            cursor: default;
        }

        #single-files-list ul li,
        #constellation-files-list ul li {
            list-style: none;
            padding-left: 1.5rem;
            cursor: pointer;
        }

        #single-files-list ul li:hover,
        #constellation-files-list ul li:hover {
            background-color: #e9ecef;
        }

        /*bagian sidebar (output menu)*/
        #output-menu ul {
            list-style: none;
            padding-left: 0.5rem;
        }

        #output-menu ul li {
            list-style: none;
            font-weight: normal;
        }

        .output-file-name {
            list-style: disc !important;
            padding-left: 1rem;
            font-weight: normal;
        }

        /* Style for the action buttons container in output sidebar */
        .output-actions {
            display: flex; /* Changed from display: flex to center items */
            justify-content: center; /* This centers the items horizontally */
            align-items: center; /* Align items vertically if they had different heights, good practice */
            padding-left: 0; /* Remove left padding to allow full centering */
            /* You might want to adjust padding or margin-bottom/top for vertical spacing */
        }

        .output-actions .btn {
            margin: 0 5px; /* Adds 5px margin to the left and right of each button */
        }

        .content {
            flex-grow: 1;
            background-color: white;
            position: relative;
        }

        /*bagian bumi*/
        /*3D view*/
        #earth-container {
            width: 100%;
            /* height: 615px;*/
            height: 100%; /* Or whatever height you define for your 3D view */
            position: relative; /* THIS IS IMPORTANT: Makes it the positioning context for absolute children */
            /* ... other #earth-container styles ... */
        }
         
        #earth2D-container {
            width: 100%;
            /* height: 615px;*/ /* Or whatever height you define for your 2D view */
            height: 100%; /* THIS IS IMPORTANT: Makes it the positioning context for absolute children */
            display: none; /* Initially hidden, will be toggled by JavaScript */    
            /* ... other #earth-container styles ... */
        }

        .hidden {
            display: none;
        }

        .nav-tabs .nav-link {
            flex: 1;
            text-align: center;
            font-size: 14px;
            padding: 8px 0;
            background-color: #001b36;
        }

        .nav-tabs .nav-link.active {
            background-color: rgb(33, 92, 151);
            color: #ffffff;
        }

        #animationStatusDisplay {
            position: absolute; /* This makes it position relative to its closest positioned ancestor */
            top: 20px;           /* Distance from the top of #earth-container */
            right: 20px;         /* Distance from the right of #earth-container */
            background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent dark background */
            color: white;        /* White text color */
            padding: 8px 15px;   /* Padding inside the box */
            border-radius: 8px; /* Rounded corners */
            font-size: 14px;     /* Slightly smaller font for compactness */
            z-index: 10;         /* Ensure it's above the 3D scene */
            backdrop-filter: blur(5px); /* Optional: Adds a subtle blur effect behind the text */
            -webkit-backdrop-filter: blur(5px); /* For Safari compatibility */
            display: flex;       /* Use flexbox for easy alignment of children */
            align-items: center; /* Vertically center items */
            gap: 8px;            /* Space between status and speed */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Soft shadow for depth */
        }

        #animationStatusDisplay2D {
            position: absolute; /* This makes it position relative to its closest positioned ancestor */
            top: 20px;           /* Distance from the top of #earth-container */
            right: 20px;         /* Distance from the right of #earth-container */
            background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent dark background */
            color: white;        /* White text color */
            padding: 8px 15px;   /* Padding inside the box */
            border-radius: 8px; /* Rounded corners */
            font-size: 14px;     /* Slightly smaller font for compactness */
            z-index: 10;         /* Ensure it's above the 3D scene */
            backdrop-filter: blur(5px); /* Optional: Adds a subtle blur effect behind the text */
            -webkit-backdrop-filter: blur(5px); /* For Safari compatibility */
            display: flex;       /* Use flexbox for easy alignment of children */
            align-items: center; /* Vertically center items */
            gap: 8px;            /* Space between status and speed */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Soft shadow for depth */
        }

        /* Optional: Style for the text within the display */
        #animationStatusDisplay span {
            font-weight: 500; /* Medium font weight for values */
        }

         /* Optional: Style for the text within the display */
        #animationStatusDisplay2D span {
            font-weight: 500; /* Medium font weight for values */
        }

        /*bagian zoom +-*/
        .zoom-controls {
            position: absolute;
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            z-index: 10;
        }

        .zoom-button {
            background-color: rgba(0, 51, 102, 0.7);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 16px;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        /* Simulation Clock Display (overlay on 3D view) - NEW */
        #simulationClockDisplay {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        #simulationClockDisplay2D {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        .zoom-button:hover {
            opacity: 1;
            background-color: rgba(0, 31, 77, 0.8);
        }

        /*bagian notifikasi peringatan*/
        .custom-alert-content {
        background-color: white;
        color: black;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .custom-alert-header {
            border-bottom: 1px solid #ddd;
            padding: 15px 20px;
        }

        .custom-alert-header .modal-title {
            color: #333;
            font-weight: bold;
        }

        .custom-alert-header .btn-close {
            filter: none;
            color: grey;
            opacity: 0.7;
            border: 1px solid white;
            border-radius: 4px;
            padding: 0.25rem 0.5rem;
            transition: all 0.1s ease-in-out;
        }

        .custom-alert-header .btn-close:hover {
            opacity: 1;
            color: white;
            background-color: red;
            border-color: red;
        }

        .custom-alert-body {
            padding: 15px;
            font-size: 1em;
            color: #333;
            text-align: center;;
        }

        .custom-alert-footer {
            border-top: 1px solid #ddd;
            padding: 10px 20px;
            justify-content: center;
        }

        .custom-alert-ok-btn {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            padding: 8px 25px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .custom-alert-ok-btn:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        /* Styles for input validation feedback */
        .text-danger {
            color: #dc3545; /* Red color for error messages */
        }
        .is-invalid {
            border-color: #dc3545 !important; /* Red border for invalid input */
        }
        /* Styles for satellite list buttons */
        #satelliteButtonsContainer {
            margin-bottom: 15px;
            border: 1px solid #eee;
            padding: 5px;
            border-radius: 5px;
            max-height: 150px; /* Limit height and make scrollable */
            overflow-y: auto;
            background-color: #fcfcfc;
        }

        .satellite-button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            padding: 5px 10px;
            margin: 3px;
            cursor: pointer;
            font-size: 0.85em;
            transition: background-color 0.2s ease;
            white-space: nowrap; /* Prevent text wrapping */
        }

        .satellite-button:hover {
            background-color: #0056b3;
        }

        .satellite-button.active {
            background-color: #28a745; /* Green for active */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

    </style>
</head>

<body>
    <header class="d-flex justify-content-between align-items-center p-3 text-white">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('images/Logo_TA.png') }}" alt="Logo" height="40">
            <nav class="menu">
                <ul class="nav">
                    @foreach(['New', 'View','Save'] as $menu)
                        <li class="nav-item dropdown position-relative menu-item">
                            <span class="nav-link dropdown-toggle" role="button">{{ $menu }}</span>
                            <ul class="contextmenu">
                                @switch($menu)
                                    @case('New')
                                        <li id="newSingleMenuBtn">Single</li>
                                        <li id="newConstellationMenuBtn">Constellation</li>
                                        <li id="newGroundStationMenuBtn">Ground Station</li>
                                        <li id="newLinkBudgetMenuBtn">Link Budget</li>
                                        @break
                                    @case('View')
                                        <li id="resetViewBtn">Reset View</li>
                                        <li id="closeViewButton">Close View</li>
                                        <li id="toggle2DViewBtn">2D View</li>
                                        @break
                                    @case('Save')
                                        <li id="showSavePopupBtn">Save TLE</li>
                                        <li id="loadTleBtn">Load TLE</li>
                                        @break
                                @endswitch
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
        <div style="width: 200px;"></div> {{-- Placeholder for spacing --}}
        <div class="d-flex align-items-center gap-2">
            <div class="btn-toolbar" role="toolbar">
                <button type="button" class="btn btn-sm" id="startButton" title="Play Animation"><i class="fas fa-play"></i></button>
                <button type="button" class="btn btn-sm" id="pauseButton" title="Pause Animation"><i class="fas fa-pause"></i></button>
                <button type="button" class="btn btn-sm" id="speedUpButton" title="Speed Up Animation"><i class="fas fa-forward"></i></button>
                <button type="button" class="btn btn-sm" id="slowDownButton" title="Slow Down Animation"><i class="fas fa-backward"></i></button>
                <button type="button" class="btn btn-sm" id="undoButton" title="Undo"><i class="fas fa-undo"></i></button>
                <button type="button" class="btn btn-sm" id="redoButton" title="Redo"><i class="fas fa-redo"></i></button>
            </div>
            <div class="logout-icon">
                <button class="btn btn-outline-light" id="logoutButton" title="Logout"><i class="fas fa-power-off"></i></button>
            </div>
        </div>
    </header>

    <div class="d-flex" style="height: calc(100vh - 80px);">
        <aside class="sidebar d-flex flex-column">
            <div class="nav nav-tabs">
                <button class="nav-link active" id="resourceTabBtn">Resource</button>
                <button class="nav-link" id="outputTabBtn">Output</button>
            </div>
            <div id="resource-menu" class="menu-content flex-grow-1">
                <ul>
                    <li id="satellite-resource-list">Satellite
                        <ul>
                            <li id="single-files-list">Single Files
                                <ul></ul>
                            </li>
                            <li id="constellation-files-list">Constellation Files
                                <ul></ul>
                            </li>
                        </ul>
                    </li>
                    <li id="ground-station-resource-list">Ground Station
                        <ul></ul>
                    </li>
                    <li id="link-budget-resource-list">Link Budget Analysis
                        <ul></ul>
                    </li>
                </ul>
            </div>
        <div id="output-menu" class="menu-content hidden flex-grow-1">
         {{-- ADDED: Container for Satellite List and Data Display --}}
        <div id="satelliteListDisplay" style="padding: 10px; border-top: 1px solid #ccc; margin-top: 10px; background-color: #f8f9fa;">
        <h6 class="text-dark">Active Satellites:</h6>
        <div id="satelliteButtonsContainer" style="display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 10px;">
        </div>
        <div id="satelliteDataDisplay" style="display: none; background-color: #e9ecef; padding: 10px; border-radius: 5px; color: black; font-size: 0.9em;">
            <h6 class="text-dark">Selected Satellite Details:</h6>
            <p><strong>Name:</strong> <span id="dataName"></span></p>
            <p><strong>Altitude:</strong> <span id="dataAltitude"></span> Km</p>
            <p><strong>Orbital Period:</strong> <span id="dataOrbitalPeriod"></span> minutes</p>
            <p><strong>Orbital Velocity:</strong> <span id="dataOrbitalVelocity"></span> km/s</p>
            <p><strong>Position (x,y,z):</strong> <span id="dataPosition"></span> (scene units)</p>
            <p><strong>Inclination:</strong> <span id="dataInclination"></span>°</p>
            <p><strong>Eccentricity:</strong> <span id="dataEccentricity"></span></p>
            <p><strong>RAAN:</strong> <span id="dataRaan"></span>°</p>
            <p><strong>Argument of Perigee:</strong> <span id="dataArgPerigee"></span>°</p>
            <p><strong>True Anomaly:</strong> <span id="dataTrueAnomaly"></span>°</p>
            </div>
            </div>
             <div id="output-actions-container"></div>
        </div>
        </aside>

        <main class="content flex-grow-1 bg-white">
            <div id="earth-container">
                <div id="animationStatusDisplay" class="text-white-50 small">
                    Status: <span id="animationState">Paused</span> | Speed: <span id="animationSpeed">1x</span>
                </div>
                <div id="simulationClockDisplay" class="text-white-50 small">
                    Current Time: <span id="currentSimulatedTime"></span> 
                </div>
            </div>
            <div id="earth2D-container" style="display: none;">
                <canvas id = map-2D-canvas></canvas>
                <div id="animationStatusDisplay2D" class="text-white-50 small">
                    Status: <span id="animationState2D">Paused</span> | Speed: <span id="animationSpeed2D">1x</span>
                </div>
                <div id="simulationClockDisplay2D" class="text-white-50 small">
                    Current Time: <span id="currentSimulatedTime2D"></span> 
                </div>
            </div>
            <div class="zoom-controls">
                <button class="zoom-button" id="zoomInButton">+</button>
                <button class="zoom-button" id="zoomOutButton">-</button>
            </div>
        </main>
    </div>

    {{-- Modals for Input/Output --}}
    <div class="modal fade" id="fileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel"></h5>
                    <button type="button" class="btn-close" id="modalCloseBtn"></button>
                </div>
                <div class="modal-body" id="fileModalBody">
                    {{-- Dynamic content injected here --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="modalFooterCloseBtn">Close</button>
                    <button type="button" class="btn btn-primary" id="fileModalResetBtn" style="display: none;">Reset</button>
                    <button type="button" class="btn btn-primary" id="fileModalSaveBtn">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="linkBudgetOutputModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Link Budget Analysis Output</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="linkBudgetOutputBody">
                    {{-- Dynamic content injected here --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="applyLinkBudgetPreviewBtn">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customAlertModal" tabindex="-1" aria-labelledby="customAlertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-alert-content">
                <div class="modal-header custom-alert-header">
                    <h5 class="modal-title" id="customAlertModalLabel">Peringatan!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body custom-alert-body"></div>
                <div class="modal-footer custom-alert-footer" id="customAlertModalFooter"></div>
            </div>
        </div>
    </div>

    {{-- Popups for Save and others (currently hidden) --}}
    <div class="custom-popup hidden" id="optionsPopup">
        <ul class="list-unstyled bg-white shadow rounded p-2" style="min-width: 150px; position: absolute; z-index: 1060;">
        </ul>
    </div>

    <div class="custom-popup hidden" id="networkConfigPopup">
        <ul class="list-unstyled bg-white shadow rounded p-2" style="min-width: 200px; position: absolute; z-index: 1060;">
        </ul>
    </div>

    <div class="custom-popup hidden" id="documentationPopup">
        <div class="bg-white shadow rounded p-3" style="min-width: 250px; position: absolute; z-index: 1060;">
        </div>
    </div>

    <div class="custom-popup hidden" id="aboutPopup">
        <div class="bg-white shadow rounded p-3" style="min-width: 200px; position: absolute; z-index: 1060;">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script> {{-- GSAP for animations --}}
    <script type="module">

        import { DEG2RAD, EarthRadius, MU_EARTH } from "{{ Vite::asset('resources/js/parametersimulation.js') }}";
        import { solveKepler, E_to_TrueAnomaly, TrueAnomaly_to_E, E_to_M, calculateDerivedOrbitalParameters } from "{{ Vite::asset('resources/js/orbitalCalculation.js') }}";
        import { calculateLinkBudget } from "{{ Vite::asset('resources/js/linkBudgetCalculations.js') }}"; // NEW Import

        // --- Global Variables and Constants for UI Logic ---
        const LOCAL_STORAGE_HISTORY_KEY = 'appHistory';
        const LOCAL_STORAGE_HISTORY_INDEX_KEY = 'appHistoryIndex';
        const LOCAL_STORAGE_FILES_KEY = 'savedFilesData'; // For Single Satellites and Constellations
        const LOCAL_STORAGE_GROUND_STATIONS_KEY = 'savedGroundStationsData';
        const LOCAL_STORAGE_LINK_BUDGETS_KEY = 'savedLinkBudgetsData';
        const MAX_HISTORY_SIZE = 50;
        const SIMULATION_STATE_KEY = 'satelliteSimulationState';
        const FIRST_LOAD_FLAG_KEY = 'satelliteSimulationFirstLoad';

        let appHistory = [];
        let appHistoryIndex = -1;

        // Shared data maps (managed by this inline script)
        let fileOutputs = new Map(); // Stores Single Satellite and Constellation data
        let groundStations = new Map(); // Stores Ground Station data
        let linkBudgetAnalyses = new Map(); // Stores Link Budget Analysis output data

        let editingFileName = null;
        let editingFileType = null;

        // This `is2DViewActive` is local to this script. Earth3Dsimulation.js uses its own flag.
        let is2DViewActive = false; // Keep this global to the inline script


        // Attach functions to the window object so they can be called from HTML onclick attributes
        window.undoOperation = undoOperation;
        window.redoOperation = redoOperation;

       //-----------------------------------------SideBar ----------------------------------------------------------

        // --- FILE STORAGE FUNCTIONS ---
        function saveFilesToLocalStorage() {
            try {
                localStorage.setItem(LOCAL_STORAGE_FILES_KEY, JSON.stringify(Array.from(fileOutputs.entries())));
                localStorage.setItem(LOCAL_STORAGE_GROUND_STATIONS_KEY, JSON.stringify(Array.from(groundStations.entries())));
                localStorage.setItem(LOCAL_STORAGE_LINK_BUDGETS_KEY, JSON.stringify(Array.from(linkBudgetAnalyses.entries())));
            } catch (e) {
                console.error("Error saving files to Local Storage:", e);
            }
        }

        // --- FILE STORAGE FUNCTIONS ---
        function loadFilesFromLocalStorage() {
            try {
                // Unconditionally clear all previous data on every page load/refresh
                console.log("Clearing all simulation data from localStorage on load/refresh.");
                localStorage.removeItem(LOCAL_STORAGE_FILES_KEY);
                localStorage.removeItem(LOCAL_STORAGE_GROUND_STATIONS_KEY);
                localStorage.removeItem(LOCAL_STORAGE_LINK_BUDGETS_KEY);
                localStorage.removeItem(LOCAL_STORAGE_HISTORY_KEY);
                localStorage.removeItem(LOCAL_STORAGE_HISTORY_INDEX_KEY);
                localStorage.removeItem(FIRST_LOAD_FLAG_KEY); // Clear the flag too for absolute reset

                // Reset in-memory data structures. This is crucial for a clean start.
                fileOutputs = new Map();
                groundStations = new Map();
                linkBudgetAnalyses = new Map();
                appHistory = [];
                appHistoryIndex = -1;

                // Since everything is cleared, there's nothing to load initially.
                // No need to return a boolean indicating success of loading existing files,
                // as we are intentionally clearing them.
                // This function's new purpose is to *initialize* a clean state on load.
                return; // No value returned, as it's now a reset function basically.

            } catch (e) {
                console.error("Error during localStorage reset on load:", e);
                // In case of error during reset, ensure data structures are still clean
                fileOutputs = new Map();
                groundStations = new Map();
                linkBudgetAnalyses = new Map();
                appHistory = [];
                appHistoryIndex = -1;
                return;
            }
        }

        // --- RESOURCE SIDEBAR UTILITIES ---
        function addFileToResourceSidebar(fileName, data, fileType) {
            let parentList;
            let listItemText = fileName;

            if (fileType === 'single') {
                parentList = document.querySelector('#single-files-list ul');
            } else if (fileType === 'constellation') {
                parentList = document.querySelector('#constellation-files-list ul');
            } else if (fileType === 'groundStation') {
                parentList = document.querySelector('#ground-station-resource-list ul');
            } else if (fileType === 'linkBudget') {
                parentList = document.querySelector('#link-budget-resource-list ul');
            } else {
                console.error(`Unknown file type: ${fileType}`);
                return;
            }

            if (parentList) {
                const existingItem = document.querySelector(`li[data-file-name="${fileName}"][data-file-type="${fileType}"]`);
                if (existingItem) {
                    existingItem.remove(); // Remove old entry if updating
                }

                const newFileItem = document.createElement('li');
                newFileItem.dataset.fileName = fileName;
                newFileItem.dataset.fileType = fileType;
                newFileItem.textContent = listItemText;

                newFileItem.addEventListener('click', function() {
                    const clickedFileName = this.dataset.fileName;
                    const clickedFileType = this.dataset.fileType;
                    let dataForButtons; // Data to pass to updateOutputSidebar for buttons

                    if (clickedFileType === 'single' || clickedFileType === 'constellation') {
                        const satData = fileOutputs.get(clickedFileName);
                        if (satData) {
                            window.selectSatellite(clickedFileName); // Show real-time satellite data
                            dataForButtons = satData; // Use satellite data for buttons
                        }
                    } else if (clickedFileType === 'groundStation') {
                        const gsData = groundStations.get(clickedFileName);
                        if (gsData) {
                            window.selectGroundStation(clickedFileName); // Handle ground station selection
                            dataForButtons = gsData; // Use ground station data for buttons
                        }
                    } else if (clickedFileType === 'linkBudget') {
                        const lbData = linkBudgetAnalyses.get(clickedFileName);
                        if (lbData) {
                            // Link budget analysis is typically shown in a separate modal,
                            // but we still want its buttons to appear.
                            window.selectedSatelliteId = null; // Ensure no satellite is selected for real-time display
                            document.getElementById("satelliteDataDisplay").style.display = 'none'; // Hide satellite display
                            dataForButtons = lbData; // Use link budget data for buttons
                        }
                    }
                    // Always update the output sidebar with buttons for the clicked item
                    updateOutputSidebar(dataForButtons); // Pass the retrieved data

                    // Switch to the output tab
                    toggleTab('output-menu', document.querySelector('.nav-tabs .nav-link[onclick*="output-menu"]'));
                });

                newFileItem.addEventListener('dblclick', function() {
                    const clickedFileName = this.dataset.fileName;
                    const clickedFileType = this.dataset.fileType;

                    if (clickedFileType === 'single') {
                        editSingleParameter(clickedFileName);
                    } else if (clickedFileType === 'constellation') {
                        editConstellationParameter(clickedFileName);
                    } else if (clickedFileType === 'groundStation') {
                        editGroundStation(clickedFileName);
                    }
                    // Link Budget analysis output is not edited via double click
                });

                parentList.appendChild(newFileItem);
            } else {
                console.error(`Resource list for ${fileType} files not found.`);
            }
        }

        // --- OUTPUT SIDEBAR UTILITIES ---
        function updateOutputSidebar(data) {
            const outputMenu = document.getElementById('output-menu');
            if (!outputMenu) {
            console.error("Element with ID 'output-menu' not found.");
            return;
            }
            // Target the specific container for action buttons
            const outputActionsContainer = document.getElementById('output-actions-container');
            if (!outputActionsContainer) {
                console.error("Element with ID 'output-actions-container' not found.");
                return;
            }
            outputActionsContainer.innerHTML = ''; // Always clear previous buttons

            // If no data (e.g., no item selected, or data deleted), hide satellite data display and don't show buttons.
            if (!data) {
                document.getElementById("satelliteDataDisplay").style.display = 'none';
                return;
            }
            // Add Edit/Delete/View buttons dynamically
            const actionContainer = document.createElement('div');
            actionContainer.classList.add('output-actions', 'mt-3', 'mb-2');

            // Create and append the View, Edit, and Delete buttons
            const viewButton = document.createElement('button');
            viewButton.textContent = 'View Simulation';
            viewButton.classList.add('btn', 'btn-sm', 'btn-info', 'me-2');
            viewButton.onclick = () => window.viewSimulation(data);
            outputActionsContainer.appendChild(viewButton);

            const editButton = document.createElement('button');
            editButton.textContent = 'Edit';
            editButton.classList.add('btn', 'btn-sm', 'btn-primary', 'me-2');
            if (data.fileType === 'single') {
                editButton.onclick = () => editSingleParameter(data.fileName);
            } else if (data.fileType === 'constellation') {
                editButton.onclick = () => editConstellationParameter(data.fileName);
            } else if (data.fileType === 'groundStation') {
                editButton.onclick = () => editGroundStation(data.name);
            } else {
                editButton.style.display = 'none'; // No direct edit for link budget analysis output
            }
            outputActionsContainer.appendChild(editButton);

            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Delete';
            deleteButton.classList.add('btn', 'btn-sm', 'btn-danger');
            deleteButton.onclick = () => deleteFile(data.fileName || data.name, data.fileType);
            outputActionsContainer.appendChild(deleteButton);

            outputMenu.appendChild(actionContainer);
        }

        // --- GENERAL MODAL AND ALERT FUNCTIONS ---
        window.showCustomConfirmation = showCustomConfirmation;
        window.showCustomAlert = showCustomAlert;
        window.toggleTab = toggleTab; // Added
        window.closepopup = closepopup; // Added
        window.formatNumberInput = formatNumberInput; // Added if it's used elsewhere in HTML (it is in `showModal` helper)
        window.showInputError = showInputError; // Added as it's used within your script
        window.clearInputError = clearInputError; // Added as it's used within your script

        function showCustomConfirmation(message, title = 'Konfirmasi', confirmButtonText = 'OK', onConfirmCallback, showCancelButton = false) {
            document.getElementById('customAlertModalLabel').textContent = title;
            document.querySelector('#customAlertModal .modal-body').innerHTML = `<p>${message}</p>`;

            const footer = document.getElementById('customAlertModalFooter');
            footer.innerHTML = '';

            const confirmButton = document.createElement('button');
            confirmButton.type = 'button';
            confirmButton.classList.add('btn', 'btn-primary', 'custom-alert-ok-btn');
            confirmButton.textContent = confirmButtonText;
            confirmButton.onclick = () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('customAlertModal'));
                if (modal) modal.hide();
                if (onConfirmCallback) {
                    onConfirmCallback();
                }
            };
            footer.appendChild(confirmButton);

            if (showCancelButton) {
                const cancelButton = document.createElement('button');
                cancelButton.type = 'button';
                cancelButton.classList.add('btn', 'btn-secondary');
                cancelButton.textContent = 'Cancel';
                cancelButton.setAttribute('data-bs-dismiss', 'modal');
                footer.appendChild(cancelButton);
            }

            const customAlert = new bootstrap.Modal(document.getElementById('customAlertModal'));
            customAlert.show();
        }

        function showCustomAlert(message, title = 'Peringatan!') {
            showCustomConfirmation(message, title, 'OK', null, false);
        }

        function showInputError(inputId, message) {
            let inputElement = document.getElementById(inputId);
            if (!inputElement) {
                console.error(`Input element with ID '${inputId}' not found.`);
                return;
            }
            let errorElement = document.getElementById(inputId + 'Error');
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.id = inputId + 'Error';
                errorElement.classList.add('text-danger', 'mt-1', 'small');
                inputElement.parentNode.appendChild(errorElement);
            }
            errorElement.textContent = message;
            inputElement.classList.add('is-invalid');
        }

        function clearInputError(inputId) {
            let inputElement = document.getElementById(inputId);
            if (!inputElement) return;
            let errorElement = document.getElementById(inputId + 'Error');
            if (errorElement) {
                errorElement.remove();
            }
            inputElement.classList.remove('is-invalid');
        }

        function toggleTab(id, btn) {
            document.querySelectorAll('.menu-content').forEach(div => div.classList.add('hidden'));
            document.getElementById(id).classList.remove('hidden');
            document.querySelectorAll('.nav-tabs .nav-link').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            // When switching to output tab, refresh satellite list display
            if (id === 'output-menu') {
                updateSatelliteListUI();
            }
        }

        function formatNumberInput(value) {
            return String(value).replace(/,/g, '.');
        }

        function showModal(title, bodyHTML, onSave, onReset = null, fileNameToEdit = null, fileTypeToEdit = null) {
        document.getElementById('fileModalLabel').textContent = title;
        document.getElementById('fileModalBody').innerHTML = bodyHTML;
        const modalElement = document.getElementById('fileModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        const applyBtn = document.getElementById('fileModalSaveBtn');
        const resetBtn = document.getElementById('fileModalResetBtn');

        applyBtn.textContent = 'Apply';
        applyBtn.onclick = null;
        applyBtn.onclick = function () {
            const inputs = document.querySelectorAll('#fileModalBody input');
            inputs.forEach(input => clearInputError(input.id));
            const success = onSave(); // Create the simulation
            if (success) {
                modal.hide();
            }
        };

        if (onReset) {
            resetBtn.style.display = 'inline-block';
            resetBtn.onclick = null;
            resetBtn.onclick = onReset;
        } else {
            resetBtn.style.display = 'none';
        }

        editingFileName = fileNameToEdit;
        editingFileType = fileTypeToEdit;

        if (fileNameToEdit && fileTypeToEdit) {
            let data;
            if (fileTypeToEdit === 'single' || fileTypeToEdit === 'constellation') {
                data = fileOutputs.get(fileNameToEdit);
            } else if (fileTypeToEdit === 'groundStation') {
                data = groundStations.get(fileNameToEdit);
            } else if (fileTypeToEdit === 'linkBudget') {
                data = linkBudgetAnalyses.get(fileNameToEdit);
            }

            if (data) {
                const fileNameInput = document.getElementById('fileNameInput') || document.getElementById('gsNameInput') || document.getElementById('lbNameInput');
                if (fileNameInput) {
                    fileNameInput.value = data.fileName || data.name;
                    fileNameInput.readOnly = true;
                }
                if (document.getElementById('altitudeInput')) document.getElementById('altitudeInput').value = formatNumberInput(data.altitude);
                if (document.getElementById('inclinationInput')) document.getElementById('inclinationInput').value = formatNumberInput(data.inclination);
                
                if (document.getElementById('eccentricityCircular')) {
                    if (data.eccentricity == 0) {
                        document.getElementById('eccentricityCircular').checked = true;
                        toggleEccentricityInput('circular');
                    } else {
                        document.getElementById('eccentricityElliptical').checked = true;
                        toggleEccentricityInput('elliptical');
                        document.getElementById('eccentricityValueInput').value = formatNumberInput(data.eccentricity);
                    }
                }

                if (document.getElementById('raanInput')) document.getElementById('raanInput').value = formatNumberInput(data.raan);
                if (document.getElementById('argumentOfPerigeeInput')) document.getElementById('argumentOfPerigeeInput').value = formatNumberInput(data.argumentOfPerigee);
                if (document.getElementById('trueAnomalyInput')) document.getElementById('trueAnomalyInput').value = formatNumberInput(data.trueAnomaly);
                if (document.getElementById('epochInput')) document.getElementById('epochInput').value = data.epoch;
                if (document.getElementById('beamwidthInput')) document.getElementById('beamwidthInput').value = formatNumberInput(data.beamwidth);

                if (document.getElementById('latitudeInput')) document.getElementById('latitudeInput').value = formatNumberInput(data.latitude);
                if (document.getElementById('longitudeInput')) document.getElementById('longitudeInput').value = formatNumberInput(data.longitude);
                if (document.getElementById('minElevationAngleInput')) document.getElementById('minElevationAngleInput').value = formatNumberInput(data.minElevationAngle);

                if (data.constellationType) {
                    if (data.constellationType === 'train') {
                        document.getElementById('constellationTypeTrain').checked = true;
                        toggleConstellationType('train');
                        document.getElementById('numSatellitesInput').value = data.numSatellites;
                        document.getElementById('separationTypeMeanAnomaly').checked = data.separationType === 'meanAnomaly';
                        document.getElementById('separationTypeTime').checked = data.separationType === 'time';
                        document.getElementById('separationValueInput').value = formatNumberInput(data.separationValue);
                    } else if (data.constellationType === 'walker') {
                        document.getElementById('constellationTypeWalker').checked = true;
                        toggleConstellationType('walker');
                        const walkerDirectionForward = document.getElementById('walkerDirectionForward');
                        if (walkerDirectionForward) walkerDirectionForward.checked = data.direction === 'forward';
                        const walkerDirectionBackward = document.getElementById('walkerDirectionBackward');
                        if (walkerDirectionBackward) walkerDirectionBackward.checked = data.direction === 'backward';
                        const walkerStartLocationSame = document.getElementById('walkerStartLocationSame');
                        if (walkerStartLocationSame) walkerStartLocationSame.checked = data.startLocation === 'same';
                        const walkerStartLocationOffset = document.getElementById('walkerStartLocationOffset');
                        if (walkerStartLocationOffset) walkerStartLocationOffset.checked = data.startLocation === 'offset';

                        if (data.startLocation === 'offset') {
                            toggleWalkerOffset(true);
                            const walkerOffsetTypeMeanAnomaly = document.getElementById('walkerOffsetTypeMeanAnomaly');
                            if (walkerOffsetTypeMeanAnomaly) walkerOffsetTypeMeanAnomaly.checked = data.offsetType === 'meanAnomaly';
                            const walkerOffsetTypeTrueAnomaly = document.getElementById('walkerOffsetTypeTrueAnomaly');
                            if (walkerOffsetTypeTrueAnomaly) walkerOffsetTypeTrueAnomaly.checked = data.offsetType === 'trueAnomaly';
                            const walkerOffsetTypeTime = document.getElementById('walkerOffsetTypeTime');
                            if (walkerOffsetTypeTime) walkerOffsetTypeTime.checked = data.offsetType === 'time';
                            document.getElementById('walkerOffsetValue').value = formatNumberInput(data.offsetValue);
                        }
                    }
                }

                // Set UTC offset dropdown to current window.utcOffset when editing
                const utcOffsetInput = document.getElementById('utcOffsetInput');
                if (utcOffsetInput) {
                    utcOffsetInput.value = window.utcOffset || 0;
                }
            }
        } else {
            const fileNameInput = document.getElementById('fileNameInput') || document.getElementById('gsNameInput') || document.getElementById('lbNameInput');
            if (fileNameInput) {
                fileNameInput.readOnly = false;
            }

            // For new entries, set UTC offset to default value of 0
            const utcOffsetInput = document.getElementById('utcOffsetInput');
            if (utcOffsetInput) {
                utcOffsetInput.value = 0;
            }
        }
    }

        function closepopup() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('fileModal'));
            if (modal) {
                modal.hide();
            }
            const inputs = document.querySelectorAll('#fileModalBody input');
            inputs.forEach(input => clearInputError(input.id));
            editingFileName = null;
            editingFileType = null;
        }



        // --- NEW MENU FUNCTIONS --- 
        window.NewSingleMenu = NewSingleMenu;
        window.toggleEccentricityInput = toggleEccentricityInput;
        window.NewConstellationMenu = NewConstellationMenu;
        window.toggleConstellationType = toggleConstellationType;
        window.toggleTrainOffset = toggleTrainOffset;
        window.NewGroundStationMenu = NewGroundStationMenu;
        window.NewLinkBudgetMenu = NewLinkBudgetMenu;
        window.showLinkBudgetOutput = showLinkBudgetOutput;

        function NewSingleMenu() {
            const initialBody = `
                <div class="mb-3">
                    <label for="fileNameInput" class="form-label">Satellite Name</label>
                    <input type="text" class="form-control" id="fileNameInput">
                </div>
                <div class="mb-3">
                    <label for="altitudeInput" class="form-label">Altitude (Km)</label>
                    <input type="number" class="form-control" id="altitudeInput" min="100" max="36000">
                </div>
                <div class="mb-3">
                    <label for="inclinationInput" class="form-label">Inclination (degree)</label>
                    <input type="number" class="form-control" id="inclinationInput" min="0" max="180">
                </div>
                <div class="mb-3">
                    <label class="form-label">Eccentricity</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="eccentricityType" id="eccentricityCircular" value="circular" checked onchange="toggleEccentricityInput('circular')">
                        <label class="form-check-label" for="eccentricityCircular">Circular</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="eccentricityType" id="eccentricityElliptical" value="elliptical" onchange="toggleEccentricityInput('elliptical')">
                        <label class="form-check-label" for="eccentricityElliptical">Elliptical</label>
                    </div>
                    <div id="eccentricityValueContainer" class="mt-2" style="display: none;">
                        <label for="eccentricityValueInput" class="form-label">Eccentricity Value (0-1)</label>
                        <input type="number" class="form-control" id="eccentricityValueInput" min="0" max="1" step="0.0001">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="raanInput" class="form-label">RAAN (degree)</label>
                    <input type="number" class="form-control" id="raanInput" min="0" max="360">
                </div>
                <div class="mb-3" id="argumentOfPerigeeContainer" style="display: none;">
                    <label for="argumentOfPerigeeInput" class="form-label">Argument of Perigee (degree)</label>
                    <input type="number" class="form-control" id="argumentOfPerigeeInput" min="0" max="360">
                </div>
                <div class="mb-3">
                    <label for="trueAnomalyInput" class="form-label">True Anomaly (degree)</label>
                    <input type="number" class="form-control" id="trueAnomalyInput" min="0" max="360">
                </div>
                <div class="mb-3">
                    <label for="epochInput" class="form-label">Epoch</label>
                    <input type="datetime-local" class="form-control" id="epochInput">
                </div>
                <div class="mb-3">
                    <label for="utcOffsetInput" class="form-label">UTC Offset</label>
                    <select class="form-control" id="utcOffsetInput">
                        <option value="0" selected>UTC+0</option>
                        <option value="1">UTC+1</option>
                        <option value="2">UTC+2</option>
                        <option value="3">UTC+3</option>
                        <option value="4">UTC+4</option>
                        <option value="5">UTC+5</option>
                        <option value="6">UTC+6</option>
                        <option value="7">UTC+7</option>
                        <option value="8">UTC+8</option>
                        <option value="9">UTC+9</option>
                        <option value="10">UTC+10</option>
                        <option value="11">UTC+11</option>
                        <option value="12">UTC+12</option>
                        <option value="13">UTC+13</option>
                        <option value="14">UTC+14</option>
                        <option value="-1">UTC-1</option>
                        <option value="-2">UTC-2</option>
                        <option value="-3">UTC-3</option>
                        <option value="-4">UTC-4</option>
                        <option value="-5">UTC-5</option>
                        <option value="-6">UTC-6</option>
                        <option value="-7">UTC-7</option>
                        <option value="-8">UTC-8</option>
                        <option value="-9">UTC-9</option>
                        <option value="-10">UTC-10</option>
                        <option value="-11">UTC-11</option>
                        <option value="-12">UTC-12</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="beamwidthInput" class="form-label">Beamwidth (degree)</label>
                    <input type="number" class="form-control" id="beamwidthInput" min="0" max="90">
                </div>`;

            showModal("Single Satellite Input", initialBody, () => {
                let hasError = false;
                const fileName = document.getElementById('fileNameInput').value.trim();
                const eccentricityType = document.querySelector('input[name="eccentricityType"]:checked').value;
                let eccentricity = 0;
                if (eccentricityType === 'elliptical') {
                    const eccValue = document.getElementById('eccentricityValueInput').value;
                    eccentricity = parseFloat(formatNumberInput(eccValue));
                }

                if (!fileName) { showInputError('fileNameInput', "Satellite Name cannot be empty."); hasError = true; }
                else if (!editingFileName && fileOutputs.has(fileName)) {
                    showInputError('fileNameInput', `Satellite Name "${fileName}" already exists. Please use a different name.`); hasError = true;
                } else { clearInputError('fileNameInput'); }

                const inputs = [
                    { id: 'altitudeInput', min: 100, max: 36000, name: 'Altitude' },
                    { id: 'inclinationInput', min: 0, max: 180, name: 'Inclination' },
                    { id: 'raanInput', min: 0, max: 360, name: 'RAAN' },
                    { id: 'trueAnomalyInput', min: 0, max: 360, name: 'True Anomaly' },
                    { id: 'beamwidthInput', min: 0, max: 90, name: 'Beamwidth' }
                ];

                if (eccentricityType === 'elliptical') {
                    inputs.push({ id: 'argumentOfPerigeeInput', min: 0, max: 360, name: 'Argument of Perigee' });
                    if (isNaN(eccentricity) || eccentricity < 0 || eccentricity > 1) {
                        showInputError('eccentricityValueInput', `Eccentricity must be between 0 and 1.`); hasError = true;
                    } else { clearInputError('eccentricityValueInput'); }
                }

                const values = {};
                inputs.forEach(input => {
                    const rawValue = document.getElementById(input.id).value;
                    const formattedValue = formatNumberInput(rawValue);
                    const value = parseFloat(formattedValue);

                    if (isNaN(value)) { showInputError(input.id, `${input.name} must be a number.`); hasError = true; }
                    else if (value < input.min || value > input.max) { showInputError(input.id, `Input must be between ${input.min} and ${input.max}.`); hasError = true; }
                    else { clearInputError(input.id); values[input.id.replace('Input', '')] = value; }
                });

                const epochInput = document.getElementById('epochInput').value;
                if (!epochInput) { showInputError('epochInput', "Epoch cannot be empty."); hasError = true; }
                else { clearInputError('epochInput'); }

                if (hasError) { return false; }

                // Convert the input to UTC timestamp with offset
                const utcOffset = parseInt(document.getElementById('utcOffsetInput').value);
                window.utcOffset = utcOffset; // Add this line
                const [datePart, timePart] = epochInput.split('T');
                const [year, month, day] = datePart.split('-').map(Number);
                const [hour, minute] = timePart.split(':').map(Number);
                const utcTimestamp = Date.UTC(year, month - 1, day, hour - utcOffset, minute, 0);
                window.currentEpochUTC = utcTimestamp;

                const newData = {
                    fileName, altitude: values.altitude, inclination: values.inclination,
                    eccentricity: eccentricity, raan: values.raan,
                    argumentOfPerigee: eccentricityType === 'elliptical' ? values.argumentOfPerigee : 0,
                    trueAnomaly: values.trueAnomaly,
                    epoch: epochInput, // This is the string epoch for storage
                    beamwidth: values.beamwidth,
                    fileType: 'single'
                };

                if (editingFileName) {
                    const oldData = { ...fileOutputs.get(editingFileName) };
                    recordAction({ type: 'editFile', fileName: editingFileName, fileType: 'single', oldData: oldData, newData: newData });
                    fileOutputs.set(editingFileName, newData);
                } else {
                    recordAction({ type: 'addFile', fileName: fileName, fileData: newData, fileType: 'single' });
                    fileOutputs.set(fileName, newData);
                }

                saveFilesToLocalStorage();
                if (window.addOrUpdateSatelliteInScene) {
                    window.addOrUpdateSatelliteInScene(newData);
                    window.selectedSatelliteId = newData.fileName;
                    window.isAnimating = false;
                    setActiveControlButton('startButton');
                }

                updateOutputSidebar(newData);
                addFileToResourceSidebar(fileName, newData, 'single');
                updateSatelliteListUI();
                selectSatellite(newData.fileName);
                return true;
            }, () => {
                document.getElementById('fileNameInput').value = '';
                document.getElementById('altitudeInput').value = '';
                document.getElementById('inclinationInput').value = '';
                document.getElementById('eccentricityCircular').checked = true;
                toggleEccentricityInput('circular');
                document.getElementById('raanInput').value = '';
                document.getElementById('argumentOfPerigeeInput').value = '';
                document.getElementById('trueAnomalyInput').value = '';
                document.getElementById('epochInput').value = '';
                document.getElementById('utcOffsetInput').value = '0';
                document.getElementById('beamwidthInput').value = '';
                const inputs = document.querySelectorAll('#fileModalBody input');
                inputs.forEach(input => clearInputError(input.id));
            }, editingFileName, 'single');

            const initialEccentricityType = document.querySelector('input[name="eccentricityType"]:checked')?.value;
            if (initialEccentricityType === 'elliptical') {
                toggleEccentricityInput('elliptical');
            } else {
                toggleEccentricityInput('circular');
            }
        }

        function toggleEccentricityInput(type) {
            const eccValueContainer = document.getElementById('eccentricityValueContainer');
            const argPerigeeContainer = document.getElementById('argumentOfPerigeeContainer');
            if (eccValueContainer && argPerigeeContainer) {
                if (type === 'elliptical') {
                    eccValueContainer.style.display = 'block';
                    argPerigeeContainer.style.display = 'block';
                } else {
                    eccValueContainer.style.display = 'none';
                    argPerigeeContainer.style.display = 'none';
                    if (document.getElementById('eccentricityValueInput')) document.getElementById('eccentricityValueInput').value = '0';
                    if (document.getElementById('argumentOfPerigeeInput')) document.getElementById('argumentOfPerigeeInput').value = '0';
                }
            }
        }

        function NewConstellationMenu() {
            const initialBody = `
                <div class="mb-3">
                    <label for="fileNameInput" class="form-label">Constellation Name</label>
                    <input type="text" class="form-control" id="fileNameInput">
                </div>
                <div class="mb-3">
                    <label for="altitudeInput" class="form-label">Altitude (Km)</label>
                    <input type="number" class="form-control" id="altitudeInput" min="100" max="36000">
                </div>
                <div class="mb-3">
                    <label for="inclinationInput" class="form-label">Inclination (degree)</label>
                    <input type="number" class="form-control" id="inclinationInput" min="0" max="180">
                </div>
                <div class="mb-3">
                    <label class="form-label">Eccentricity</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="eccentricityType" id="eccentricityCircular" value="circular" checked onchange="toggleEccentricityInput('circular')">
                        <label class="form-check-label" for="eccentricityCircular">Circular</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="eccentricityType" id="eccentricityElliptical" value="elliptical" onchange="toggleEccentricityInput('elliptical')">
                        <label class="form-check-label" for="eccentricityElliptical">Elliptical</label>
                    </div>
                    <div id="eccentricityValueContainer" class="mt-2" style="display: none;">
                        <label for="eccentricityValueInput" class="form-label">Eccentricity Value (0-1)</label>
                        <input type="number" class="form-control" id="eccentricityValueInput" min="0" max="1" step="0.0001">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="raanInput" class="form-label">RAAN (degree)</label>
                    <input type="number" class="form-control" id="raanInput" min="0" max="360">
                </div>
                <div class="mb-3" id="argumentOfPerigeeContainer" style="display: none;">
                    <label for="argumentOfPerigeeInput" class="form-label">Argument of Perigee (degree)</label>
                    <input type="number" class="form-control" id="argumentOfPerigeeInput" min="0" max="360">
                </div>
                <div class="mb-3">
                    <label for="trueAnomalyInput" class="form-label">True Anomaly (degree)</label>
                    <input type="number" class="form-control" id="trueAnomalyInput" min="0" max="360">
                </div>
                <div class="mb-3">
                    <label for="epochInput" class="form-label">Epoch</label>
                    <input type="datetime-local" class="form-control" id="epochInput">
                </div>
                <div class="mb-3">
                    <label for="utcOffsetInput" class="form-label">UTC Offset</label>
                    <select class="form-control" id="utcOffsetInput">
                        <option value="0" selected>UTC+0</option>
                        <option value="1">UTC+1</option>
                        <option value="2">UTC+2</option>
                        <option value="3">UTC+3</option>
                        <option value="4">UTC+4</option>
                        <option value="5">UTC+5</option>
                        <option value="6">UTC+6</option>
                        <option value="7">UTC+7</option>
                        <option value="8">UTC+8</option>
                        <option value="9">UTC+9</option>
                        <option value="10">UTC+10</option>
                        <option value="11">UTC+11</option>
                        <option value="12">UTC+12</option>
                        <option value="13">UTC+13</option>
                        <option value="14">UTC+14</option>
                        <option value="-1">UTC-1</option>
                        <option value="-2">UTC-2</option>
                        <option value="-3">UTC-3</option>
                        <option value="-4">UTC-4</option>
                        <option value="-5">UTC-5</option>
                        <option value="-6">UTC-6</option>
                        <option value="-7">UTC-7</option>
                        <option value="-8">UTC-8</option>
                        <option value="-9">UTC-9</option>
                        <option value="-10">UTC-10</option>
                        <option value="-11">UTC-11</option>
                        <option value="-12">UTC-12</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="beamwidthInput" class="form-label">Beamwidth (degree)</label>
                    <input type="number" class="form-control" id="beamwidthInput" min="0" max="90">
                </div>
                <hr>
                <h6 class="mt-4 mb-3">Constellation Type</h6>
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="constellationType" id="constellationTypeTrain" value="train" checked>
                        <label class="form-check-label" for="constellationTypeTrain">Train</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="constellationType" id="constellationTypeWalker" value="walker">
                        <label class="form-check-label" for="constellationTypeWalker">Walker</label>
                    </div>
                </div>
                <div id="trainConstellationFields">
                    <div class="mb-3">
                        <label for="numSatellitesInput" class="form-label">Number of Satellites</label>
                        <input type="number" class="form-control" id="numSatellitesInput" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Separation Type</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="separationType" id="separationTypeMeanAnomaly" value="meanAnomaly" checked>
                            <label class="form-check-label" for="separationTypeMeanAnomaly">Mean Anomaly (Degrees)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="separationType" id="separationTypeTime" value="time">
                            <label class="form-check-label" for="separationTypeTime">Time (Seconds)</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="separationValueInput" class="form-label">Separation Value</label>
                        <input type="number" class="form-control" id="separationValueInput" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Direction</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="trainDirection" id="trainDirectionForward" value="forward" checked>
                            <label class="form-check-label" for="trainDirectionForward">Forward</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="trainDirection" id="trainDirectionBackward" value="backward">
                            <label class="form-check-label" for="trainDirectionBackward">Backward</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Location</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="trainStartLocation" id="trainStartLocationSame" value="same" checked>
                            <label class="form-check-label" for="trainStartLocationSame">Same as Seed</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="trainStartLocation" id="trainStartLocationOffset" value="offset">
                            <label class="form-check-label" for="trainStartLocationOffset">Offset from Seed</label>
                        </div>
                    </div>
                    <div id="trainOffsetFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Offset Type</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="trainOffsetType" id="trainOffsetTypeMeanAnomaly" value="meanAnomaly" checked>
                                <label class="form-check-label" for="trainOffsetTypeMeanAnomaly">Mean Anomaly</label>
                            </div>
                            <div v class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="trainOffsetType" id="trainOffsetTypeTrueAnomaly" value="trueAnomaly">
                                <label class="form-check-label" for="trainOffsetTypeTrueAnomaly">True Anomaly</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="trainOffsetType" id="trainOffsetTypeTime" value="time">
                                <label class="form-check-label" for="trainOffsetTypeTime">Time (s)</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="trainOffsetValue" class="form-label">Offset Value</label>
                            <input type="number" class="form-control" id="trainOffsetValue" min="0">
                        </div>
                    </div>
                </div>
                <div id="walkerConstellationFields" style="display: none;">
                    <div class="mb-3">
                        <label for="numPlanesInput" class="form-label">Number of Planes</label>
                        <input type="number" class="form-control" id="numPlanesInput" min="1">
                    </div>
                    <div class="mb-3">
                        <label for="satellitesPerPlaneInput" class="form-label">Satellites per Plane</label>
                        <input type="number" class="form-control" id="satellitesPerPlaneInput" min="1">
                    </div>
                    <div class="mb-3">
                        <label for="raanSpreadInput" class="form-label">RAAN Spread</label>
                        <input type="number" class="form-control" id="raanSpreadInput" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="phasingFactorInput" class="form-label">Phasing Factor</label>
                        <input type="number" class="form-control" id="phasingFactorInput" min="0">
                    </div>
                </div>`;

            showModal("Constellation Parameters", initialBody, () => {
                let hasError = false;
                const fileName = document.getElementById('fileNameInput').value.trim();
                const eccentricityType = document.querySelector('input[name="eccentricityType"]:checked').value;
                let eccentricity = 0;
                if (eccentricityType === 'elliptical') {
                    const eccValue = document.getElementById('eccentricityValueInput').value;
                    eccentricity = parseFloat(formatNumberInput(eccValue));
                }

                if (!fileName) { showInputError('fileNameInput', "Constellation Name cannot be empty."); hasError = true; }
                else if (!editingFileName && fileOutputs.has(fileName)) {
                    showInputError('fileNameInput', `Constellation Name "${fileName}" already exists. Please use a different name.`); hasError = true;
                } else { clearInputError('fileNameInput'); }

                const inputs = [
                    { id: 'altitudeInput', min: 100, max: 36000, name: 'Altitude' },
                    { id: 'inclinationInput', min: 0, max: 180, name: 'Inclination' },
                    { id: 'raanInput', min: 0, max: 360, name: 'RAAN' },
                    { id: 'trueAnomalyInput', min: 0, max: 360, name: 'True Anomaly' },
                    { id: 'beamwidthInput', min: 0, max: 90, name: 'Beamwidth' }
                ];

                if (eccentricityType === 'elliptical') {
                    inputs.push({ id: 'argumentOfPerigeeInput', min: 0, max: 360, name: 'Argument of Perigee' });
                    if (isNaN(eccentricity) || eccentricity < 0 || eccentricity > 1) {
                        showInputError('eccentricityValueInput', `Eccentricity must be between 0 and 1.`); hasError = true;
                    } else { clearInputError('eccentricityValueInput'); }
                }

                const values = {};
                inputs.forEach(input => {
                    const rawValue = document.getElementById(input.id).value;
                    const formattedValue = formatNumberInput(rawValue);
                    const value = parseFloat(formattedValue);

                    if (isNaN(value)) { showInputError(input.id, `${input.name} must be a number.`); hasError = true; }
                    else if (value < input.min || value > input.max) { showInputError(input.id, `Input must be between ${input.min} and ${input.max}.`); hasError = true; }
                    else { clearInputError(input.id); values[input.id.replace('Input', '')] = value; }
                });

                const epochInput = document.getElementById('epochInput').value;
                if (!epochInput) { showInputError('epochInput', "Epoch cannot be empty."); hasError = true; }
                else { clearInputError('epochInput'); }

                const constellationType = document.querySelector('input[name="constellationType"]:checked').value;
                const constellationData = { constellationType };

                if (constellationType === 'train') {
                    const numSatellites = parseInt(document.getElementById('numSatellitesInput').value);
                    const separationType = document.querySelector('input[name="separationType"]:checked').value;
                    const separationValue = parseFloat(formatNumberInput(document.getElementById('separationValueInput').value));
                    const trainDirection = document.querySelector('input[name="trainDirection"]:checked').value;
                    const trainStartLocation = document.querySelector('input[name="trainStartLocation"]:checked').value;

                    if (isNaN(numSatellites) || numSatellites < 1) { showInputError('numSatellitesInput', 'Number of Satellites must be at least 1.'); hasError = true; }
                    else { clearInputError('numSatellitesInput'); }

                    if (isNaN(separationValue) || separationValue < 0) { showInputError('separationValueInput', 'Separation Value must be a non-negative number.'); hasError = true; }
                    else { clearInputError('separationValueInput'); }

                    Object.assign(constellationData, { numSatellites, separationType, separationValue, trainDirection, trainStartLocation });

                    if (trainStartLocation === 'offset') {
                        const trainOffsetType = document.querySelector('input[name="trainOffsetType"]:checked').value;
                        const trainOffsetValue = parseFloat(formatNumberInput(document.getElementById('trainOffsetValue').value));

                        if (isNaN(trainOffsetValue)) { showInputError('trainOffsetValue', 'Offset Value must be a number.'); hasError = true; }
                        else { clearInputError('trainOffsetValue'); }
                        Object.assign(constellationData, { trainOffsetType, trainOffsetValue });
                    }
                } else if (constellationType === 'walker') {
                    const numPlanes = parseInt(document.getElementById('numPlanesInput').value);
                    const satellitesPerPlane = parseInt(document.getElementById('satellitesPerPlaneInput').value);
                    const raanSpread = parseFloat(formatNumberInput(document.getElementById('raanSpreadInput').value));
                    const phasingFactor = parseFloat(formatNumberInput(document.getElementById('phasingFactorInput').value));

                    if (isNaN(numPlanes) || numPlanes < 1) { showInputError('numPlanesInput', 'Number of Planes must be at least 1.'); hasError = true; }
                    else { clearInputError('numPlanesInput'); }

                    if (isNaN(satellitesPerPlane) || satellitesPerPlane < 1) { showInputError('satellitesPerPlaneInput', 'Satellites per Plane must be at least 1.'); hasError = true; }
                    else { clearInputError('satellitesPerPlaneInput'); }

                    if (isNaN(raanSpread)) { showInputError('raanSpreadInput', 'RAAN Spread must be a number.'); hasError = true; }
                    else { clearInputError('raanSpreadInput'); }

                    if (isNaN(phasingFactor)) { showInputError('phasingFactorInput', 'Phasing Factor must be a number.'); hasError = true; }
                    else { clearInputError('phasingFactorInput'); }

                    Object.assign(constellationData, { numPlanes, satellitesPerPlane, raanSpread, phasingFactor });
                }

                if (hasError) { return false; }

                // Convert the input to UTC timestamp with offset
                const utcOffset = parseInt(document.getElementById('utcOffsetInput').value);
                window.utcOffset = utcOffset; // Add this line
                const [datePart, timePart] = epochInput.split('T');
                const [year, month, day] = datePart.split('-').map(Number);
                const [hour, minute] = timePart.split(':').map(Number);
                const utcTimestamp = Date.UTC(year, month - 1, day, hour - utcOffset, minute, 0);
                window.currentEpochUTC = utcTimestamp;

                const newData = {
                    fileName, altitude: values.altitude, inclination: values.inclination,
                    eccentricity: eccentricity, raan: values.raan,
                    argumentOfPerigee: eccentricityType === 'elliptical' ? values.argumentOfPerigee : 0,
                    trueAnomaly: values.trueAnomaly, epoch: epochInput,
                    beamwidth: values.beamwidth,
                    fileType: 'constellation',
                    ...constellationData
                };

                if (editingFileName) {
                    const oldData = { ...fileOutputs.get(editingFileName) };
                    recordAction({ type: 'editFile', fileName: editingFileName, fileType: 'constellation', oldData: oldData, newData: newData });
                    fileOutputs.set(editingFileName, newData);
                } else {
                    recordAction({ type: 'addFile', fileName: fileName, fileData: newData, fileType: 'constellation' });
                    fileOutputs.set(fileName, newData);
                }

                saveFilesToLocalStorage();
                if (window.viewSimulation) {
                    window.viewSimulation(newData);
                    window.isAnimating = false;
                    setActiveControlButton('startButton');
                }

                updateOutputSidebar(newData);
                addFileToResourceSidebar(fileName, newData, 'constellation');
                updateSatelliteListUI();
                return true;
            }, () => {
                document.getElementById('fileNameInput').value = '';
                document.getElementById('altitudeInput').value = '';
                document.getElementById('inclinationInput').value = '';
                document.getElementById('eccentricityCircular').checked = true;
                toggleEccentricityInput('circular');
                document.getElementById('raanInput').value = '';
                document.getElementById('argumentOfPerigeeInput').value = '';
                document.getElementById('trueAnomalyInput').value = '';
                document.getElementById('epochInput').value = '';
                document.getElementById('utcOffsetInput').value = '0';
                document.getElementById('beamwidthInput').value = '';
                document.getElementById('constellationTypeTrain').checked = true;
                toggleConstellationType('train');
                document.getElementById('numSatellitesInput').value = '';
                document.getElementById('separationTypeMeanAnomaly').checked = true;
                document.getElementById('separationValueInput').value = '';
                document.getElementById('trainDirectionForward').checked = true;
                document.getElementById('trainStartLocationSame').checked = true;
                toggleTrainOffset(false);
                document.getElementById('numPlanesInput').value = '';
                document.getElementById('satellitesPerPlaneInput').value = '';
                document.getElementById('raanSpreadInput').value = '';
                document.getElementById('phasingFactorInput').value = '';
                const inputs = document.querySelectorAll('#fileModalBody input');
                inputs.forEach(input => clearInputError(input.id));
            }, editingFileName, 'constellation');

            document.getElementById('constellationTypeTrain').addEventListener('change', () => toggleConstellationType('train'));
            document.getElementById('constellationTypeWalker').addEventListener('change', () => toggleConstellationType('walker'));
            document.getElementById('trainStartLocationSame').addEventListener('change', () => toggleTrainOffset(false));
            document.getElementById('trainStartLocationOffset').addEventListener('change', () => toggleTrainOffset(true));

            const initialEccentricityType = document.querySelector('input[name="eccentricityType"]:checked')?.value;
            toggleEccentricityInput(initialEccentricityType);
            const initialConstellationType = document.querySelector('input[name="constellationType"]:checked')?.value;
            toggleConstellationType(initialConstellationType);
            const initialTrainStartLocation = document.querySelector('input[name="trainStartLocation"]:checked')?.value;
            toggleTrainOffset(initialTrainStartLocation === 'offset');
        }

        function toggleConstellationType(type) {
            const trainFields = document.getElementById('trainConstellationFields');
            const walkerFields = document.getElementById('walkerConstellationFields');
            if (type === 'train') {
                trainFields.style.display = 'block';
                walkerFields.style.display = 'none';
                const trainStartLocationOffset = document.getElementById('trainStartLocationOffset');
                toggleTrainOffset(trainStartLocationOffset.checked);
            } else {
                trainFields.style.display = 'none';
                walkerFields.style.display = 'block';
            }
        }

        function toggleTrainOffset(show) {
            const trainOffsetFields = document.getElementById('trainOffsetFields');
            if (show) {
                trainOffsetFields.style.display = 'block';
            } else {
                trainOffsetFields.style.display = 'none';
            }
        }

        function NewGroundStationMenu() {
            const initialBody = `
                <div class="mb-3">
                    <label for="gsNameInput" class="form-label">Ground Station Name</label>
                    <input type="text" class="form-control" id="gsNameInput">
                </div>
                <div class="mb-3">
                    <label for="latitudeInput" class="form-label">Latitude (Degrees: North to South)</label>
                    <input type="number" class="form-control" id="latitudeInput" min="-90" max="90" step="0.0001">
                </div>
                <div class="mb-3">
                    <label for="longitudeInput" class="form-label">Longitude (Degrees)</label>
                    <input type="number" class="form-control" id="longitudeInput" min="-180" max="180" step="0.0001">
                </div>
                <div class="mb-3">
                    <label for="minElevationAngleInput" class="form-label">Minimum Elevation Angle (degree)</label>
                    <input type="number" class="form-control" id="minElevationAngleInput" min="0" max="90" step="0.1">
                </div>`;

            showModal("Ground Station Input", initialBody, () => {
                let hasError = false;
                const gsName = document.getElementById('gsNameInput').value.trim();

                if (!gsName) { showInputError('gsNameInput', "Ground Station Name cannot be empty."); hasError = true; }
                else if (!editingFileName && groundStations.has(gsName)) {
                    showInputError('gsNameInput', `Ground Station Name "${gsName}" already exists. Please use a different name.`); hasError = true;
                } else { clearInputError('gsNameInput'); }

                const inputs = [
                    { id: 'latitudeInput', min: -90, max: 90, name: 'Latitude' },
                    { id: 'longitudeInput', min: -180, max: 180, name: 'Longitude' },
                    { id: 'minElevationAngleInput', min: 0, max: 90, name: 'Minimum Elevation Angle' }
                ];

                const values = {};
                inputs.forEach(input => {
                    const rawValue = document.getElementById(input.id).value;
                    const formattedValue = formatNumberInput(rawValue);
                    const value = parseFloat(formattedValue);

                    if (isNaN(value)) { showInputError(input.id, `${input.name} must be a number.`); hasError = true; }
                    else if (value < input.min || value > input.max) { showInputError(input.id, `Input must be between ${input.min} and ${input.max}.`); hasError = true; }
                    else { clearInputError(input.id); values[input.id.replace('Input', '')] = value; }
                });

                if (hasError) { return false; }

                const newData = {
                    id: gsName,
                    name: gsName,
                    latitude: values.latitude,
                    longitude: values.longitude,
                    minElevationAngle: values.minElevationAngle,
                    altitude: 0,
                    fileType: 'groundStation'
                };

                if (editingFileName) {
                    const oldData = { ...groundStations.get(editingFileName) };
                    recordAction({ type: 'editFile', fileName: editingFileName, fileType: 'groundStation', oldData: oldData, newData: newData });
                    groundStations.set(editingFileName, newData);
                } else {
                    recordAction({ type: 'addFile', fileName: gsName, fileData: newData, fileType: 'groundStation' });
                    groundStations.set(gsName, newData);
                }

                saveFilesToLocalStorage();
                if (window.addOrUpdateGroundStationInScene) {
                    window.addOrUpdateGroundStationInScene(newData);
                }
                updateOutputSidebar(newData);
                addFileToResourceSidebar(gsName, newData, 'groundStation');
                return true;
            }, () => {
                document.getElementById('gsNameInput').value = '';
                document.getElementById('latitudeInput').value = '';
                document.getElementById('longitudeInput').value = '';
                document.getElementById('minElevationAngleInput').value = '';
                const inputs = document.querySelectorAll('#fileModalBody input');
                inputs.forEach(input => clearInputError(input.id));
            }, editingFileName, 'groundStation');
        }

        function NewLinkBudgetMenu() {
            const inputBody = `
                <div class="mb-3">
                    <label for="lbNameInput" class="form-label">Analysis Name</label>
                    <input type="text" class="form-control" id="lbNameInput">
                </div>
                <div class="mb-3">
                    <label for="transmitPowerInput" class="form-label">Transmit Power (dBm)</label>
                    <input type="number" class="form-control" id="transmitPowerInput" step="0.1">
                </div>
                <div class="mb-3">
                    <label for="txAntennaGainInput" class="form-label">Tx Antenna Gain (dBi)</label>
                    <input type="number" class="form-control" id="txAntennaGainInput" step="0.1">
                </div>
                <div class="mb-3">
                    <label for="rxAntennaGainInput" class="form-label">Rx Antenna Gain (dBi)</label>
                    <input type="number" class="form-control" id="rxAntennaGainInput" step="0.1">
                </div>
                <div class="mb-3">
                    <label for="frequencyInput" class="form-label">Frequency (GHz)</label>
                    <input type="number" class="form-control" id="frequencyInput" min="0.1" step="0.1">
                </div>
                <div class="mb-3">
                    <label for="distanceInput" class="form-label">Distance (Km)</label>
                    <input type="number" class="form-control" id="distanceInput" min="100">
                </div>
                <div class="mb-3">
                    <label for="bandwidthInput" class="form-label">Bandwidth (MHz)</label>
                    <input type="number" class="form-control" id="bandwidthInput" min="0.1" step="0.1">
                </div>
                <div class="mb-3">
                    <label for="noiseFigureInput" class="form-label">Noise Figure (dB)</label>
                    <input type="number" class="form-control" id="noiseFigureInput" step="0.1">
                </div>
                <div class="mb-3">
                    <label for="atmosphericLossInput" class="form-label">Atmospheric Loss (dB)</label>
                    <input type="number" class="form-control" id="atmosphericLossInput" min="0" step="0.1">
                </div>
                <div class="mb-3">
                    <label for="orbitHeightInput" class="form-label">Orbit Height (Km)</label>
                    <input type="number" class="form-control" id="orbitHeightInput" min="100" max="36000">
                </div>
                <div class="mb-3">
                    <label for="elevationAngleInput" class="form-label">Elevation Angle (degree)</label>
                    <input type="number" class="form-control" id="elevationAngleInput" min="0" max="90">
                </div>
                <div class="mb-3">
                    <label for="targetAreaInput" class="form-label">Target Area (Km^2)</label>
                    <input type="number" class="form-control" id="targetAreaInput" min="1">
                </div>
                <div class="mb-3">
                    <label for="minimumSNRInput" class="form-label">Minimum SNR (dB)</label>
                    <input type="number" class="form-control" id="minimumSNRInput" step="0.1">
                </div>
                <div class="mb-3">
                    <label for="orbitInclinationInput" class="form-label">Orbit Inclination (degree)</label>
                    <input type="number" class="form-control" id="orbitInclinationInput" min="0" max="180">
                </div>
                <div class="mb-3">
                    <label for="minSatellitesInViewInput" class="form-label">Minimum Satellite in View</label>
                    <input type="number" class="form-control" id="minSatellitesInViewInput" min="1">
                </div>`;

            showModal("Link Budget Analysis", inputBody, () => {
                let hasError = false;
                const lbName = document.getElementById('lbNameInput').value.trim();

                if (!lbName) { showInputError('lbNameInput', "Analysis Name cannot be empty."); hasError = true; }
                else if (linkBudgetAnalyses.has(lbName) && !editingFileName) {
                    showInputError('lbNameInput', `Analysis Name "${lbName}" already exists. Please use a different name.`); hasError = true;
                } else { clearInputError('lbNameInput'); }

                const inputs = [
                    { id: 'transmitPowerInput', name: 'Transmit Power' },
                    { id: 'txAntennaGainInput', name: 'Tx Antenna Gain' },
                    { id: 'rxAntennaGainInput', name: 'Rx Antenna Gain' },
                    { id: 'frequencyInput', min: 0.1, name: 'Frequency' },
                    { id: 'distanceInput', min: 100, name: 'Distance' },
                    { id: 'bandwidthInput', min: 0.1, name: 'Bandwidth' },
                    { id: 'noiseFigureInput', name: 'Noise Figure' },
                    { id: 'atmosphericLossInput', min: 0, name: 'Atmospheric Loss' },
                    { id: 'orbitHeightInput', min: 100, max: 36000, name: 'Orbit Height' },
                    { id: 'elevationAngleInput', min: 0, max: 90, name: 'Elevation Angle' },
                    { id: 'targetAreaInput', min: 1, name: 'Target Area' },
                    { id: 'minimumSNRInput', name: 'Minimum SNR' },
                    { id: 'orbitInclinationInput', min: 0, max: 180, name: 'Orbit Inclination' },
                    { id: 'minSatellitesInViewInput', min: 1, name: 'Minimum Satellite in View' }
                ];

                const values = {};
                inputs.forEach(input => {
                    const rawValue = document.getElementById(input.id).value;
                    const formattedValue = formatNumberInput(rawValue);
                    const value = parseFloat(formattedValue);

                    if (isNaN(value)) { showInputError(input.id, `${input.name} must be a number.`); hasError = true; }
                    else if ((input.min !== undefined && value < input.min) || (input.max !== undefined && value > input.max)) { showInputError(input.id, `Input must be within valid range.`); hasError = true; }
                    else { clearInputError(input.id); values[input.id.replace('Input', '')] = value; }
                });

                if (hasError) { return false; }

                const calculatedData = calculateLinkBudget({
                    name: lbName,
                    transmitPower: values.transmitPower,
                    txAntennaGain: values.txAntennaGain,
                    rxAntennaGain: values.rxAntennaGain,
                    frequency: values.frequency,
                    distance: values.distance,
                    bandwidth: values.bandwidth,
                    noiseFigure: values.noiseFigure,
                    atmosphericLoss: values.atmosphericLoss,
                    orbitHeight: values.orbitHeight,
                    elevationAngle: values.elevationAngle,
                    targetArea: values.targetArea,
                    minimumSNR: values.minimumSNR,
                    orbitInclination: values.orbitInclination,
                    minSatellitesInView: values.minSatellitesInView
                });

                const fullDataToSave = { ...calculatedData, fileType: 'linkBudget' };
                showLinkBudgetOutput(fullDataToSave);
                return false;
            }, () => {
                document.getElementById('lbNameInput').value = '';
                document.getElementById('transmitPowerInput').value = '';
                document.getElementById('txAntennaGainInput').value = '';
                document.getElementById('rxAntennaGainInput').value = '';
                document.getElementById('frequencyInput').value = '';
                document.getElementById('distanceInput').value = '';
                document.getElementById('bandwidthInput').value = '';
                document.getElementById('noiseFigureInput').value = '';
                document.getElementById('atmosphericLossInput').value = '';
                document.getElementById('orbitHeightInput').value = '';
                document.getElementById('elevationAngleInput').value = '';
                document.getElementById('targetAreaInput').value = '';
                document.getElementById('minimumSNRInput').value = '';
                document.getElementById('orbitInclinationInput').value = '';
                document.getElementById('minSatellitesInViewInput').value = '';
                const inputs = document.querySelectorAll('#fileModalBody input');
                inputs.forEach(input => clearInputError(input.id));
            });
        }

        function showLinkBudgetOutput(data) {
            const outputBody = document.createElement('div');
            outputBody.id = 'linkBudgetOutputBody';
            outputBody.innerHTML = `
                <p><strong>Analysis Name:</strong> ${data.name}</p>
                <hr>
                <h6>Calculated Results:</h6>
                <p><strong>Received Power:</strong> ${data.receivedPower.toFixed(2).replace('.', ',')} dBm</p>
                <p><strong>SNR:</strong> ${data.snr.toFixed(2).replace('.', ',')} dB</p>
                <p><strong>Shannon Capacity:</strong> ${data.shannonCapacity.toExponential(4).replace('.', ',')} bps</p>
                <h6>Constellation Needs (Walker Constellation by default):</h6>
                <p><strong>Number of Satellites Needed:</strong> ${Math.ceil(data.numSatellitesNeeded)}</p>
                <p><strong>Number of Orbital Planes:</strong> ${Math.ceil(data.numOrbitalPlanes)}</p>
                <p><strong>Revisit Time:</strong> ${data.revisitTime.toFixed(2).replace('.', ',')} minutes</p>
                <p><strong>Peak Throughput Per User:</strong> ${data.peakThroughputPerUser.toExponential(4).replace('.', ',')} bps</p>
            `;
            document.body.appendChild(outputBody);
        }



// --- EDIT MENU FUNCTIONS (triggered by double-click on resource items) ---
    window.editSingleParameter = editSingleParameter;
    window.editConstellationParameter = editConstellationParameter;
    window.editGroundStation = editGroundStation;

    function editSingleParameter(fileName) {
        const dataToEdit = fileOutputs.get(fileName);
        if (!dataToEdit) { showCustomAlert("Data file not found."); return; }

        const modalBody = `
            <div class="mb-3">
                <label for="fileNameInput" class="form-label">Satellite Name</label>
                <input type="text" class="form-control" id="fileNameInput" readonly>
            </div>
            <div class="mb-3">
                <label for="altitudeInput" class="form-label">Altitude (Km)</label>
                <input type="number" class="form-control" id="altitudeInput" min="100" max="36000">
            </div>
            <div class="mb-3">
                <label for="inclinationInput" class="form-label">Inclination (degree)</label>
                <input type="number" class="form-control" id="inclinationInput" min="0" max="180">
            </div>
            <div class="mb-3">
                <label class="form-label">Eccentricity</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="eccentricityType" id="eccentricityCircular" value="circular" onchange="toggleEccentricityInput('circular')">
                    <label class="form-check-label" for="eccentricityCircular">Circular</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="eccentricityType" id="eccentricityElliptical" value="elliptical" onchange="toggleEccentricityInput('elliptical')">
                    <label class="form-check-label" for="eccentricityElliptical">Elliptical</label>
                </div>
                <div id="eccentricityValueContainer" class="mt-2" style="display: none;">
                    <label for="eccentricityValueInput" class="form-label">Eccentricity Value (0-1)</label>
                    <input type="number" class="form-control" id="eccentricityValueInput" min="0" max="1" step="0.0001">
                </div>
            </div>
            <div class="mb-3">
                <label for="raanInput" class="form-label">RAAN (degree)</label>
                <input type="number" class="form-control" id="raanInput" min="0" max="360">
            </div>
            <div class="mb-3" id="argumentOfPerigeeContainer" style="display: none;">
                <label for="argumentOfPerigeeInput" class="form-label">Argument of Perigee (degree)</label>
                <input type="number" class="form-control" id="argumentOfPerigeeInput" min="0" max="360">
            </div>
            <div class="mb-3">
                <label for="trueAnomalyInput" class="form-label">True Anomaly (degree)</label>
                <input type="number" class="form-control" id="trueAnomalyInput" min="0" max="360">
            </div>
            <div class="mb-3">
                <label for="epochInput" class="form-label">Epoch</label>
                <input type="datetime-local" class="form-control" id="epochInput">
            </div>
            <div class="mb-3">
                <label for="utcOffsetInput" class="form-label">UTC Offset</label>
                <select class="form-control" id="utcOffsetInput">
                    <option value="0" selected>UTC+0</option>
                    <option value="1">UTC+1</option>
                    <option value="2">UTC+2</option>
                    <option value="3">UTC+3</option>
                    <option value="4">UTC+4</option>
                    <option value="5">UTC+5</option>
                    <option value="6">UTC+6</option>
                    <option value="7">UTC+7</option>
                    <option value="8">UTC+8</option>
                    <option value="9">UTC+9</option>
                    <option value="10">UTC+10</option>
                    <option value="11">UTC+11</option>
                    <option value="12">UTC+12</option>
                    <option value="13">UTC+13</option>
                    <option value="14">UTC+14</option>
                    <option value="-1">UTC-1</option>
                    <option value="-2">UTC-2</option>
                    <option value="-3">UTC-3</option>
                    <option value="-4">UTC-4</option>
                    <option value="-5">UTC-5</option>
                    <option value="-6">UTC-6</option>
                    <option value="-7">UTC-7</option>
                    <option value="-8">UTC-8</option>
                    <option value="-9">UTC-9</option>
                    <option value="-10">UTC-10</option>
                    <option value="-11">UTC-11</option>
                    <option value="-12">UTC-12</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="beamwidthInput" class="form-label">Beamwidth (degree)</label>
                <input type="number" class="form-control" id="beamwidthInput" min="0" max="90">
            </div>
        `;

        showModal("Edit Single Satellite", modalBody, () => {
            let hasError = false;
            const currentFileName = document.getElementById('fileNameInput').value;
            const eccentricityType = document.querySelector('input[name="eccentricityType"]:checked').value;
            let eccentricity = 0;
            if (eccentricityType === 'elliptical') {
                const eccValue = document.getElementById('eccentricityValueInput').value;
                eccentricity = parseFloat(formatNumberInput(eccValue));
            }

            const inputs = [
                { id: 'altitudeInput', min: 100, max: 36000, name: 'Altitude' },
                { id: 'inclinationInput', min: 0, max: 180, name: 'Inclination' },
                { id: 'raanInput', min: 0, max: 360, name: 'RAAN' },
                { id: 'trueAnomalyInput', min: 0, max: 360, name: 'True Anomaly' },
                { id: 'beamwidthInput', min: 0, max: 90, name: 'Beamwidth' }
            ];

            if (eccentricityType === 'elliptical') {
                inputs.push({ id: 'argumentOfPerigeeInput', min: 0, max: 360, name: 'Argument of Perigee' });
                if (isNaN(eccentricity) || eccentricity < 0 || eccentricity > 1) {
                    showInputError('eccentricityValueInput', `Eccentricity must be between 0 and 1.`); hasError = true;
                } else { clearInputError('eccentricityValueInput'); }
            }

            const values = {};
            inputs.forEach(input => {
                const rawValue = document.getElementById(input.id).value;
                const formattedValue = formatNumberInput(rawValue);
                const value = parseFloat(formattedValue);

                if (isNaN(value)) { showInputError(input.id, `${input.name} must be a number.`); hasError = true; }
                else if (value < input.min || value > input.max) { showInputError(input.id, `Input must be between ${input.min} and ${input.max}.`); hasError = true; }
                else { clearInputError(input.id); values[input.id.replace('Input', '')] = value; }
            });

            const epochInput = document.getElementById('epochInput').value;
            if (!epochInput) { showInputError('epochInput', "Epoch cannot be empty."); hasError = true; }
            else { clearInputError('epochInput'); }

            if (hasError) { return false; }

            // Calculate UTC timestamp with selected offset
            const utcOffset = parseInt(document.getElementById('utcOffsetInput').value);
            window.utcOffset = utcOffset; // Add this line
            const [datePart, timePart] = epochInput.split('T');
            const [year, month, day] = datePart.split('-').map(Number);
            const [hour, minute] = timePart.split(':').map(Number);
            const utcHour = hour - utcOffset;
            const utcDate = new Date(Date.UTC(year, month - 1, day, utcHour, minute, 0));
            window.currentEpochUTC = utcDate.getTime();

            const updatedData = {
                fileName: currentFileName, altitude: values.altitude, inclination: values.inclination,
                eccentricity: eccentricity, raan: values.raan,
                argumentOfPerigee: eccentricityType === 'elliptical' ? values.argumentOfPerigee : 0,
                trueAnomaly: values.trueAnomaly,
                epoch: epochInput, // Store the input string as entered
                beamwidth: values.beamwidth,
                fileType: 'single'
            };

            const oldData = { ...fileOutputs.get(currentFileName) };
            recordAction({ type: 'editFile', fileName: currentFileName, fileType: 'single', oldData: oldData, newData: updatedData });
            fileOutputs.set(currentFileName, updatedData);
            saveFilesToLocalStorage();

            if (window.addOrUpdateSatelliteInScene) {
                window.addOrUpdateSatelliteInScene(updatedData);
            }

            updateOutputSidebar(updatedData);
            addFileToResourceSidebar(currentFileName, updatedData, 'single');
            updateSatelliteListUI();
            selectSatellite(currentFileName);
            return true;
        }, null, fileName, 'single');

        // Populate modal with existing data
        document.getElementById('fileNameInput').value = dataToEdit.fileName;
        document.getElementById('altitudeInput').value = dataToEdit.altitude;
        document.getElementById('inclinationInput').value = dataToEdit.inclination;
        document.getElementById('raanInput').value = dataToEdit.raan;
        document.getElementById('trueAnomalyInput').value = dataToEdit.trueAnomaly;
        document.getElementById('beamwidthInput').value = dataToEdit.beamwidth;

        // For epoch: convert stored UTC epoch to local time
        const storedEpoch = dataToEdit.epoch;
        const utcDate = new Date(storedEpoch + 'Z');
        const localYear = utcDate.getFullYear();
        const localMonth = String(utcDate.getMonth() + 1).padStart(2, '0');
        const localDay = String(utcDate.getDate()).padStart(2, '0');
        const localHour = String(utcDate.getHours()).padStart(2, '0');
        const localMinute = String(utcDate.getMinutes()).padStart(2, '0');
        const localEpochString = `${localYear}-${localMonth}-${localDay}T${localHour}:${localMinute}`;
        document.getElementById('epochInput').value = localEpochString;

        // Set UTC offset to local offset
        const localOffsetMinutes = new Date().getTimezoneOffset();
        const localOffsetHours = -localOffsetMinutes / 60;
        document.getElementById('utcOffsetInput').value = localOffsetHours.toString();

        if (dataToEdit.eccentricity == 0) {
            document.getElementById('eccentricityCircular').checked = true;
            toggleEccentricityInput('circular');
        }  else {
            document.getElementById('eccentricityElliptical').checked = true;
            document.getElementById('eccentricityValueInput').value = formatNumberInput(dataToEdit.eccentricity);
            toggleEccentricityInput('elliptical');
        }
    }

    function editConstellationParameter(fileName) {
        const dataToEdit = fileOutputs.get(fileName);
        if (!dataToEdit) { showCustomAlert("Constellation data not found."); return; }

        const modalBody = `
            <div class="mb-3">
                <label for="fileNameInput" class="form-label">Constellation Name</label>
                <input type="text" class="form-control" id="fileNameInput" readonly>
            </div>
            <div class="mb-3">
                <label for="altitudeInput" class="form-label">Altitude (Km)</label>
                <input type="number" class="form-control" id="altitudeInput" min="100" max="36000">
            </div>
            <div class="mb-3">
                <label for="inclinationInput" class="form-label">Inclination (degree)</label>
                <input type="number" class="form-control" id="inclinationInput" min="0" max="180">
            </div>
            <div class="mb-3">
                <label class="form-label">Eccentricity</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="eccentricityType" id="eccentricityCircular" value="circular" onchange="toggleEccentricityInput('circular')">
                    <label class="form-check-label" for="eccentricityCircular">Circular</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="eccentricityType" id="eccentricityElliptical" value="elliptical" onchange="toggleEccentricityInput('elliptical')">
                    <label class="form-check-label" for="eccentricityElliptical">Elliptical</label>
                </div>
                <div id="eccentricityValueContainer" class="mt-2" style="display: none;">
                    <label for="eccentricityValueInput" class="form-label">Eccentricity Value (0-1)</label>
                    <input type="number" class="form-control" id="eccentricityValueInput" min="0" max="1" step="0.0001">
                </div>
            </div>
            <div class="mb-3">
                <label for="raanInput" class="form-label">RAAN (degree)</label>
                <input type="number" class="form-control" id="raanInput" min="0" max="360">
            </div>
            <div class="mb-3" id="argumentOfPerigeeContainer" style="display: none;">
                <label for="argumentOfPerigeeInput" class="form-label">Argument of Perigee (degree)</label>
                <input type="number" class="form-control" id="argumentOfPerigeeInput" min="0" max="360">
            </div>
            <div class="mb-3">
                <label for="trueAnomalyInput" class="form-label">True Anomaly (degree)</label>
                <input type="number" class="form-control" id="trueAnomalyInput" min="0" max="360">
            </div>
            <div class="mb-3">
                <label for="epochInput" class="form-label">Epoch</label>
                <input type="datetime-local" class="form-control" id="epochInput">
            </div>
            <div class="mb-3">
                <label for="utcOffsetInput" class="form-label">UTC Offset</label>
                <select class="form-control" id="utcOffsetInput">
                    <option value="0" selected>UTC+0</option>
                    <option value="1">UTC+1</option>
                    <option value="2">UTC+2</option>
                    <option value="3">UTC+3</option>
                    <option value="4">UTC+4</option>
                    <option value="5">UTC+5</option>
                    <option value="6">UTC+6</option>
                    <option value="7">UTC+7</option>
                    <option value="8">UTC+8</option>
                    <option value="9">UTC+9</option>
                    <option value="10">UTC+10</option>
                    <option value="11">UTC+11</option>
                    <option value="12">UTC+12</option>
                    <option value="13">UTC+13</option>
                    <option value="14">UTC+14</option>
                    <option value="-1">UTC-1</option>
                    <option value="-2">UTC-2</option>
                    <option value="-3">UTC-3</option>
                    <option value="-4">UTC-4</option>
                    <option value="-5">UTC-5</option>
                    <option value="-6">UTC-6</option>
                    <option value="-7">UTC-7</option>
                    <option value="-8">UTC-8</option>
                    <option value="-9">UTC-9</option>
                    <option value="-10">UTC-10</option>
                    <option value="-11">UTC-11</option>
                    <option value="-12">UTC-12</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="beamwidthInput" class="form-label">Beamwidth (degree)</label>
                <input type="number" class="form-control" id="beamwidthInput" min="0" max="90">
            </div>
            <hr>
            <h6 class="mt-4 mb-3">Constellation Type</h6>
            <div class="mb-3">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="constellationType" id="constellationTypeTrain" value="train">
                    <label class="form-check-label" for="constellationTypeTrain">Train</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="constellationType" id="constellationTypeWalker" value="walker">
                    <label class="form-check-label" for="constellationTypeWalker">Walker</label>
                </div>
            </div>
            <div id="trainConstellationFields">
                <div class="mb-3">
                    <label for="numSatellitesInput" class="form-label">Number of Satellites</label>
                    <input type="number" class="form-control" id="numSatellitesInput" min="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Separation Type</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="separationType" id="separationTypeMeanAnomaly" value="meanAnomaly">
                        <label class="form-check-label" for="separationTypeMeanAnomaly">Mean Anomaly (Degrees)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="separationType" id="separationTypeTime" value="time">
                        <label class="form-check-label" for="separationTypeTime">Time (Seconds)</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="separationValueInput" class="form-label">Separation Value</label>
                    <input type="number" class="form-control" id="separationValueInput" min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Direction</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="trainDirection" id="trainDirectionForward" value="forward">
                        <label class="form-check-label" for="trainDirectionForward">Forward</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="trainDirection" id="trainDirectionBackward" value="backward">
                        <label class="form-check-label" for="trainDirectionBackward">Backward</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Start Location</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="trainStartLocation" id="trainStartLocationSame" value="same">
                        <label class="form-check-label" for="trainStartLocationSame">Same as Seed</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="trainStartLocation" id="trainStartLocationOffset" value="offset">
                        <label class="form-check-label" for="trainStartLocationOffset">Offset from Seed</label>
                    </div>
                </div>
                <div id="trainOffsetFields" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Offset Type</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="trainOffsetType" id="trainOffsetTypeMeanAnomaly" value="meanAnomaly">
                            <label class="form-check-label" for="trainOffsetTypeMeanAnomaly">Mean Anomaly</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="trainOffsetType" id="trainOffsetTypeTrueAnomaly" value="trueAnomaly">
                            <label class="form-check-label" for="trainOffsetTypeTrueAnomaly">True Anomaly</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="trainOffsetType" id="trainOffsetTypeTime" value="time">
                            <label class="form-check-label" for="trainOffsetTypeTime">Time (s)</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="trainOffsetValue" class="form-label">Offset Value</label>
                        <input type="number" class="form-control" id="trainOffsetValue" min="0">
                    </div>
                </div>
            </div>
            <div id="walkerConstellationFields" style="display: none;">
                <div class="mb-3">
                    <label for="numPlanesInput" class="form-label">Number of Planes應用

            PlanesInput" class="form-label">Number of Planes</label>
                    <input type="number" class="form-control" id="numPlanesInput" min="1">
                </div>
                <div class="mb-3">
                    <label for="satellitesPerPlaneInput" class="form-label">Satellites per Plane</label>
                    <input type="number" class="form-control" id="satellitesPerPlaneInput" min="1">
                </div>
                <div class="mb-3">
                    <label for="raanSpreadInput" class="form-label">RAAN Spread</label>
                    <input type="number" class="form-control" id="raanSpreadInput" min="0">
                </div>
                <div class="mb-3">
                    <label for="phasingFactorInput" class="form-label">Phasing Factor</label>
                    <input type="number" class="form-control" id="phasingFactorInput" min="0">
                </div>
            </div>`;

        showModal("Edit Constellation Parameters", modalBody, () => {
            let hasError = false;
            const currentFileName = document.getElementById('fileNameInput').value;
            const eccentricityType = document.querySelector('input[name="eccentricityType"]:checked').value;
            let eccentricity = 0;
            if (eccentricityType === 'elliptical') {
                const eccValue = document.getElementById('eccentricityValueInput').value;
                eccentricity = parseFloat(formatNumberInput(eccValue));
            }

            const inputs = [
                { id: 'altitudeInput', min: 100, max: 36000, name: 'Altitude' },
                { id: 'inclinationInput', min: 0, max: 180, name: 'Inclination' },
                { id: 'raanInput', min: 0, max: 360, name: 'RAAN' },
                { id: 'trueAnomalyInput', min: 0, max: 360, name: 'True Anomaly' },
                { id: 'beamwidthInput', min: 0, max: 90, name: 'Beamwidth' }
            ];

            if (eccentricityType === 'elliptical') {
                inputs.push({ id: 'argumentOfPerigeeInput', min: 0, max: 360, name: 'Argument of Perigee' });
                if (isNaN(eccentricity) || eccentricity < 0 || eccentricity > 1) {
                    showInputError('eccentricityValueInput', `Eccentricity must be between 0 and 1.`); hasError = true;
                } else { clearInputError('eccentricityValueInput'); }
            }

            const values = {};
            inputs.forEach(input => {
                const rawValue = document.getElementById(input.id).value;
                const formattedValue = formatNumberInput(rawValue);
                const value = parseFloat(formattedValue);

                if (isNaN(value)) { showInputError(input.id, `${input.name} must be a number.`); hasError = true; }
                else if (value < input.min || value > input.max) { showInputError(input.id, `Input must be between ${input.min} and ${input.max}.`); hasError = true; }
                else { clearInputError(input.id); values[input.id.replace('Input', '')] = value; }
            });

            const epochInput = document.getElementById('epochInput').value;
            if (!epochInput) { showInputError('epochInput', "Epoch cannot be empty."); hasError = true; }
            else { clearInputError('epochInput'); }

            const constellationType = document.querySelector('input[name="constellationType"]:checked').value;
            const constellationData = { constellationType };

            if (constellationType === 'train') {
                const numSatellites = parseInt(document.getElementById('numSatellitesInput').value);
                const separationType = document.querySelector('input[name="separationType"]:checked').value;
                const separationValue = parseFloat(formatNumberInput(document.getElementById('separationValueInput').value));
                const trainDirection = document.querySelector('input[name="trainDirection"]:checked').value;
                const trainStartLocation = document.querySelector('input[name="trainStartLocation"]:checked').value;

                if (isNaN(numSatellites) || numSatellites < 1) { showInputError('numSatellitesInput', 'Number of Satellites must be at least 1.'); hasError = true; }
                else { clearInputError('numSatellitesInput'); }

                if (isNaN(separationValue) || separationValue < 0) { showInputError('separationValueInput', 'Separation Value must be a non-negative number.'); hasError = true; }
                else { clearInputError('separationValueInput'); }

                Object.assign(constellationData, { numSatellites, separationType, separationValue, trainDirection, trainStartLocation });

                if (trainStartLocation === 'offset') {
                    const trainOffsetType = document.querySelector('input[name="trainOffsetType"]:checked').value;
                    const trainOffsetValue = parseFloat(formatNumberInput(document.getElementById('trainOffsetValue').value));

                    if (isNaN(trainOffsetValue)) { showInputError('trainOffsetValue', 'Offset Value must be a number.'); hasError = true; }
                    else { clearInputError('trainOffsetValue'); }
                    Object.assign(constellationData, { trainOffsetType, trainOffsetValue });
                }
            } else if (constellationType === 'walker') {
                const numPlanes = parseInt(document.getElementById('numPlanesInput').value);
                const satellitesPerPlane = parseInt(document.getElementById('satellitesPerPlaneInput').value);
                const raanSpread = parseFloat(formatNumberInput(document.getElementById('raanSpreadInput').value));
                const phasingFactor = parseFloat(formatNumberInput(document.getElementById('phasingFactorInput').value));

                if (isNaN(numPlanes) || numPlanes < 1) { showInputError('numPlanesInput', 'Number of Planes must be at least 1.'); hasError = true; }
                else { clearInputError('numPlanesInput'); }

                if (isNaN(satellitesPerPlane) || satellitesPerPlane < 1) { showInputError('satellitesPerPlaneInput', 'Satellites per Plane must be at least 1.'); hasError = true; }
                else { clearInputError('satellitesPerPlaneInput'); }

                if (isNaN(raanSpread)) { showInputError('raanSpreadInput', 'RAAN Spread must be a number.'); hasError = true; }
                else { clearInputError('raanSpreadInput'); }

                if (isNaN(phasingFactor)) { showInputError('phasingFactorInput', 'Phasing Factor must be a number.'); hasError = true; }
                else { clearInputError('phasingFactorInput'); }

                Object.assign(constellationData, { numPlanes, satellitesPerPlane, raanSpread, phasingFactor });
            }

            if (hasError) { return false; }

            // Calculate UTC timestamp with selected offset
            const utcOffset = parseInt(document.getElementById('utcOffsetInput').value);
            window.utcOffset = utcOffset; // Add this line
            const [datePart, timePart] = epochInput.split('T');
            const [year, month, day] = datePart.split('-').map(Number);
            const [hour, minute] = timePart.split(':').map(Number);
            const utcHour = hour - utcOffset;
            const utcDate = new Date(Date.UTC(year, month - 1, day, utcHour, minute, 0));
            window.currentEpochUTC = utcDate.getTime();

            const updatedData = {
                fileName: currentFileName, altitude: values.altitude, inclination: values.inclination,
                eccentricity: eccentricity, raan: values.raan,
                argumentOfPerigee: eccentricityType === 'elliptical' ? values.argumentOfPerigee : 0,
                trueAnomaly: values.trueAnomaly, epoch: epochInput,
                beamwidth: values.beamwidth,
                fileType: 'constellation',
                ...constellationData
            };

            const oldData = { ...fileOutputs.get(currentFileName) };
            recordAction({ type: 'editFile', fileName: currentFileName, fileType: 'constellation', oldData: oldData, newData: updatedData });
            fileOutputs.set(currentFileName, updatedData);
            saveFilesToLocalStorage();

            if (window.viewSimulation) {
                window.viewSimulation(updatedData);
                setActiveControlButton('startButton');
            }

            updateOutputSidebar(updatedData);
            addFileToResourceSidebar(currentFileName, updatedData, 'constellation');
            updateSatelliteListUI();
            return true;
        }, () => { // Reset function
            document.getElementById('fileNameInput').value = dataToEdit.fileName;
            document.getElementById('altitudeInput').value = dataToEdit.altitude;
            document.getElementById('inclinationInput').value = dataToEdit.inclination;

            if (dataToEdit.eccentricity == 0) {
                document.getElementById('eccentricityCircular').checked = true;
                toggleEccentricityInput('circular');
            } else {
                document.getElementById('eccentricityElliptical').checked = true;
                document.getElementById('eccentricityValueInput').value = formatNumberInput(dataToEdit.eccentricity);
                toggleEccentricityInput('elliptical');
            }
            document.getElementById('raanInput').value = dataToEdit.raan;
            document.getElementById('argumentOfPerigeeInput').value = dataToEdit.argumentOfPerigee || '';
            document.getElementById('trueAnomalyInput').value = dataToEdit.trueAnomaly;

            // For epoch: convert stored UTC epoch to local time
            const storedEpoch = dataToEdit.epoch;
            const utcDate = new Date(storedEpoch + 'Z');
            const localYear = utcDate.getFullYear();
            const localMonth = String(utcDate.getMonth() + 1).padStart(2, '0');
            const localDay = String(utcDate.getDate()).padStart(2, '0');
            const localHour = String(utcDate.getHours()).padStart(2, '0');
            const localMinute = String(utcDate.getMinutes()).padStart(2, '0');
            const localEpochString = `${localYear}-${localMonth}-${localDay}T${localHour}:${localMinute}`;
            document.getElementById('epochInput').value = localEpochString;

            // Set UTC offset to local offset
            const localOffsetMinutes = new Date().getTimezoneOffset();
            const localOffsetHours = -localOffsetMinutes / 60;
            document.getElementById('utcOffsetInput').value = localOffsetHours.toString();

            document.getElementById('beamwidthInput').value = dataToEdit.beamwidth;

            if (dataToEdit.constellationType === 'walker') {
                document.getElementById('constellationTypeWalker').checked = true;
                toggleConstellationType('walker');
            } else {
                document.getElementById('constellationTypeTrain').checked = true;
                toggleConstellationType('train');
            }

            document.getElementById('numSatellitesInput').value = dataToEdit.numSatellites || '';
            if (dataToEdit.separationType === 'time') {
                document.getElementById('separationTypeTime').checked = true;
            } else {
                document.getElementById('separationTypeMeanAnomaly').checked = true;
            }
            document.getElementById('separationValueInput').value = dataToEdit.separationValue || '';
            if (dataToEdit.trainDirection === 'backward') {
                document.getElementById('trainDirectionBackward').checked = true;
            } else {
                document.getElementById('trainDirectionForward').checked = true;
            }
            if (dataToEdit.trainStartLocation === 'offset') {
                document.getElementById('trainStartLocationOffset').checked = true;
                toggleTrainOffset(true);
                if (dataToEdit.trainOffsetType === 'trueAnomaly') {
                    document.getElementById('trainOffsetTypeTrueAnomaly').checked = true;
                } else if (dataToEdit.trainOffsetType === 'time') {
                    document.getElementById('trainOffsetTypeTime').checked = true;
                } else {
                    document.getElementById('trainOffsetTypeMeanAnomaly').checked = true;
                }
                document.getElementById('trainOffsetValue').value = dataToEdit.trainOffsetValue || '';
            } else {
                document.getElementById('trainStartLocationSame').checked = true;
                toggleTrainOffset(false);
            }

            document.getElementById('numPlanesInput').value = dataToEdit.numPlanes || '';
            document.getElementById('satellitesPerPlaneInput').value = dataToEdit.satellitesPerPlane || '';
            document.getElementById('raanSpreadInput').value = dataToEdit.raanSpread || '';
            document.getElementById('phasingFactorInput').value = dataToEdit.phasingFactor || '';

            const inputs = document.querySelectorAll('#fileModalBody input');
            inputs.forEach(input => clearInputError(input.id));
        }, fileName, 'constellation');

        // Event listeners
        document.getElementById('eccentricityCircular').addEventListener('change', () => toggleEccentricityInput('circular'));
        document.getElementById('eccentricityElliptical').addEventListener('change', () => toggleEccentricityInput('elliptical'));
        document.getElementById('constellationTypeTrain').addEventListener('change', () => toggleConstellationType('train'));
        document.getElementById('constellationTypeWalker').addEventListener('change', () => toggleConstellationType('walker'));
        document.getElementById('trainStartLocationSame').addEventListener('change', () => toggleTrainOffset(false));
        document.getElementById('trainStartLocationOffset').addEventListener('change', () => toggleTrainOffset(true));

        // Populate modal with existing data
        document.getElementById('fileNameInput').value = dataToEdit.fileName;
        document.getElementById('altitudeInput').value = dataToEdit.altitude;
        document.getElementById('inclinationInput').value = dataToEdit.inclination;
        document.getElementById('raanInput').value = dataToEdit.raan;
        document.getElementById('trueAnomalyInput').value = dataToEdit.trueAnomaly;
        document.getElementById('beamwidthInput').value = dataToEdit.beamwidth;

        // For epoch: convert stored UTC epoch to local time
        const storedEpoch = dataToEdit.epoch;
        const utcDate = new Date(storedEpoch + 'Z');
        const localYear = utcDate.getFullYear();
        const localMonth = String(utcDate.getMonth() + 1).padStart(2, '0');
        const localDay = String(utcDate.getDate()).padStart(2, '0');
        const localHour = String(utcDate.getHours()).padStart(2, '0');
        const localMinute = String(utcDate.getMinutes()).padStart(2, '0');
        const localEpochString = `${localYear}-${localMonth}-${localDay}T${localHour}:${localMinute}`;
        document.getElementById('epochInput').value = localEpochString;

        // Set UTC offset to local offset
        const localOffsetMinutes = new Date().getTimezoneOffset();
        const localOffsetHours = -localOffsetMinutes / 60;
        document.getElementById('utcOffsetInput').value = localOffsetHours.toString();

        if (dataToEdit.eccentricity == 0) {
            document.getElementById('eccentricityCircular').checked = true;
            toggleEccentricityInput('circular');
        } else {
            document.getElementById('eccentricityElliptical').checked = true;
            document.getElementById('eccentricityValueInput').value = formatNumberInput(dataToEdit.eccentricity);
            toggleEccentricityInput('elliptical');
        }

        if (dataToEdit.constellationType === 'walker') {
            document.getElementById('constellationTypeWalker').checked = true;
            toggleConstellationType('walker');
            document.getElementById('numPlanesInput').value = dataToEdit.numPlanes;
            document.getElementById('satellitesPerPlaneInput').value = dataToEdit.satellitesPerPlane;
            document.getElementById('raanSpreadInput').value = dataToEdit.raanSpread;
            document.getElementById('phasingFactorInput').value = dataToEdit.phasingFactor;
        } else {
            document.getElementById('constellationTypeTrain').checked = true;
            toggleConstellationType('train');
            document.getElementById('numSatellitesInput').value = dataToEdit.numSatellites;
            if (dataToEdit.separationType === 'time') {
                document.getElementById('separationTypeTime').checked = true;
            } else {
                document.getElementById('separationTypeMeanAnomaly').checked = true;
            }
            document.getElementById('separationValueInput').value = dataToEdit.separationValue;
            if (dataToEdit.trainDirection === 'backward') {
                document.getElementById('trainDirectionBackward').checked = true;
            } else {
                document.getElementById('trainDirectionForward').checked = true;
            }
            if (dataToEdit.trainStartLocation === 'offset') {
                document.getElementById('trainStartLocationOffset').checked = true;
                toggleTrainOffset(true);
                if (dataToEdit.trainOffsetType === 'trueAnomaly') {
                    document.getElementById('trainOffsetTypeTrueAnomaly').checked = true;
                } else if (dataToEdit.trainOffsetType === 'time') {
                    document.getElementById('trainOffsetTypeTime').checked = true;
                } else {
                    document.getElementById('trainOffsetTypeMeanAnomaly').checked = true;
                }
                document.getElementById('trainOffsetValue').value = dataToEdit.trainOffsetValue;
            } else {
                document.getElementById('trainStartLocationSame').checked = true;
                toggleTrainOffset(false);
            }
        }
    }

    function editGroundStation(name) {
        const dataToEdit = groundStations.get(name);
        if (!dataToEdit) { showCustomAlert("Ground Station data not found."); return; }

        const modalBody = `
            <div class="mb-3">
                <label for="gsNameInput" class="form-label">Ground Station Name</label>
                <input type="text" class="form-control" id="gsNameInput" readonly>
            </div>
            <div class="mb-3">
                <label for="latitudeInput" class="form-label">Latitude (Degrees: North to South)</label>
                <input type="number" class="form-control" id="latitudeInput" min="-90" max="90" step="0.0001">
            </div>
            <div class="mb-3">
                <label for="longitudeInput" class="form-label">Longitude (Degrees)</label>
                <input type="number" class="form-control" id="longitudeInput" min="-180" max="180" step="0.0001">
            </div>
            <div class="mb-3">
                <label for="minElevationAngleInput" class="form-label">Minimum Elevation Angle (degree)</label>
                <input type="number" class="form-control" id="minElevationAngleInput" min="0" max="90" step="0.1">
            </div>`;

        showModal("Edit Ground Station", modalBody, () => {
            let hasError = false;
            const currentName = document.getElementById('gsNameInput').value.trim();
            const inputs = [
                { id: 'latitudeInput', min: -90, max: 90, name: 'Latitude' },
                { id: 'longitudeInput', min: -180, max: 180, name: 'Longitude' },
                { id: 'minElevationAngleInput', min: 0, max: 90, name: 'Minimum Elevation Angle' }
            ];

            const values = {};
            inputs.forEach(input => {
                const rawValue = document.getElementById(input.id).value;
                const formattedValue = formatNumberInput(rawValue);
                const value = parseFloat(formattedValue);

                if (isNaN(value)) { showInputError(input.id, `${input.name} must be a number.`); hasError = true; }
                else if (value < input.min || value > input.max) { showInputError(input.id, `Input must be between ${input.min} and ${input.max}.`); hasError = true; }
                else { clearInputError(input.id); values[input.id.replace('Input', '')] = value; }
            });

            if (hasError) { return false; }

            const updatedData = {
                id: currentName,
                name: currentName,
                latitude: values.latitude,
                longitude: values.longitude,
                minElevationAngle: values.minElevationAngle,
                altitude: 0,
                fileType: 'groundStation'
            };

            const oldData = { ...groundStations.get(currentName) };
            recordAction({ type: 'editFile', fileName: currentName, fileType: 'groundStation', oldData: oldData, newData: updatedData });
            groundStations.set(currentName, updatedData);
            saveFilesToLocalStorage();
            if (window.addOrUpdateGroundStationInScene) {
                window.addOrUpdateGroundStationInScene(updatedData);
            }
            updateOutputSidebar(updatedData);
            addFileToResourceSidebar(currentName, updatedData, 'groundStation');
            return true;
        }, null, name, 'groundStation');

        // Populate modal with existing data
        document.getElementById('gsNameInput').value = dataToEdit.name;
        document.getElementById('latitudeInput').value = dataToEdit.latitude;
        document.getElementById('longitudeInput').value = dataToEdit.longitude;
        document.getElementById('minElevationAngleInput').value = dataToEdit.minElevationAngle;
    }

    
window.deleteFile = deleteFile;
        // --------------------------------------------- DELETE FUNCTION -------------------------------------------

        function deleteFile(fileName, fileType) {
            showCustomConfirmation(
                `Are you sure you want to delete "${fileName}"?`,
                "Konfirmasi Penghapusan",
                "OK",
                () => {
                    let fileDataToDelete;
                    if (fileType === 'single' || fileType === 'constellation') {
                        fileDataToDelete = { ...fileOutputs.get(fileName) };
                        fileOutputs.delete(fileName);
                        if (window.removeObjectFromScene) window.removeObjectFromScene(fileName, 'satellite');
                    } else if (fileType === 'groundStation') {
                        fileDataToDelete = { ...groundStations.get(fileName) };
                        groundStations.delete(fileName);
                        if (window.removeObjectFromScene) window.removeObjectFromScene(fileName, 'groundStation');
                    } else if (fileType === 'linkBudget') {
                        fileDataToDelete = { ...linkBudgetAnalyses.get(fileName) };
                        linkBudgetAnalyses.delete(fileName);
                        // No 3D object to remove for link budget
                    }

                    recordAction({
                        type: 'deleteFile',
                        fileName: fileName,
                        fileType: fileType,
                        fileData: fileDataToDelete
                    });

                    saveFilesToLocalStorage();

                    const listItem = document.querySelector(`li[data-file-name="${fileName}"][data-file-type="${fileType}"]`);
                    if (listItem) {
                        listItem.remove();
                    }

                    const outputMenu = document.getElementById('output-menu').querySelector('ul');
                    const displayedFileNameElement = outputMenu.querySelector('.output-file-name');
                    if (displayedFileNameElement && displayedFileNameElement.textContent.includes(fileName)) {
                        updateOutputSidebar(null); // Clear displayed data if it was the deleted item
                    }
                    updateSatelliteListUI(); // Re-render satellite list if any changes (e.g., if deleted selected one)
                },
                true // Show cancel button
            );
        }
// --------------------------------------------- END DELETE FUNCTION -------------------------------------------

        
        // ----------------------------------------- VIEW MENU FUNCTIONS ---------------------------------------------
        window.toggle2DView = toggle2DView;
        window.resetView = resetView;
        window.toggleCloseView = toggleCloseView;

        function toggle2DView() {
            is2DViewActive = !is2DViewActive;
            window.is2DViewActive = is2DViewActive; // Sync global flag
            recordAction({ type: 'viewToggle', prevState: { is2D: !is2DViewActive, closeView: window.closeViewEnabled }, newState: { is2D: is2DViewActive, closeView: window.closeViewEnabled } });
            toggle2DViewVisuals();
            if (window.is2DViewActive && window.texturesLoaded) { //New
            window.draw2D(); //initial draw
            }
        }

        // In simulation.blade.php script
        function toggle2DViewVisuals() {
            const earthContainer = document.getElementById('earth-container');
            const earth2DContainer = document.getElementById('earth2D-container');

            if (is2DViewActive) { // If switching TO 2D
                if (earthContainer) earthContainer.style.display = 'none';
                if (earth2DContainer) {
                    earth2DContainer.style.display = 'flex'; // Make 2D container visible
                    window.resizeCanvas2D(); // <--- CRUCIAL: Resize 2D canvas immediately after making visible
                }
            } else { // If switching TO 3D
                if (earthContainer) earthContainer.style.display = 'flex'; // Make 3D container visible
                if (earth2DContainer) earth2DContainer.style.display = 'none';
            }
        }

        function resetView() {
            const core3D = window.getSimulationCoreObjects();
            if (!core3D.camera || !core3D.controls) { console.warn("Three.js not initialized for reset view."); return; }
            const prevState = { position: core3D.camera.position.clone(), rotation: core3D.camera.rotation.clone(), target: core3D.controls.target.clone() };

            // Ensure controls are re-enabled before setting, and then update.
            core3D.controls.enabled = true;
            core3D.camera.position.set(0, 0, 5); // Assuming default position
            core3D.controls.target.set(0, 0, 0);
            core3D.controls.object.up.set(0, 1, 0); // Reset camera up direction
            core3D.controls.minDistance = 0.001; // Reset min/max distance
            core3D.controls.maxDistance = 1000;
            core3D.controls.update();

            const newState = { position: core3D.camera.position.clone(), rotation: core3D.camera.rotation.clone(), target: core3D.controls.target.clone() };
            recordAction({ type: 'camera', prevState: prevState, newState: newState });
        }

        function toggleCloseView() {
            if (!window.getSimulationCoreObjects) {
                console.warn("3D simulation not initialized.");
                showCustomAlert("3D simulation not ready yet.");
                return;
            }
            if (window.activeSatellites.size === 0) {
                showCustomAlert("No satellites to view in close-up. Please create an orbit first.");
                return;
            }
            
            const core3D = window.getSimulationCoreObjects();
            const selectedSat = window.activeSatellites.get(window.selectedSatelliteId);
            
            // Capture current camera state before changes for undo/redo
            const prevState = {
                position: core3D.camera.position.clone(),
                rotation: core3D.camera.rotation.clone(),
                target: core3D.controls.target.clone(),
                closeView: window.closeViewEnabled // Capture current closeView state
            };

            // Toggle the global flag in Earth3Dsimulation.js
            window.closeViewEnabled = !window.closeViewEnabled;

            // Update button text in UI
            document.getElementById('closeViewButton').textContent = window.closeViewEnabled ? 'Normal View' : 'Close View';

            // Tell Earth3Dsimulation.js to update its active meshes (sphere vs GLB)
            window.activeSatellites.forEach(sat => sat.setActiveMesh(window.closeViewEnabled));

            // Adjust camera immediately with GSAP animation
            // Temporarily disable OrbitControls to allow GSAP to control the camera smoothly
            core3D.controls.enabled = false;
            
            if (window.closeViewEnabled && selectedSat) {
                const currentSatPos = selectedSat.mesh.position.clone();
                const forwardDir = selectedSat.velocity.length() > 0 ? selectedSat.velocity.clone().normalize() : new THREE.Vector3(0, 0, 1);
                const upDir = currentSatPos.clone().normalize();

                // Define camera offset relative to satellite
                const cameraOffset = forwardDir.clone().multiplyScalar(-0.08).add(upDir.clone().multiplyScalar(0.04));
                const desiredCameraPos = currentSatPos.clone().add(cameraOffset);

                gsap.to(core3D.camera.position, {
                    duration: 0.5,
                    x: desiredCameraPos.x,
                    y: desiredCameraPos.y,
                    z: desiredCameraPos.z,
                    ease: "power2.inOut",
                    onUpdate: () => core3D.controls.update(),
                    onComplete: () => {
                        // Re-enable controls only if still in closeView mode after animation
                        // This prevents unexpected control re-enabling if toggleCloseView is called quickly again
                        if (window.closeViewEnabled) {
                            core3D.controls.enabled = true;
                        }
                    }
                });
                gsap.to(core3D.controls.target, {
                    duration: 0.5,
                    x: currentSatPos.x,
                    y: currentSatPos.y,
                    z: currentSatPos.z,
                    ease: "power2.inOut",
                    onUpdate: () => core3D.controls.update()
                });

                core3D.controls.object.up.copy(upDir);
                core3D.controls.update();

                core3D.controls.minDistance = 0.01;
                core3D.controls.maxDistance = 0.2;

            } else { // Exiting close view, return to normal view
                core3D.controls.object.up.set(0, 1, 0); // Reset camera up direction

                gsap.to(core3D.camera.position, {
                    duration: 1.5,
                    x: 0, y: 0, z: 5, // Default normal view position
                    ease: "power2.inOut",
                    onUpdate: () => core3D.controls.update(),
                    onComplete: () => {
                        // Re-enable controls after animation completes
                        core3D.controls.enabled = true;
                    }
                });
                gsap.to(core3D.controls.target, {
                    duration: 1.5,
                    x: 0, y: 0, z: 0, // Default normal view target
                    ease: "power2.inOut",
                    onUpdate: () => core3D.controls.update()
                });

                core3D.controls.minDistance = 0.001; // Restore default limits
                core3D.controls.maxDistance = 1000;
            }
            // Record action for undo/redo after determining new state
            const newState = {
                position: core3D.camera.position.clone(), // Capture final animated position
                rotation: core3D.camera.rotation.clone(),
                target: core3D.controls.target.clone(),
                closeView: window.closeViewEnabled // Capture new closeView state
            };
            recordAction({ type: 'viewToggle', prevState: prevState, newState: newState });
        }

        // ----------------------------------------- END VIEW MENU FUNCTIONS ---------------------------------------------

        // ------------------------------------- SAVE MENU FUNCTIONS ------------------------------------------------
        window.showSavePopup = showSavePopup;
        window.generateAndSaveSelectedScripts = generateAndSaveSelectedScripts;
        window.Load = Load; // This is a placeholder, you might want to implement file loading

        function showSavePopup() {
        const existingPopup = document.querySelector('.custom-popup');
        if (existingPopup) existingPopup.remove();
        const savePopup = document.createElement('div');
        savePopup.classList.add('custom-popup');
        savePopup.style.position = 'absolute';
        savePopup.style.left = '50%';
        savePopup.style.top = '50%';
        savePopup.style.transform = 'translate(-50%, -50%)';
        savePopup.style.background = '#fff';
        savePopup.style.padding = '20px';
        savePopup.style.border = '1px solid #ccc';
        let content = `<h5>Select items to save:</h5><ul style="list-style: none; padding: 0;">`;
        // Assuming fileOutputs is a Map containing satellite and constellation data
        if (fileOutputs.size > 0) {
            let singleSatsExist = false;
            let constellationsExist = false;
            fileOutputs.forEach(data => {
                if (data.fileType === 'single') singleSatsExist = true;
                if (data.fileType === 'constellation') constellationsExist = true;
            });

            if (singleSatsExist) {
                content += `<li><strong>Single Satellites</strong><ul style="list-style: none; padding-left: 20px;">`;
                fileOutputs.forEach((data, fileName) => {
                    if (data.fileType === 'single') {
                        content += `<li><input type="checkbox" id="save-${fileName}" value="${fileName}" data-type="single"> <label for="save-${fileName}">${fileName}</label></li>`;
                    }
                });
                content += `</ul></li>`;
            }
            if (constellationsExist) {
                content += `<li><strong>Constellations</strong><ul style="list-style: none; padding-left: 20px;">`;
                fileOutputs.forEach((data, fileName) => {
                    if (data.fileType === 'constellation') {
                        content += `<li><input type="checkbox" id="save-${fileName}" value="${fileName}" data-type="constellation"> <label for="save-${fileName}">${fileName}</label></li>`;
                    }
                });
                content += `</ul></li>`;
            }
        }

        content += `</ul>
            <div style="display: flex; justify-content: end; margin-top: 20px;">
                <button style="margin-right: 10px;" onclick="this.parentNode.parentNode.remove()">Close</button>
                <button style="background: #007bff; color: #fff; border: none; padding: 5px 10px;" onclick="generateAndSaveSelectedScripts()">Generate & Save</button>
            </div>`;

        savePopup.innerHTML = content;
        document.body.appendChild(savePopup);
    }

    function generateAndSaveSelectedScripts() {
        const selectedItems = [];
        document.querySelectorAll('.custom-popup input[type="checkbox"]:checked').forEach(checkbox => {
            selectedItems.push({ name: checkbox.value, type: checkbox.dataset.type });
        });

        if (selectedItems.length === 0) {
            alert("Please select at least one item to save.");
            return;
        }

        let csvContent = 'Satellite Name,Position-x,Position-y,Position-z,Latitude,Longitude,UTC Time,Simulation Time\n';

        selectedItems.forEach(item => {
            if (item.type === 'single') {
                const satelliteData = fileOutputs.get(item.name);
                if (satelliteData) {
                    csvContent += formatSatelliteData(satelliteData) + '\n';
                }
            } else if (item.type === 'constellation') {
                const constellationData = fileOutputs.get(item.name);
                if (constellationData && constellationData.satellites) {
                    constellationData.satellites.forEach(satName => {
                        const satelliteData = fileOutputs.get(satName);
                        if (satelliteData) {
                            csvContent += formatSatelliteData(satelliteData) + '\n';
                        }
                    });
                }
            }
        });
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'satellite_data.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            document.querySelector('.custom-popup').remove();
            alert("Selected data saved as satellite_data.csv!");
    }

        function formatSatelliteData(satelliteData) {
        const { name, position, latitude, longitude, utcTime, simulationTime } = satelliteData;
        const posX = position.x.toFixed(3);
        const posY = position.y.toFixed(3);
        const posZ = position.z.toFixed(3);
        const lat = latitude.toFixed(6);
        const lon = longitude.toFixed(6);
        const utc = utcTime.toISOString();
        const simTime = simulationTime.toFixed(3);

        return `"${name}",${posX},${posY},${posZ},${lat},${lon},"${utc}",${simTime}`;
    }

    // Example usage: Assuming fileOutputs is predefined
    // const fileOutputs = new Map([
    //     ['Sat1', { fileType: 'single', name: 'Sat1', position: { x: 1.234, y: 0.567, z: -0.891 }, latitude: 45.123456, longitude: -123.654321, utcTime: new Date(), simulationTime: 100.5 }],
    //     ['Constellation1', { fileType: 'constellation', satellites: ['Sat2', 'Sat3'] }],
    //     ['Sat2', { fileType: 'single', name: 'Sat2', position: { x: 2.345, y: 1.678, z: 0.123 }, latitude: 46.234567, longitude: -122.543210, utcTime: new Date(), simulationTime: 101.2 }],
    //     ['Sat3', { fileType: 'single', name: 'Sat3', position: { x: 3.456, y: 2.789, z: 1.234 }, latitude: 47.345678, longitude: -121.432109, utcTime: new Date(), simulationTime: 102.0 }]
    // ]);

        
        // ------------------------------------- LOAD TLE FUNCTION ------------------------------------------------
        function Load() {
            const tleName = document.getElementById('tle-name').value;
            const tleLine1 = document.getElementById('tle-line1').value.trim();
            const tleLine2 = document.getElementById('tle-line2').value.trim();

            if (!tleLine1 || !tleLine2) {
                showCustomAlert("Please enter both TLE Line 1 and TLE Line 2.");
                return;
            }

            // You can optionally add more robust TLE format validation here
            if (tleLine1.length !== 69 || tleLine2.length !== 69) {
                showCustomAlert("TLE lines typically have a length of 69 characters. Please check your input.");
                // Continue even with warning to allow some flexibility for testing.
            }

            // Automatically generate a default name if not provided
            const satelliteName = tleName || "TLE_Sat_" + Date.now(); 

            const satData = {
                fileType: 'single', // TLEs represent single satellites
                id: satelliteName, // Use the provided name or auto-generated as ID
                name: satelliteName,
                tleLine1: tleLine1,
                tleLine2: tleLine2,
                // For TLE-based satellites, altitude, inclination, etc., are derived from TLE.
                // Provide dummy values or omit if they are not explicitly required by addOrUpdateSatelliteInScene's
                // constructor for TLEs, but it's safer to include them for consistency.
                altitude: 0, 
                inclination: 0,
                eccentricity: 0,
                raan: 0,
                argumentOfPerigee: 0,
                trueAnomaly: 0,
                epoch: new Date().toISOString(), // Use current time, TLE parser extracts its own epoch
                beamwidth: 0 // Default beamwidth for TLEs unless specified otherwise
            };
            
            // Clear current scene and add this TLE satellite
            window.clearSimulationScene(); // Clear existing objects first
            window.addOrUpdateSatelliteInScene(satData);

            // TLEs usually specify an epoch from which they are valid.
            // The SGP4 propagation function (propagateSGP4 in sgp4.js) should handle
            // taking the TLE's internal epoch into account when calculating position
            // for the current simulation time.

            window.isAnimating = true; // Start animation after loading TLE
            showCustomAlert(`TLE for "${satelliteName}" loaded.`);
        }

        // ------------------------------------- TOOLBAR FUNCTIONS ------------------------------------------------
        
        // --- TOOLBAR FUNCTIONS (Animation and Undo/Redo) ---
        window.playAnimation = playAnimation;
        window.pauseAnimation = pauseAnimation;
        window.speedUpAnimation = speedUpAnimation;
        window.slowDownAnimation = slowDownAnimation;
        window.updateAnimationDisplay = updateAnimationDisplay;

        function updateAnimationDisplay() {
        const is3DActive = document.getElementById('earth-container').style.display !== 'none';

        const statusElement3D = document.getElementById('animationState');
        const speedElement3D = document.getElementById('animationSpeed');
        const clockElement3D = document.getElementById('currentSimulatedTime');

        const statusElement2D = document.getElementById('animationState2D');
        const speedElement2D = document.getElementById('animationSpeed2D');
        const clockElement2D = document.getElementById('currentSimulatedTime2D');

        // Get UTC offset from global variable, default to 0 if not set
        const utcOffset = window.utcOffset || 0;

        // Calculate current time with offset
        const currentDateTime = new Date(window.currentEpochUTC + (window.totalSimulatedTime * 1000) + (utcOffset * 3600 * 1000));
        
        // Format the time string
        const formattedTime = currentDateTime.toISOString().replace('T', ' ').substring(0, 19) + ` UTC${utcOffset >= 0 ? '+' : ''}${utcOffset}`;

        if (statusElement3D && speedElement3D && clockElement3D) {
            statusElement3D.textContent = window.isAnimating ? 'Playing' : 'Paused';
            speedElement3D.textContent = `${window.currentSpeedMultiplier}x`;
            clockElement3D.textContent = formattedTime;
        }
        if (statusElement2D && speedElement2D && clockElement2D) {
            statusElement2D.textContent = window.isAnimating ? 'Playing' : 'Paused';
            speedElement2D.textContent = `${window.currentSpeedMultiplier}x`;
            clockElement2D.textContent = formattedTime;
        }

        // Toggle visibility for the display containers themselves based on the active view
        const animationStatusDisplay3DContainer = document.getElementById('animationStatusDisplay').parentElement;
        const simulationClockDisplay3DContainer = document.getElementById('simulationClockDisplay').parentElement;

        const animationStatusDisplay2DContainer = document.getElementById('animationStatusDisplay2D').parentElement;
        const simulationClockDisplay2DContainer = document.getElementById('simulationClockDisplay2D').parentElement;

        if (animationStatusDisplay3DContainer) animationStatusDisplay3DContainer.style.display = is3DActive ? 'flex' : 'none';
        if (simulationClockDisplay3DContainer) simulationClockDisplay3DContainer.style.display = is3DActive ? 'flex' : 'none';

        if (animationStatusDisplay2DContainer) animationStatusDisplay2DContainer.style.display = is3DActive ? 'none' : 'flex';
        if (simulationClockDisplay2DContainer) simulationClockDisplay2DContainer.style.display = is3DActive ? 'none' : 'flex';
    }

        function setActiveControlButton(activeButtonId) {
            const controlButtons = ['startButton', 'pauseButton', 'speedUpButton', 'slowDownButton'];
            controlButtons.forEach(id => {
                const button = document.getElementById(id);
                if (button) {
                    if (id === activeButtonId) {
                        button.classList.add('pressed');
                    } else {
                        button.classList.remove('pressed');
                    }
                }
            });
        }

        function playAnimation() {
            if (!window.isAnimating) {
                recordAction({
                    type: 'animationState',
                    prevState: { isAnimating: window.isAnimating, speed: window.currentSpeedMultiplier },
                    newState: { isAnimating: true, speed: window.currentSpeedMultiplier }
                });
            }
            window.isAnimating = true;
            setActiveControlButton('startButton');
            updateAnimationDisplay();
        }

        function pauseAnimation() {
            if (window.isAnimating) {
                recordAction({
                    type: 'animationState',
                    prevState: { isAnimating: window.isAnimating, speed: window.currentSpeedMultiplier },
                    newState: { isAnimating: false, speed: window.currentSpeedMultiplier }
                });
            }
            window.isAnimating = false;
            setActiveControlButton('pauseButton');
            updateAnimationDisplay();
        }

        function speedUpAnimation() {
            const prevState = {
                speedMultiplier: window.currentSpeedMultiplier,
                isAnimating: window.isAnimating
            };
            window.currentSpeedMultiplier *= 2;
            if (isNaN(window.currentSpeedMultiplier)) { // Defensive check
                window.currentSpeedMultiplier = 1;
            }
            window.isAnimating = true; // Ensure it's playing
            setActiveControlButton('speedUpButton');
            updateAnimationDisplay();
            recordAction({
                type: 'animationSpeed',
                prevState: prevState,
                newState: { speedMultiplier: window.currentSpeedMultiplier, isAnimating: window.isAnimating }
            });
        }

        function slowDownAnimation() {
            const prevState = {
                speedMultiplier: window.currentSpeedMultiplier,
                isAnimating: window.isAnimating
            };
            window.currentSpeedMultiplier /= 2;
            if (isNaN(window.currentSpeedMultiplier)) { // Defensive check
                window.currentSpeedMultiplier = 1;
            }
            if (window.currentSpeedMultiplier < 0.125) window.currentSpeedMultiplier = 0.125; // Prevent too slow
            window.isAnimating = true; // Ensure it's playing
            setActiveControlButton('slowDownButton');
            updateAnimationDisplay();
            recordAction({
                type: 'animationSpeed',
                prevState: prevState,
                newState: { speedMultiplier: window.currentSpeedMultiplier, isAnimating: window.isAnimating }
            });
        }

        // --- Three.js related functions (now directly call exposed functions from Earth3Dsimulation.js) ---
        window.zoomIn = zoomIn;
        window.zoomOut = zoomOut;
        // Zoom in and out functions for camera control
        function zoomIn() {
            const core3D = window.getSimulationCoreObjects();
            if (!core3D.camera || !core3D.controls) { console.warn("Three.js not initialized for zoom."); return; }
            const prevState = { position: core3D.camera.position.clone(), rotation: core3D.camera.rotation.clone(), target: core3D.controls.target.clone() };
            core3D.camera.position.z -= 1;
            core3D.controls.update();
            const newState = { position: core3D.camera.position.clone(), rotation: core3D.camera.rotation.clone(), target: core3D.controls.target.clone() };
            recordAction({ type: 'camera', prevState: prevState, newState: newState });
        }
        // Zoom out function
        function zoomOut() {
            const core3D = window.getSimulationCoreObjects();
            if (!core3D.camera || !core3D.controls) { console.warn("Three.js not initialized for zoom."); return; }
            const prevState = { position: core3D.camera.position.clone(), rotation: core3D.camera.rotation.clone(), target: core3D.controls.target.clone() };
            core3D.camera.position.z += 1;
            core3D.controls.update();
            const newState = { position: core3D.camera.position.clone(), rotation: core3D.camera.rotation.clone(), target: core3D.controls.target.clone() };
            recordAction({ type: 'camera', prevState: prevState, newState: newState });
        }

        // --- HISTORY MANAGEMENT FUNCTIONS ---
        function saveHistoryToLocalStorage() {
            try {
                localStorage.setItem(LOCAL_STORAGE_HISTORY_KEY, JSON.stringify(appHistory));
                localStorage.setItem(LOCAL_STORAGE_HISTORY_INDEX_KEY, appHistoryIndex);
            } catch (e) {
                console.error("Error saving history to Local Storage:", e);
            }
        }
        function loadHistoryFromLocalStorage() {
            try {
                const savedHistory = localStorage.getItem(LOCAL_STORAGE_HISTORY_KEY);
                const savedIndex = localStorage.getItem(LOCAL_STORAGE_HISTORY_INDEX_KEY);

                if (savedHistory) {
                    appHistory = JSON.parse(savedHistory);
                    appHistory.forEach(action => {
                        // Recreate Vector3/Euler from plain objects for camera states in history
                        if (action.type === 'camera') {
                            if (action.prevState && action.prevState.position) action.prevState.position = new THREE.Vector3().copy(action.prevState.position);
                            if (action.prevState && action.prevState.rotation) action.prevState.rotation = new THREE.Euler().copy(action.prevState.rotation);
                            if (action.prevState && action.prevState.target) action.prevState.target = new THREE.Vector3().copy(action.prevState.target);
                            
                            if (action.newState && action.newState.position) action.newState.position = new THREE.Vector3().copy(action.newState.position);
                            if (action.newState && action.newState.rotation) action.newState.rotation = new THREE.Euler().copy(action.newState.rotation);
                            if (action.newState && action.newState.target) action.newState.target = new THREE.Vector3().copy(action.newState.target);
                        }
                    });
                } else {
                    appHistory = [];
                }

                if (savedIndex !== null) {
                    appHistoryIndex = parseInt(savedIndex, 10);
                    if (isNaN(appHistoryIndex) || appHistoryIndex < -1 || appHistoryIndex >= appHistory.length) {
                        appHistoryIndex = appHistory.length - 1;
                    }
                } else {
                    appHistoryIndex = appHistory.length - 1;
                }
            } catch (e) {
                console.error("Error loading history from Local Storage:", e);
                appHistory = [];
                appHistoryIndex = -1;
            }
        }

        // Initialize history from local storage on page load
        function recordAction(action) {
            appHistory = appHistory.slice(0, appHistoryIndex + 1);
            appHistory.push(action);

            if (appHistory.length > MAX_HISTORY_SIZE) {
                appHistory.shift();
            }

            appHistoryIndex = appHistory.length - 1;
            saveHistoryToLocalStorage();
        }

        // --- CAMERA STATE FUNCTIONS ---
        window.revertCameraState = revertCameraState;
        window.applyCameraState = applyCameraState;
        function revertCameraState(state) {
            const core3D = window.getSimulationCoreObjects();
            if (state && core3D.camera && core3D.controls) {
                // Kill any ongoing GSAP animations on camera/controls.target
                gsap.killTweensOf(core3D.camera.position);
                gsap.killTweensOf(core3D.controls.target);

                core3D.camera.position.copy(state.position);
                core3D.camera.rotation.copy(state.rotation);
                core3D.controls.target.copy(state.target);
                core3D.controls.enabled = true; // Ensure controls are enabled
                core3D.controls.update();
            } else {
                // Fallback to a default camera state if the saved state is invalid or missing
                if (core3D.camera && core3D.controls) {
                    core3D.camera.position.set(0, 0, 5); // Default camera position
                    core3D.camera.rotation.set(0, 0, 0); // Default camera rotation
                    core3D.controls.target.set(0, 0, 0); // Default controls target
                    core3D.controls.enabled = true; // Ensure controls are enabled
                    core3D.controls.update();
                }
            }
            // If closeView was enabled/disabled, revert that state as well
            if (state && typeof state.closeView !== 'undefined') {
                window.closeViewEnabled = state.closeView;
                document.getElementById('closeViewButton').textContent = window.closeViewEnabled ? 'Normal View' : 'Close View';
                // Trigger active mesh update for all satellites if necessary
                window.activeSatellites.forEach(sat => sat.setActiveMesh(window.closeViewEnabled));
            }
        }

        // Function to apply a camera state, updating the camera and controls
        function applyCameraState(state) {
            const core3D = window.getSimulationCoreObjects();
            if (state && core3D.camera && core3D.controls) {
                gsap.killTweensOf(core3D.camera.position);
                gsap.killTweensOf(core3D.controls.target);
                
                core3D.camera.position.copy(state.position);
                core3D.camera.rotation.copy(state.rotation);
                core3D.controls.target.copy(state.target);
                core3D.controls.enabled = true; // Ensure controls are enabled
                core3D.controls.update();
            }
            // If closeView was enabled/disabled, apply that state as well
            if (state && typeof state.closeView !== 'undefined') {
                window.closeViewEnabled = state.closeView;
                document.getElementById('closeViewButton').textContent = window.closeViewEnabled ? 'Normal View' : 'Close View';
                 // Trigger active mesh update for all satellites if necessary
                window.activeSatellites.forEach(sat => sat.setActiveMesh(window.closeViewEnabled));
            }
        }

        // Function to apply an animation state, updating the global flags and UI
        function applyAnimationState(state) {
            window.isAnimating = state.isAnimating;
            window.currentSpeedMultiplier = state.speedMultiplier !== undefined ? state.speedMultiplier : 1;
            updateAnimationDisplay();
            setActiveControlButton(window.isAnimating ? 'startButton' : 'pauseButton');
        }

        // --- FILE MANAGEMENT FUNCTIONS ---
        function revertAddFile(fileName, fileData, fileType) {
            if (fileType === 'single' || fileType === 'constellation') {
                fileOutputs.delete(fileName);
                window.removeObjectFromScene(fileName, 'satellite');
            } else if (fileType === 'groundStation') {
                groundStations.delete(fileName);
                window.removeObjectFromScene(fileName, 'groundStation');
            } else if (fileType === 'linkBudget') {
                linkBudgetAnalyses.delete(fileName);
            }
            saveFilesToLocalStorage();
            const listItem = document.querySelector(`li[data-file-name="${fileName}"][data-file-type="${fileType}"]`);
            if (listItem) listItem.remove();
            updateOutputSidebar(null); // Clear output if removed item was displayed
            updateSatelliteListUI(); // Refresh list if a satellite was removed
        }

        // Function to apply an add operation, adding the file to the scene and local storage
        // This function is called when a new file is added or an existing file is updated
        function applyAddFile(fileName, fileData, fileType) {
            if (fileType === 'single' || fileType === 'constellation') {
                fileOutputs.set(fileName, fileData);
                window.addOrUpdateSatelliteInScene(fileData);
            } else if (fileType === 'groundStation') {
                groundStations.set(fileName, fileData);
                window.addOrUpdateGroundStationInScene(fileData);
            } else if (fileType === 'linkBudget') {
                linkBudgetAnalyses.set(fileName, fileData);
            }
            saveFilesToLocalStorage();
            addFileToResourceSidebar(fileName, fileData, fileType);
            updateOutputSidebar(fileData); // Show this new/updated item in output
            updateSatelliteListUI(); // Refresh list if a satellite was added
        }

        // Function to revert a delete operation, re-adding the file back to the scene and local storage
        function revertDeleteFile(fileName, fileData, fileType) {
            applyAddFile(fileName, fileData, fileType); // Re-add the deleted file
        }

        // Function to apply a delete operation, removing the file from the scene and local storage 
        function applyDeleteFile(fileName, fileData, fileType) {
            revertAddFile(fileName, fileData, fileType); // Re-delete the re-added file
        }

        // Function to revert an edit operation, restoring the old data
        function revertEditFile(fileName, oldData, fileType) {
            if (fileType === 'single' || fileType === 'constellation') {
                fileOutputs.set(fileName, oldData);
                window.addOrUpdateSatelliteInScene(oldData);
            } else if (fileType === 'groundStation') {
                groundStations.set(fileName, oldData);
                window.addOrUpdateGroundStationInScene(oldData);
            } else if (fileType === 'linkBudget') { // Assuming edit for link budget means apply the old calculated data
                linkBudgetAnalyses.set(fileName, oldData);
            }
            saveFilesToLocalStorage();
            addFileToResourceSidebar(fileName, oldData, fileType); // Re-add/update sidebar entry
            updateOutputSidebar(oldData); // Update output display
            updateSatelliteListUI(); // Refresh UI lists
            selectSatellite(fileName); // Re-select to update data display (for satellites)
        }


        // Function to apply edits to a file, updating the scene and local storage
        function applyEditFile(fileName, newData, fileType) {
            if (fileType === 'single' || fileType === 'constellation') {
                fileOutputs.set(fileName, newData);
                window.addOrUpdateSatelliteInScene(newData);
            } else if (fileType === 'groundStation') {
                groundStations.set(fileName, newData);
                window.addOrUpdateGroundStationInScene(newData);
            } else if (fileType === 'linkBudget') { // Assuming edit for link budget means apply the new calculated data
                linkBudgetAnalyses.set(fileName, newData);
            }
            saveFilesToLocalStorage();
            addFileToResourceSidebar(fileName, newData, fileType); // Re-add/update sidebar entry
            updateOutputSidebar(newData); // Update output display
            updateSatelliteListUI(); // Refresh UI lists
            selectSatellite(fileName); // Re-select to update data display (for satellites)
        }
        
        // Undo function to revert the last action
        function undoOperation() {
            if (appHistoryIndex >= 0) {
                const action = appHistory[appHistoryIndex];
                appHistoryIndex--;
                saveHistoryToLocalStorage();

                switch (action.type) {
                    case 'camera':
                        revertCameraState(action.prevState);
                        break;
                    case 'animationState':
                    case 'animationSpeed':
                        applyAnimationState(action.prevState);
                        break;
                    case 'addFile':
                        revertAddFile(action.fileName, action.fileData, action.fileType);
                        break;
                    case 'deleteFile':
                        revertDeleteFile(action.fileName, action.fileData, action.fileType);
                        break;
                    case 'editFile':
                        revertEditFile(action.fileName, action.oldData, action.fileType);
                        break;
                    case 'viewToggle':
                        // Revert both 2D view and close view states
                        is2DViewActive = action.prevState.is2D;
                        window.closeViewEnabled = action.prevState.closeView;
                        document.getElementById('closeViewButton').textContent = window.closeViewEnabled ? 'Normal View' : 'Close View';
                        toggle2DViewVisuals();
                        window.activeSatellites.forEach(sat => sat.setActiveMesh(window.closeViewEnabled)); // Ensure meshes are correct
                        const core3D = window.getSimulationCoreObjects();
                        revertCameraState(action.prevState); // Revert camera for viewToggle
                        break;
                    default:
                        console.warn("Unknown action type for undo:", action.type);
                }
                updateSatelliteListUI(); // Ensure UI lists are up to date after undo/redo
                // Attempt to re-select the original selected item if it still exists
                const currentSelectedData = fileOutputs.get(window.selectedSatelliteId) || groundStations.get(window.selectedSatelliteId) || linkBudgetAnalyses.get(window.selectedSatelliteId);
                if (currentSelectedData) updateOutputSidebar(currentSelectedData);
                else updateOutputSidebar(null);
            } else {
                showCustomAlert("Tidak ada tindakan untuk diurungkan.");
            }
        }

        // Redo function to re-apply the last undone action
        function redoOperation() {
            if (appHistoryIndex < appHistory.length - 1) {
                appHistoryIndex++;
                saveHistoryToLocalStorage();
                const action = appHistory[appHistoryIndex];

                switch (action.type) {
                    case 'camera':
                        applyCameraState(action.newState);
                        break;
                    case 'animationState':
                    case 'animationSpeed':
                        applyAnimationState(action.newState);
                        break;
                    case 'addFile':
                        applyAddFile(action.fileName, action.fileData, action.fileType);
                        break;
                    case 'deleteFile':
                        applyDeleteFile(action.fileName, action.fileData, action.fileType);
                        break;
                    case 'editFile':
                        applyEditFile(action.fileName, action.newData, action.fileType);
                        break;
                    case 'viewToggle':
                        // Apply both 2D view and close view states
                        is2DViewActive = action.newState.is2D;
                        window.closeViewEnabled = action.newState.closeView;
                        document.getElementById('closeViewButton').textContent = window.closeViewEnabled ? 'Normal View' : 'Close View';
                        toggle2DViewVisuals();
                        window.activeSatellites.forEach(sat => sat.setActiveMesh(window.closeViewEnabled)); // Ensure meshes are correct
                        const core3D = window.getSimulationCoreObjects();
                        applyCameraState(action.newState); // Apply camera for viewToggle
                        break;
                    default:
                        console.warn("Unknown action type for redo:", action.type);
                }
                updateSatelliteListUI(); // Ensure UI lists are up to date after undo/redo
                const currentSelectedData = fileOutputs.get(window.selectedSatelliteId) || groundStations.get(window.selectedSatelliteId) || linkBudgetAnalyses.get(window.selectedSatelliteId);
                if (currentSelectedData) updateOutputSidebar(currentSelectedData);
                else updateOutputSidebar(null);
            } else {
                showCustomAlert("Tidak ada tindakan untuk diulang.");
            }
        }

        // ------------------------------------- SATELLITE LIST UI FUNCTIONS ----------------------------------------
        // Function to select an output item (satellite, ground station, or link budget)
// Add this new function to your existing script
        function highlightOutputButton(id, type) {
            // Remove active class from all buttons
            document.querySelectorAll('#satelliteButtonsContainer .satellite-button').forEach(btn => {
                btn.classList.remove('active');
            });
            // Add active class to the newly selected button
            const selectedButton = document.querySelector(`#satelliteButtonsContainer .satellite-button[data-id="${id}"][data-type="${type}"]`);
            if (selectedButton) {
                selectedButton.classList.add('active');
            }
        }

        // Locate your existing updateSatelliteListUI function and replace its content with this:
        function updateSatelliteListUI() {
            const mainContainer = document.getElementById("satelliteButtonsContainer");
            if (!mainContainer) {
                console.warn("Element #satelliteButtonsContainer not found.");
                return;
            }
            mainContainer.innerHTML = ''; // Clear previous content

            const satelliteListDisplay = document.getElementById("satelliteListDisplay");
            const satelliteDataDisplay = document.getElementById("satelliteDataDisplay");

            // Hide data display initially unless an item is explicitly selected
            satelliteDataDisplay.style.display = 'none';

            // Check if there are any items to display across all categories
            const hasAnyItems = fileOutputs.size > 0 || groundStations.size > 0 || linkBudgetAnalyses.size > 0;

            if (hasAnyItems) {
                if (satelliteListDisplay) satelliteListDisplay.style.display = 'block';

                // --- Create containers for each category ---
                // Single Satellites
                mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Single Satellites:</h6><div id="singleSatButtons" class="btn-group-container"></div>`);
                const singleSatButtonsContainer = document.getElementById('singleSatButtons');

                // Constellations
                mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Constellations:</h6><div id="constellationButtons" class="btn-group-container"></div>`);
                const constellationButtonsContainer = document.getElementById('constellationButtons');

                // Ground Stations
                mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Ground Stations:</h6><div id="groundStationButtons" class="btn-group-container"></div>`);
                const groundStationButtonsContainer = document.getElementById('groundStationButtons');

                // Link Budget Analyses
                mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Link Budget Analyses:</h6><div id="linkBudgetButtons" class="btn-group-container"></div>`);
                const linkBudgetButtonsContainer = document.getElementById('linkBudgetButtons');


                // --- Populate buttons into their respective containers ---

                // Add buttons for all saved satellites (single and constellation)
                fileOutputs.forEach(data => {
                    const button = document.createElement("button");
                    button.className = "satellite-button";
                    button.innerText = data.fileName;
                    button.setAttribute('data-id', data.fileName);
                    button.setAttribute('data-type', data.fileType);
                    button.onclick = () => {
                        selectOutputItem(data.fileName, data.fileType);
                    };
                    if (data.fileType === 'single') {
                        singleSatButtonsContainer.appendChild(button);
                    } else if (data.fileType === 'constellation') {
                        constellationButtonsContainer.appendChild(button);
                    }
                });

                // Add buttons for all saved ground stations
                groundStations.forEach(data => {
                    const button = document.createElement("button");
                    button.className = "satellite-button";
                    button.innerText = data.name + " (GS)";
                    button.setAttribute('data-id', data.name);
                    button.setAttribute('data-type', data.fileType);
                    button.onclick = () => {
                        selectOutputItem(data.name, data.fileType);
                    };
                    groundStationButtonsContainer.appendChild(button);
                });

                // Add buttons for all saved link budget analyses
                linkBudgetAnalyses.forEach(data => {
                    const button = document.createElement("button");
                    button.className = "satellite-button";
                    button.innerText = data.name + " (LB)";
                    button.setAttribute('data-id', data.name);
                    button.setAttribute('data-type', data.fileType);
                    button.onclick = () => {
                        selectOutputItem(data.name, data.fileType);
                    };
                    linkBudgetButtonsContainer.appendChild(button);
                });

                // Hide empty categories (optional, but makes UI cleaner)
                if (singleSatButtonsContainer.children.length === 0) {
                    singleSatButtonsContainer.previousElementSibling.style.display = 'none'; // Hide heading
                    singleSatButtonsContainer.style.display = 'none'; // Hide container
                }
                if (constellationButtonsContainer.children.length === 0) {
                    constellationButtonsContainer.previousElementSibling.style.display = 'none';
                    constellationButtonsContainer.style.display = 'none';
                }
                if (groundStationButtonsContainer.children.length === 0) {
                    groundStationButtonsContainer.previousElementSibling.style.display = 'none';
                    groundStationButtonsContainer.style.display = 'none';
                }
                if (linkBudgetButtonsContainer.children.length === 0) {
                    linkBudgetButtonsContainer.previousElementSibling.style.display = 'none';
                    linkBudgetButtonsContainer.style.display = 'none';
                }

            } else {
                // If no items exist, hide the entire section and clear any selected state
                if (satelliteListDisplay) satelliteListDisplay.style.display = 'none';
                window.selectedSatelliteId = null;
                window.selectedItemType = null;
                updateOutputSidebar(null);
                return;
            }

            // Re-select and highlight the previously selected item on UI refresh
            let itemToSelectId = null;
            let itemToSelectType = null;

            if (window.selectedSatelliteId && (fileOutputs.has(window.selectedSatelliteId) || groundStations.has(window.selectedSatelliteId) || linkBudgetAnalyses.has(window.selectedSatelliteId))) {
                itemToSelectId = window.selectedSatelliteId;
                // Determine the type from the existing maps
                if (fileOutputs.has(itemToSelectId)) itemToSelectType = fileOutputs.get(itemToSelectId).fileType;
                else if (groundStations.has(itemToSelectId)) itemToSelectType = groundStations.get(itemToSelectId).fileType;
                else if (linkBudgetAnalyses.has(itemToSelectId)) itemToSelectType = linkBudgetAnalyses.get(itemToSelectId).fileType;
            } else if (fileOutputs.size > 0) { // If no previous selection, default to the first single satellite if available
                const firstSingleSat = Array.from(fileOutputs.values()).find(data => data.fileType === 'single');
                if (firstSingleSat) {
                    itemToSelectId = firstSingleSat.fileName;
                    itemToSelectType = firstSingleSat.fileType;
                } else { // Or first constellation
                    const firstConstellation = Array.from(fileOutputs.values()).find(data => data.fileType === 'constellation');
                    if (firstConstellation) {
                        itemToSelectId = firstConstellation.fileName;
                        itemToSelectType = firstConstellation.fileType;
                    }
                }
            } else if (groundStations.size > 0) { // Or first ground station
                itemToSelectId = groundStations.keys().next().value;
                itemToSelectType = groundStations.get(itemToSelectId).fileType;
            } else if (linkBudgetAnalyses.size > 0) { // Or first link budget
                itemToSelectId = linkBudgetAnalyses.keys().next().value;
                itemToSelectType = linkBudgetAnalyses.get(itemToSelectId).fileType;
            }

            if (itemToSelectId && itemToSelectType) {
                selectOutputItem(itemToSelectId, itemToSelectType); // Re-select to update UI and data
            } else {
                // If no item is available to select, ensure displays are hidden and buttons cleared
                window.selectedSatelliteId = null;
                window.selectedItemType = null;
                satelliteDataDisplay.style.display = 'none';
                updateOutputSidebar(null);
            }
        }

        // Add this new function to your existing script, preferably near selectSatellite/selectGroundStation
        function selectOutputItem(id, type) {
            // Highlight the button in the output list
            highlightOutputButton(id, type);

            let selectedData = null;
            // Set global selected ID and type
            window.selectedSatelliteId = id;
            window.selectedItemType = type;

            if (type === 'single' || type === 'constellation') {
                selectedData = fileOutputs.get(id);
                updateSatelliteDataDisplay(); // Update data display for satellites/constellations
                document.getElementById("satelliteDataDisplay").style.display = 'block';

            } else if (type === 'groundStation') {
                selectedData = groundStations.get(id);
                document.getElementById("satelliteDataDisplay").style.display = 'none'; // Hide satellite data display for GS
                // Optionally, you might have a dedicated GS data display here
                if (window.viewSimulation) window.viewSimulation(null); // Clear existing satellites
                if (window.addOrUpdateGroundStationInScene) window.addOrUpdateGroundStationInScene(selectedData); // Show only this GS

            } else if (type === 'linkBudget') {
                selectedData = linkBudgetAnalyses.get(id);
                document.getElementById("satelliteDataDisplay").style.display = 'none'; // Hide satellite data display for LB
            }

            // Update the action buttons (View, Edit, Delete) based on the selected item's data
            updateOutputSidebar(selectedData);
        }

        // **CRUCIAL LINES FOR YOUR ERROR:**
        window.updateSatelliteListUI = updateSatelliteListUI;
        window.selectSatellite = selectSatellite;
        window.updateSatelliteDataDisplay = updateSatelliteDataDisplay;
        window.selectGroundStation = selectGroundStation; // Ensure this is also exposed

        // Function to select a ground station and update the UI
        function selectSatellite(id) {
            // Only update if selection truly changes
            if (window.selectedSatelliteId === id) return;

            // Remove active class from previously selected button
            if (window.selectedSatelliteId) {
                const prevButton = document.querySelector(`#satelliteButtonsContainer .satellite-button.active`);
                if (prevButton) prevButton.classList.remove('active');
            }

            window.selectedSatelliteId = id; // Set new selected ID

            const satelliteDataDisplay = document.getElementById("satelliteDataDisplay");
            if (window.selectedSatelliteId) {
                const newButton = document.querySelector(`.satellite-button[data-id="${window.selectedSatelliteId}"]`);
                if (newButton) newButton.classList.add('active'); // Add active class to new button
                if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'block'; // Show data display
            } else {
                if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none'; // Hide data display
            }
            updateSatelliteDataDisplay(); // Call to update the data shown in the detail panel
        }

        // Add this function to your existing script, for example, below selectSatellite:
        window.selectGroundStation = selectGroundStation;

        function selectGroundStation(id) {
            // Only update if selection truly changes
            selectOutputItem(id, groundStations.get(id)?.fileType || 'groundStation');
        }

        function updateSatelliteDataDisplay() {
            const displayDiv = document.getElementById("satelliteDataDisplay");
            const selectedSat = window.activeSatellites.get(window.selectedSatelliteId);

            if (selectedSat) {
                if (displayDiv) displayDiv.style.display = 'block';

                // Ensure elements exist before trying to update innerText
                if (document.getElementById("dataName")) document.getElementById("dataName").innerText = selectedSat.name;

                // Altitude: Convert scene units back to KM for display (selectedSat.mesh.position.length() is in scene units)
                // Assuming EarthRadius (from parametersimulation.js) is in KM and SCENE_EARTH_RADIUS is 1.
                // Altitude = (distance from origin in scene units * KM_per_scene_unit) - EarthRadius_in_KM
                const kmPerSceneUnit = EarthRadius; // If SCENE_EARTH_RADIUS is 1, then EarthRadius is the scaling factor
                const currentAltitudeKm = (selectedSat.mesh.position.length() * kmPerSceneUnit) - EarthRadius;
                if (document.getElementById("dataAltitude")) document.getElementById("dataAltitude").innerText = currentAltitudeKm.toFixed(2);

                const { orbitalPeriod, orbitalVelocity } = calculateDerivedOrbitalParameters(
                    selectedSat.params.semiMajorAxis - EarthRadius, // orbitalCalculation expects altitude in KM
                    selectedSat.params.eccentricity
                );
                if (document.getElementById("dataOrbitalPeriod")) document.getElementById("dataOrbitalPeriod").innerText = (orbitalPeriod / 60).toFixed(2);
                if (document.getElementById("dataOrbitalVelocity")) document.getElementById("dataOrbitalVelocity").innerText = orbitalVelocity.toFixed(2);
                if (document.getElementById("dataPosition")) document.getElementById("dataPosition").innerText = `(${selectedSat.mesh.position.x.toFixed(3)}, ${selectedSat.mesh.position.y.toFixed(3)}, ${selectedSat.mesh.position.z.toFixed(3)})`;
                if (document.getElementById("dataInclination")) document.getElementById("dataInclination").innerText = (selectedSat.params.inclinationRad * (180 / Math.PI)).toFixed(2);
                if (document.getElementById("dataEccentricity")) document.getElementById("dataEccentricity").innerText = selectedSat.params.eccentricity.toFixed(4);
                if (document.getElementById("dataRaan")) document.getElementById("dataRaan").innerText = (selectedSat.currentRAAN * (180 / Math.PI)).toFixed(2);
                if (document.getElementById("dataArgPerigee")) document.getElementById("dataArgPerigee").innerText = (selectedSat.params.argPerigeeRad * (180 / Math.PI)).toFixed(2);
                if (document.getElementById("dataTrueAnomaly")) document.getElementById("dataTrueAnomaly").innerText = (selectedSat.currentTrueAnomaly * (180 / Math.PI)).toFixed(2);
            } else {
                if (displayDiv) displayDiv.style.display = 'none'; // Hide if no satellite is selected
            }
        }

      // --- LOGOUT FUNCTION ---
        window.handleLogout = handleLogout; // Expose this one too
        function handleLogout() {
            console.log("handleLogout function called.");
            // Clear all local storage related to the simulation
            localStorage.removeItem(LOCAL_STORAGE_FILES_KEY);
            localStorage.removeItem(LOCAL_STORAGE_GROUND_STATIONS_KEY);
            localStorage.removeItem(LOCAL_STORAGE_LINK_BUDGETS_KEY);
            localStorage.removeItem(LOCAL_STORAGE_HISTORY_KEY);
            localStorage.removeItem(LOCAL_STORAGE_HISTORY_INDEX_KEY);
            localStorage.removeItem(SIMULATION_STATE_KEY); // Clear the main simulation state
            localStorage.removeItem(FIRST_LOAD_FLAG_KEY); // Clear the first load flag

            // Clear in-memory data
            fileOutputs.clear();
            groundStations.clear();
            linkBudgetAnalyses.clear();
            appHistory = [];
            appHistoryIndex = -1;

            // Clear 3D scene objects
            if (window.clearSimulationScene) {
                window.clearSimulationScene();
            }

            // Clear UI elements
            document.querySelector('#single-files-list ul').innerHTML = '';
            document.querySelector('#constellation-files-list ul').innerHTML = '';
            document.querySelector('#ground-station-resource-list ul').innerHTML = '';
            document.querySelector('#link-budget-resource-list ul').innerHTML = '';
            document.querySelector('#output-menu ul').innerHTML = '';
            updateSatelliteListUI(); // Reset satellite list display

            console.log("Redirecting to homepage...");
            window.location.href = "/"; // Redirect to your home page or login page
        }
      
      
      // --- DOMContentLoaded: Initial setup and load ---
        document.addEventListener('DOMContentLoaded', function () {
            // Initial load of files and history
            const filesLoaded = loadFilesFromLocalStorage(); // This will clear storage on first load

            // Only load history if files were actually loaded (i.e., not first launch)
            if (filesLoaded) {
                loadHistoryFromLocalStorage(); // This populates appHistory
            } else {
                console.log("Skipping history load as it's the first launch (data cleared).");
                appHistory = [];
                appHistoryIndex = -1;
            }

                    // --- Attach Event Listeners for Menu Items ---
        document.getElementById('newSingleMenuBtn')?.addEventListener('click', NewSingleMenu);
        document.getElementById('newConstellationMenuBtn')?.addEventListener('click', NewConstellationMenu);
        document.getElementById('newGroundStationMenuBtn')?.addEventListener('click', NewGroundStationMenu);
        document.getElementById('newLinkBudgetMenuBtn')?.addEventListener('click', NewLinkBudgetMenu);

        // --- Attach Event Listeners for View Menu Items ---
        document.getElementById('resetViewBtn')?.addEventListener('click', resetView); // Assuming ID for reset view
        document.getElementById('closeViewButton')?.addEventListener('click', toggleCloseView);
        document.getElementById('toggle2DViewBtn')?.addEventListener('click', toggle2DView); // Assuming ID for 2D view

        // --- Attach Event Listeners for Save Menu Items ---
        document.getElementById('showSavePopupBtn')?.addEventListener('click', showSavePopup); // Assuming ID for save popup
        document.getElementById('loadTleBtn')?.addEventListener('click', Load); // Assuming ID for Load TLE

        // --- Attach Event Listeners for Toolbar Buttons ---
        document.getElementById('startButton')?.addEventListener('click', playAnimation);
        document.getElementById('pauseButton')?.addEventListener('click', pauseAnimation);
        document.getElementById('speedUpButton')?.addEventListener('click', speedUpAnimation);
        document.getElementById('slowDownButton')?.addEventListener('click', slowDownAnimation);
        document.getElementById('undoButton')?.addEventListener('click', undoOperation);
        document.getElementById('redoButton')?.addEventListener('click', redoOperation);
        document.getElementById('logoutButton')?.addEventListener('click', handleLogout);

        // --- Attach Event Listeners for Zoom Controls ---
        document.getElementById('zoomInButton')?.addEventListener('click', zoomIn);
        document.getElementById('zoomOutButton')?.addEventListener('click', zoomOut);

        // --- Attach Event Listeners for Modal Close Buttons ---
        // If you named them modalCloseBtn and modalFooterCloseBtn as suggested
        document.getElementById('modalCloseBtn')?.addEventListener('click', closepopup);
        document.getElementById('modalFooterCloseBtn')?.addEventListener('click', closepopup);

        // --- Attach Event Listeners for Sidebar Tab Buttons ---
        document.getElementById('resourceTabBtn')?.addEventListener('click', function() { toggleTab('resource-menu', this); });
        document.getElementById('outputTabBtn')?.addEventListener('click', function() { toggleTab('output-menu', this); });

            setTimeout(() => {
                // Ensure Earth3Dsimulation.js has fully loaded and exposed its functions
                if (typeof window.load3DSimulationState === 'function') {
                    window.load3DSimulationState(); // Call the exposed loading function to create 3D meshes
                    
                    // After 3D objects are loaded, update the UI lists and selected satellite display
                    updateSatelliteListUI(); // This will handle selecting the first item and updating its display

                    // Initialize the output sidebar with buttons for the initially selected item
                    let initialSelectedData = null;
                    if (window.selectedSatelliteId && window.activeSatellites.has(window.selectedSatelliteId)) {
                        initialSelectedData = fileOutputs.get(window.selectedSatelliteId);
                    } else if (window.activeGroundStations.size > 0) {
                        initialSelectedData = groundStations.values().next().value;
                    }
                    updateOutputSidebar(initialSelectedData); // Pass the data to show buttons

                } else {
                    console.error("Critical: load3DSimulationState function not found. Earth3Dsimulation.js might not be loaded or exposed correctly.");
                    // Fallback UI updates even if 3D initialization fails
                    updateSatelliteListUI();
                    updateOutputSidebar(null); // Clear buttons if 3D not ready
                }
                updateAnimationDisplay(); // Make sure initial animation status is correct
                setActiveControlButton(window.isAnimating ? 'startButton' : 'pauseButton'); // Set correct button state
            }, 500); // Small delay to allow Earth3Dsimulation.js module to execute
        });
    </script>
</body>
</html>