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

        .satellite-label {
            text-shadow: 0 0 3px rgba(0,0,0,0.7);
            transform: translateX(-50%);
        }

        /* New styles for constellation toggle and members */
        .constellation-group {
            border: 1px solid #003366;
            border-radius: 5px;
            margin-bottom: 5px;
            overflow: hidden; /* Ensures rounded corners apply to content */
        }

        .constellation-toggle {
            background-color: #004080 !important; /* Darker blue for constellation main button */
            width: 100%;
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            font-size: 0.9em;
        }

        .constellation-toggle:hover {
            background-color: #0056b3 !important;
        }

        .constellation-members-list {
            background-color: #00274e; /* Background for the dropdown list */
            padding: 5px;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            border-top: 1px solid #003366;
        }

        .constellation-members-list .satellite-button {
            background-color: #0056b3; /* Slightly different color for members */
            padding: 4px 8px;
            font-size: 0.8em;
        }

        .constellation-members-list .satellite-button:hover {
            background-color: #007bff;
        }

        .toggle-icon {
            margin-left: 10px;
            transition: transform 0.2s ease;
        }

        .constellation-members-list.hidden {
            display: none;
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
            <p><strong>Latitude:</strong> <span id="dataLatitude"></span>°</p>
            <p><strong>Longitude:</strong> <span id="dataLongitude"></span>°</p>
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
                const savedFiles = localStorage.getItem(LOCAL_STORAGE_FILES_KEY);
                if (savedFiles) {
                    fileOutputs = new Map(JSON.parse(savedFiles));
                } else {
                    fileOutputs = new Map();
                }

                const savedGroundStations = localStorage.getItem(LOCAL_STORAGE_GROUND_STATIONS_KEY);
                if (savedGroundStations) {
                    groundStations = new Map(JSON.parse(savedGroundStations));
                } else {
                    groundStations = new Map();
                }

                const savedLinkBudgets = localStorage.getItem(LOCAL_STORAGE_LINK_BUDGETS_KEY);
                if (savedLinkBudgets) {
                    linkBudgetAnalyses = new Map(JSON.parse(savedLinkBudgets));
                } else {
                    linkBudgetAnalyses = new Map();
                }

                // Load history, but ensure it's within bounds
                const savedHistory = localStorage.getItem(LOCAL_STORAGE_HISTORY_KEY);
                const savedHistoryIndex = localStorage.getItem(LOCAL_STORAGE_HISTORY_INDEX_KEY);
                if (savedHistory) {
                    appHistory = JSON.parse(savedHistory);
                    appHistoryIndex = savedHistoryIndex !== null ? parseInt(savedHistoryIndex) : -1;
                    // Trim history if it's too large from a previous session
                    if (appHistory.length > MAX_HISTORY_SIZE) {
                        appHistory = appHistory.slice(appHistory.length - MAX_HISTORY_SIZE);
                        appHistoryIndex = appHistory.length -1; // Adjust index
                    }
                } else {
                    appHistory = [];
                    appHistoryIndex = -1;
                }

                // Restore simulation state (isAnimating, speed, etc.)
                const savedSimulationState = localStorage.getItem(SIMULATION_STATE_KEY);
                if (savedSimulationState) {
                    const state = JSON.parse(savedSimulationState);
                    if (window.getSimulationCoreObjects) {
                        const core3D = window.getSimulationCoreObjects();
                        core3D.setIsAnimating(state.isAnimating);
                        core3D.setCurrentSpeedMultiplier(state.currentSpeedMultiplier);
                        core3D.setTotalSimulatedTime(state.totalSimulatedTime);
                        core3D.setCurrentEpochUTC(state.currentEpochUTC);
                        // selectedSatelliteId and closeViewEnabled will be handled by updateSatelliteListUI and load3DSimulationState
                    }
                }

            } catch (e) {
                console.error("Error loading files from Local Storage:", e);
                // Fallback to empty maps in case of parsing error
                fileOutputs = new Map();
                groundStations = new Map();
                linkBudgetAnalyses = new Map();
                appHistory = [];
                appHistoryIndex = -1;
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
                    let dataForButtons; // Data to pass to viewSimulation and selectOutputItem

                    if (clickedFileType === 'single' || clickedFileType === 'constellation') {
                        dataForButtons = fileOutputs.get(clickedFileName);
                    } else if (clickedFileType === 'groundStation') {
                        dataForButtons = groundStations.get(clickedFileName);
                    } else if (clickedFileType === 'linkBudget') {
                        dataForButtons = linkBudgetAnalyses.get(clickedFileName);
                    }

                    if (dataForButtons) {
                        // This is the "reset and show" action
                        window.viewSimulation(dataForButtons);
                        // After loading into simulation, select the item in the output tab
                        window.selectOutputItem(clickedFileName, clickedFileType);
                    } else {
                        console.warn(`Data for ${clickedFileType} file '${clickedFileName}' not found.`);
                        // Clear output sidebar if data not found
                        updateOutputSidebar(null);
                    }
                    // Always switch to the output tab
                    toggleTab('output-menu', document.getElementById('outputTabBtn'));
                     // …after you’ve toggled into the Output tab…
                    updateOutputTabForFile(clickedFileName, clickedFileType);
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
                    } else if (clickedFileType === 'linkBudget') { // Added for Link Budget
                        editLinkBudget(clickedFileName);
                    }
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
            // This button's click should re-trigger the full view simulation for the selected item
            viewButton.onclick = () => {
                let dataToView;
                if (data.fileType === 'single' || data.fileType === 'constellation') {
                    dataToView = fileOutputs.get(data.fileName);
                } else if (data.fileType === 'groundStation') {
                    dataToView = groundStations.get(data.name);
                } else if (data.fileType === 'linkBudget') {
                    dataToView = linkBudgetAnalyses.get(data.name);
                }
                if (dataToView) {
                    window.viewSimulation(dataToView);
                    // Ensure the output tab is active and button highlighted after viewing
                    toggleTab('output-menu', document.getElementById('outputTabBtn'));
                    highlightOutputButton(data.fileName || data.name, data.fileType);
                }
            };
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
            } else if (data.fileType === 'linkBudget') { // Added for Link Budget
                editButton.onclick = () => editLinkBudget(data.name);
            } else {
                editButton.style.display = 'none'; // No direct edit for unknown types
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

// ------------------------------------- SATELLITE LIST UI FUNCTIONS ----------------------------------------

/**
 * Highlights the selected output button (satellite, ground station, or link budget).
 * Removes 'active' class from all buttons and adds it to the specified button.
 * @param {string} id - The ID of the item to highlight (e.g., satellite name, ground station name).
 * @param {string} type - The type of the item ('single', 'constellation', 'groundStation', 'linkBudget').
 */
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


/**
 * Updates the display for selected satellite data.
 * Assumes `window.activeSatellites`, `EarthRadius`, and `calculateDerivedOrbitalParameters` are defined globally.
 */
function updateSatelliteDataDisplay() {
    const displayDiv = document.getElementById("satelliteDataDisplay");
    // Ensure selectedSatelliteId and activeSatellites are available
    if (!window.selectedSatelliteId || !window.activeSatellites || !window.activeSatellites.get) {
        if (displayDiv) displayDiv.style.display = 'none';
        return;
    }

    const selectedSat = window.activeSatellites.get(window.selectedSatelliteId);

    if (selectedSat && selectedSat.mesh && selectedSat.mesh.position && selectedSat.params) {
        if (displayDiv) displayDiv.style.display = 'block';

        // Ensure elements exist before trying to update innerText
        if (document.getElementById("dataName")) document.getElementById("dataName").innerText = selectedSat.name;

        // Altitude: Convert scene units back to KM for display (selectedSat.mesh.position.length() is in scene units)
        // Assuming EarthRadius (from parametersimulation.js) is in KM and SCENE_EARTH_RADIUS is 1.
        // Altitude = (distance from origin in scene units * KM_per_scene_unit) - EarthRadius_in_KM
        // Make sure EarthRadius is accessible globally, perhaps through a global variable or import
        const kmPerSceneUnit = typeof EarthRadius !== 'undefined' ? EarthRadius : 6371; // Default to EarthRadius if not defined
        const currentAltitudeKm = (selectedSat.mesh.position.length() * kmPerSceneUnit) - kmPerSceneUnit; // Corrected: subtract EarthRadius from total radius in KM
        if (document.getElementById("dataAltitude")) document.getElementById("dataAltitude").innerText = currentAltitudeKm.toFixed(2);

        // Ensure calculateDerivedOrbitalParameters is accessible globally
        if (typeof calculateDerivedOrbitalParameters === 'function') {
            const { orbitalPeriod, orbitalVelocity } = calculateDerivedOrbitalParameters(
                selectedSat.params.semiMajorAxis - kmPerSceneUnit, // orbitalCalculation expects altitude in KM
                selectedSat.params.eccentricity
            );
            if (document.getElementById("dataOrbitalPeriod")) document.getElementById("dataOrbitalPeriod").innerText = (orbitalPeriod / 60).toFixed(2);
            if (document.getElementById("dataOrbitalVelocity")) document.getElementById("dataOrbitalVelocity").innerText = orbitalVelocity.toFixed(2);
        } else {
            console.warn("calculateDerivedOrbitalParameters function not found. Orbital period and velocity will not be displayed.");
            if (document.getElementById("dataOrbitalPeriod")) document.getElementById("dataOrbitalPeriod").innerText = "N/A";
            if (document.getElementById("dataOrbitalVelocity")) document.getElementById("dataOrbitalVelocity").innerText = "N/A";
        }

        if (document.getElementById("dataPosition")) document.getElementById("dataPosition").innerText = `(${selectedSat.mesh.position.x.toFixed(3)}, ${selectedSat.mesh.position.y.toFixed(3)}, ${selectedSat.mesh.position.z.toFixed(3)})`;
        // Add Latitude and Longitude
        if (document.getElementById("dataLatitude"))  document.getElementById("dataLatitude").innerText = selectedSat.latitudeDeg.toFixed(2);
        if (document.getElementById("dataLongitude")) document.getElementById("dataLongitude").innerText = selectedSat.longitudeDeg.toFixed(2);

        if (document.getElementById("dataInclination")) document.getElementById("dataInclination").innerText = (selectedSat.params.inclinationRad * (180 / Math.PI)).toFixed(2);
        if (document.getElementById("dataEccentricity")) document.getElementById("dataEccentricity").innerText = selectedSat.params.eccentricity.toFixed(4);
        if (document.getElementById("dataRaan")) document.getElementById("dataRaan").innerText = (selectedSat.currentRAAN * (180 / Math.PI)).toFixed(2);
        if (document.getElementById("dataArgPerigee")) document.getElementById("dataArgPerigee").innerText = (selectedSat.params.argPerigeeRad * (180 / Math.PI)).toFixed(2);
        if (document.getElementById("dataTrueAnomaly")) document.getElementById("dataTrueAnomaly").innerText = (selectedSat.currentTrueAnomaly * (180 / Math.PI)).toFixed(2);
    } else {
        if (displayDiv) displayDiv.style.display = 'none'; // Hide if no valid satellite data is selected
    }
}


/**
 * Selects an output item (satellite, ground station, or link budget) and updates the UI accordingly.
 * This is the unified function for selecting any item.
 * @param {string} id - The ID of the item to select.
 * @param {string} type - The type of the item ('single', 'constellation', 'groundStation', 'linkBudget').
 */
function selectOutputItem(id, type) {
    // Highlight the button in the output list
    highlightOutputButton(id, type);

    let selectedData = null;
    // Set global selected ID and type
    window.selectedSatelliteId = null; // Clear previous satellite selection
    window.selectedGroundStationId = null; // Clear previous ground station selection
    window.selectedItemType = type;

    const satelliteDataDisplay = document.getElementById("satelliteDataDisplay");

    // Hide all constellation member lists before potentially showing one
    document.querySelectorAll('.constellation-members-list').forEach(list => {
        list.classList.add('hidden');
        const toggleIcon = list.previousElementSibling.querySelector('.toggle-icon');
        if (toggleIcon) toggleIcon.innerText = '▼';
    });

    if (type === 'single') {
        selectedData = fileOutputs.get(id) || (window.activeSatellites ? window.activeSatellites.get(id) : null);
        window.selectedSatelliteId = id; // Set selected satellite for real-time display
        updateSatelliteDataDisplay(); // Update data display for satellites
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'block';
        if (window.highlightSatelliteInScene) window.highlightSatelliteInScene(id);
        if (window.highlightGroundStationInScene) window.highlightGroundStationInScene(null); // Clear GS highlight

    } else if (type === 'constellation') {
        selectedData = fileOutputs.get(id); // constellation data from fileOutputs map
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none'; // Constellations don't have detailed data panel
        if (window.highlightSatelliteInScene) window.highlightSatelliteInScene(null); // Clear satellite highlight
        if (window.highlightGroundStationInScene) window.highlightGroundStationInScene(null); // Clear GS highlight

        // Toggle the visibility of the constellation's member list
        const memberList = document.getElementById(`constellation-${id}-members`);
        if (memberList) {
            memberList.classList.toggle('hidden');
            const toggleIcon = memberList.previousElementSibling.querySelector('.toggle-icon');
            if (toggleIcon) {
                if (memberList.classList.contains('hidden')) {
                    toggleIcon.innerText = '▼';
                } else {
                    toggleIcon.innerText = '▲';
                }
            }
        }
    } else if (type === 'groundStation') {
        selectedData = groundStations.get(id);
        window.selectedGroundStationId = id; // Set selected ground station
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none'; // Hide satellite data display for GS
        if (window.highlightGroundStationInScene) window.highlightGroundStationInScene(id);
        if (window.highlightSatelliteInScene) window.highlightSatelliteInScene(null); // Clear satellite highlight

    } else if (type === 'linkBudget') {
        selectedData = linkBudgetAnalyses.get(id);
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none'; // Hide satellite data display for LB
        if (window.highlightSatelliteInScene) window.highlightSatelliteInScene(null); // Clear satellite highlight
        if (window.highlightGroundStationInScene) window.highlightGroundStationInScene(null); // Clear GS highlight
    }

    // Update the action buttons (View, Edit, Delete) based on the selected item's data
    if (window.updateOutputSidebar) {
        window.updateOutputSidebar(selectedData);
    }
}


/**
 * Function to select a single satellite and update the UI.
 * Delegates to selectOutputItem for unified handling.
 * @param {string} id - The ID of the satellite to select.
 */
function selectSatellite(id) {
    // Determine the type: check if it's a constellation's top-level definition, or a standalone single, or an active satellite instance.
    let itemType;
    if (fileOutputs.has(id)) {
        itemType = fileOutputs.get(id).fileType;
    } else if (window.activeSatellites && window.activeSatellites.has(id)) {
        itemType = 'single'; // Treat as a 'single' if it's just an active satellite instance
    } else {
        console.warn(`Attempted to select satellite with ID '${id}' but its type could not be determined.`);
        return;
    }
    selectOutputItem(id, itemType);
}

/**
 * Function to select a ground station and update the UI.
 * Delegates to selectOutputItem for unified handling.
 * @param {string} id - The ID of the ground station to select.
 */
function selectGroundStation(id) {
    selectOutputItem(id, groundStations.get(id)?.fileType || 'groundStation');
}

/**
 * Updates the satellite list UI, displaying buttons for single satellites, constellations,
 * ground stations, and link budget analyses, with expandable constellation views.
 * Assumes `fileOutputs`, `groundStations`, `linkBudgetAnalyses`, and `window.activeSatellites` maps are globally accessible.
 */
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
    if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none';

    // Check if there are any items to display across all categories
    const hasAnyItems = fileOutputs.size > 0 || groundStations.size > 0 || linkBudgetAnalyses.size > 0;

    if (hasAnyItems) {
        if (satelliteListDisplay) satelliteListDisplay.style.display = 'block';

        // --- Create containers for each category ---
        mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Single Satellites:</h6><div id="singleSatButtons" class="btn-group-container"></div>`);
        const singleSatButtonsContainer = document.getElementById('singleSatButtons');

        mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Constellations:</h6><div id="constellationButtons" class="btn-group-container flex-col"></div>`);
        const constellationButtonsContainer = document.getElementById('constellationButtons');

        mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Ground Stations:</h6><div id="groundStationButtons" class="btn-group-container"></div>`);
        const groundStationButtonsContainer = document.getElementById('groundStationButtons');

        mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Link Budget Analyses:</h6><div id="linkBudgetButtons" class="btn-group-container"></div>`);
        const linkBudgetButtonsContainer = document.getElementById('linkBudgetButtons');


        // --- Populate buttons into their respective containers ---

        // Add buttons for all saved single satellites from fileOutputs
        fileOutputs.forEach(data => {
            if (data.fileType === 'single') {
                const button = document.createElement("button");
                button.className = "satellite-button btn btn-sm btn-primary"; // Using Bootstrap classes
                button.innerText = data.fileName;
                button.setAttribute('data-id', data.fileName);
                button.setAttribute('data-type', data.fileType);
                button.onclick = () => {
                    selectOutputItem(data.fileName, data.fileType);
                };
                singleSatButtonsContainer.appendChild(button);
            }
        });

        // Add buttons for constellations
        fileOutputs.forEach(data => {
            if (data.fileType === 'constellation') {
                constellationGroup = createConstellationGroup(data.fileName);
                constellationButtonsContainer.appendChild(constellationGroup);
            }
        });

        // Add buttons for all saved ground stations
        groundStations.forEach(data => {
            const button = document.createElement("button");
            button.className = "satellite-button btn btn-sm btn-warning"; // Using Bootstrap classes
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
            button.className = "satellite-button btn btn-sm btn-danger"; // Using Bootstrap classes
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
            if (singleSatButtonsContainer.previousElementSibling) singleSatButtonsContainer.previousElementSibling.style.display = 'none'; // Hide heading
            singleSatButtonsContainer.style.display = 'none'; // Hide container
        }
        if (constellationButtonsContainer.children.length === 0) {
            if (constellationButtonsContainer.previousElementSibling) constellationButtonsContainer.previousElementSibling.style.display = 'none';
            constellationButtonsContainer.style.display = 'none';
        }
        if (groundStationButtonsContainer.children.length === 0) {
            if (groundStationButtonsContainer.previousElementSibling) groundStationButtonsContainer.previousElementSibling.style.display = 'none';
            groundStationButtonsContainer.style.display = 'none';
        }
        if (linkBudgetButtonsContainer.children.length === 0) {
            if (linkBudgetButtonsContainer.previousElementSibling) linkBudgetButtonsContainer.previousElementSibling.style.display = 'none';
            linkBudgetButtonsContainer.style.display = 'none';
        }

    } else {
        // If no items exist, hide the entire section and clear any selected state
        if (satelliteListDisplay) satelliteListDisplay.style.display = 'none';
        window.selectedSatelliteId = null;
        window.selectedGroundStationId = null;
        window.selectedItemType = null;
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none';
        if (window.updateOutputSidebar) window.updateOutputSidebar(null);
        return;
    }

    // Re-select and highlight the previously selected item on UI refresh
    let itemToSelectId = null;
    let itemToSelectType = null;

    // Prioritize previously selected item if it still exists
    if (window.selectedSatelliteId || window.selectedGroundStationId) {
        const currentSelectedId = window.selectedSatelliteId || window.selectedGroundStationId;
        const currentSelectedType = window.selectedItemType; // Assuming this is correctly set

        if (fileOutputs.has(currentSelectedId)) {
            itemToSelectId = currentSelectedId;
            itemToSelectType = fileOutputs.get(itemToSelectId).fileType;
        } else if (groundStations.has(currentSelectedId)) {
            itemToSelectId = currentSelectedId;
            itemToSelectType = groundStations.get(itemToSelectId).fileType;
        } else if (linkBudgetAnalyses.has(currentSelectedId)) {
            itemToSelectId = currentSelectedId;
            itemToSelectType = linkBudgetAnalyses.get(itemToSelectId).fileType;
        } else if (window.activeSatellites && window.activeSatellites.has(currentSelectedId)) {
            itemToSelectId = currentSelectedId;
            itemToSelectType = 'single'; // Treat as single for display
        }
    }

    // If no previous selection, or previously selected item no longer exists, default to first available
    if (!itemToSelectId) {
        if (fileOutputs.size > 0) {
            const firstSingleSat = Array.from(fileOutputs.values()).find(data => data.fileType === 'single');
            if (firstSingleSat) {
                itemToSelectId = firstSingleSat.fileName;
                itemToSelectType = firstSingleSat.fileType;
            } else {
                const firstConstellation = Array.from(fileOutputs.values()).find(data => data.fileType === 'constellation');
                if (firstConstellation) {
                    itemToSelectId = firstConstellation.fileName;
                    itemToSelectType = firstConstellation.fileType;
                }
            }
        } else if (groundStations.size > 0) {
            itemToSelectId = groundStations.keys().next().value;
            itemToSelectType = groundStations.get(itemToSelectId).fileType;
        } else if (linkBudgetAnalyses.size > 0) {
            itemToSelectId = linkBudgetAnalyses.keys().next().value;
            itemToSelectType = linkBudgetAnalyses.get(itemToSelectId).fileType;
        }
    }

    if (itemToSelectId && itemToSelectType) {
        selectOutputItem(itemToSelectId, itemToSelectType); // Re-select to update UI and data
    } else {
        // If no item is available to select, ensure displays are hidden and buttons cleared
        window.selectedSatelliteId = null;
        window.selectedGroundStationId = null;
        window.selectedItemType = null;
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none';
        if (window.updateOutputSidebar) window.updateOutputSidebar(null);
    }
}

