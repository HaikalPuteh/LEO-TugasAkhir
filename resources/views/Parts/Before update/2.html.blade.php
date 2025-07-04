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

        
