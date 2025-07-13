<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satellite UI/UX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    @vite([
        'resources/js/Earth2Dsimulation.js',
        'resources/js/Earth3Dsimulation.js'
    ])
    <style>
        body {
            font-family: 'Rubik', sans-serif;
            background-color: #16214a;
            color: white;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #00274e !important;
        }

        .nav-link {
            font-size: 14px;
            padding: 6px 12px;
            color: White !important;
        }

        .nav-link:hover {
            background-color: #001f4d;
            border-radius: 4px;
        }
     
        /* Navigation Menu Styles (Top Left)*/
        .custom-contextmenu, .settings-contextmenu {
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

        .custom-contextmenu  li,
        .settings-contextmenu li {
            padding: 2px 10px;
            cursor: pointer;
            white-space: nowrap;
        }

        .contextmenu li:hover, .settings-contextmenu li:hover {
            background-color: rgb(200, 200, 200);
        }

        /* Toolbar Styles (Top Right)*/
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

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: #ffffff; /* Mengubah warna latar belakang sidebar menjadi putih */
            color: rgb(0, 0, 0); /* Mengubah warna font sidebar menjadi hitam */
            display: flex;
            flex-direction: column;
        }

        .menu-content {
            flex-grow: 1;
            overflow-y: auto;
        }
        /* Resource Tab Styles */
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
            /* Tambahkan ini untuk memastikan teks di item list berwarna hitam */
            color: rgb(0, 0, 0);
        }

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
        #constellation-files-list ul li, #ground-station-resource-list ul li, #link-budget-resource-list ul li {
            list-style: none;
            padding-left: 1.5rem;
            cursor: pointer;
            /* Tambahkan ini untuk memastikan teks di sub-item list berwarna hitam */
            color: rgb(0, 0, 0);
        }

        #single-files-list ul li:hover,
        #constellation-files-list ul li:hover,
        #ground-station-resource-list ul li:hover, /* Tambahkan ini */
        #link-budget-resource-list ul li:hover { /* Tambahkan ini juga untuk Link Budget */
            background-color: #e9ecef;
        }

        .sidebar-icon {
            width: 16px;
            text-align: center;
            margin-right: 6px;
            color:rgb(59, 132, 228); /* Mengubah warna ikon menjadi abu-abu gelap agar terlihat di latar putih */
        }

        #output-menu ul {
            list-style: none;
            padding-left: 0.5rem;
            color: rgb(0, 0, 0); /* Mengubah warna font pada output-menu ul menjadi hitam */
        }

        #output-menu ul li {
            list-style: none;
            font-weight: normal;
            cursor: pointer;
            color: rgb(0, 0, 0); /* Mengubah warna font pada output-menu ul li menjadi hitam */
        }

        #output-menu ul li:hover {
            background-color: #e9ecef;
        }

        #earth-container {
            width: 100%;
            height: 100%;
            position: relative;
        }

        #earth2D-container {
            width: 100%;
            height: 100%;
            display: none;
        }

        .hidden {
            display: none;
        }

        /* Sidebar Tab Styles */
        .nav-tabs .nav-link {
            flex: 1;
            text-align: center;
            font-size: 14px;
            padding: 8px 0;
            background-color: rgb(33, 92, 151); /* Warna latar belakang tab default */
            color: white; /* Warna font tab default menjadi putih */
            border-bottom: 1px solid rgb(33, 92, 151); /* Pastikan border bawah sesuai warna biru */
            border-radius: 0; /* Menghilangkan border-radius untuk membuat persegi panjang */
        }

        .nav-tabs .nav-link.active {
            background-color: rgb(33, 92, 151); /* Warna latar belakang tab aktif */
            color: white; /* Warna font tab aktif tetap putih */
            border-bottom: 1px solid rgb(33, 92, 151); /* Pastikan border bawah tab aktif juga biru */
            border-radius: 0; /* Menghilangkan border-radius untuk membuat persegi panjang */
        }

        #animationStatusDisplay { /* Target kedua set */
            position: absolute; /* Penting agar muncul di atas kanvas */
            top: 13px;
            right: 13px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10; /* Pastikan di atas elemen lain */
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        #simulationClockDisplay { /* Target kedua set */
            position: absolute; /* Penting agar muncul di atas kanvas */
            top: 13px;
            left: 13px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10; /* Pastikan di atas elemen lain */
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        #animationStatusDisplay2D { /* Target kedua set */
            position: absolute; /* Penting agar muncul di atas kanvas */
            top: 85px;
            right: 13px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10; /* Pastikan di atas elemen lain */
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        #simulationClockDisplay2D { /* Target kedua set */
            position: absolute; /* Penting agar muncul di atas kanvas */
            top: 85px;
            left: 262px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10; /* Pastikan di atas elemen lain */
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

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

        .zoom-button:hover {
            opacity: 1;
            background-color: rgba(0, 31, 77, 0.8);
        }

        .custom-popup {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            color: black;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            z-index: 10000;
            width: 350px; /* UBAH LEBAR DI SINI, misalnya 350px */
            padding: 0; /* Hapus padding di sini, karena sudah ada di header/body/footer */
            cursor: move;
            border: none;
        }

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
            text-align: center;
        }

        .custom-alert-footer {
            border-top: 1px solid #ddd;
            padding: 10px 20px;
            display: flex; /* Tambahkan ini */
            justify-content: center;
        }

                /* Styling untuk Header Pop-up Kustom */
        .custom-popup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 15px 20px;
        }

        .custom-popup-header .modal-title {
            color: #333; /* Font gelap */
            font-weight: bold;
            margin-bottom: 0; /* Hapus margin default h5 */
        }

        /* Tombol Close 'X' di Header */
        .custom-popup-header .custom-popup-close-btn {
            filter: none; /* Pastikan tidak ada filter dari Bootstrap */
            color: grey;
            opacity: 0.7;
            border: 1px solid transparent; /* Border transparan default */
            border-radius: 4px;
            padding: 0.25rem 0.5rem;
            transition: all 0.1s ease-in-out;
            cursor: pointer;
        }

        .custom-popup-header .custom-popup-close-btn:hover {
            opacity: 1;
            color: white;
            background-color: red;
            border-color: red;
        }

        /* Styling untuk Body Pop-up Kustom */
        .custom-popup-body {
            padding: 15px 20px; /* Padding yang konsisten */
            font-size: 0.95em; /* Sedikit lebih kecil dari default */
            color: #333;
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
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ $menu }}</a>
                            <ul class="dropdown-menu">
                                @switch($menu)
                                    @case('New')
                                    <li><a class="dropdown-item" href="#" id="newSingleMenuBtn">Single</a></li>
                                    <li><a class="dropdown-item" href="#" id="newConstellationMenuBtn">Constellation</a></li>
                                    <li><a class="dropdown-item" href="#" id="newGroundStationMenuBtn">Ground Station</a></li>
                                    <li><a class="dropdown-item" href="#" id="newLinkBudgetMenuBtn">Link Budget</a></li>
                                    @break
                                    @case('View')
                                        <li><a class="dropdown-item" href="#" id="resetViewBtn">Reset View</a></li>
                                        <li><a class="dropdown-item" href="#" id="closeViewButton">Close View</a></li>
                                        <li> <a class="dropdown-item" href="#" id="toggle2DViewBtn">2D View</a></li>
                                        @break
                                    @case('Save')
                                        <li><a class="dropdown-item" href="#" id="showSavePopupBtn">Save</a></li>
                                        <li><a class="dropdown-item" href="#" id="loadTleBtn">Load TLE</a></li>
                                        @break
                                @endswitch
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
        <div style="width: 200px;"></div>
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
                    <li id="satellite-resource-list">
                        <i class="fas fa-satellite sidebar-icon"></i>Satellites
                        <ul>
                            <li id="single-files-list">
                                <i class="fas fa-folder sidebar-icon"></i> Single Files
                                <ul></ul>
                            </li>
                            <li id="constellation-files-list">
                                <i class="fas fa-folder sidebar-icon"></i>Constellation Files
                                <ul></ul>
                            </li>
                        </ul>
                    </li>
                    <li id="ground-station-resource-list">
                        <i class="fas fa-satellite-dish sidebar-icon"></i>Ground Station
                        <ul></ul>
                    </li>
                    <li id="link-budget-resource-list">
                        <i class="fas fa-tower-broadcast sidebar-icon"></i>Link Budget
                        <ul></ul>
                    </li>
                </ul>
            </div>

            <div id="output-menu" class="menu-content hidden flex-grow-1">
                <div id="reports-section">
                    <h6 class="text-dark">Reports</h6>
                    <ul id="reports-list"></ul>
                </div>
                <div id="satellite-link-section">
                    <h6 class="text-dark">Satellite Link</h6>
                    <button id="create-link-report-btn" class="btn btn-sm btn-primary">Create Link Report</button>
                    <ul id="link-reports-list"></ul>
                </div>
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
                <canvas id="map-2D-canvas"></canvas>
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

    <div class="modal fade" id="fileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel"></h5>
                    <button type="button" class="btn-close" id="modalCloseBtn"></button>
                </div>
                <div class="modal-body" id="fileModalBody"></div>
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
                <div class="modal-body" id="linkBudgetOutputBody"></div>
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
                    <h5 class="modal-title" id="customAlertModalLabel">Caution!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body custom-alert-body"></div>
                <div class="modal-footer custom-alert-footer" id="customAlertModalFooter"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.150.1/build/three.min.js"></script>

    <script type="module">
        import { DEG2RAD, EarthRadius, MU_EARTH, SCENE_EARTH_RADIUS } from "{{ Vite::asset('resources/js/parametersimulation.js') }}";
        import { solveKepler, E_to_TrueAnomaly, TrueAnomaly_to_E, E_to_M, calculateDerivedOrbitalParameters } from "{{ Vite::asset('resources/js/orbitalCalculation.js') }}";
        import { calculateLinkBudget } from "{{ Vite::asset('resources/js/linkBudgetCalculations.js') }}";
        // import * as THREE from "three";
        // window.THREE = THREE; // so your inline checkVisibility can see it

        const LOCAL_STORAGE_HISTORY_KEY = 'appHistory';
        const LOCAL_STORAGE_HISTORY_INDEX_KEY = 'appHistoryIndex';

        const LOCAL_STORAGE_FILES_KEY = 'savedFilesData';
        const LOCAL_STORAGE_GROUND_STATIONS_KEY = 'savedGroundStationsData';
        const LOCAL_STORAGE_LINK_BUDGETS_KEY = 'savedLinkBudgetsData';
        const MAX_HISTORY_SIZE = 50;
        const SIMULATION_STATE_KEY = 'satelliteSimulationState';
        const FIRST_LOAD_FLAG_KEY = 'satelliteSimulationFirstLoad';

        // history state
        let appHistory = [];
        let appHistoryIndex = -1;

        let is2DViewActive = false; // Keep this global to the inline script
        // add these two lines before any functions use them:
        let editingFileName  = null;
        let editingFileType  = null;
        let activeSatellitePopup = null;

        window.undoOperation = undoOperation;
        window.redoOperation = redoOperation;

        let fileOutputs = new Map();
        let groundStations = new Map();
        let linkBudgetAnalysis = new Map();

        window.fileOutputs        = fileOutputs;
        window.groundStations     = groundStations;
        window.linkBudgetAnalysis = linkBudgetAnalysis;


        function saveFilesToLocalStorage() {
            try {
                localStorage.setItem(LOCAL_STORAGE_FILES_KEY, JSON.stringify(Array.from(fileOutputs.entries())));
                localStorage.setItem(LOCAL_STORAGE_GROUND_STATIONS_KEY, JSON.stringify(Array.from(groundStations.entries())));
                localStorage.setItem(LOCAL_STORAGE_LINK_BUDGETS_KEY, JSON.stringify(Array.from(linkBudgetAnalysis.entries())));
            } catch (e) {
                console.error("Error saving files to Local Storage:", e);
            }
        }

        function loadFilesFromLocalStorage() {
            try {
                const savedFiles = localStorage.getItem(LOCAL_STORAGE_FILES_KEY);
                if (savedFiles) fileOutputs = new Map(JSON.parse(savedFiles));
                else fileOutputs = new Map();

                const savedGroundStations = localStorage.getItem(LOCAL_STORAGE_GROUND_STATIONS_KEY);
                if (savedGroundStations) groundStations = new Map(JSON.parse(savedGroundStations));
                else groundStations = new Map();

                const savedLinkBudgets = localStorage.getItem(LOCAL_STORAGE_LINK_BUDGETS_KEY);
                if (savedLinkBudgets) linkBudgetAnalysis = new Map(JSON.parse(savedLinkBudgets));
                else linkBudgetAnalysis = new Map();
            } catch (e) {
                console.error("Error loading files from Local Storage:", e);
                fileOutputs = new Map();
                groundStations = new Map();
                linkBudgetAnalysis = new Map();
            }
        }

        function addFileToResourceSidebar(fileName, data, fileType) {
            let parentList;
            if (fileType === 'single') parentList = document.querySelector('#single-files-list ul');
            else if (fileType === 'constellation') parentList = document.querySelector('#constellation-files-list ul');
            else if (fileType === 'groundStation') parentList = document.querySelector('#ground-station-resource-list ul');
            else if (fileType === 'linkBudget') parentList = document.querySelector('#link-budget-resource-list ul');
            else return;

            const existingItem = document.querySelector(`li[data-file-name="${fileName}"][data-file-type="${fileType}"]`);
            if (existingItem) existingItem.remove();

            const newFileItem = document.createElement('li');
            newFileItem.dataset.fileName = fileName;
            newFileItem.dataset.fileType = fileType;
            let iconClass = fileType === 'single' || fileType === 'constellation' ? 'fas fa-satellite' : fileType === 'groundStation' ? 'fas fa-satellite-dish' : 'fas fa-tower-broadcast';
            newFileItem.innerHTML = `<i class="${iconClass} sidebar-icon"></i>${fileName}`;

            newFileItem.addEventListener('click', function() {
                const clickedFileName = this.dataset.fileName;
                const clickedFileType = this.dataset.fileType;
                let dataForDisplay;

                if (clickedFileType === 'single') {
                    dataForDisplay = fileOutputs.get(clickedFileName);
                    if (dataForDisplay && window.viewSimulation) {
                        window.viewSimulation(dataForDisplay);
                        showSatellitePopup(clickedFileName);
                    }
                } else if (clickedFileType === 'constellation') {
                    dataForDisplay = fileOutputs.get(clickedFileName);
                    if (dataForDisplay && window.viewSimulation) {
                        window.viewSimulation(dataForDisplay);
                    }
                } else if (clickedFileType === 'groundStation') {
                    dataForDisplay = groundStations.get(clickedFileName);
                    if (dataForDisplay && window.addOrUpdateGroundStationInScene) {
                        window.addOrUpdateGroundStationInScene(dataForDisplay);
                        showGroundStationPopup(clickedFileName);
                    }
                } else if (clickedFileType === 'linkBudget') {
                    dataForDisplay = linkBudgetAnalysis.get(clickedFileName);
                    if (dataForDisplay && window.showLinkBudgetOutput) {
                        showLinkBudgetOutput(dataForDisplay);
                    }
                }
                toggleTab('output-menu', document.getElementById('outputTabBtn'));
                populateReportsList();
            });

            newFileItem.addEventListener('contextmenu', function(event) {
                event.preventDefault();
                showContextMenu(event, this, this.dataset.fileName, this.dataset.fileType);
            });

            parentList.appendChild(newFileItem);
        }

        function showContextMenu(event, element, fileName, fileType) {
            const existingMenu = document.querySelector('.custom-contextmenu');
            if (existingMenu) existingMenu.remove();

              const contextMenu = document.createElement('div');
              contextMenu.className = 'custom-contextmenu';
            contextMenu.innerHTML = `
                <li onclick="editFile('${fileName}', '${fileType}')">Edit</li>
                <li onclick="deleteFile('${fileName}', '${fileType}')">Delete</li>
            `;
            document.body.appendChild(contextMenu);
            contextMenu.style.top = `${event.clientY}px`;
            contextMenu.style.left = `${event.clientX}px`;
            contextMenu.style.display = 'block';

            document.addEventListener('click', function closeContextMenu() {
                contextMenu.remove();
                document.removeEventListener('click', closeContextMenu);
            });
        }

        function editFile(fileName, fileType) {
            if (fileType === 'single') editSingleParameter(fileName);
            else if (fileType === 'constellation') editConstellationParameter(fileName);
            else if (fileType === 'groundStation') editGroundStation(fileName);
            else if (fileType === 'linkBudget') editLinkBudget(fileName);
        }

        // ----------------------
        // ensure Output tab shows the latest files + sats
        window.updateOutputTabForFile = function(fileName, fileType) {
        // simplest: just rebuild the entire list
        populateReportsList();
        };

       function removeSatelliteFromScene(id) {
        const sat = window.activeSatellites.get(id);
        if (!sat) return;

        // Let the class clean up everything it added to the scene
        sat.dispose();

        // Remove from our map
        window.activeSatellites.delete(id);
        }
        function removeGroundStationFromScene(id) {
        const gs = window.activeGroundStations.get(id);
        if (!gs) return;

        gs.dispose();
        window.activeGroundStations.delete(id);
        }

        function deleteFile(fileName, fileType) {
        if (fileType === 'single') {
            removeSatelliteFromScene(fileName);
            fileOutputs.delete(fileName);

        } else if (fileType === 'constellation') {
            const data = fileOutputs.get(fileName) || {};
            // 1) dispose every sat mesh/orbit/label, etc.
            (data.satellites || []).forEach(satId => removeSatelliteFromScene(satId));
            // 2) now just remove the _one_ constellation entry
            fileOutputs.delete(fileName);
        } else if (fileType === 'groundStation') {
            removeGroundStationFromScene(fileName);
            // remove the 3D object …
            window.activeGroundStations.delete(fileName);
            // … and remove the data so the name is truly gone
            groundStations.delete(fileName);

        } else if (fileType === 'linkBudget') {
            linkBudgetAnalysis.delete(fileName);
        }

        saveFilesToLocalStorage();
        populateResourceTab();
        populateReportsList();
        }
        
        function populateReportsList() {
            const reportsList = document.getElementById('reports-list');
            reportsList.innerHTML = ''; // Pastikan daftar dibersihkan setiap kali

            // Gunakan Set untuk melacak nama entri utama yang sudah ditambahkan
            const addedMainEntries = new Set();

            // 1) Bagian untuk Single Files
            // Kumpulkan semua satelit tunggal terlebih dahulu
            const singleSatellites = [];
            fileOutputs.forEach((data, fileName) => {
                if (data.fileType === 'single') {
                    singleSatellites.push({ fileName: fileName, data: data });
                }
            });

            if (singleSatellites.length > 0) {
                const singleFilesLi = document.createElement('li');
                singleFilesLi.textContent = 'Single Files: ';
                const names = singleSatellites.map(s => s.fileName).join(', ');
                singleFilesLi.textContent += names;

                // Event listener untuk "Single Files: [nama]" di OUTPUT
                singleFilesLi.addEventListener('click', function() {
                    if (singleSatellites.length > 0) {
                        const firstSatData = singleSatellites[0].data;
                        if (window.viewSimulation) {
                            // Ini akan menambahkan satelit ke scene, tidak menghapus yang lain
                            window.viewSimulation(firstSatData);
                        }
                        showSatellitePopup(firstSatData.fileName); // Tampilkan popup untuk satelit pertama
                    }
                });
                reportsList.appendChild(singleFilesLi);
            }


        fileOutputs.forEach((data, fileName) => {
            if (data.fileType === 'constellation') {
                const constellationLi = document.createElement('li');
                constellationLi.textContent = `Constellation Files: ${fileName}`;
                constellationLi.dataset.fileName = fileName;
                constellationLi.dataset.fileType = data.fileType;

                // Event listener untuk "Constellation Files: [nama]" di OUTPUT
                constellationLi.addEventListener('click', function() {
                     if (window.viewSimulation) {
                         // Ini akan menambahkan satelit konstelasi ke scene, tidak menghapus yang lain
                         window.viewSimulation(data);
                     }
                     // Opsional: Tampilkan popup khusus konstelasi jika ada
                });

                const subSatUl = document.createElement('ul');
                (data.satellites || []).forEach(satId => {
                    const sat = window.activeSatellites.get(satId);
                    if (sat) {
                        const satLi = document.createElement('li');
                        satLi.textContent = `- ${sat.name}`;
                        satLi.dataset.id = satId;
                        satLi.dataset.type = 'single';
                        satLi.addEventListener('click', () => showSatellitePopup(satId)); // Listener untuk satelit individu di dalam konstelasi
                        subSatUl.appendChild(satLi);
                    }
                });
                if (subSatUl.children.length > 0) {
                    constellationLi.appendChild(subSatUl);
                }
                reportsList.appendChild(constellationLi);
            }
        });

            // 3) Bagian untuk Ground Stations
            groundStations.forEach((data, name) => {
                const groundStationLi = document.createElement('li');
                groundStationLi.textContent = `Ground Station: ${name}`;
                groundStationLi.dataset.id = name;
                groundStationLi.dataset.type = 'groundStation';
                // Event listener untuk "Ground Station: [nama]" di OUTPUT
                groundStationLi.addEventListener('click', function() {
                    if (window.addOrUpdateGroundStationInScene) {
                        // Ini akan menambahkan ground station ke scene, tidak menghapus yang lain
                        window.addOrUpdateGroundStationInScene(data);
                    }
                    showGroundStationPopup(name); // Tampilkan popup informasi ground station
                });
                reportsList.appendChild(groundStationLi);
            });

            // 4) Bagian untuk Link Budget
            linkBudgetAnalysis.forEach((data, name) => {
                const linkBudgetLi = document.createElement('li');
                linkBudgetLi.textContent = `Link Budget: ${name}`;
                linkBudgetLi.dataset.id = name;
                linkBudgetLi.dataset.type = 'linkBudget';
                // Event listener untuk "Link Budget: [nama]" di OUTPUT
                linkBudgetLi.addEventListener('click', function() {
                    showLinkBudgetOutput(data); // Menampilkan output link budget di modal
                });
                reportsList.appendChild(linkBudgetLi);
            });
        }

        // function to convert radians to degrees
        function toRad(deg) { return (deg * Math.PI / 180).toFixed(2); }
      // function to convert radians to degrees
        function toDeg(rad) { return (rad * 180/Math.PI).toFixed(2); }
        function computeAltitude(sat) {
        // mirror your original calculation:
        const kmPerUnit = EarthRadius;
        return ((sat.mesh.position.length() * kmPerUnit) - kmPerUnit).toFixed(2);
        }   