function createConstellationGroup(fileName) {
    const constellationGroup = document.createElement("div");
    constellationGroup.className = "constellation-group";

    const mainButton = document.createElement("button");
    mainButton.className = "satellite-button constellation-toggle btn btn-sm btn-success";
    mainButton.innerText = fileName;
    mainButton.setAttribute('data-id', fileName);
    mainButton.setAttribute('data-type', 'constellation');
    mainButton.innerHTML += `<span class="toggle-icon">▼</span>`;

    mainButton.onclick = () => {
        const memberList = constellationGroup.querySelector('.constellation-members-list');
        const toggleIcon = mainButton.querySelector('.toggle-icon');
        if (memberList.classList.contains('hidden')) {
            memberList.classList.remove('hidden');
            toggleIcon.innerText = '▲';
        } else {
            memberList.classList.add('hidden');
            toggleIcon.innerText = '▼';
        }
        selectOutputItem(fileName, 'constellation');
    };
    constellationGroup.appendChild(mainButton);

    const memberList = document.createElement("div");
    memberList.id = `constellation-${fileName}-members`;
    memberList.className = "constellation-members-list hidden";

    const constellationData = fileOutputs.get(fileName);
    if (constellationData && constellationData.satellites) {
        constellationData.satellites.forEach(satId => {
            const subButton = createSatelliteButton(satId, 'single');
            memberList.appendChild(subButton);
        });
    }
    constellationGroup.appendChild(memberList);
    return constellationGroup;
}

