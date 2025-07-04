
<script>
    
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
        document.getElementById('loadTleBtn')?.addEventListener('click', LoadTLE); // Assuming ID for Load TLE

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