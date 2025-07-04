<script>

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


        <script>

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
    window.selectedSatelliteId = id;
    window.selectedItemType = type;

    const satelliteDataDisplay = document.getElementById("satelliteDataDisplay");

    // Hide all constellation member lists before potentially showing one
    document.querySelectorAll('.constellation-members-list').forEach(list => {
        list.classList.add('hidden');
    });

    if (type === 'single') {
        selectedData = fileOutputs.get(id) || (window.activeSatellites ? window.activeSatellites.get(id) : null);
        updateSatelliteDataDisplay(); // Update data display for satellites/constellations
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'block';

        // View only this single satellite in the simulation
        if (window.viewSimulation && selectedData) {
            window.viewSimulation([selectedData]);
        } else if (window.viewSimulation) {
            window.viewSimulation(null); // Clear scene if satellite not found
        }
    } else if (type === 'constellation') {
        selectedData = fileOutputs.get(id); // constellation data from fileOutputs map
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none'; // Constellations don't have detailed data panel

        // Toggle the visibility of the constellation's member list
        const memberList = document.getElementById(`constellation-${id}-members`);
        if (memberList) {
            memberList.classList.toggle('hidden');
        }

        // View all satellites within this constellation in the simulation
        if (window.viewSimulation && selectedData && selectedData.satellites && window.activeSatellites) {
            const constellationSats = selectedData.satellites
                .map(satId => window.activeSatellites.get(satId))
                .filter(Boolean); // Filter out any undefined/null entries
            window.viewSimulation(constellationSats);
        } else if (window.viewSimulation) {
            window.viewSimulation(null); // Clear scene if constellation data not found
        }
    } else if (type === 'groundStation') {
        selectedData = groundStations.get(id);
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none'; // Hide satellite data display for GS
        if (window.viewSimulation) window.viewSimulation(null); // Clear existing satellites
        if (window.addOrUpdateGroundStationInScene) window.addOrUpdateGroundStationInScene(selectedData); // Show only this GS in scene
    } else if (type === 'linkBudget') {
        selectedData = linkBudgetAnalyses.get(id);
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none'; // Hide satellite data display for LB
        // Logic to display link budget data (e.g., in a dedicated section)
        if (window.viewSimulation) window.viewSimulation(null); // Clear existing satellites/GS
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
    const hasAnyItems = fileOutputs.size > 0 || (window.activeSatellites ? window.activeSatellites.size > 0 : false) || groundStations.size > 0 || linkBudgetAnalyses.size > 0;

    if (hasAnyItems) {
        if (satelliteListDisplay) satelliteListDisplay.style.display = 'block';

        // --- Create containers for each category ---
        mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Single Satellites:</h6><div id="singleSatButtons" class="btn-group-container flex flex-wrap gap-2 mb-4"></div>`);
        const singleSatButtonsContainer = document.getElementById('singleSatButtons');

        mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Constellations:</h6><div id="constellationButtons" class="btn-group-container flex flex-col gap-2 mb-4"></div>`);
        const constellationButtonsContainer = document.getElementById('constellationButtons');

        mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Ground Stations:</h6><div id="groundStationButtons" class="btn-group-container flex flex-wrap gap-2 mb-4"></div>`);
        const groundStationButtonsContainer = document.getElementById('groundStationButtons');

        mainContainer.insertAdjacentHTML('beforeend', `<h6 class="text-dark mt-2">Link Budget Analyses:</h6><div id="linkBudgetButtons" class="btn-group-container flex flex-wrap gap-2 mb-4"></div>`);
        const linkBudgetButtonsContainer = document.getElementById('linkBudgetButtons');


        // --- Populate buttons into their respective containers ---

        // Keep track of satellites already part of a listed constellation to avoid duplicate 'single' buttons
        const constellationMemberIds = new Set();
        fileOutputs.forEach(data => {
            if (data.fileType === 'constellation' && data.satellites) {
                data.satellites.forEach(satId => constellationMemberIds.add(satId));
            }
        });

        // Add buttons for all saved satellites (single) from fileOutputs
        fileOutputs.forEach(data => {
            if (data.fileType === 'single') {
                const button = document.createElement("button");
                button.className = "satellite-button bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out";
                button.innerText = data.fileName;
                button.setAttribute('data-id', data.fileName);
                button.setAttribute('data-type', data.fileType);
                button.onclick = () => {
                    selectOutputItem(data.fileName, data.fileType);
                };
                singleSatButtonsContainer.appendChild(button);
            }
        });

        // Add buttons for individual active satellites that are NOT part of a saved constellation
        if (window.activeSatellites) {
            window.activeSatellites.forEach((satelliteObj, satId) => {
                // Only add a button if this active satellite is not already a member of a listed constellation
                if (!constellationMemberIds.has(satId) && !fileOutputs.has(satId)) { // Also check if it's not a top-level fileOutput already
                    const btn = document.createElement('button');
                    btn.className = 'satellite-button bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out';
                    btn.innerText = satelliteObj.name;
                    btn.dataset.id = satId;
                    btn.dataset.type = 'single'; // treat as a 'single'
                    btn.onclick = () => selectOutputItem(satId, 'single');
                    singleSatButtonsContainer.appendChild(btn);
                }
            });
        }


        // Add buttons for constellations
        fileOutputs.forEach(data => {
            if (data.fileType === 'constellation') {
                const constellationGroup = document.createElement("div");
                constellationGroup.className = "constellation-group flex flex-col items-start w-full";

                const mainButton = document.createElement("button");
                mainButton.className = "satellite-button constellation-toggle bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out w-full text-left flex justify-between items-center";
                mainButton.innerText = data.fileName;
                mainButton.setAttribute('data-id', data.fileName);
                mainButton.setAttribute('data-type', data.fileType);
                mainButton.innerHTML += `<span class="toggle-icon">▼</span>`; // Add a toggle icon

                mainButton.onclick = () => {
                    // Clicking the main button expands/collapses the list and selects the constellation
                    selectOutputItem(data.fileName, data.fileType);
                    const toggleIcon = mainButton.querySelector('.toggle-icon');
                    if (toggleIcon) {
                        if (memberList.classList.contains('hidden')) {
                            toggleIcon.innerText = '▲';
                        } else {
                            toggleIcon.innerText = '▼';
                        }
                    }
                };
                constellationGroup.appendChild(mainButton);

                const memberList = document.createElement("div");
                memberList.id = `constellation-${data.fileName}-members`;
                memberList.className = "constellation-members-list hidden pl-4 pt-2 flex flex-wrap gap-2 w-full"; // Initially hidden

                // Add individual satellite buttons within the constellation
                if (data.satellites && window.activeSatellites) {
                    data.satellites.forEach(satId => {
                        const satelliteObj = window.activeSatellites.get(satId);
                        if (satelliteObj) {
                            const subButton = document.createElement("button");
                            subButton.className = "satellite-button bg-purple-500 hover:bg-purple-600 text-white font-semibold py-1 px-3 rounded-lg shadow-sm text-sm transition duration-200 ease-in-out";
                            subButton.innerText = satelliteObj.name;
                            subButton.setAttribute('data-id', satId);
                            subButton.setAttribute('data-type', 'single'); // Treat as single for selection/display
                            subButton.onclick = (event) => {
                                event.stopPropagation(); // Prevent main constellation button click
                                selectOutputItem(satId, 'single');
                            };
                            memberList.appendChild(subButton);
                        }
                    });
                }
                constellationGroup.appendChild(memberList);
                constellationButtonsContainer.appendChild(constellationGroup);
            }
        });


        // Add buttons for all saved ground stations
        groundStations.forEach(data => {
            const button = document.createElement("button");
            button.className = "satellite-button bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out";
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
            button.className = "satellite-button bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out";
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
        window.selectedItemType = null;
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none';
        if (window.updateOutputSidebar) window.updateOutputSidebar(null);
        return;
    }

    // Re-select and highlight the previously selected item on UI refresh
    let itemToSelectId = null;
    let itemToSelectType = null;

    // Prioritize previously selected item if it still exists
    if (window.selectedSatelliteId) {
        if (fileOutputs.has(window.selectedSatelliteId)) {
            itemToSelectId = window.selectedSatelliteId;
            itemToSelectType = fileOutputs.get(itemToSelectId).fileType;
        } else if (groundStations.has(window.selectedSatelliteId)) {
            itemToSelectId = window.selectedSatelliteId;
            itemToSelectType = groundStations.get(itemToSelectId).fileType;
        } else if (linkBudgetAnalyses.has(window.selectedSatelliteId)) {
            itemToSelectId = window.selectedSatelliteId;
            itemToSelectType = linkBudgetAnalyses.get(itemToSelectId).fileType;
        } else if (window.activeSatellites && window.activeSatellites.has(window.selectedSatelliteId)) {
            // Check if a dynamically created active satellite was selected
            itemToSelectId = window.selectedSatelliteId;
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
        } else if (window.activeSatellites && window.activeSatellites.size > 0) {
            // Default to the first active satellite if no file outputs
            const firstActiveSat = window.activeSatellites.keys().next().value;
            itemToSelectId = firstActiveSat;
            itemToSelectType = 'single';
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
        window.selectedItemType = null;
        if (satelliteDataDisplay) satelliteDataDisplay.style.display = 'none';
        if (window.updateOutputSidebar) window.updateOutputSidebar(null);
    }
}

// Expose functions to the global window object for accessibility from HTML
window.highlightOutputButton = highlightOutputButton;
window.updateSatelliteDataDisplay = updateSatelliteDataDisplay;
window.selectOutputItem = selectOutputItem;
window.selectSatellite = selectSatellite;
window.selectGroundStation = selectGroundStation;
window.updateSatelliteListUI = updateSatelliteListUI; // Make this available globally as it's a main entry point for UI refresh