function createSatelliteButton(id, type) {
    const button = document.createElement("button");
    button.className = "satellite-button btn btn-sm btn-info";
    button.innerText = id;
    button.setAttribute('data-id', id);
    button.setAttribute('data-type', type);
    button.onclick = () => selectOutputItem(id, type);
    return button;
}

// Expose functions to the global window object for accessibility from HTML
window.highlightOutputButton = highlightOutputButton;
window.updateSatelliteDataDisplay = updateSatelliteDataDisplay;
window.selectOutputItem = selectOutputItem;
window.selectSatellite = selectSatellite;
window.selectGroundStation = selectGroundStation;
window.updateSatelliteListUI = updateSatelliteListUI; // Make this available globally as it's a main entry point for UI refresh

// Initial load: Load saved data and update UI
window.onload = function() {
    const navigationEntries = performance.getEntriesByType('navigation');
    if (navigationEntries.length > 0 && navigationEntries[0].type === 'reload') {
        fileOutputs = new Map();
        groundStations = new Map();
        linkBudgetAnalyses = new Map();
        localStorage.removeItem(LOCAL_STORAGE_FILES_KEY);
        localStorage.removeItem(LOCAL_STORAGE_GROUND_STATIONS_KEY);
        localStorage.removeItem(LOCAL_STORAGE_LINK_BUDGETS_KEY);
        clearResourceTab();
        updateSatelliteListUI(); // Assumed function to update Output Tab
    } else {
        loadFilesFromLocalStorage();
        populateResourceTab();
        updateSatelliteListUI();
    }
};

