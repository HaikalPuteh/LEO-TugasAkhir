<script>

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
        
        