// ---------------- showSatellitePopup ----------------
        function showSatellitePopup(satId) {
        // 1) Close old popup and unsubscribe its updater
        if (window.activeSatellitePopup) {
            const { element, updateHandler } = window.activeSatellitePopup;
            element.remove();
            window.removeEventListener('epochUpdated', updateHandler);
            window.activeSatellitePopup = null;
        }

        // 2) Grab the sat
        const sat = window.activeSatellites.get(satId);
        if (!sat) return;

        // 3) Build the popup
        const popup = document.createElement('div');
        popup.className = 'custom-popup';
        popup.innerHTML = `
            <div class="custom-popup-header">
                <h5 class="modal-title">${sat.name}</h5>
                <button type="button" class="btn-close custom-popup-close-btn" aria-label="Close"></button>
            </div>
            <div class="custom-popup-body">
                <p><strong>Altitude:</strong>      <span class="altitude"></span> km</p>
                <p><strong>Inclination:</strong>   <span class="inclination"></span>°</p>
                <p><strong>Latitude:</strong>      <span class="latitude"></span>°</p>
                <p><strong>Longitude:</strong>     <span class="longitude"></span>°</p>
                <p><strong>RAAN:</strong>          <span class="raan"></span>°</p>
                <p><strong>Orbital Period:</strong><span class="orbitalPeriod"></span> min</p>
                <p><strong>Orbital Velocity:</strong><span class="orbitalVelocity"></span> km/s</p>
                <p><strong>Beamwidth:</strong>     <span class="beamwidth"></span>°</p>
                <p><strong>True Anomaly:</strong>  <span class="trueAnomaly"></span>°</p>
                <p><strong>Eccentricity:</strong>   <span class="eccentricity"></span></p>
                <p><strong>Arg. of Perigee:</strong><span class="argPerigee"></span>°</p>
            </div>
            `;
        document.body.appendChild(popup);
        makeDraggable(popup);

        // 4) The updater function
        const updatePopup = () => {
            // recalc derived params
            const { orbitalPeriod, orbitalVelocity } = calculateDerivedOrbitalParameters(
            sat.params.semiMajorAxis - SCENE_EARTH_RADIUS,
            sat.params.eccentricity
            );
            // write into spans
            popup.querySelector('.altitude').textContent      = computeAltitude(sat);
            popup.querySelector('.inclination').textContent   = toDeg(sat.params.inclinationRad);
            popup.querySelector('.latitude').textContent     = sat.latitudeDeg.toFixed(2);
            popup.querySelector('.longitude').textContent    = sat.longitudeDeg.toFixed(2);
            popup.querySelector('.raan').textContent         = toDeg(sat.currentRAAN);
            popup.querySelector('.orbitalPeriod').textContent= (orbitalPeriod/60).toFixed(2);
            popup.querySelector('.orbitalVelocity').textContent = orbitalVelocity.toFixed(2);
            popup.querySelector('.beamwidth').textContent    = sat.params.beamwidth;
            popup.querySelector('.trueAnomaly').textContent  = toDeg(sat.currentTrueAnomaly);
            popup.querySelector('.eccentricity').textContent = sat.params.eccentricity.toFixed(4);
            popup.querySelector('.argPerigee').textContent   = toDeg(sat.params.argPerigeeRad);
        };

        // 5) Hook it up to your simulation’s epochUpdated event
        window.addEventListener('epochUpdated', updatePopup);
        // also call it once immediately so all fields are set
        updatePopup();

        // 6) Close‐button tears down the handler & popup
        // Ubah selector untuk tombol close
        popup.querySelector('.custom-popup-close-btn').addEventListener('click', () => {
            popup.remove();
            window.removeEventListener('epochUpdated', updatePopup);
            window.activeSatellitePopup = null;
        });

        // Tambahkan juga listener untuk tombol 'X' di header
        popup.querySelector('.custom-popup-close-btn').addEventListener('click', () => {
            popup.remove();
            window.removeEventListener('epochUpdated', updatePopup);
            window.activeSatellitePopup = null;
        });

        // 7) Save state so next time we can unsubscribe it
        window.activeSatellitePopup = {
            element:      popup,
            satId:        satId,
            updateHandler: updatePopup
        };
        }


        // Show Ground Station Popup
        // In simulation.blade.php - showGroundStationPopup function
        function showGroundStationPopup(gsId) {
            const gs = window.activeGroundStations.get(gsId);
            if (!gs) return;

            const popup = document.createElement('div');
            popup.className = 'custom-popup';
            popup.innerHTML = `
                <div class="custom-popup-header">
                    <h5 class="modal-title">${gs.name}</h5>
                    <button type="button" class="btn-close custom-popup-close-btn" aria-label="Close"></button>
                </div>
                <div class="custom-popup-body">
                    <p><strong>Latitude:</strong> ${gs.latitude}°</p>
                    <p><strong>Longitude:</strong> ${gs.longitude}°</p>
                    <p><strong>Minimum Elevation Angle:</strong> ${gs.minElevationAngle}°</p>
                </div>
                `;
            document.body.appendChild(popup);
            makeDraggable(popup);

            popup.querySelector('.custom-popup-close-btn').addEventListener('click', () => popup.remove());
        }

        function makeDraggable(element) {
            let isDragging = false;
            let offsetX, offsetY;

            element.addEventListener('mousedown', (e) => {
                isDragging = true;
                offsetX = e.clientX - element.getBoundingClientRect().left;
                offsetY = e.clientY - element.getBoundingClientRect().top;
            });

            document.addEventListener('mousemove', (e) => {
                if (isDragging) {
                    element.style.left = `${e.clientX - offsetX}px`;
                    element.style.top = `${e.clientY - offsetY}px`;
                }
            });

            document.addEventListener('mouseup', () => {
                isDragging = false;
            });
        }


        //------------------------------ Link Budget Report Management-----------------------------------

        // somewhere in your initialization code
        document
        .getElementById('create-link-report-btn')
        .addEventListener('click', showLinkReportPopup);

        window.showLinkReportPopup = showLinkReportPopup;

        // Show the "Save Link Report" dialog
        function showLinkReportPopup() {
            // remove any existing popup
            document.querySelectorAll('.custom-popup').forEach(el => el.remove());
            // gather lists
            const sats = Array.from(window.activeSatellites.values())
                        .map(s => ({ id: s.id, name: s.name, start: s.initialEpochUTC }));
            const gses = Array.from(window.activeGroundStations.values())
                        .map(g => ({ id: g.id, name: g.name }));

            if (!sats.length || !gses.length) {
                // Memanggil showCustomAlert untuk menampilkan pesan yang konsisten
                showCustomAlert("You need at least one satellite and one GS active", "Caution!");
                return; // Penting untuk keluar dari fungsi setelah menampilkan alert
            }

            const popup = document.createElement('div');
            popup.className = 'custom-popup';
            Object.assign(popup.style, {
                position: 'absolute',
                left: '50%', top: '50%',
                transform: 'translate(-50%,-50%)',
                background: '#fff',
                color: '#000',
                padding: '20px',
                border: '1px solid #ccc',
                zIndex: 10000,
                width: '420px',
                maxHeight: '80vh',
                overflowY: 'auto'
            });

            // helper: ms→ local-datetime
            const fmtLocal = ms => {
                const dt = new Date(ms);
                const pad = n => String(n).padStart(2, '0');
                return `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())}` +
                    `T${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
            };

            popup.innerHTML = `
                <h5>Create Satellite–GS Link Report</h5>
                <label>Satellite:</label>
                <select id="linkSatSel" class="form-control mb-2">
                ${sats.map(s => `<option value="${s.id}" data-start="${s.start}">${s.name}</option>`).join('')}
                </select>
                <label>Ground Station:</label>
                <select id="linkGsSel" class="form-control mb-2">
                ${gses.map(g => `<option value="${g.id}">${g.name}</option>`).join('')}
                </select>
                <label>Start Time:</label>
                <input type="datetime-local" id="linkStart" class="form-control mb-2" disabled />
                <label>End Time:</label>
                <input type="datetime-local" id="linkEnd" class="form-control mb-2" />
                <label>Time Step (sec):</label>
                <input type="number" id="linkStep" class="form-control mb-2" min="1" value="60" />
                <label>File Type:</label>
                <select id="linkFormat" class="form-control mb-3">
                <option value="csv" selected>.csv</option>
                <option value="txt">.txt</option>
                </select>

                <div class="text-end mb-2">
                <button class="btn btn-secondary btn-sm" id="linkCancel">Close</button>
                <button class="btn btn-info btn-sm"   id="linkCompute">Compute</button>
                </div>

                <div id="linkReportContainer" style="font-family:monospace; white-space:pre; max-height:200px; overflow:auto; border:1px solid #ddd; padding:8px;"></div>

                <div class="text-end mt-2">
                <button class="btn btn-secondary btn-sm" id="linkClose2">Close</button>
                <button class="btn btn-primary btn-sm"  id="linkSave" disabled>Save</button>
                </div>
            `;

            document.body.appendChild(popup);

            const satSel   = popup.querySelector('#linkSatSel');
            const gsSel    = popup.querySelector('#linkGsSel');
            const startIn  = popup.querySelector('#linkStart');
            const endIn    = popup.querySelector('#linkEnd');
            const stepIn   = popup.querySelector('#linkStep');
            const fmtSel   = popup.querySelector('#linkFormat');
            const compute  = popup.querySelector('#linkCompute');
            const saveBtn  = popup.querySelector('#linkSave');
            const close1   = popup.querySelector('#linkCancel');
            const close2   = popup.querySelector('#linkClose2');
            const reportCt = popup.querySelector('#linkReportContainer');

            // initialize start/end
        function refreshTimes() {
                const startMs = +satSel.selectedOptions[0].dataset.start;
                startIn.value   = fmtLocal(startMs);
                endIn.min       = fmtLocal(startMs);
                if (!endIn.value || endIn.value < startIn.value) endIn.value = fmtLocal(startMs + 3600*1000);
            }
            satSel.onchange = refreshTimes;
            refreshTimes();

            close1.onclick = close2.onclick = () => popup.remove();

            compute.onclick = () => {
                reportCt.textContent = '⏳ computing…';
                saveBtn.disabled    = true;

                // grab values
                const satId  = satSel.value;
                const gsId   = gsSel.value;
                const t0     = new Date(startIn.value).getTime();
                const t1     = new Date(endIn.value  ).getTime();
                const step   = parseFloat(stepIn.value) * 1000;
                const ext    = fmtSel.value;

                if (t1 < t0) {
                alert('End must be ≥ start');
                return;
                }

                const sat = window.activeSatellites.get(satId);
                const gs  = window.activeGroundStations.get(gsId);
                const access = calculateAccessPeriods(sat, gs, t0, t1, step);

                // Number of passes:
                const numPasses = access.length;

                // Total contact time (in seconds):
                const totalContactSec = access.reduce((sum, p) => sum + p.duration, 0);

                // Average pass duration:
                const avgSec = numPasses ? totalContactSec / numPasses : 0;

                // Prep a little summary text:
                const summary = [
                `# Pass Summary`,
                `Total passes: ${numPasses}`,
                `Total contact time: ${(totalContactSec/3600).toFixed(2)} h (${totalContactSec.toFixed(0)} s)`,
                `Average pass: ${(avgSec/60).toFixed(2)} min (${avgSec.toFixed(0)} s)`,
                ``,  // blank line before the detail table
                ];

                const detailLines = generateLinkReportContent(sat, gs, t0, t1, access, ext);
                // combine summary + details:
                const lines = summary.concat(detailLines);
                reportCt.textContent = lines.join('\n');

                saveBtn.disabled    = false;
                // stash for save
                saveBtn._data = { filename:`link_${sat.name}_${gs.name}.${ext}`, text:lines.join('\n')+'\n' };
            };

            saveBtn.onclick = () => {
                const { filename, text } = saveBtn._data;
                downloadText(filename, text);
                popup.remove();
            };
            }

        function calculateAccessPeriods(sat, gs, startTs, endTs, step) {
        const core     = window.getSimulationCoreObjects();
        const oldT     = core.totalSimulatedTime;
        const oldE     = core.currentEpochUTC;
        const oldRot   = core.earthGroup.rotation.y;
        const initEpoch= sat.initialEpochUTC;
        const periods  = [];

        let prevVis = false;
        let visStartCoarse = null;

        // helper: isVisible at exact time t (ms)
        function isVisAt(t) {
            const simSec = (t - initEpoch)/1000;
            core.setTotalSimulatedTime(simSec);
            core.setCurrentEpochUTC(initEpoch);
            core.earthGroup.rotation.y =
            window.initialEarthRotationOffset
            + simSec * window.EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;
            sat.updatePosition(simSec, 0);
            return checkVisibility(sat, gs);
        }

        // refine the flip between [t0,t1] to ±1s accuracy
        function findTransition(t0, t1) {
            let lo = t0, hi = t1;
            while (hi - lo > 1000) {           // stop when within 1 s
            const mid = (lo + hi) / 2;
            if (isVisAt(mid)) hi = mid;
            else lo = mid;
            }
            return (lo + hi) / 2;
        }

        // coarse scan
        for (let t = startTs; t <= endTs; t += step) {
            const vis = isVisAt(t);

            // off→on: record coarse start
            if (vis && !prevVis) {
            visStartCoarse = t;
            }
            // on→off: refine both boundaries and push
            if (!vis && prevVis) {
            const tOn  = findTransition(visStartCoarse - step, visStartCoarse);
            const tOff = findTransition(t - step, t);
            periods.push({
                start:    tOn,
                stop:     tOff,
                duration: (tOff - tOn) / 1000
            });
            visStartCoarse = null;
            }
            prevVis = vis;
        }

        // if still in view at endTs, refine the final off
        if (prevVis && visStartCoarse != null) {
            const tOn  = findTransition(visStartCoarse - step, visStartCoarse);
            const tOff = findTransition(endTs - step, endTs);
            periods.push({
            start:    tOn,
            stop:     tOff,
            duration: (tOff - tOn) / 1000
            });
        }

        // restore state
        core.setTotalSimulatedTime(oldT);
        core.setCurrentEpochUTC(oldE);
        core.earthGroup.rotation.y = oldRot;
        sat.updatePosition(oldT, 0);

        return periods;
        }

        function checkVisibility(sat, gs) {
            // Get world position of ground station (transforms from ECEF to ECI)
            const gsWorldPos = new THREE.Vector3();
            gs.mesh.getWorldPosition(gsWorldPos);
            // Satellite position is already in world coordinates (ECI)
            const satWorldPos = sat.mesh.position;

            // 1) Inside beam cone?
            const satToGs = gsWorldPos.clone().sub(satWorldPos).normalize();
            const nadir = satWorldPos.clone().negate().normalize();
            const halfBeamRad = sat.params.beamwidth * Math.PI / 360; // Half beam angle in radians
            const coneOK = nadir.dot(satToGs) >= Math.cos(halfBeamRad);

            // 2) Above horizon?
            const gsDir = gsWorldPos.clone().normalize();
            const satDir = satWorldPos.clone().normalize();
            const centralAngle = Math.acos(THREE.MathUtils.clamp(gsDir.dot(satDir), -1, 1));
            const horizonOK = centralAngle <= sat.coverageAngleRad;

            return coneOK && horizonOK;
        }


        // Generate the report content
        function generateLinkReportContent(sat, gs, startTs, endTs, accessPeriods, fileExt) {
        const lines = [];
        const gsPos = `${gs.longitude.toFixed(6)}, ${gs.latitude.toFixed(6)}`;

        if (fileExt === 'txt') {
            lines.push(`Satellite Name: ${sat.name}`);
            lines.push(`Ground Station Name: ${gs.name}`);
            lines.push(`Ground Station Position: ${gsPos}`);
            lines.push(`Start Time: ${new Date(startTs).toUTCString()}`);
            lines.push(`Stop Time: ${new Date(endTs).toUTCString()}`);
            lines.push('');
            lines.push(`Access\tStart Time (UTC)\tStop Time (UTC)\tDuration (sec)`);
            accessPeriods.forEach((p, i) => {
            lines.push(`${i + 1}\t${new Date(p.start).toUTCString()}\t${new Date(p.stop).toUTCString()}\t${p.duration.toFixed(2)}`);
            });
        } else {
            lines.push(`Satellite Name:,${sat.name}`);
            lines.push(`Ground Station Name:,${gs.name}`);
            lines.push(`Ground Station Position:,${gsPos}`);
            lines.push(`Start Time:,${new Date(startTs).toUTCString()}`);
            lines.push(`Stop Time:,${new Date(endTs).toUTCString()}`);
            lines.push('');
            lines.push(`Access,Start Time (UTC),Stop Time (UTC),Duration (sec)`);
            accessPeriods.forEach((p, i) => {
            lines.push(`${i + 1},${new Date(p.start).toUTCString()},${new Date(p.stop).toUTCString()},${p.duration.toFixed(2)}`);
            });
        }
        return lines;
        }

        // Reuse existing download helper
        function downloadText(filename, txt) {
        const blob = new Blob([txt], { type: 'text/plain' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(a.href);
        }

        //------------------------------ End of Link Budget Report Management-----------------------------------
        // Clear the resource tab content
        document.getElementById('resourceTabBtn').addEventListener('click', function() {
            toggleTab('resource-menu', this);
        });
        document.getElementById('outputTabBtn').addEventListener('click', function() {
            toggleTab('output-menu', this);
        });
        document.getElementById('resourceTabBtn').click(); // Set default tab to Resource
        function clearResourceTab() {
            document.querySelector('#single-files-list ul').innerHTML = '';
            document.querySelector('#constellation-files-list ul').innerHTML = '';
            document.querySelector('#ground-station-resource-list ul').innerHTML = '';
            document.querySelector('#link-budget-resource-list ul').innerHTML = '';
        }

        function populateResourceTab() {
            clearResourceTab();
            fileOutputs.forEach((data, fileName) => addFileToResourceSidebar(fileName, data, data.fileType));
            groundStations.forEach((data, name) => addFileToResourceSidebar(name, data, 'groundStation'));
            linkBudgetAnalysis.forEach((data, name) => addFileToResourceSidebar(name, data, 'linkBudget'));
        }

        function toggleTab(id, btn) {
            document.querySelectorAll('.menu-content').forEach(div => div.classList.add('hidden'));
            document.getElementById(id).classList.remove('hidden');
            document.querySelectorAll('.nav-tabs .nav-link').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (id === 'output-menu') populateReportsList();
        }

        window.onload = function() {
            const navigationEntries = performance.getEntriesByType('navigation');
            if (navigationEntries.length > 0 && navigationEntries[0].type === 'reload') {
                fileOutputs = new Map();
                groundStations = new Map();
                linkBudgetAnalysis = new Map();
                localStorage.removeItem(LOCAL_STORAGE_FILES_KEY);
                localStorage.removeItem(LOCAL_STORAGE_GROUND_STATIONS_KEY);
                localStorage.removeItem(LOCAL_STORAGE_LINK_BUDGETS_KEY);
                clearResourceTab();
                populateReportsList();
            } else {
                loadFilesFromLocalStorage();
                populateResourceTab();
                populateReportsList();
            }
        };
        window.toggleTab = toggleTab;

// ------------------------------------- GENERAL MODAL AND ALERT FUNCTIONS ---------------------------------------
        window.showCustomConfirmation = showCustomConfirmation;
        window.showCustomAlert = showCustomAlert;
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
            confirmButton.classList.add('btn', 'btn-primary', 'custom-alert-ok-btn'); // Menambahkan kelas 'custom-alert-ok-btn'
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

        function showCustomAlert(message, title = 'Caution!') {
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
            const success = onSave(); // Save Simulation
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
                data = linkBudgetAnalysis.get(fileNameToEdit);
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