function clearResourceTab() {
    document.querySelector('#single-files-list ul').innerHTML = '';
    document.querySelector('#constellation-files-list ul').innerHTML = '';
    document.querySelector('#ground-station-resource-list ul').innerHTML = '';
    document.querySelector('#link-budget-resource-list ul').innerHTML = '';
}

function populateResourceTab() {
    clearResourceTab();
    fileOutputs.forEach((data, fileName) => {
        addFileToResourceSidebar(fileName, data, data.fileType);
    });
    groundStations.forEach((data, name) => {
        addFileToResourceSidebar(name, data, 'groundStation');
    });
    linkBudgetAnalyses.forEach((data, name) => {
        addFileToResourceSidebar(name, data, 'linkBudget');
    });
}

function updateOutputTabForFile(fileName, fileType) {
    const mainContainer = document.getElementById("satelliteButtonsContainer");
    mainContainer.innerHTML = ''; // Clear all existing buttons

    if (fileType === 'single') {
        const data = fileOutputs.get(fileName);
        if (data) {
            const button = createSatelliteButton(fileName, 'single');
            mainContainer.appendChild(button);
        }
    } else if (fileType === 'constellation') {
        const data = fileOutputs.get(fileName);
        if (data) {
            const constellationGroup = createConstellationGroup(fileName);
            mainContainer.appendChild(constellationGroup);
        }
    }
}