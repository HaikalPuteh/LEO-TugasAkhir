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
                // Modifikasi baris ini:
                else if ((!editingFileName && fileOutputs.has(fileName)) || groundStations.has(fileName) || linkBudgetAnalysis.has(fileName)) {
                    showInputError('fileNameInput', `Name "${fileName}" already exists as another file type or is already in use. Please use a different name.`); hasError = true;
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

                const newData = {
                    fileName, altitude: values.altitude, inclination: values.inclination,
                    eccentricity: eccentricity, raan: values.raan,
                    argumentOfPerigee: eccentricityType === 'elliptical' ? values.argumentOfPerigee : 0,
                    trueAnomaly: values.trueAnomaly,
                    epoch: epochInput, // This is the string epoch for storage
                    utcTimestamp: utcTimestamp, // Store the UTC offset for later use
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
                // Pass the new data to the JavaScript function for scene update
                if (window.addOrUpdateSatelliteInScene) {
                    window.addOrUpdateSatelliteInScene(newData); //Pass to Javascript
                    //window.selectedSatelliteId = newData.fileName; // Set selected satellite (Commented if not needed)
                    window.isAnimating = false;
                    setActiveControlButton('pauseButton');
                }

                // updateOutputSidebar(newData); //(Removed to avoid double update) - Keep commented as selectSatellite handles it
                addFileToResourceSidebar(fileName, newData, 'single');
                //updateSatelliteListUI();
                //selectSatellite(newData.fileName); // Select and highlight the newly created satellite ( To Do :Removed)
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


//----------------------------- Toggle Eccentricity Input Functionality--------------------------------
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


//-----------------------------New Constellation Menu Functionality--------------------------------
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

// Di dalam NewConstellationMenu() pada bagian onSave:
                if (!fileName) { showInputError('fileNameInput', "Constellation Name cannot be empty."); hasError = true; }
                // Modifikasi baris ini:
                else if ((!editingFileName && fileOutputs.has(fileName)) || groundStations.has(fileName) || linkBudgetAnalysis.has(fileName)) {
                    showInputError('fileNameInput', `Name "${fileName}" already exists as another file type or is already in use. Please use a different name.`); hasError = true;
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
                //window.currentEpochUTC = utcTimestamp;

                const newData = {
                    fileName, altitude: values.altitude, inclination: values.inclination,
                    eccentricity: eccentricity, raan: values.raan,
                    argumentOfPerigee: eccentricityType === 'elliptical' ? values.argumentOfPerigee : 0,
                    trueAnomaly: values.trueAnomaly, 
                    epoch: epochInput,
                    utcTimestamp: utcTimestamp, // Store the UTC offset for later use
                    beamwidth: values.beamwidth,
                    fileType: 'constellation',
                    satellites: [], 
                    ...constellationData
                };
                //fileOutputs.set(fileName, newData);

                if (editingFileName) {
                    const oldData = { ...fileOutputs.get(editingFileName) };
                    recordAction({ type: 'editFile', fileName: editingFileName, fileType: 'constellation', oldData: oldData, newData: newData });
                    fileOutputs.set(editingFileName, newData);
                } else {
                    recordAction({ type: 'addFile', fileName: fileName, fileData: newData, fileType: 'constellation' });
                    fileOutputs.set(fileName, newData);
                }

                saveFilesToLocalStorage();

                // If the viewSimulation function exists, call it with the new data
                if (window.viewSimulation) {
                    window.viewSimulation(newData); //Pass to Javascript
                    //window.selectedSatelliteId = newData.fileName; // Set selected satellite (Commented if not needed)
                    // If constellations are not selected, clear the selectedSatelliteId
                    window.selectedSatelliteId = null; // Clear selected satellite for constellations
                    window.isAnimating = false;
                    setActiveControlButton('pauseButton');
                }

                // updateOutputSidebar(newData); //(Removed to avoid duplicate updates) - Keep commented as selectOutputItem handles it
                addFileToResourceSidebar(fileName, newData, 'constellation');
                populateReportsList();
                //updateSatelliteListUI();
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
                document.getElementById('trainDirectionForward').checked = false; 
                document.getElementById('trainDirectionBackward').checked = true; // Default to backward direction  
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

// Di dalam NewGroundStationMenu() pada bagian onSave:
                if (!gsName) { showInputError('gsNameInput', "Ground Station Name cannot be empty."); hasError = true; }
                // Modifikasi baris ini:
                else if (fileOutputs.has(gsName) || (!editingFileName && groundStations.has(gsName)) || linkBudgetAnalysis.has(gsName)) {
                    showInputError('gsNameInput', `Name "${gsName}" already exists as another file type or is already in use. Please use a different name.`); hasError = true;
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
                    //window.selectedGroundStationId = newData.id; // Set selected ground station
                }
                // updateOutputSidebar(newData); // Keep commented as selectGroundStation handles it
                addFileToResourceSidebar(gsName, newData, 'groundStation');
                //updateSatelliteListUI();
                //selectGroundStation(newData.id); // Select and highlight the newly created ground station
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
            <hr>
            <h6>Performance Requirements</h6>
            <div class="mb-3">
                <label for="minimumSNRInput" class="form-label">Minimum Required SNR (dB)</label>
                <input type="number" class="form-control" id="minimumSNRInput" step="0.1" placeholder="e.g., 10">
            </div>
            <div class="mb-3">
                <label for="targetAreaInput" class="form-label">Total Coverage Area Required (km²)</label>
                <input type="number" class="form-control" id="targetAreaInput" min="1" placeholder="e.g., 1,000,000">
            </div>
            <div class="mb-3">
                <label for="elevationAngleInput" class="form-label">Minimum Elevation Angle at Edge of Coverage (°)</label>
                <input type="number" class="form-control" id="elevationAngleInput" min="0" max="90" placeholder="e.g., 10">
            </div>
            <div class="mb-3">
                <label for="orbitInclinationInput" class="form-label">Target Orbit Inclination (°)</label>
                <input type="number" class="form-control" id="orbitInclinationInput" min="0" max="180" placeholder="Determines max latitude coverage">
            </div>
            <hr>
            <h6>RF System Parameters</h6>
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
                <label for="bandwidthInput" class="form-label">Bandwidth (MHz)</label>
                <input type="number" class="form-control" id="bandwidthInput" min="0.1" step="0.1">
            </div>
            <div class="mb-3">
                <label for="noiseFigureInput" class="form-label">Receiver Noise Figure (dB)</label>
                <input type="number" class="form-control" id="noiseFigureInput" step="0.1">
            </div>
            <div class="mb-3">
                <label for="atmosphericLossInput" class="form-label">Atmospheric & Other Losses (dB)</label>
                <input type="number" class="form-control" id="atmosphericLossInput" min="0" step="0.1">
            </div>
            <div class="mb-3">
                <label for="minSatellitesInViewInput" class="form-label">Minimum Satellites in View (for throughput calc)</label>
                <input type="number" class="form-control" id="minSatellitesInViewInput" min="1" value="1">
            </div>
            `;

        showModal("Link Budget Constellation Designer", inputBody, () => {
            let hasError = false;
            const lbName = document.getElementById('lbNameInput').value.trim();

            if (!lbName) { showInputError('lbNameInput', "Analysis Name cannot be empty."); hasError = true; }
            else if (fileOutputs.has(lbName) || groundStations.has(lbName) || (!editingFileName && linkBudgetAnalysis.has(lbName))) {
                showInputError('lbNameInput', `Name "${lbName}" already exists. Please use a different name.`); hasError = true;
            } else { clearInputError('lbNameInput'); }

            const inputs = [
                { id: 'minimumSNRInput', name: 'Minimum SNR' },
                { id: 'targetAreaInput', min: 1, name: 'Target Area' },
                { id: 'elevationAngleInput', min: 0, max: 90, name: 'Elevation Angle' },
                { id: 'orbitInclinationInput', min: 0, max: 180, name: 'Orbit Inclination' },
                { id: 'transmitPowerInput', name: 'Transmit Power' },
                { id: 'txAntennaGainInput', name: 'Tx Antenna Gain' },
                { id: 'rxAntennaGainInput', name: 'Rx Antenna Gain' },
                { id: 'frequencyInput', min: 0.1, name: 'Frequency' },
                { id: 'bandwidthInput', min: 0.1, name: 'Bandwidth' },
                { id: 'noiseFigureInput', name: 'Noise Figure' },
                { id: 'atmosphericLossInput', min: 0, name: 'Atmospheric Loss' },
                { id: 'minSatellitesInViewInput', min: 1, name: 'Minimum Satellites in View' }
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

            const calculatedData = calculateLinkBudget(values);

            const fullDataToSave = {
                name: lbName,
                ...calculatedData,
                fileType: 'linkBudget'
            };
            showLinkBudgetOutput(fullDataToSave, editingFileName !== null);
            return true;
        });
    }

/**
 * REVISED Link Budget Output. Displays the calculated results and provides
 * the button to visualize the designed constellation.
 */
        function showLinkBudgetOutput(data, isEditing = false) {
            const modalElement = document.getElementById('linkBudgetOutputModal');
            const modalBody = document.getElementById('linkBudgetOutputBody');
            const modal = new bootstrap.Modal(modalElement);

            modalBody.innerHTML = `
                <p><strong>Analysis Name:</strong> ${data.name}</p>
                <hr>
                <h6>Verified RF Performance:</h6>
                <p><strong>Received Power:</strong> ${data.receivedPower.toFixed(2)} dBm</p>
                <p><strong>SNR:</strong> ${data.snr.toFixed(2)} dB (Target: ${data.minimumSNR} dB)</p>
                <p><strong>Shannon Capacity:</strong> ${(data.shannonCapacity / 1e6).toFixed(2)} Mbps</p>
                <hr>
                <h6>Generated Constellation Design (Walker):</h6>
                <p><strong>Required Altitude:</strong> ${data.altitude.toFixed(2)} km</p>
                <p><strong>Required Inclination:</strong> ${data.inclination.toFixed(2)}°</p>
                <p><strong>Satellite Beamwidth:</strong> ${data.beamwidth.toFixed(2)}°</p>
                <p><strong>Total Satellites:</strong> ${data.numSatellitesNeeded} (${data.numOrbitalPlanes} planes of ${data.satsPerPlane} satellites)</p>
                <p><strong>Revisit Time:</strong> ${data.revisitTime.toFixed(2)} minutes</p>
            `;

            const applyBtn = document.getElementById('applyLinkBudgetPreviewBtn');
            applyBtn.textContent = "Visualize Constellation";
            applyBtn.onclick = () => {
                // 1. Save the analysis report itself
                linkBudgetAnalysis.set(data.name, data);
                addFileToResourceSidebar(data.name, data, 'linkBudget');
                
                // 2. Generate and visualize the constellation
                if (window.generateConstellationFromLinkBudget) {
                    window.generateConstellationFromLinkBudget(data);
                } else {
                    showCustomAlert("Error: Constellation generation function not found in Earth3Dsimulation.js.");
                }
                
                saveFilesToLocalStorage();
                modal.hide();
            };

            modal.show();
        }
        
        
// --- EDIT MENU FUNCTIONS (triggered by double-click on resource items) ---
    window.editFile = editFile;
    window.deleteFile = deleteFile;
    window.editSingleParameter = editSingleParameter;
    window.editConstellationParameter = editConstellationParameter;
    window.editGroundStation = editGroundStation;
    window.editLinkBudget = editLinkBudget; // Expose the new function

    function editSingleParameter(fileName) {
    const dataToEdit = fileOutputs.get(fileName);
    if (!dataToEdit) {
        showCustomAlert("Data file not found.");
        return;
    }

    // NOTE: The modalBody HTML is unchanged. The fix is in how we populate it.
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
            <label for="beamwidthInput" class="form-label">Beamwidth (degree)</label>
            <input type="number" class="form-control" id="beamwidthInput" min="0" max="90">
        </div>
    `;

    // NOTE: The onSave logic is unchanged. It correctly reads the local time
    // from the input and converts it to a UTC timestamp.
    showModal("Edit Single Satellite", modalBody, () => {
        let hasError = false;
        const currentFileName = document.getElementById('fileNameInput').value;
        const eccentricityType = document.querySelector('input[name="eccentricityType"]:checked').value;
        let eccentricity = 0;
        if (eccentricityType === 'elliptical') {
            const eccValue = document.getElementById('eccentricityValueInput').value;
            eccentricity = parseFloat(formatNumberInput(eccValue));
        }

        const inputs = [{
            id: 'altitudeInput',
            min: 100,
            max: 36000,
            name: 'Altitude'
        }, {
            id: 'inclinationInput',
            min: 0,
            max: 180,
            name: 'Inclination'
        }, {
            id: 'raanInput',
            min: 0,
            max: 360,
            name: 'RAAN'
        }, {
            id: 'trueAnomalyInput',
            min: 0,
            max: 360,
            name: 'True Anomaly'
        }, {
            id: 'beamwidthInput',
            min: 0,
            max: 90,
            name: 'Beamwidth'
        }];

        if (eccentricityType === 'elliptical') {
            inputs.push({
                id: 'argumentOfPerigeeInput',
                min: 0,
                max: 360,
                name: 'Argument of Perigee'
            });
            if (isNaN(eccentricity) || eccentricity < 0 || eccentricity >= 1) { // Note: >= 1 for eccentricity
                showInputError('eccentricityValueInput', `Eccentricity must be between 0 and 1.`);
                hasError = true;
            } else {
                clearInputError('eccentricityValueInput');
            }
        }

        const values = {};
        inputs.forEach(input => {
            const rawValue = document.getElementById(input.id).value;
            const formattedValue = formatNumberInput(rawValue);
            const value = parseFloat(formattedValue);

            if (isNaN(value)) {
                showInputError(input.id, `${input.name} must be a number.`);
                hasError = true;
            } else if (value < input.min || value > input.max) {
                showInputError(input.id, `Input must be between ${input.min} and ${input.max}.`);
                hasError = true;
            } else {
                clearInputError(input.id);
                values[input.id.replace('Input', '')] = value;
            }
        });

        const epochInput = document.getElementById('epochInput').value;
        if (!epochInput) {
            showInputError('epochInput', "Epoch cannot be empty.");
            hasError = true;
        } else {
            clearInputError('epochInput');
        }

        if (hasError) {
            return false;
        }

        // This part correctly converts the LOCAL time from the input to a UTC timestamp.
        const localDate = new Date(epochInput);
        const utcTimestamp = localDate.getTime();

        const updatedData = {
            fileName: currentFileName,
            altitude: values.altitude,
            inclination: values.inclination,
            eccentricity: eccentricity,
            raan: values.raan,
            argumentOfPerigee: eccentricityType === 'elliptical' ? values.argumentOfPerigee : 0,
            trueAnomaly: values.trueAnomaly,
            epoch: epochInput,
            utcTimestamp: utcTimestamp,
            beamwidth: values.beamwidth,
            fileType: 'single'
        };

        const oldData = { ...fileOutputs.get(currentFileName)
        };
        recordAction({
            type: 'editFile',
            fileName: currentFileName,
            fileType: 'single',
            oldData: oldData,
            newData: updatedData
        });
        fileOutputs.set(currentFileName, updatedData);
        saveFilesToLocalStorage();

        if (window.viewSimulation) {
            window.viewSimulation(updatedData);
        }

        addFileToResourceSidebar(currentFileName, updatedData, 'single');
        return true;
    }, null, fileName, 'single');

    // --- START OF FIX ---
    // Populate modal with existing data
    document.getElementById('fileNameInput').value = dataToEdit.fileName;
    document.getElementById('altitudeInput').value = dataToEdit.altitude;
    document.getElementById('inclinationInput').value = dataToEdit.inclination;
    document.getElementById('raanInput').value = dataToEdit.raan;
    document.getElementById('trueAnomalyInput').value = dataToEdit.trueAnomaly;
    document.getElementById('beamwidthInput').value = dataToEdit.beamwidth;

    // FIX: Convert the stored UTC timestamp to the user's local time for the input field.
    const utcTimestamp = dataToEdit.utcTimestamp;
    const localDate = new Date(utcTimestamp);

    // Create a string in the "YYYY-MM-DDTHH:MM" format required by datetime-local input
    const year = localDate.getFullYear();
    const month = String(localDate.getMonth() + 1).padStart(2, '0');
    const day = String(localDate.getDate()).padStart(2, '0');
    const hours = String(localDate.getHours()).padStart(2, '0');
    const minutes = String(localDate.getMinutes()).padStart(2, '0');
    const localDateTimeString = `${year}-${month}-${day}T${hours}:${minutes}`;

    document.getElementById('epochInput').value = localDateTimeString;
    // --- END OF FIX ---

    if (dataToEdit.eccentricity == 0) {
        document.getElementById('eccentricityCircular').checked = true;
        toggleEccentricityInput('circular');
    } else {
        document.getElementById('eccentricityElliptical').checked = true;
        document.getElementById('eccentricityValueInput').value = formatNumberInput(dataToEdit.eccentricity);
        document.getElementById('argumentOfPerigeeInput').value = formatNumberInput(dataToEdit.argumentOfPerigee);
        toggleEccentricityInput('elliptical');
        }
    }

    function editConstellationParameter(fileName) {
    const dataToEdit = fileOutputs.get(fileName);
    if (!dataToEdit) {
        showCustomAlert("Constellation data not found.");
        return;
    }

    // NOTE: The modalBody HTML and onSave logic are unchanged.
    // The fix is in how the form is populated below.
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

    showModal("Edit Constellation Parameters", modalBody, () => {
        // This onSave logic is unchanged.
        let hasError = false;
        const currentFileName = document.getElementById('fileNameInput').value;
        const eccentricityType = document.querySelector('input[name="eccentricityType"]:checked').value;
        let eccentricity = 0;
        if (eccentricityType === 'elliptical') {
            const eccValue = document.getElementById('eccentricityValueInput').value;
            eccentricity = parseFloat(formatNumberInput(eccValue));
        }

        const inputs = [{
            id: 'altitudeInput',
            min: 100,
            max: 36000,
            name: 'Altitude'
        }, {
            id: 'inclinationInput',
            min: 0,
            max: 180,
            name: 'Inclination'
        }, {
            id: 'raanInput',
            min: 0,
            max: 360,
            name: 'RAAN'
        }, {
            id: 'trueAnomalyInput',
            min: 0,
            max: 360,
            name: 'True Anomaly'
        }, {
            id: 'beamwidthInput',
            min: 0,
            max: 90,
            name: 'Beamwidth'
        }];

        if (eccentricityType === 'elliptical') {
            inputs.push({
                id: 'argumentOfPerigeeInput',
                min: 0,
                max: 360,
                name: 'Argument of Perigee'
            });
            if (isNaN(eccentricity) || eccentricity < 0 || eccentricity >= 1) {
                showInputError('eccentricityValueInput', `Eccentricity must be between 0 and 1.`);
                hasError = true;
            } else {
                clearInputError('eccentricityValueInput');
            }
        }

        const values = {};
        inputs.forEach(input => {
            const rawValue = document.getElementById(input.id).value;
            const formattedValue = formatNumberInput(rawValue);
            const value = parseFloat(formattedValue);

            if (isNaN(value)) {
                showInputError(input.id, `${input.name} must be a number.`);
                hasError = true;
            } else if (value < input.min || value > input.max) {
                showInputError(input.id, `Input must be between ${input.min} and ${input.max}.`);
                hasError = true;
            } else {
                clearInputError(input.id);
                values[input.id.replace('Input', '')] = value;
            }
        });

        const epochInput = document.getElementById('epochInput').value;
        if (!epochInput) {
            showInputError('epochInput', "Epoch cannot be empty.");
            hasError = true;
        } else {
            clearInputError('epochInput');
        }

        const constellationType = document.querySelector('input[name="constellationType"]:checked').value;
        const constellationData = {
            constellationType
        };

        if (constellationType === 'train') {
            const numSatellites = parseInt(document.getElementById('numSatellitesInput').value);
            const separationType = document.querySelector('input[name="separationType"]:checked').value;
            const separationValue = parseFloat(formatNumberInput(document.getElementById('separationValueInput').value));

            if (isNaN(numSatellites) || numSatellites < 1) {
                showInputError('numSatellitesInput', 'Number of Satellites must be at least 1.');
                hasError = true;
            } else {
                clearInputError('numSatellitesInput');
            }

            if (isNaN(separationValue) || separationValue < 0) {
                showInputError('separationValueInput', 'Separation Value must be a non-negative number.');
                hasError = true;
            } else {
                clearInputError('separationValueInput');
            }

            Object.assign(constellationData, {
                numSatellites,
                separationType,
                separationValue
            });

        } else if (constellationType === 'walker') {
            const numPlanes = parseInt(document.getElementById('numPlanesInput').value);
            const satellitesPerPlane = parseInt(document.getElementById('satellitesPerPlaneInput').value);
            const raanSpread = parseFloat(formatNumberInput(document.getElementById('raanSpreadInput').value));
            const phasingFactor = parseFloat(formatNumberInput(document.getElementById('phasingFactorInput').value));

            if (isNaN(numPlanes) || numPlanes < 1) {
                showInputError('numPlanesInput', 'Number of Planes must be at least 1.');
                hasError = true;
            } else {
                clearInputError('numPlanesInput');
            }

            if (isNaN(satellitesPerPlane) || satellitesPerPlane < 1) {
                showInputError('satellitesPerPlaneInput', 'Satellites per Plane must be at least 1.');
                hasError = true;
            } else {
                clearInputError('satellitesPerPlaneInput');
            }

            if (isNaN(raanSpread)) {
                showInputError('raanSpreadInput', 'RAAN Spread must be a number.');
                hasError = true;
            } else {
                clearInputError('raanSpreadInput');
            }

            if (isNaN(phasingFactor)) {
                showInputError('phasingFactorInput', 'Phasing Factor must be a number.');
                hasError = true;
            } else {
                clearInputError('phasingFactorInput');
            }

            Object.assign(constellationData, {
                numPlanes,
                satellitesPerPlane,
                raanSpread,
                phasingFactor
            });
        }

        if (hasError) {
            return false;
        }

        const localDate = new Date(epochInput);
        const utcTimestamp = localDate.getTime();

        const updatedData = {
            fileName: currentFileName,
            altitude: values.altitude,
            inclination: values.inclination,
            eccentricity: eccentricity,
            raan: values.raan,
            argumentOfPerigee: eccentricityType === 'elliptical' ? values.argumentOfPerigee : 0,
            trueAnomaly: values.trueAnomaly,
            epoch: epochInput,
            utcTimestamp: utcTimestamp,
            beamwidth: values.beamwidth,
            satellites: [],
            fileType: 'constellation',
            ...constellationData
        };

        const oldData = { ...fileOutputs.get(currentFileName)
        };
        recordAction({
            type: 'editFile',
            fileName: currentFileName,
            fileType: 'constellation',
            oldData: oldData,
            newData: updatedData
        });
        fileOutputs.set(currentFileName, updatedData);
        saveFilesToLocalStorage();

        if (window.viewSimulation) {
            window.viewSimulation(updatedData);
        }

        addFileToResourceSidebar(currentFileName, updatedData, 'constellation');
        return true;
    }, null, fileName, 'constellation');

    // --- START OF FIX ---
    // Populate modal with existing data
    document.getElementById('fileNameInput').value = dataToEdit.fileName;
    document.getElementById('altitudeInput').value = dataToEdit.altitude;
    document.getElementById('inclinationInput').value = dataToEdit.inclination;
    document.getElementById('raanInput').value = dataToEdit.raan;
    document.getElementById('trueAnomalyInput').value = dataToEdit.trueAnomaly;
    document.getElementById('beamwidthInput').value = dataToEdit.beamwidth;

    // FIX: Convert the stored UTC timestamp to the user's local time for the input field.
    const utcTimestamp = dataToEdit.utcTimestamp;
    const localDate = new Date(utcTimestamp);

    const year = localDate.getFullYear();
    const month = String(localDate.getMonth() + 1).padStart(2, '0');
    const day = String(localDate.getDate()).padStart(2, '0');
    const hours = String(localDate.getHours()).padStart(2, '0');
    const minutes = String(localDate.getMinutes()).padStart(2, '0');
    const localDateTimeString = `${year}-${month}-${day}T${hours}:${minutes}`;

    document.getElementById('epochInput').value = localDateTimeString;
    // --- END OF FIX ---

    if (dataToEdit.eccentricity == 0) {
        document.getElementById('eccentricityCircular').checked = true;
        toggleEccentricityInput('circular');
    } else {
        document.getElementById('eccentricityElliptical').checked = true;
        document.getElementById('eccentricityValueInput').value = formatNumberInput(dataToEdit.eccentricity);
        document.getElementById('argumentOfPerigeeInput').value = formatNumberInput(dataToEdit.argumentOfPerigee);
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
            // RE-RENDER UPDATED GROUND STATION IMMEDIATELY
            if (window.viewSimulation) {
                window.viewSimulation(updatedData);
            } else if (window.addOrUpdateGroundStationInScene) {
                window.addOrUpdateGroundStationInScene(updatedData);
            }
            //updateOutputSidebar(updatedData);
            addFileToResourceSidebar(currentName, updatedData, 'groundStation');
            return true;
        }, null, name, 'groundStation');

        // Populate modal with existing data
        document.getElementById('gsNameInput').value = dataToEdit.name;
        document.getElementById('latitudeInput').value = dataToEdit.latitude;
        document.getElementById('longitudeInput').value = dataToEdit.longitude;
        document.getElementById('minElevationAngleInput').value = dataToEdit.minElevationAngle;
    }

    function editLinkBudget(name) {
        const dataToEdit = linkBudgetAnalysis.get(name);
        if (!dataToEdit) { showCustomAlert("Link Budget Analysis data not found."); return; }

        // Re-use the NewLinkBudgetMenu modal body and logic
        NewLinkBudgetMenu();

        // Populate the modal fields with the *original inputs* from the saved analysis
        document.getElementById('lbNameInput').value = dataToEdit.name;
        document.getElementById('lbNameInput').readOnly = true;
        
        // Performance Requirements
        document.getElementById('minimumSNRInput').value = dataToEdit.minimumSNR;
        document.getElementById('targetAreaInput').value = dataToEdit.targetArea;
        document.getElementById('elevationAngleInput').value = dataToEdit.elevationAngle;
        document.getElementById('orbitInclinationInput').value = dataToEdit.orbitInclination;

        // RF Parameters
        document.getElementById('transmitPowerInput').value = dataToEdit.transmitPower;
        document.getElementById('txAntennaGainInput').value = dataToEdit.txAntennaGain;
        document.getElementById('rxAntennaGainInput').value = dataToEdit.rxAntennaGain;
        document.getElementById('frequencyInput').value = dataToEdit.frequency;
        document.getElementById('bandwidthInput').value = dataToEdit.bandwidth;
        document.getElementById('noiseFigureInput').value = dataToEdit.noiseFigure;
        document.getElementById('atmosphericLossInput').value = dataToEdit.atmosphericLoss;
        document.getElementById('minSatellitesInViewInput').value = dataToEdit.minSatellitesInView;

        // Set editing context for the save function within NewLinkBudgetMenu's onSave
        editingFileName = name;
        editingFileType = 'linkBudget';
    }


// Placeholder for recordAction function if not defined elsewhere
if (typeof window.recordAction === 'undefined') {
    window.recordAction = function(action) {
        console.log("Action recorded:", action);
        // Implement actual history management here
    };
}

// Placeholder for setActiveControlButton function if not defined elsewhere
if (typeof window.setActiveControlButton === 'undefined') {
    window.setActiveControlButton = function(buttonId) {
        console.log("Active control button set to:", buttonId);
        // Implement actual button highlighting here
    };
}



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
            const toggle2DViewBtn = document.getElementById('toggle2DViewBtn'); // Dapatkan elemen tombol

            if (is2DViewActive) { // If switching TO 2D
                if (earthContainer) earthContainer.style.display = 'none';
                if (earth2DContainer) {
                    earth2DContainer.style.display = 'flex';
                    window.resizeCanvas2D();
                }
                // Ubah teks tombol menjadi "3D View"
                if (toggle2DViewBtn) {
                    toggle2DViewBtn.textContent = '3D View';
                }
            } else { // If switching TO 3D
                if (earthContainer) earthContainer.style.display = 'flex';
                if (earth2DContainer) earth2DContainer.style.display = 'none';
                // Ubah teks tombol menjadi "2D View"
                if (toggle2DViewBtn) {
                    toggle2DViewBtn.textContent = '2D View';
                }
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

        // --- START OF FIX ---
        // 1. Check if a satellite ID is selected.
        if (!window.selectedSatelliteId || window.activeSatellites.size === 0) {
            showCustomAlert("No satellite is selected to focus on. Please click one from the list in the 'Output' tab.");
            return;
        }

        // 2. Get the satellite object from the map using the ID.
        const selectedSat = window.activeSatellites.get(window.selectedSatelliteId);

        // 3. Check if the satellite object was actually found.
        if (!selectedSat) {
            showCustomAlert("Selected satellite could not be found. It may have been deleted.");
            window.selectedSatelliteId = null; // Clear the stale ID
            return;
        }
        // --- END OF FIX ---

        const core3D = window.getSimulationCoreObjects();

        // The rest of the original logic can now proceed safely because `selectedSat` is guaranteed to be a valid object.
        const prevState = {
            position: core3D.camera.position.clone(),
            rotation: core3D.camera.rotation.clone(),
            target: core3D.controls.target.clone(),
            closeView: window.closeViewEnabled
        };

        window.closeViewEnabled = !window.closeViewEnabled;
        document.getElementById('closeViewButton').textContent = window.closeViewEnabled ? 'Normal View' : 'Close View';
        window.activeSatellites.forEach(sat => sat.setActiveMesh(window.closeViewEnabled));
        core3D.controls.enabled = false;

        if (window.closeViewEnabled) {
            // This block now works correctly.
            const currentSatPos = selectedSat.mesh.position.clone();
            const forwardDir = selectedSat.velocity.length() > 0 ? selectedSat.velocity.clone().normalize() : new THREE.Vector3(0, 0, 1);
            const upDir = currentSatPos.clone().normalize();
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

        } else {
            core3D.controls.object.up.set(0, 1, 0);
            gsap.to(core3D.camera.position, {
                duration: 1.5,
                x: 0,
                y: 0,
                z: 5,
                ease: "power2.inOut",
                onUpdate: () => core3D.controls.update(),
                onComplete: () => {
                    core3D.controls.enabled = true;
                }
            });
            gsap.to(core3D.controls.target, {
                duration: 1.5,
                x: 0,
                y: 0,
                z: 0,
                ease: "power2.inOut",
                onUpdate: () => core3D.controls.update()
            });

            core3D.controls.minDistance = 1.2;
            core3D.controls.maxDistance = 10;
        }

        const newState = {
            position: core3D.camera.position.clone(),
            rotation: core3D.camera.rotation.clone(),
            target: core3D.controls.target.clone(),
            closeView: window.closeViewEnabled
        };
        recordAction({
            type: 'viewToggle',
            prevState: prevState,
            newState: newState
        });
    }

    // ----------------------------------------- END VIEW MENU FUNCTIONS ---------------------------------------------

// ------------------------------------- SAVE MENU FUNCTIONS (Corrected) ------------------------------------------------

// Expose functions to the global window object
window.showSavePopup = showSavePopup;
window.generateAndSaveSelected = generateAndSaveSelected;

/**
 * Calculates a satellite's lat/lon/alt at a specific UTC timestamp without
 * altering the live simulation state. This is a "pure" function for accurate,
 * on-demand position calculation.
 * @param {Satellite} sat - The satellite object from window.activeSatellites.
 * @param {number} utcTimestamp - The absolute UTC time in milliseconds.
 * @returns {{latitudeDeg: number, longitudeDeg: number, altitudeKm: number}|null} An object with coordinates, or null on failure.
 */
function calculatePositionAtTime(sat, utcTimestamp) {
    // This function replicates the core logic from sat.updatePosition but is "pure"
    // and does not modify any global state, ensuring consistent results.
    let positionEci;
    const {
        EarthRadius,
        SCENE_EARTH_RADIUS
    } = window.getSimulationCoreObjects();

    // --- 1. Propagate to get ECI position at the given time ---

    // Branch for TLE-based satellites using SGP4
    if (sat.parsedTle) {
        try {
            const sgp4Result = window.propagateSGP4(sat.parsedTle, new Date(utcTimestamp));
            if (sgp4Result && sgp4Result.position) {
                positionEci = sgp4Result.position; // This is a THREE.Vector3 in scene units
            } else {
                console.warn(`SGP4 propagation failed for save function at ${new Date(utcTimestamp).toISOString()}`);
                return null;
            }
        } catch (e) {
            console.error(`Error during SGP4 propagation for save:`, e);
            return null;
        }
    }
    // Branch for Keplerian-based satellites
    else {
        const timeSinceSatelliteEpoch = (utcTimestamp - sat.initialEpochUTC) / 1000;

        // Create a temporary satellite object for perturbation calculation to avoid modifying the live one.
        const tempSat = {
            params: { ...sat.params
            },
            initialEpochUTC: sat.initialEpochUTC,
            initialMeanAnomaly: sat.initialMeanAnomaly,
            initialRAAN: sat.initialRAAN,
            // These are needed by updateOrbitalElements
            initialArgPerigee: sat.params.argPerigeeRad,
            initialSemiMajorAxis: sat.params.semiMajorAxis * EarthRadius,
            initialEccentricity: sat.params.eccentricity,
            currentMeanAnomaly: sat.currentMeanAnomaly,
            currentRAAN: sat.currentRAAN,
        };

        // This function calculates perturbations and updates the state of tempSat
        window.updateOrbitalElements(tempSat, timeSinceSatelliteEpoch);

        // Now calculate the final ECI position using the perturbed elements
        const pos = window.calculateSatellitePositionECI(
            tempSat.params,
            tempSat.currentMeanAnomaly,
            tempSat.currentRAAN,
            SCENE_EARTH_RADIUS
        );
        positionEci = new THREE.Vector3(pos.x, pos.y, pos.z);
    }

    if (!positionEci) {
        return null; // Calculation failed
    }

    // --- 2. Convert ECI position to Geodetic (Lat/Lon/Alt) ---

    // Get Earth's rotation (GMST) for the specific timestamp. This is the most robust method.
    const gmst = window.getGMST(new Date(utcTimestamp));

    // Rotate the ECI position vector to the ECEF frame by the calculated GMST.
    const ecef = positionEci.clone().applyAxisAngle(new THREE.Vector3(0, 1, 0), -gmst);

    // Convert the ECEF vector to geodetic coordinates.
    const r_scene = ecef.length(); // distance from center in scene units
    if (r_scene < 1e-6) return {
        latitudeDeg: 0,
        longitudeDeg: 0,
        altitudeKm: -EarthRadius
    };

    const latRad = Math.asin(ecef.y / r_scene);
    const lonRad = Math.atan2(-ecef.z, ecef.x);

    const latitudeDeg = latRad * (180 / Math.PI);
    const longitudeDeg = lonRad * (180 / Math.PI);

    // Calculate altitude in kilometers.
    const altitudeKm = (r_scene * EarthRadius) - EarthRadius;

    return {
        latitudeDeg,
        longitudeDeg,
        altitudeKm
    };
}


// Show the “Save” dialog
function showSavePopup() {
    // remove any existing popup
    document.querySelectorAll('.custom-popup').forEach(el => el.remove());

    // gather list of selectable satellites
    const sats = [];
    window.activeSatellites.forEach((sat, id) => {
        sats.push({
            id,
            name: sat.name,
            startEpoch: sat.initialEpochUTC
        });
    });
    if (!sats.length) {
        showCustomAlert("No active satellites to save", "Caution!");
        return;
    }

    // build popup container
    const popup = document.createElement('div');
    popup.className = 'custom-popup';
    Object.assign(popup.style, {
        position: 'absolute',
        left: '50%',
        top: '50%',
        transform: 'translate(-50%,-50%)',
        background: '#fff',
        color: '#000',
        padding: '20px',
        border: '1px solid #ccc',
        zIndex: 10000,
        width: '360px',
        boxShadow: '0 5px 15px rgba(0,0,0,0.3)'
    });

    // helper: ms → datetime-local string
    const fmtLocal = ms => {
        const dt = new Date(ms);
        const pad = n => String(n).padStart(2, '0');
        return `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())}` +
            `T${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
    };

    // build HTML
    popup.innerHTML = `
    <h5>Select Satellite & Interval</h5>
    <label>Satellite:</label>
    <select id="saveSatSelect" class="form-control mb-2">
      ${sats.map(s=>
        `<option value="${s.id}" data-start="${s.startEpoch}">
            ${s.name}
          </option>`
      ).join('')}
    </select>
    <label>Start Time:</label>
    <input type="datetime-local" id="saveStartTime" class="form-control mb-2" disabled/>
    <label>End Time:</label>
    <input type="datetime-local" id="saveEndTime" class="form-control mb-2"/>
    <label>Sampling Interval:</label>
    <select id="saveStep" class="form-control mb-2">
      <option value="1">1 second</option>
      <option value="10">10 seconds</option>
      <option value="60">1 minute</option>
      <option value="600">10 minutes</option>
      <option value="3600">1 hour</option>
    </select>
    <label>Format:</label>
    <div class="mb-3">
      <label class="form-check-label me-3">
        <input type="radio" name="saveFormat" value="coordinates" checked> Coordinates
      </label>
      <label class="form-check-label me-3">
        <input type="radio" name="saveFormat" value="tle"> TLE
      </label>
      <select id="saveFileExt" class="form-select form-select-sm w-auto d-inline-block">
        <option value="csv" selected>.csv</option>
        <option value="txt">.txt</option>
      </select>
    </div>
    <div class="text-end">
      <button class="btn btn-secondary btn-sm" id="saveCancel">Cancel</button>
      <button class="btn btn-primary btn-sm" id="saveDoIt">Save</button>
    </div>
  `;

    document.body.appendChild(popup);

    const satSelect = popup.querySelector('#saveSatSelect');
    const startInput = popup.querySelector('#saveStartTime');
    const endInput = popup.querySelector('#saveEndTime');
    const fmtRadios = popup.querySelectorAll('input[name=saveFormat]');
    const extSelect = popup.querySelector('#saveFileExt');

    // when SAT changes, update start/end constraints
    function refreshTimes() {
        const opt = satSelect.selectedOptions[0];
        const startMs = Number(opt.dataset.start);
        const iso = fmtLocal(startMs);
        startInput.value = iso;
        endInput.min = iso;
        if (endInput.value < iso) endInput.value = iso;
    }
    satSelect.addEventListener('change', refreshTimes);
    refreshTimes();

    // show/hide ext chooser depending on format
    function toggleExt() {
        extSelect.style.display =
            popup.querySelector('input[name=saveFormat]:checked').value === 'coordinates' ?
            'inline-block' :
            'none';
    }
    fmtRadios.forEach(r => r.addEventListener('change', toggleExt));
    toggleExt();

    popup.querySelector('#saveCancel').onclick = () => popup.remove();
    popup.querySelector('#saveDoIt').onclick = () => generateAndSaveSelected(popup);
}

// Generate & download the file
function generateAndSaveSelected(popup) {
    const satId = popup.querySelector('#saveSatSelect').value;
    const startTs = new Date(popup.querySelector('#saveStartTime').value).getTime();
    const endTs = new Date(popup.querySelector('#saveEndTime').value).getTime();
    const step = parseInt(popup.querySelector('#saveStep').value, 10) * 1000;
    const fmt = popup.querySelector('input[name=saveFormat]:checked').value;
    const fileExt = popup.querySelector('#saveFileExt')?.value || 'csv';

    if (!satId || isNaN(endTs) || endTs < startTs) {
        showCustomAlert('Please pick a valid end time (must be after start time).', 'Error');
        return;
    }
    const sat = window.activeSatellites.get(satId);
    if (!sat) {
        showCustomAlert('Selected satellite not found in the simulation.', 'Error');
        return;
    }

    // ----- TLE export -----
    if (fmt === 'tle') {
        let txt = '';
        if (sat.tleLine1 && sat.tleLine2) {
            txt = sat.tleLine1 + "\n" + sat.tleLine2 + "\n";
        } else {
            // This part for generating a pseudo-TLE remains a snapshot-in-time, which is correct for TLEs.
            const {
                EarthRadius
            } = window.getSimulationCoreObjects();
            txt = makePseudoTle(sat.name, {
                epoch: sat.initialEpochUTC,
                inclination: sat.params.inclinationRad * (180 / Math.PI),
                raan: sat.currentRAAN * (180 / Math.PI),
                eccentricity: sat.params.eccentricity,
                argumentOfPerigee: sat.params.argPerigeeRad * (180 / Math.PI),
                trueAnomaly: sat.currentTrueAnomaly * (180 / Math.PI),
                altitude: (sat.mesh.position.length() * EarthRadius) - EarthRadius
            }) + "\n";
        }
        downloadText(`sat_${sat.name}.tle`, txt);
        popup.remove();
        return;
    }

    // ----- Coordinates export -----
    const lines = [];
    const firstPos = calculatePositionAtTime(sat, startTs);
    if (!firstPos) {
        showCustomAlert('Could not calculate initial position for the satellite.', 'Error');
        return;
    }

    const {
        orbitalPeriod
    } = calculateDerivedOrbitalParameters(
        (sat.params.semiMajorAxis - 1) * window.EarthRadius, // Use scene-unit based calculation
        sat.params.eccentricity
    );

    const header = {
        "Satellite Name": sat.name,
        "Start Time": new Date(startTs).toISOString(),
        "Stop Time": new Date(endTs).toISOString(),
        "UTC Offset": 0,
        "Altitude (km)": firstPos.altitudeKm.toFixed(3),
        "Inclination (°)": (sat.params.inclinationRad * 180 / Math.PI).toFixed(3),
        "Orbital Period (min)": (orbitalPeriod / 60).toFixed(3),
        "Orbit Type": sat.params.eccentricity < 1e-3 ? 'Circular' : 'Elliptical'
    };

    if (fileExt === 'txt') {
        Object.entries(header).forEach(([key, value]) => lines.push(`${key}:\t${value}`));
        lines.push('');
        lines.push(`Longitude\tLatitude\tAltitude(km)\tTime (UTC)\t\tElapsed(s)`);
    } else { // CSV
        Object.entries(header).forEach(([key, value]) => lines.push(`"${key}","${value}"`));
        lines.push('');
        lines.push(`Longitude,Latitude,Altitude(km),Time (UTC),Elapsed(s)`);
    }

    // Loop through the time range and calculate position at each step
    // This no longer modifies the live simulation state.
    for (let t = startTs; t <= endTs; t += step) {
        const posData = calculatePositionAtTime(sat, t);

        if (posData) {
            const {
                latitudeDeg: lat,
                longitudeDeg: lon,
                altitudeKm: alt
            } = posData;
            const elapsed = Math.round((t - startTs) / 1000);
            const timeStr = new Date(t).toISOString();

            if (fileExt === 'txt') {
                lines.push(`${lon.toFixed(6)}\t${lat.toFixed(6)}\t${alt.toFixed(3)}\t${timeStr}\t${elapsed}`);
            } else { // CSV
                lines.push(`${lon.toFixed(6)},${lat.toFixed(6)},${alt.toFixed(3)},"${timeStr}",${elapsed}`);
            }
        }
    }

    // The live simulation was never altered, so no state restoration is needed.

    // download the generated file
    const finalExt = fmt === 'coordinates' ? `.${fileExt}` : '.tle';
    downloadText(`sat_${sat.name}_coords${finalExt}`, lines.join('\n') + '\n');
    popup.remove();
}

// ------------------------------------- END SAVE MENU FUNCTIONS ---------------------------------------------


  // ------------------------------------- LOAD TLE FUNCTION ------------------------------------------------
  // This function will now create its own pop-up for TLE data entry.
function LoadTLE() {
    // Remove any other popups first
    document.querySelectorAll('.custom-popup').forEach(el => el.remove());

    const popup = document.createElement('div');
    popup.className = 'custom-popup';
    popup.style.width = '500px'; // Make it wider for TLE lines
    popup.innerHTML = `
        <div class="custom-popup-header">
            <h5 class="modal-title">Load TLE Data</h5>
            <button type="button" class="btn-close custom-popup-close-btn" aria-label="Close"></button>
        </div>
        <div class="custom-popup-body">
            <div class="mb-2">
                <label for="tle-name" class="form-label">Satellite Name (Optional)</label>
                <input type="text" id="tle-name" class="form-control" placeholder="e.g., ISS (ZARYA)">
            </div>
            <div class="mb-2">
                <label for="tle-line1" class="form-label">TLE Line 1</label>
                <input type="text" id="tle-line1" class="form-control" placeholder="1 25544U 98067A   24194.88263889  .00016717  00000-0  30777-3 0  9990">
            </div>
            <div class="mb-3">
                <label for="tle-line2" class="form-label">TLE Line 2</label>
                <input type="text" id="tle-line2" class="form-control" placeholder="2 25544  51.6416 252.1266 0006703 130.5360 325.0169 15.49384113135425">
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-primary" id="tle-load-btn">Load Satellite</button>
            </div>
        </div>
    `;
    document.body.appendChild(popup);
    makeDraggable(popup); // Make the new popup draggable

    // Attach event listeners for the new popup
    popup.querySelector('.custom-popup-close-btn').addEventListener('click', () => popup.remove());

    popup.querySelector('#tle-load-btn').addEventListener('click', () => {
        // This is the original logic from your LoadTLE function
        const nameInput = document.getElementById('tle-name').value.trim();
        const line1 = document.getElementById('tle-line1').value.trim();
        const line2 = document.getElementById('tle-line2').value.trim();

        if (!line1 || !line2) {
            return showCustomAlert("You must supply both TLE Line 1 and Line 2.");
        }

        let parsed;
        try {
            // Use the globally exposed parseTle function from sgp4.js
            parsed = window.parseTle(line1, line2);
        } catch (err) {
            return showCustomAlert("Invalid TLE format: " + err.message);
        }

        const satName = nameInput || `TLE_${parsed.satrec.satnum}`;

        // Use viewSimulation to properly clear the scene and set the epoch
        window.viewSimulation({
            fileType: 'tle', // Use a specific type for clarity
            fileName: satName,
            tleLine1: line1,
            tleLine2: line2
        });
        
        popup.remove(); // Close the popup on success
        showCustomAlert(`Loaded TLE for “${satName}”`);
    });
}
    // ------------------------------------- END LOAD TLE FUNCTION ------------------------------------------------


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
            const animationStatusDisplay3D = document.getElementById('animationStatusDisplay');
            const simulationClockDisplay3D = document.getElementById('simulationClockDisplay');

            const animationStatusDisplay2D = document.getElementById('animationStatusDisplay2D');
            const simulationClockDisplay2D = document.getElementById('simulationClockDisplay2D');

            if (animationStatusDisplay3D) animationStatusDisplay3D.style.display = is3DActive ? 'flex' : 'none';
            if (simulationClockDisplay3D) simulationClockDisplay3D.style.display = is3DActive ? 'flex' : 'none';

            if (animationStatusDisplay2D) animationStatusDisplay2D.style.display = is3DActive ? 'none' : 'flex';
            if (simulationClockDisplay2D) simulationClockDisplay2D.style.display = is3DActive ? 'none' : 'flex';
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
                linkBudgetAnalysis.delete(fileName);
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
                linkBudgetAnalysis.set(fileName, fileData);
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
                linkBudgetAnalysis.set(fileName, oldData);
            }
            saveFilesToLocalStorage();
            addFileToResourceSidebar(fileName, oldData, fileType); // Re-add/update sidebar entry
            // updateOutputSidebar(oldData); // Update output display
            // updateSatelliteListUI(); // Refresh UI lists
            // selectSatellite(fileName); // Re-select to update data display (for satellites)
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
                linkBudgetAnalysis.set(fileName, newData);
            }
            saveFilesToLocalStorage();
            addFileToResourceSidebar(fileName, newData, fileType); // Re-add/update sidebar entry
            //updateOutputSidebar(newData); // Update output display
           // updateSatelliteListUI(); // Refresh UI lists
            //selectSatellite(fileName); // Re-select to update data display (for satellites)
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
                //updateSatelliteListUI(); // Ensure UI lists are up to date after undo/redo
                // Attempt to re-select the original selected item if it still exists
                const currentSelectedData = fileOutputs.get(window.selectedSatelliteId) || groundStations.get(window.selectedSatelliteId) || linkBudgetAnalysis.get(window.selectedSatelliteId);
            } else {
                showCustomAlert("No actions to undo");
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
                //updateSatelliteListUI(); // Ensure UI lists are up to date after undo/redo
                const currentSelectedData = fileOutputs.get(window.selectedSatelliteId) || groundStations.get(window.selectedSatelliteId) || linkBudgetAnalysis.get(window.selectedSatelliteId);
            
            } else {
                showCustomAlert("No actions to redo");
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
            linkBudgetAnalysis.clear();
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
            //updateSatelliteListUI(); // Reset satellite list display

            console.log("Redirecting to homepage...");
            window.location.href = "/"; // Redirect to your home page or login page
        } //End of handleLogout function
      
        window.is2DViewActive = false; // Default: 3D view aktif
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
            // New menu items
        document.getElementById('newSingleMenuBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            NewSingleMenu();
        });
        document.getElementById('newConstellationMenuBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            NewConstellationMenu();
        });
        document.getElementById('newGroundStationMenuBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            NewGroundStationMenu();
        });
        document.getElementById('newLinkBudgetMenuBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            NewLinkBudgetMenu();
        });

        // --- Attach Event Listeners for View Menu Items ---
       // View menu items
        document.getElementById('resetViewBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            resetView();
        });
        document.getElementById('closeViewButton')?.addEventListener('click', function(event) {
            event.preventDefault();
            toggleCloseView();
        });
        document.getElementById('toggle2DViewBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            toggle2DView();
        });

        // Save menu items
        document.getElementById('showSavePopupBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            showSavePopup();
        });
        document.getElementById('loadTleBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            LoadTLE();
        });

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
            if (typeof window.load3DSimulationState === 'function') {
                window.load3DSimulationState();
                updateAnimationDisplay();
                setActiveControlButton(window.isAnimating ? 'startButton' : 'pauseButton');
            } else {
                console.error("Critical: load3DSimulationState function not found. Earth3Dsimulation.js might not be loaded or exposed correctly.");
            }
            // Panggil ini untuk mengatur teks tombol "2D View" atau "3D View" saat halaman dimuat
            toggle2DViewVisuals();
        }, 500);
    });
    </script>
</body>
</html>
