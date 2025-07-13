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

        .custom-contextmenu  li, .settings-contextmenu li {
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
            height: 28px !important;         /* Force specific height */
            padding: 4px 8px !important;     /* Force smaller padding */
            font-size: 12px !important;      /* Force smaller font */
            min-width: 28px !important;      /* Force minimum width */
        }

        .btn-toolbar .btn:hover {
            background-color: #001f4d;
            font-size: 11px !important; 
            
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: #ffffff;
            color: rgb(0, 0, 0);
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

        #single-files-list ul li, #constellation-files-list ul li, #ground-station-resource-list ul li, #link-budget-resource-list ul li {
            list-style: none;
            padding-left: 1.5rem;
            cursor: pointer;
            color: rgb(0, 0, 0);
        }

        #single-files-list ul li:hover, #constellation-files-list ul li:hover, #ground-station-resource-list ul li:hover, #link-budget-resource-list ul li:hover { /* Tambahkan ini juga untuk Link Budget */
            background-color: #e9ecef;
        }

        .sidebar-icon {
            width: 16px;
            text-align: center;
            margin-right: 6px;
            color:rgb(59, 132, 228); 
        }

        #output-menu ul {
            list-style: none;
            padding-left: 0.5rem;
            color: rgb(0, 0, 0);
        }

        #output-menu ul li {
            list-style: none;
            font-weight: normal;
            cursor: pointer;
            color: rgb(0, 0, 0);
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
            background-color: rgb(33, 92, 151);
            color: white; 
            border-bottom: 1px solid rgb(33, 92, 151);
            border-radius: 0;
        }

        .nav-tabs .nav-link.active {
            background-color: rgb(33, 92, 151); 
            color: white; 
            border-bottom: 1px solid rgb(33, 92, 151); 
            border-radius: 0; 
        }

        #animationStatusDisplay {
            position: absolute; 
            top: 13px;
            right: 13px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10;
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        #simulationClockDisplay {
            position: absolute; 
            top: 13px;
            left: 13px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10;
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        #animationStatusDisplay2D { 
            position: absolute;
            top: 85px;
            right: 13px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10;
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        #simulationClockDisplay2D {
            position: absolute;
            top: 85px;
            left: 262px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10;
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
            width: 350px;
            padding: 0;
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
            display: flex;
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
            color: #333;
            font-weight: bold;
            margin-bottom: 0;
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

        .label-controls {
            position: absolute;
            bottom: 20px;
            left: calc(250px + 20px);    /* Sidebar width + gap */
            display: flex;
            flex-direction: column;
            gap: 5px;
            z-index: 10;
        }

        .label-button {
            background-color: rgba(0, 51, 102, 0.7);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 10px;
            cursor: pointer;
            font-size: 14px;
            opacity: 0.8;
            transition: opacity 0.3s ease;
            min-width: 40px;
        }

        .label-button:hover {
            opacity: 1;
            background-color: rgba(0, 31, 77, 0.8);
        }

        .label-button.active {
            background-color: rgba(0, 100, 200, 0.9);
            opacity: 1;
        }

        /* Label configuration modal styles */
        .label-config-popup {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            color: black;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            z-index: 10000;
            width: 400px;
            padding: 0;
            border: none;
        }

        .label-config-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 15px 20px;
        }

        .label-config-body {
            padding: 20px;
        }

        .slider-container {
            margin: 15px 0;
        }

        .slider-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .slider-container input[type="range"] {
            width: 100%;
            margin: 5px 0;
        }

        .slider-value {
            float: right;
            color: #666;
            font-size: 0.9em;
        }

        /* Enhanced link-report-popup container */
        .link-report-popup {
            position: absolute;
            left: 50%; 
            top: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            color: #000;
            padding: 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            width: 700px;           /* Increased from 420px */
            max-width: 90vw;        /* Responsive for smaller screens */
            max-height: 85vh;       /* Don't take up entire screen */
            display: flex;
            flex-direction: column;
        }

        /* Header styling */
        .link-report-header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
            border-bottom: 1px solid #34495e;
        }

        .link-report-header h5 {
            margin: 0;
            font-size: 1.2em;
            font-weight: 600;
        }

        /* Form section styling */
        .link-report-form {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
            font-size: 0.9em;
        }

        .form-control {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.9em;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        /* Button styling */
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background: #138496;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* IMPROVED REPORT CONTAINER */
        .link-report-results {
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .report-container {
            flex: 1;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 13px;           /* Increased from default */
            line-height: 1.4;          /* Better readability */
            overflow: auto;
            border: 1px solid #dee2e6;
            background: #ffffff;
            margin: 0;
            padding: 15px;             /* Increased padding */
            white-space: pre-wrap;     /* Preserve formatting but allow wrapping */
            max-height: 400px;         /* Increased height */
        }

        /* Enhanced table-like formatting for CSV data */
        .report-container-formatted {
            flex: 1;
            overflow: auto;
            background: #ffffff;
            margin: 0;
            padding: 0;
            max-height: 400px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin: 0;
        }

        .report-table th {
            background: #f1f3f4;
            padding: 8px 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #2c3e50;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .report-table td {
            padding: 6px 12px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .report-table tr:hover {
            background: #f8f9fa;
        }

        .report-table tr:nth-child(even) {
            background: #fbfbfb;
        }

        /* Summary section styling */
        .report-summary {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            color: #0c5460;
        }

        .report-summary h6 {
            margin: 0 0 10px 0;
            color: #0c5460;
            font-weight: 600;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid rgba(12, 84, 96, 0.1);
        }

        .summary-label {
            font-weight: 500;
        }

        .summary-value {
            font-weight: 600;
            color: #0c5460;
        }

        /* Loading and status styling */
        .status-message {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
            font-style: italic;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .link-report-popup {
                width: 95vw;
                height: 90vh;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .report-table {
                font-size: 11px;
            }
            
            .report-table th,
            .report-table td {
                padding: 4px 8px;
            }
        }

        /* Additional For Report (Style) */
        /* Detailed report section styling */
        .report-detailed-section {
            margin-top: 20px;
            border-top: 2px solid #dee2e6;
            padding-top: 15px;
        }

        .report-detailed-section h6 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-weight: 600;
            font-size: 1.1em;
        }

        /* Scrollable container for detailed data */
        .report-scroll-container {
            max-height: 300px;
            overflow-y: auto;
            overflow-x: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: #ffffff;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Detailed table styling */
        .report-detailed-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 12px;
            min-width: 800px; /* Ensure table doesn't get too cramped */
        }

        .report-detailed-table thead {
            background: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .report-detailed-table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #dee2e6;
            border-right: 1px solid #dee2e6;
            font-size: 11px;
            white-space: nowrap;
        }

        .report-detailed-table th:last-child {
            border-right: none;
        }

        .report-detailed-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            border-right: 1px solid #eee;
            vertical-align: top;
            font-size: 11px;
        }

        .report-detailed-table td:last-child {
            border-right: none;
        }

        .report-detailed-table tr:hover {
            background: #f8f9fa;
        }

        .report-detailed-table tr:nth-child(even) {
            background: #fbfbfb;
        }

        .report-detailed-table tr:nth-child(even):hover {
            background: #f1f3f4;
        }

        /* Improve summary section spacing */
        .report-summary {
            margin-bottom: 10px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 10px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid rgba(12, 84, 96, 0.1);
            font-size: 13px;
        }

        .summary-label {
            font-weight: 500;
            color: #495057;
        }

        .summary-value {
            font-weight: 600;
            color: #0c5460;
            text-align: right;
        }

        /* Custom scrollbar for the detailed container */
        .report-scroll-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .report-scroll-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .report-scroll-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .report-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Responsive adjustments for detailed table */
        @media (max-width: 768px) {
            .report-scroll-container {
                max-height: 250px;
            }
            
            .report-detailed-table {
                font-size: 10px;
                min-width: 700px;
            }
            
            .report-detailed-table th,
            .report-detailed-table td {
                padding: 6px 4px;
                font-size: 10px;
            }
            
            .summary-grid {
                grid-template-columns: 1fr;
                gap: 6px;
            }
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
        <div class="d-flex align-items-center gap-1">
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
            <!-- 2. Alternative: Add floating label controls (place after zoom-controls div) -->
            <div class="label-controls">
                <button class="label-button" id="toggleNadirFloatBtn" title="Toggle Nadir Lines">
                    <i class="fas fa-arrow-down"></i>
                </button>
                <button class="label-button" id="toggleLabelsFloatBtn" title="Toggle All Labels">
                    <i class="fas fa-tag"></i>
                </button>
                <button class="label-button" id="toggleProximityLabelsFloatBtn" title="Smart Labels">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="label-button" id="configureLabelFloatBtn" title="Label Settings">
                    <i class="fas fa-cog"></i>
                </button>
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
                </div>
                `;
            document.body.appendChild(popup);
            makeDraggable(popup);

            // HAPUS listener untuk tombol .popup-close, HANYA Sisakan untuk .custom-popup-close-btn (X)
            // popup.querySelector('.popup-close').addEventListener('click', () => popup.remove());
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

        //window.showLinkReportPopup = showLinkReportPopup;

        // IMPROVED showLinkReportPopup function
        function showLinkReportPopup() {
            // Remove any existing popup
            document.querySelectorAll('.link-report-popup').forEach(el => el.remove());
            
            // Gather lists
            const sats = Array.from(window.activeSatellites.values())
                        .map(s => ({ id: s.id, name: s.name, start: s.initialEpochUTC }));
            const gses = Array.from(window.activeGroundStations.values())
                        .map(g => ({ id: g.id, name: g.name }));

            if (!sats.length || !gses.length) {
                showCustomAlert("You need at least one satellite and one ground station active", "Caution!");
                return;
            }

            const popup = document.createElement('div');
            popup.className = 'link-report-popup';

            // Helper: ms → local-datetime
            const fmtLocal = ms => {
                const dt = new Date(ms);
                const pad = n => String(n).padStart(2, '0');
                return `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())}` +
                    `T${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
            };

            popup.innerHTML = `
                <div class="link-report-header">
                    <h5>Satellite–Ground Station Link Analysis</h5>
                </div>
                <div class="link-report-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="linkSatSel">Satellite:</label>
                            <select id="linkSatSel" class="form-control">
                                ${sats.map(s => `<option value="${s.id}" data-start="${s.start}">${s.name}</option>`).join('')}
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="linkGsSel">Ground Station:</label>
                            <select id="linkGsSel" class="form-control">
                                ${gses.map(g => `<option value="${g.id}">${g.name}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="linkStart">Start Time:</label>
                            <input type="datetime-local" id="linkStart" class="form-control" disabled />
                        </div>
                        <div class="form-group">
                            <label for="linkEnd">End Time:</label>
                            <input type="datetime-local" id="linkEnd" class="form-control" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="linkStep">Time Step:</label>
                            <select id="linkStep" class="form-control">
                                <option value="1">1 second</option>
                                <option value="10">10 seconds</option>
                                <option value="60" selected>1 minute</option>
                                <option value="600">10 minutes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="linkFormat">Export Format:</label>
                            <select id="linkFormat" class="form-control">
                                <option value="csv" selected>CSV (.csv)</option>
                                <option value="txt">Text (.txt)</option>
                            </select>
                        </div>
                    </div>
                    <div class="button-group">
                        <button class="btn btn-secondary" id="linkCancel">Close</button>
                        <button class="btn btn-info" id="linkCompute">Analyze Link</button>
                        <button class="btn btn-primary" id="linkSave" disabled style="display: none;">Save Report</button>
                    </div>
                </div>
                <div class="link-report-results">
                    <div id="linkReportContainer" class="status-message">
                        Click "Analyze Link" to generate visibility report
                    </div>
                </div>
            `;

            document.body.appendChild(popup);

            const satSel = popup.querySelector('#linkSatSel');
            const gsSel = popup.querySelector('#linkGsSel');
            const startIn = popup.querySelector('#linkStart');
            const endIn = popup.querySelector('#linkEnd');
            const stepIn = popup.querySelector('#linkStep');
            const fmtSel = popup.querySelector('#linkFormat');
            const compute = popup.querySelector('#linkCompute');
            const saveBtn = popup.querySelector('#linkSave');
            const close1 = popup.querySelector('#linkCancel');
            const reportCt = popup.querySelector('#linkReportContainer');

            // Initialize start/end times
            function refreshTimes() {
                const startMs = +satSel.selectedOptions[0].dataset.start;
                startIn.value = fmtLocal(startMs);
                endIn.min = fmtLocal(startMs);
                if (!endIn.value || endIn.value < startIn.value) {
                    endIn.value = fmtLocal(startMs + 3600*1000);
                }
            }
            satSel.onchange = refreshTimes;
            refreshTimes();

            close1.onclick = () => popup.remove();

            compute.onclick = () => {
                // Show loading state
                reportCt.innerHTML = `
                    <div class="status-message">
                        <div class="loading-spinner"></div>
                        Analyzing satellite visibility...
                    </div>
                `;
                saveBtn.disabled = true;
                saveBtn.style.display = 'none';

                // Grab values
                const satId = satSel.value;
                const gsId = gsSel.value;
                const t0 = new Date(startIn.value).getTime();
                const t1 = new Date(endIn.value).getTime();
                const step = parseFloat(stepIn.value) * 1000;
                const ext = fmtSel.value;

                if (t1 < t0) {
                    reportCt.innerHTML = `<div class="status-message" style="color: #dc3545;">End time must be after start time</div>`;
                    return;
                }

                const sat = window.activeSatellites.get(satId);
                const gs = window.activeGroundStations.get(gsId);

                if (!sat || !gs) {
                    reportCt.innerHTML = `<div class="status-message" style="color: #dc3545;">Selected satellite or ground station not found</div>`;
                    return;
                }

                // Use setTimeout to allow UI to update before heavy computation
                setTimeout(() => {
                    try {
                        const access = calculateAccessPeriods(sat, gs, t0, t1, step);
                        displayFormattedReport(sat, gs, t0, t1, access, ext, reportCt, saveBtn);
                    } catch (error) {
                        console.error('Error in access calculation:', error);
                        reportCt.innerHTML = `<div class="status-message" style="color: #dc3545;">Error calculating access periods: ${error.message}</div>`;
                    }
                }, 100);
            };
        }

        // NEW: Enhanced report display function
        function displayFormattedReport(sat, gs, startTs, endTs, accessPeriods, fileExt, container, saveBtn) {
            console.log('Access periods found:', accessPeriods.length); // Debug log
            
            // Calculate statistics
            const numPasses = accessPeriods.length;
            const totalContactSec = accessPeriods.reduce((sum, p) => sum + (p.duration || 0), 0);
            const avgSec = numPasses ? totalContactSec / numPasses : 0;
            const maxDuration = numPasses ? Math.max(...accessPeriods.map(p => p.duration || 0)) : 0;
            const minDuration = numPasses ? Math.min(...accessPeriods.map(p => p.duration || 0)) : 0;

            // Create summary section
            const summaryHtml = `
                <div class="report-summary">
                    <h6>📊 Link Analysis Summary</h6>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span class="summary-label">Satellite:</span>
                            <span class="summary-value">${sat.name}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Ground Station:</span>
                            <span class="summary-value">${gs.name}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Analysis Period:</span>
                            <span class="summary-value">${new Date(startTs).toUTCString().slice(0, 16)} to ${new Date(endTs).toUTCString().slice(0, 16)}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Passes:</span>
                            <span class="summary-value">${numPasses}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Contact Time:</span>
                            <span class="summary-value">${(totalContactSec/3600).toFixed(2)} hours</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Average Pass Duration:</span>
                            <span class="summary-value">${(avgSec/60).toFixed(1)} minutes</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Longest Pass:</span>
                            <span class="summary-value">${(maxDuration/60).toFixed(1)} minutes</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Shortest Pass:</span>
                            <span class="summary-value">${numPasses ? (minDuration/60).toFixed(1) : 'N/A'} minutes</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Coverage Efficiency:</span>
                            <span class="summary-value">${((totalContactSec/((endTs-startTs)/1000))*100).toFixed(1)}%</span>
                        </div>
                    </div>
                </div>
            `;

            // Create detailed access data section
            let detailedDataHtml = '';
            if (numPasses > 0) {
                console.log('Creating detailed data for', numPasses, 'passes'); // Debug log
                
                // Build the complete table HTML including all rows
                let tableRowsHtml = '';
                accessPeriods.forEach((pass, i) => {
                    console.log(`Processing pass ${i + 1}:`, pass); // Debug log
                    
                    if (!pass || typeof pass.start !== 'number' || typeof pass.stop !== 'number') {
                        console.warn(`Invalid pass data at index ${i}:`, pass);
                        return;
                    }
                    
                    const nextGap = i < accessPeriods.length - 1 && accessPeriods[i + 1] ? 
                        ((accessPeriods[i + 1].start - pass.stop) / 1000 / 60) : null;
                    
                    const durationSec = pass.duration || 0;
                    const durationMin = durationSec / 60;
                    
                    tableRowsHtml += `
                        <tr>
                            <td><strong>${i + 1}</strong></td>
                            <td>${new Date(pass.start).toUTCString()}</td>
                            <td>${new Date(pass.stop).toUTCString()}</td>
                            <td>${durationSec.toFixed(2)}</td>
                            <td>${durationMin.toFixed(2)}</td>
                            <td>${nextGap !== null ? nextGap.toFixed(1) : '—'}</td>
                        </tr>
                    `;
                });
                
                detailedDataHtml = `
                    <div class="report-detailed-section">
                        <h6>📋 Detailed Access Data</h6>
                        <div class="report-scroll-container">
                            <table class="report-detailed-table">
                                <thead>
                                    <tr>
                                        <th>Access</th>
                                        <th>Start Time (UTC)</th>
                                        <th>End Time (UTC)</th>
                                        <th>Duration (sec)</th>
                                        <th>Duration (min)</th>
                                        <th>Gap to Next (min)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tableRowsHtml}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            } else {
                detailedDataHtml = `
                    <div class="report-detailed-section">
                        <h6>📋 Detailed Access Data</h6>
                        <div class="status-message" style="color: #856404; background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 10px 0; border-radius: 4px;">
                            <strong>No visibility periods found</strong><br>
                            The satellite is not visible from this ground station during the selected time period.<br>
                            Try extending the time range or checking the satellite's orbital parameters.
                        </div>
                    </div>
                `;
            }

            // CRITICAL FIX: Change the container's class to prevent CSS conflicts
            container.className = 'link-report-content';
            
            // Combine summary and detailed data and set it all at once
            container.innerHTML = summaryHtml + detailedDataHtml;
            
            // CRITICAL FIX: Show and enable save button
            saveBtn.disabled = false;
            saveBtn.style.display = 'inline-block';
            
            // Generate the detailed report content for saving
            const detailLines = generateLinkReportContent(sat, gs, startTs, endTs, accessPeriods, fileExt);
            const allLines = [
                `# Satellite-Ground Station Link Analysis Report`,
                `Generated: ${new Date().toISOString()}`,
                ``,
                `## Summary`,
                `Satellite: ${sat.name}`,
                `Ground Station: ${gs.name}`,
                `Analysis Period: ${new Date(startTs).toUTCString()} to ${new Date(endTs).toUTCString()}`,
                `Total passes: ${numPasses}`,
                `Total contact time: ${(totalContactSec/3600).toFixed(2)} hours`,
                `Average pass duration: ${(avgSec/60).toFixed(1)} minutes`,
                `Coverage efficiency: ${((totalContactSec/((endTs-startTs)/1000))*100).toFixed(1)}%`,
                ``,
                `## Detailed Access Data`
            ].concat(detailLines);

            const timestamp = new Date().toISOString().slice(0,10);
            const filename = `link_${sat.name}_${gs.name}_${timestamp}.${fileExt}`;

            saveBtn._data = { 
                filename: filename, 
                text: allLines.join('\n') + '\n' 
            };

            // Remove any existing click handlers and add new one
            saveBtn.onclick = null;
            saveBtn.onclick = () => {
                try {
                    const { filename, text } = saveBtn._data;
                    downloadText(filename, text);
                    showCustomAlert(`Report saved as ${filename}`, "Success!");
                } catch (error) {
                    console.error('Error saving report:', error);
                    showCustomAlert(`Error saving report: ${error.message}`, "Error!");
                }
            };

            console.log('Report display completed. Save button enabled:', !saveBtn.disabled); // Debug log
        }

