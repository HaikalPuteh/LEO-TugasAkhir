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