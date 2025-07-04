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