// Update the main link report popup caller
window.showLinkReportPopup = showLinkReportPopup;

        function calculateAccessPeriods(sat, gs, startTs, endTs, step) {
            const core = window.getSimulationCoreObjects();
            const oldT = core.totalSimulatedTime;
            const oldE = core.currentEpochUTC;
            const oldRot = core.earthGroup.rotation.y;
            const initEpoch = sat.initialEpochUTC;
            const periods = [];

            let prevVis = false;
            let visStartCoarse = null;

            // CRITICAL FIX: Use a consistent time base for all calculations
            // The satellite's epoch becomes our reference point
            const referenceEpoch = sat.initialEpochUTC;

            // Helper: Check visibility at exact time t (ms)
            function isVisAt(t) {
                // Calculate simulation seconds from the satellite's epoch
                const simSec = (t - referenceEpoch) / 1000;
                
                // CRITICAL FIX: Set the simulation state properly
                core.setTotalSimulatedTime(simSec);
                core.setCurrentEpochUTC(referenceEpoch);
                
                // CRITICAL FIX: Update Earth rotation using the rotation manager
                if (window.earthRotationManager) {
                    const rotationAngle = window.earthRotationManager.peekRotationAngle(simSec);
                    core.earthGroup.rotation.y = rotationAngle;
                } else {
                    // Fallback if rotation manager not available
                    core.earthGroup.rotation.y = window.initialEarthRotationOffset + 
                        simSec * window.EARTH_ANGULAR_VELOCITY_RAD_PER_SEC;
                }
                
                // Update satellite position
                sat.updatePosition(simSec, 0);
                
                // IMPROVED: More robust visibility check
                return checkVisibilityImproved(sat, gs);
            }

            // Refine the transition between [t0,t1] to ±1s accuracy
            function findTransition(t0, t1, targetVisibility) {
                let lo = t0, hi = t1;
                while (hi - lo > 1000) { // Stop when within 1 second
                    const mid = (lo + hi) / 2;
                    const midVis = isVisAt(mid);
                    
                    if (midVis === targetVisibility) {
                        hi = mid;
                    } else {
                        lo = mid;
                    }
                }
                return (lo + hi) / 2;
            }

            // Coarse scan with improved transition detection
            for (let t = startTs; t <= endTs; t += step) {
                const vis = isVisAt(t);

                // Off→On: visibility gained
                if (vis && !prevVis) {
                    visStartCoarse = t;
                }
                // On→Off: visibility lost, record the access period
                if (!vis && prevVis && visStartCoarse !== null) {
                    // Refine both boundaries
                    const tOn = findTransition(visStartCoarse - step, visStartCoarse, true);
                    const tOff = findTransition(t - step, t, false);
                    
                    const duration = (tOff - tOn) / 1000; // Duration in seconds
                    
                    // Only add periods with meaningful duration (> 1 second)
                    if (duration > 1) {
                        periods.push({
                            start: tOn,
                            stop: tOff,
                            duration: duration
                        });
                    }
                    visStartCoarse = null;
                }
                prevVis = vis;
            }

            // Handle case where satellite is still visible at end time
            if (prevVis && visStartCoarse !== null) {
                const tOn = findTransition(visStartCoarse - step, visStartCoarse, true);
                let tOff = endTs;
                
                // Check if visibility actually ends before endTs
                if (!isVisAt(endTs)) {
                    tOff = findTransition(endTs - step, endTs, false);
                }
                
                const duration = (tOff - tOn) / 1000;
                if (duration > 1) {
                    periods.push({
                        start: tOn,
                        stop: tOff,
                        duration: duration
                    });
                }
            }

            // CRITICAL FIX: Restore the original simulation state
            core.setTotalSimulatedTime(oldT);
            core.setCurrentEpochUTC(oldE);
            core.earthGroup.rotation.y = oldRot;
            
            // CRITICAL FIX: Re-initialize Earth rotation manager to original epoch
            if (window.earthRotationManager) {
                window.earthRotationManager.initialize(oldE);
            }
            
            // Restore satellite position
            sat.updatePosition(oldT, 0);

            return periods;
        }

        // IMPROVED checkVisibility function with better numerical stability
        function checkVisibilityImproved(sat, gs) {
            // CRITICAL FIX: Ensure both objects have valid positions
            if (!sat || !sat.mesh || !sat.mesh.position || 
                !gs || !gs.mesh || !gs.mesh.position) {
                console.warn('Invalid satellite or ground station for visibility check');
                return false;
            }

            // Get world positions with error handling
            let gsWorldPos, satWorldPos;
            try {
                gsWorldPos = new THREE.Vector3();
                gs.mesh.getWorldPosition(gsWorldPos);
                satWorldPos = sat.mesh.position.clone(); // Satellite is already in world coords (ECI)
            } catch (error) {
                console.warn('Error getting world positions for visibility check:', error);
                return false;
            }

            // CRITICAL FIX: Improved beam cone check with better numerical stability
            const satToGs = gsWorldPos.clone().sub(satWorldPos);
            const satToGsDistance = satToGs.length();
            
            // Avoid division by zero
            if (satToGsDistance < 1e-10) {
                return false;
            }
            
            satToGs.normalize();
            const nadir = satWorldPos.clone().negate().normalize();
            
            // Use the satellite's beamwidth parameter with safety check
            const beamwidth = sat.params && typeof sat.params.beamwidth === 'number' ? 
                sat.params.beamwidth : 60; // Default 60 degrees if not specified
            
            const halfBeamRad = THREE.MathUtils.degToRad(beamwidth / 2);
            const dotProduct = THREE.MathUtils.clamp(nadir.dot(satToGs), -1, 1);
            const coneOK = Math.acos(dotProduct) <= halfBeamRad;

            // CRITICAL FIX: Improved horizon check
            const gsDir = gsWorldPos.clone().normalize();
            const satDir = satWorldPos.clone().normalize();
            const centralAngleDot = THREE.MathUtils.clamp(gsDir.dot(satDir), -1, 1);
            const centralAngle = Math.acos(centralAngleDot);
            
            // Use satellite's coverage angle if available, otherwise calculate from altitude
            let horizonAngle;
            if (sat.coverageAngleRad && typeof sat.coverageAngleRad === 'number') {
                horizonAngle = sat.coverageAngleRad;
            } else {
                // Calculate horizon angle from satellite altitude
                const satAltitude = satWorldPos.length() * window.EarthRadius; // Convert to km
                const earthRadius = window.EarthRadius || 6371; // km
                horizonAngle = Math.acos(earthRadius / satAltitude);
            }
            
            const horizonOK = centralAngle <= horizonAngle;

            // ADDITIONAL CHECK: Minimum elevation angle (5 degrees default)
            const minElevationRad = THREE.MathUtils.degToRad(5); // 5 degrees minimum elevation
            const elevationAngle = Math.PI/2 - centralAngle;
            const elevationOK = elevationAngle >= minElevationRad;

            return coneOK && horizonOK && elevationOK;
        }

        // ENHANCED checkVisibility function (replace the existing one)
        function checkVisibility(sat, gs) {
            return checkVisibilityImproved(sat, gs);
        }


        // Generate the report content
       // IMPROVED: Enhanced report generation with better validation
        function generateLinkReportContent(sat, gs, startTs, endTs, accessPeriods, fileExt) {
            const lines = [];
            // Validate inputs
            if (!sat || !gs) {
                lines.push('Error: Invalid satellite or ground station data');
                return lines;
            }
            
            const gsPos = `${gs.longitude?.toFixed(6) || 'N/A'}, ${gs.latitude?.toFixed(6) || 'N/A'}`;
            const satName = sat.name || 'Unknown Satellite';
            const gsName = gs.name || 'Unknown Ground Station';

            if (fileExt === 'txt') {
                lines.push(`Satellite Name: ${satName}`);
                lines.push(`Ground Station Name: ${gsName}`);
                lines.push(`Ground Station Position: ${gsPos}`);
                lines.push(`Start Time: ${new Date(startTs).toUTCString()}`);
                lines.push(`Stop Time: ${new Date(endTs).toUTCString()}`);
                lines.push('');
                lines.push(`Access\tStart Time (UTC)\tStop Time (UTC)\tDuration (sec)`);
                accessPeriods.forEach((p, i) => {
                    if (p && typeof p.start === 'number' && typeof p.stop === 'number' && typeof p.duration === 'number') {
                        lines.push(`${i + 1}\t${new Date(p.start).toUTCString()}\t${new Date(p.stop).toUTCString()}\t${p.duration.toFixed(2)}`);
                    }
                });
            } else {
                lines.push(`Satellite Name:,${satName}`);
                lines.push(`Ground Station Name:,${gsName}`);
                lines.push(`Ground Station Position:,${gsPos}`);
                lines.push(`Start Time:,${new Date(startTs).toUTCString()}`);
                lines.push(`Stop Time:,${new Date(endTs).toUTCString()}`);
                lines.push('');
                lines.push(`Access,Start Time (UTC),Stop Time (UTC),Duration (sec)`);
                accessPeriods.forEach((p, i) => {
                    if (p && typeof p.start === 'number' && typeof p.stop === 'number' && typeof p.duration === 'number') {
                        lines.push(`${i + 1},${new Date(p.start).toUTCString()},${new Date(p.stop).toUTCString()},${p.duration.toFixed(2)}`);
                    }
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
            closeAllCustomPopups();
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
            closeAllCustomPopups();
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
                    //{ id: 'minElevationAngleInput', min: 0, max: 90, name: 'Minimum Elevation Angle' }
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
                    //minElevationAngle: values.minElevationAngle,
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
               // document.getElementById('minElevationAngleInput').value = '';
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
            </div>`;

        showModal("Edit Ground Station", modalBody, () => {
            let hasError = false;
            const currentName = document.getElementById('gsNameInput').value.trim();
            const inputs = [
                { id: 'latitudeInput', min: -90, max: 90, name: 'Latitude' },
                { id: 'longitudeInput', min: -180, max: 180, name: 'Longitude' },
                //{ id: 'minElevationAngleInput', min: 0, max: 90, name: 'Minimum Elevation Angle' }
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
                //minElevationAngle: values.minElevationAngle,
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
        //document.getElementById('minElevationAngleInput').value = dataToEdit.minElevationAngle;
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

// ---------------------------------------------- End of Edit -----------------------------


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

                // 1) record the sat’s current world-position
                prevSatFollowPos.copy( selectedSat.mesh.position );

                // optional: record the fixed camera→sat vector if you want to preserve the zoom-distance
                followOffset
                .copy( core3D.camera.position )
                .sub( selectedSat.mesh.position );

                // lock zoom so they never tumble back to a global Earth view
                core3D.controls.minDistance = SCENE_EARTH_RADIUS * 0.02;  // ~2% of scene-Earth radius
                core3D.controls.maxDistance = SCENE_EARTH_RADIUS * 0.15;  // ~15% of scene-Earth radius

                // allow the user to tilt from straight up (sky) down to the horizon,
                // but never “under” it (so Earth can’t swing overhead)
                core3D.controls.minPolarAngle = 0;          // straight up
                core3D.controls.maxPolarAngle = Math.PI * 0.6; // about 108°
                


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
window.showSavePopup            = showSavePopup;
window.generateAndSaveSelected = generateAndSaveSelected;

// Show the “Save” dialog
function showSavePopup() {
  // remove any existing popup
  document.querySelectorAll('.custom-popup').forEach(el=>el.remove());

  // gather list of selectable satellites
  const sats = [];
  window.activeSatellites.forEach((sat, id) => {
    sats.push({ id, name: sat.name, startEpoch: sat.initialEpochUTC });
  });
  if (!sats.length) {
    // Memanggil showCustomAlert untuk menampilkan pesan yang konsisten
    showCustomAlert("No active satellites to save", "Caution!");
    return; // Penting untuk keluar dari fungsi setelah menampilkan alert
  }

  // build popup container
  const popup = document.createElement('div');
  popup.className = 'custom-popup';
  Object.assign(popup.style, {
    position: 'absolute', left: '50%', top: '50%',
    transform: 'translate(-50%,-50%)',
    background: '#fff', color:'#000',
    padding: '20px', border: '1px solid #ccc', zIndex:10000,
    width: '360px',
  });

  // helper: ms → datetime-local string
  const fmtLocal = ms => {
    const dt = new Date(ms);
    const pad = n=> String(n).padStart(2,'0');
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

  const satSelect  = popup.querySelector('#saveSatSelect');
  const startInput = popup.querySelector('#saveStartTime');
  const endInput   = popup.querySelector('#saveEndTime');
  const fmtRadios  = popup.querySelectorAll('input[name=saveFormat]');
  const extSelect  = popup.querySelector('#saveFileExt');

  // when SAT changes, update start/end constraints
  function refreshTimes() {
    const opt = satSelect.selectedOptions[0];
    const startMs = Number(opt.dataset.start);
    const iso     = fmtLocal(startMs);
    startInput.value = iso;
    endInput.min     = iso;
    if (endInput.value < iso) endInput.value = iso;
  }
  satSelect.addEventListener('change', refreshTimes);
  refreshTimes();

  // show/hide ext chooser depending on format
  function toggleExt() {
    extSelect.style.display =
      popup.querySelector('input[name=saveFormat]:checked').value === 'coordinates'
        ? 'inline-block'
        : 'none';
  }
  fmtRadios.forEach(r=>r.addEventListener('change', toggleExt));
  toggleExt();

  popup.querySelector('#saveCancel').onclick = ()=> popup.remove();
  popup.querySelector('#saveDoIt').onclick   = ()=> generateAndSaveSelected(popup);
}

// Generate & download the file
function generateAndSaveSelected(popup) {
  const satId   = popup.querySelector('#saveSatSelect').value;
  const startTs = new Date(popup.querySelector('#saveStartTime').value).getTime();
  const endTs   = new Date(popup.querySelector('#saveEndTime').value).getTime();
  const step    = parseInt(popup.querySelector('#saveStep').value, 10) * 1000;
  const fmt     = popup.querySelector('input[name=saveFormat]:checked').value;
  const fileExt = popup.querySelector('#saveFileExt')?.value || 'csv';

  if (!satId || isNaN(endTs) || endTs < startTs) {
    return alert('Please pick a valid end time (≥ start time).');
  }
  const sat = window.activeSatellites.get(satId);
  if (!sat) return alert('Satellite not found.');

  // Grab core objects and save original simulation state
  const core     = window.getSimulationCoreObjects();
  const origEpoch = core.currentEpochUTC;
  const origSim   = core.totalSimulatedTime;

  // CRITICAL FIX: Re‐base simulation to the sat's own epoch
  core.setCurrentEpochUTC(sat.initialEpochUTC);
  core.setTotalSimulatedTime(0);
  
  // CRITICAL FIX: Update Earth rotation manager for new epoch
  window.earthRotationManager.initialize(sat.initialEpochUTC);

  // Prepare header
  const lines = [];
  const altKm = (sat.mesh.position.length() * EarthRadius / SCENE_EARTH_RADIUS) - EarthRadius;
  const { orbitalPeriod } = calculateDerivedOrbitalParameters(
    (sat.params.semiMajorAxis - SCENE_EARTH_RADIUS) * (EarthRadius / SCENE_EARTH_RADIUS),
    sat.params.eccentricity
  );

  if (fmt === 'tle') {
    let txt = '';
    if (sat.tleLine1 && sat.tleLine2) {
      txt = sat.tleLine1 + "\n" + sat.tleLine2 + "\n";
    } else {
      txt = makePseudoTle(sat.name, {
        epoch:             sat.initialEpochUTC,
        inclination:       sat.params.inclinationRad * (180/Math.PI),
        raan:              sat.currentRAAN * (180/Math.PI),
        eccentricity:      sat.params.eccentricity,
        argumentOfPerigee: sat.params.argPerigeeRad * (180/Math.PI),
        trueAnomaly:       sat.currentTrueAnomaly * (180/Math.PI),
        altitude:          altKm
      }) + "\n";
    }
    downloadText(`sat_${sat.name}.tle`, txt);
    popup.remove();
    
    // CRITICAL FIX: Restore state AND Earth rotation manager
    core.setCurrentEpochUTC(origEpoch);
    core.setTotalSimulatedTime(origSim);
    window.earthRotationManager.initialize(origEpoch);
    sat.updatePosition(origSim, 0);
    return;
  }

  // Coordinates export header
  if (fileExt === 'txt') {
    lines.push(`Satellite Name:\t${sat.name}`);
    lines.push(`Start Time:\t${new Date(startTs).toISOString()}`);
    lines.push(`Stop Time:\t${new Date(endTs).toISOString()}`);
    lines.push(`UTC Offset:\t0`);
    lines.push(`Altitude (km):\t${altKm.toFixed(3)}`);
    lines.push(`Inclination (°):\t${(sat.params.inclinationRad * 180/Math.PI).toFixed(3)}`);
    lines.push(`Orbital Period (min):\t${(orbitalPeriod/60).toFixed(3)}`);
    lines.push(`Orbit Type:\t${sat.params.eccentricity < 1e-3 ? 'Circular' : 'Elliptical'}`);
    lines.push('');
    lines.push(`Longitude\tLatitude\t\tTime (UTC)\tElapsed(s)`);
  } else {
    lines.push(`Satellite Name:,${sat.name}`);
    lines.push(`Start Time:,${new Date(startTs).toISOString()}`);
    lines.push(`Stop Time:,${new Date(endTs).toISOString()}`);
    lines.push(`UTC Offset:,0`);
    lines.push(`Altitude (km):,${altKm.toFixed(3)}`);
    lines.push(`Inclination (°):,${(sat.params.inclinationRad * 180/Math.PI).toFixed(3)}`);
    lines.push(`Orbital Period (min):,${(orbitalPeriod/60).toFixed(3)}`);
    lines.push(`Orbit Type:,${sat.params.eccentricity < 1e-3 ? 'Circular' : 'Elliptical'}`);
    lines.push('');
    lines.push(`Longitude,Latitude,Time (UTC),Elapsed(s)`);
  }

  // Step through and record positions
  for (let t = startTs; t <= endTs; t += step) {
    const simSec = (t - sat.initialEpochUTC) / 1000;
    core.setTotalSimulatedTime(simSec);
    sat.updatePosition(simSec, 0);

    const { latitudeDeg: lat, longitudeDeg: lon } = sat;
    const elapsed = Math.round((t - startTs) / 1000);

    if (fileExt === 'txt') {
      lines.push(`${lon.toFixed(6)}\t${lat.toFixed(6)}\t${new Date(t).toISOString()}\t${elapsed}`);
    } else {
      lines.push(`${lon.toFixed(6)},${lat.toFixed(6)},${new Date(t).toISOString()},${elapsed}`);
    }
  }

  // CRITICAL FIX: Restore the original simulation state AND Earth rotation manager
  core.setCurrentEpochUTC(origEpoch);
  core.setTotalSimulatedTime(origSim);
  window.earthRotationManager.initialize(origEpoch);
  sat.updatePosition(origSim, 0);

  // Trigger download
  const ext = `.${fileExt}`;
  downloadText(`sat_${sat.name}${ext}`, lines.join('\n') + '\n');
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

        // Zoom in function for camera control
        function zoomIn() {
            const core3D = window.getSimulationCoreObjects();
            if (!core3D.camera || !core3D.controls) { console.warn("Three.js not initialized for zoom."); return; }

            const prevState = {
                position: core3D.camera.position.clone(),
                rotation: core3D.camera.rotation.clone(),
                target: core3D.controls.target.clone()
            };

            // Tentukan posisi target zoom in yang baru
            // Kita akan mendekatkan kamera ke target kontrolnya (biasanya pusat Bumi atau satelit)
            // Cara paling sederhana adalah mendekatkan kamera ke target kontrolnya
            const newTargetPosition = core3D.controls.target.clone(); // Ambil target saat ini
            const newCameraPosition = core3D.camera.position.clone();
            newCameraPosition.sub(newTargetPosition); // Vektor dari target ke kamera
            newCameraPosition.multiplyScalar(0.8); // Perkecil jarak sebesar 20% (zoom in)
            newCameraPosition.add(newTargetPosition); // Tambahkan kembali ke target

            // Rekam state baru (sebelum animasi selesai, ini akan menjadi target akhir animasi)
            const newState = {
                position: newCameraPosition.clone(),
                rotation: core3D.camera.rotation.clone(), // Rotasi kamera mungkin tidak berubah banyak
                target: newTargetPosition.clone()
            };

            // Nonaktifkan sementara OrbitControls agar GSAP bisa mengontrol kamera dengan mulus
            core3D.controls.enabled = false;

            gsap.to(core3D.camera.position, {
                duration: 0.5, // Durasi animasi dalam detik
                x: newCameraPosition.x,
                y: newCameraPosition.y,
                z: newCameraPosition.z,
                ease: "power2.out", // Jenis easing untuk efek smooth
                onUpdate: () => core3D.controls.update(), // Perbarui kontrol saat animasi berjalan
                onComplete: () => {
                    core3D.controls.enabled = true; // Aktifkan kembali kontrol setelah animasi selesai
                    core3D.controls.update();
                }
            });

            // Animasikan juga target kontrol jika kamera bergerak relatif terhadap target
            // Jika targetnya selalu (0,0,0) maka ini tidak perlu, tapi jika mengikuti satelit akan penting
            gsap.to(core3D.controls.target, {
                duration: 0.5,
                x: newTargetPosition.x,
                y: newTargetPosition.y,
                z: newTargetPosition.z,
                ease: "power2.out",
                onUpdate: () => core3D.controls.update()
            });

            recordAction({ type: 'camera', prevState: prevState, newState: newState });
        }

        // Zoom out function
        function zoomOut() {
            const core3D = window.getSimulationCoreObjects();
            if (!core3D.camera || !core3D.controls) { console.warn("Three.js not initialized for zoom."); return; }

            const prevState = {
                position: core3D.camera.position.clone(),
                rotation: core3D.camera.rotation.clone(),
                target: core3D.controls.target.clone()
            };

            // Tentukan posisi target zoom out yang baru
            const newTargetPosition = core3D.controls.target.clone(); // Ambil target saat ini
            const newCameraPosition = core3D.camera.position.clone();
            newCameraPosition.sub(newTargetPosition); // Vektor dari target ke kamera
            newCameraPosition.multiplyScalar(1.25); // Perbesar jarak sebesar 25% (zoom out)
            newCameraPosition.add(newTargetPosition); // Tambahkan kembali ke target

            // Rekam state baru
            const newState = {
                position: newCameraPosition.clone(),
                rotation: core3D.camera.rotation.clone(),
                target: newTargetPosition.clone()
            };

            // Nonaktifkan sementara OrbitControls
            core3D.controls.enabled = false;

            gsap.to(core3D.camera.position, {
                duration: 0.5, // Durasi animasi
                x: newCameraPosition.x,
                y: newCameraPosition.y,
                z: newCameraPosition.z,
                ease: "power2.out",
                onUpdate: () => core3D.controls.update(),
                onComplete: () => {
                    core3D.controls.enabled = true; // Aktifkan kembali kontrol
                    core3D.controls.update();
                }
            });

            // Animasikan juga target kontrol jika perlu
            gsap.to(core3D.controls.target, {
                duration: 0.5,
                x: newTargetPosition.x,
                y: newTargetPosition.y,
                z: newTargetPosition.z,
                ease: "power2.out",
                onUpdate: () => core3D.controls.update()
            });

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
            addFileToResourceSidebar(fileName, oldData, fileType); // Re-add/update sidebar entr
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
                populateResourceTab();
                populateReportsList();
                // --- TEMPATKAN KODE INI DI SINI setelah populateReportsList(); ---
                const outputTabBtn = document.getElementById('outputTabBtn');
                const resourceTabBtn = document.getElementById('resourceTabBtn'); // Ini sebenarnya tidak digunakan dalam baris selanjutnya, tapi tidak masalah jika ada.
                // --- END OF PLACEMENT ---
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
                populateResourceTab();
                populateReportsList();
                // --- TEMPATKAN KODE INI DI SINI setelah populateReportsList(); ---
                const outputTabBtn = document.getElementById('outputTabBtn');
                const resourceTabBtn = document.getElementById('resourceTabBtn'); // Ini sebenarnya tidak digunakan dalam baris selanjutnya, tapi tidak masalah jika ada.
                // --- END OF PLACEMENT ---
            } else {
                showCustomAlert("No actions to redo");
            }
        }


// Add these event handlers to your simulation.blade.php script section

// Label configuration popup function
function showLabelConfigPopup() {
    // Remove any existing popup
    document.querySelectorAll('.label-config-popup').forEach(el => el.remove());

    const popup = document.createElement('div');
    popup.className = 'label-config-popup';
    popup.innerHTML = `
        <div class="label-config-header">
            <h5 class="modal-title">Label Settings</h5>
            <button type="button" class="btn-close custom-popup-close-btn" aria-label="Close"></button>
        </div>
        <div class="label-config-body">
            <div class="mb-3">
                <label class="form-check-label">
                    <input type="checkbox" id="nadirLinesCheck" ${window.nadirLinesEnabled ? 'checked' : ''}> 
                    Show Nadir Lines
                </label>
            </div>
            <div class="mb-3">
                <label class="form-check-label">
                    <input type="checkbox" id="globalLabelsCheck" ${window.labelVisibilityEnabled ? 'checked' : ''}> 
                    Show Labels
                </label>
            </div>
            <div class="mb-3">
                <label class="form-check-label">
                    <input type="checkbox" id="proximityLabelsCheck" ${window.proximityLabelsEnabled ? 'checked' : ''}> 
                    Smart Labels (Proximity-based)
                </label>
            </div>
            <div class="slider-container">
                <label for="proximitySlider">
                    Proximity Distance
                    <span class="slider-value" id="proximityValue">${window.labelProximityDistance.toFixed(1)}</span>
                </label>
                <input type="range" id="proximitySlider" min="0.5" max="5" step="0.1" 
                    value="${window.labelProximityDistance}">
                <small class="text-muted">Distance from camera to show labels</small>
            </div>
            <div class="slider-container">
                <label for="maxLabelsSlider">
                    Max Visible Labels
                    <span class="slider-value" id="maxLabelsValue">${window.maxVisibleLabels}</span>
                </label>
                <input type="range" id="maxLabelsSlider" min="1" max="50" step="1" 
                    value="${window.maxVisibleLabels}">
                <small class="text-muted">Maximum number of labels to show at once</small>
            </div>
            <div class="text-end mt-3">
                <button class="btn btn-secondary btn-sm" id="labelConfigCancel">Cancel</button>
                <button class="btn btn-primary btn-sm" id="labelConfigApply">Apply</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(popup);
    makeDraggable(popup);

    // Event listeners for the popup
    const proximitySlider = popup.querySelector('#proximitySlider');
    const maxLabelsSlider = popup.querySelector('#maxLabelsSlider');
    const proximityValue = popup.querySelector('#proximityValue');
    const maxLabelsValue = popup.querySelector('#maxLabelsValue');

    proximitySlider.addEventListener('input', () => {
        proximityValue.textContent = parseFloat(proximitySlider.value).toFixed(1);
    });

    maxLabelsSlider.addEventListener('input', () => {
        maxLabelsValue.textContent = maxLabelsSlider.value;
    });

    popup.querySelector('.custom-popup-close-btn').addEventListener('click', () => popup.remove());
    popup.querySelector('#labelConfigCancel').addEventListener('click', () => popup.remove());

    popup.querySelector('#labelConfigApply').addEventListener('click', () => {
        const globalEnabled = popup.querySelector('#globalLabelsCheck').checked;
        const proximityEnabled = popup.querySelector('#proximityLabelsCheck').checked;
        const proximityDistance = parseFloat(proximitySlider.value);
        const maxLabels = parseInt(maxLabelsSlider.value);

        // Apply settings
        if (window.labelVisibilityManager) {
            window.labelVisibilityManager.setGlobalVisibility(globalEnabled);
            window.labelVisibilityManager.setProximityMode(proximityEnabled, proximityDistance, maxLabels);
        }
        const nadirEnabled = popup.querySelector('#nadirLinesCheck').checked;
        
        // Apply nadir lines setting
        if (window.labelVisibilityManager) {
            window.labelVisibilityManager.setNadirLinesVisibility(nadirEnabled);
        }

        // Update button states
        updateLabelButtonStates();
        
        popup.remove();
        showCustomAlert('Label settings applied successfully!');
    });
}

// Update button visual states
function updateLabelButtonStates() {
    // Update dropdown menu buttons
    const toggleBtn = document.getElementById('toggleLabelsBtn');
    const proximityBtn = document.getElementById('toggleProximityLabelsBtn');
    
    if (toggleBtn) {
        toggleBtn.textContent = window.labelVisibilityEnabled ? 'Hide Labels' : 'Show Labels';
    }
    if (proximityBtn) {
        proximityBtn.textContent = window.proximityLabelsEnabled ? 'Disable Smart Labels' : 'Enable Smart Labels';
    }

    // Update floating buttons
    const toggleFloatBtn = document.getElementById('toggleLabelsFloatBtn');
    const proximityFloatBtn = document.getElementById('toggleProximityLabelsFloatBtn');
    
    if (toggleFloatBtn) {
        toggleFloatBtn.classList.toggle('active', window.labelVisibilityEnabled);
        toggleFloatBtn.title = window.labelVisibilityEnabled ? 'Hide Labels' : 'Show Labels';
    }
    if (proximityFloatBtn) {
        proximityFloatBtn.classList.toggle('active', window.proximityLabelsEnabled);
        proximityFloatBtn.title = window.proximityLabelsEnabled ? 'Disable Smart Labels' : 'Enable Smart Labels';
    }
    updateNadirButtonStates();
}

// Expose the functions globally
window.showLabelConfigPopup = showLabelConfigPopup;
window.updateLabelButtonStates = updateLabelButtonStates;

// 4. ADD BUTTON STATE UPDATE FUNCTION (simulation.blade.php)
function updateNadirButtonStates() {
    // Update dropdown menu button
    const toggleBtn = document.getElementById('toggleNadirBtn');
    if (toggleBtn) {
        toggleBtn.textContent = window.nadirLinesEnabled ? 'Hide Nadir Lines' : 'Show Nadir Lines';
    }

    // Update floating button
    const toggleFloatBtn = document.getElementById('toggleNadirFloatBtn');
    if (toggleFloatBtn) {
        toggleFloatBtn.classList.toggle('active', window.nadirLinesEnabled);
        toggleFloatBtn.title = window.nadirLinesEnabled ? 'Hide Nadir Lines' : 'Show Nadir Lines';
        
        // Optional: Change icon color when active
        if (window.nadirLinesEnabled) {
            toggleFloatBtn.classList.add('nadir-button-active');
        } else {
            toggleFloatBtn.classList.remove('nadir-button-active');
        }
    }
}
window.updateNadirButtonStates = updateNadirButtonStates;


// --- LOGOUT FUNCTION ---
    window.handleLogout = handleLogout; // Expose this one too
    function handleLogout() {
    console.log("handleLogout function called.");
    console.log("Attempting to redirect to:", window.location.origin + "/");
    window.location.href = "/"; // Target homepage
        }
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

        // Label control event listeners - Dropdown menu
        document.getElementById('toggleLabelsBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            if (window.toggleLabels) {
                window.toggleLabels();
                updateLabelButtonStates();
            }
        });

        document.getElementById('toggleProximityLabelsBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            if (window.toggleProximityLabels) {
                window.toggleProximityLabels();
                updateLabelButtonStates();
            }
        });

        document.getElementById('configureLabelBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            showLabelConfigPopup();
        });

        // Label control event listeners - Floating buttons
        document.getElementById('toggleLabelsFloatBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            if (window.toggleLabels) {
                window.toggleLabels();
                updateLabelButtonStates();
            }
        });

        document.getElementById('toggleProximityLabelsFloatBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            if (window.toggleProximityLabels) {
                window.toggleProximityLabels();
                updateLabelButtonStates();
            }
        });

        document.getElementById('configureLabelFloatBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            showLabelConfigPopup();
        });

        // Nadir line control event listeners - Dropdown menu
        document.getElementById('toggleNadirBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            if (window.toggleNadirLines) {
                window.toggleNadirLines();
            }
        });

        // Nadir line control event listeners - Floating button
        document.getElementById('toggleNadirFloatBtn')?.addEventListener('click', function(event) {
            event.preventDefault();
            if (window.toggleNadirLines) {
                window.toggleNadirLines();
            }
        });


        // Initialize button states
        setTimeout(() => {
            updateLabelButtonStates();
            updateNadirButtonStates(); // ADD THIS LINE
        }, 1000);

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

    // Tambahkan fungsi ini di dalam <script type="module"> Anda,
    // di scope global atau di dekat fungsi-fungsi pembantu lainnya.
    function closeAllCustomPopups() {
        // Menutup popup satelit/ground station yang sedang aktif
        if (window.activeSatellitePopup) {
            const { element, updateHandler } = window.activeSatellitePopup;
            element.remove();
            window.removeEventListener('epochUpdated', updateHandler); // Hapus listener
            window.activeSatellitePopup = null;
        }

        // Menutup popup lain yang mungkin menggunakan class 'custom-popup'
        // yang tidak dikelola oleh activeSatellitePopup
        document.querySelectorAll('.custom-popup').forEach(popup => {
            // Hanya hapus jika popup tersebut bukan bagian dari Bootstrap modal
            // Bootstrap modal memiliki class 'modal-dialog' pada elemen parent dari '.custom-popup'
            // atau kita bisa membedakan berdasarkan ID yang diketahui (misal, untuk save popup)
            if (!popup.closest('.modal-dialog')) {
                popup.remove();
            }
        });
    }

    // Tambahkan di mana saja di scope <script type="module"> Anda
// Fungsi ini akan membersihkan detail yang mungkin ditampilkan di tab Output
function updateOutputSidebar(data = null) {
    // Implementasi sederhana: membersihkan area detail atau menampilkan info default
    const reportsList = document.getElementById('reports-list');
    const linkReportsList = document.getElementById('link-reports-list'); // Jika Anda punya area terpisah untuk link reports

    // Clear the existing reports and links output to rebuild it
    reportsList.innerHTML = '';
    linkReportsList.innerHTML = ''; // Pastikan ini juga dibersihkan jika diperlukan

    // Membangun ulang daftar laporan utama
    populateReportsList();
}

function updateSatelliteListUI() {
    populateResourceTab(); // Memanggil fungsi yang sudah ada untuk me-render ulang daftar resource
}
    </script>
</body>
</html>

