
<script>
    
// --- EDIT MENU FUNCTIONS (triggered by double-click on resource items) ---
    window.editSingleParameter = editSingleParameter;
    window.editConstellationParameter = editConstellationParameter;
    window.editGroundStation = editGroundStation;
    window.editLinkBudget = editLinkBudget; // Expose the new function

    function editSingleParameter(fileName) {
        const dataToEdit = fileOutputs.get(fileName);
        if (!dataToEdit) { showCustomAlert("Data file not found."); return; }

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
        `;

        showModal("Edit Single Satellite", modalBody, () => {
            let hasError = false;
            const currentFileName = document.getElementById('fileNameInput').value;
            const eccentricityType = document.querySelector('input[name="eccentricityType"]:checked').value;
            let eccentricity = 0;
            if (eccentricityType === 'elliptical') {
                const eccValue = document.getElementById('eccentricityValueInput').value;
                eccentricity = parseFloat(formatNumberInput(eccValue));
            }

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

            // Calculate UTC timestamp with selected offset
            const utcOffset = parseInt(document.getElementById('utcOffsetInput').value);
            window.utcOffset = utcOffset; // Add this line
            const [datePart, timePart] = epochInput.split('T');
            const [year, month, day] = datePart.split('-').map(Number);
            const [hour, minute] = timePart.split(':').map(Number);
            const utcHour = hour - utcOffset;
            const utcDate = new Date(Date.UTC(year, month - 1, day, utcHour, minute, 0));
            const utcTimestamp = utcDate.getTime(); // Store UTC timestamp for later use

            const updatedData = {
                fileName: currentFileName, altitude: values.altitude, inclination: values.inclination,
                eccentricity: eccentricity, raan: values.raan,
                argumentOfPerigee: eccentricityType === 'elliptical' ? values.argumentOfPerigee : 0,
                trueAnomaly: values.trueAnomaly,
                epoch: epochInput, // Store the input string as entered
                utcTimestamp : utcTimestamp,
                beamwidth: values.beamwidth,
                fileType: 'single'
            };

            const oldData = { ...fileOutputs.get(currentFileName) };
            recordAction({ type: 'editFile', fileName: currentFileName, fileType: 'single', oldData: oldData, newData: updatedData });
            fileOutputs.set(currentFileName, updatedData);
            saveFilesToLocalStorage();

            if (window.addOrUpdateSatelliteInScene) {
                window.addOrUpdateSatelliteInScene(updatedData);
            }

            updateOutputSidebar(updatedData);
            addFileToResourceSidebar(currentFileName, updatedData, 'single');
            updateSatelliteListUI();
            selectSatellite(currentFileName);
            return true;
        }, null, fileName, 'single');

        // Populate modal with existing data
        document.getElementById('fileNameInput').value = dataToEdit.fileName;
        document.getElementById('altitudeInput').value = dataToEdit.altitude;
        document.getElementById('inclinationInput').value = dataToEdit.inclination;
        document.getElementById('raanInput').value = dataToEdit.raan;
        document.getElementById('trueAnomalyInput').value = dataToEdit.trueAnomaly;
        document.getElementById('beamwidthInput').value = dataToEdit.beamwidth;

        // For epoch: convert stored UTC epoch to local time
        const storedEpoch = dataToEdit.epoch;
        const utcDate = new Date(storedEpoch + 'Z');
        const localYear = utcDate.getFullYear();
        const localMonth = String(utcDate.getMonth() + 1).padStart(2, '0');
        const localDay = String(utcDate.getDate()).padStart(2, '0');
        const localHour = String(utcDate.getHours()).padStart(2, '0');
        const localMinute = String(utcDate.getMinutes()).padStart(2, '0');
        const localEpochString = `${localYear}-${localMonth}-${localDay}T${localHour}:${localMinute}`;
        document.getElementById('epochInput').value = localEpochString;

        // Set UTC offset to local offset
        const localOffsetMinutes = new Date().getTimezoneOffset();
        const localOffsetHours = -localOffsetMinutes / 60;
        document.getElementById('utcOffsetInput').value = localOffsetHours.toString();

        if (dataToEdit.eccentricity == 0) {
            document.getElementById('eccentricityCircular').checked = true;
            toggleEccentricityInput('circular');
        } else {
            document.getElementById('eccentricityElliptical').checked = true;
            document.getElementById('eccentricityValueInput').value = formatNumberInput(dataToEdit.eccentricity);
            toggleEccentricityInput('elliptical');
        }
    }

    function editConstellationParameter(fileName) {
        const dataToEdit = fileOutputs.get(fileName);
        if (!dataToEdit) { showCustomAlert("Constellation data not found."); return; }

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
                <div class="mb-3">
                    <label class="form-label">Direction</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="trainDirection" id="trainDirectionForward" value="forward">
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
                        <input class="form-check-input" type="radio" name="trainStartLocation" id="trainStartLocationSame" value="same">
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
                            <input class="form-check-input" type="radio" name="trainOffsetType" id="trainOffsetTypeMeanAnomaly" value="meanAnomaly">
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

        showModal("Edit Constellation Parameters", modalBody, () => {
            let hasError = false;
            const currentFileName = document.getElementById('fileNameInput').value;
            const eccentricityType = document.querySelector('input[name="eccentricityType"]:checked').value;
            let eccentricity = 0;
            if (eccentricityType === 'elliptical') {
                const eccValue = document.getElementById('eccentricityValueInput').value;
                eccentricity = parseFloat(formatNumberInput(eccValue));
            }

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

            // Calculate UTC timestamp with selected offset
            const utcOffset = parseInt(document.getElementById('utcOffsetInput').value);
            window.utcOffset = utcOffset; // Add this line
            const [datePart, timePart] = epochInput.split('T');
            const [year, month, day] = datePart.split('-').map(Number);
            const [hour, minute] = timePart.split(':').map(Number);
            const utcHour = hour - utcOffset;
            const utcDate = new Date(Date.UTC(year, month - 1, day, utcHour, minute, 0));
            const utcTimestamp = utcDate.getTime();
            
            const updatedData = {
                fileName: currentFileName, altitude: values.altitude, inclination: values.inclination,
                eccentricity: eccentricity, raan: values.raan,
                argumentOfPerigee: eccentricityType === 'elliptical' ? values.argumentOfPerigee : 0,
                trueAnomaly: values.trueAnomaly, 
                epoch: epochInput,
                utcTimestamp : utcTimestamp,
                beamwidth: values.beamwidth,
                fileType: 'constellation',
                ...constellationData
            };

            const oldData = { ...fileOutputs.get(currentFileName) };
            recordAction({ type: 'editFile', fileName: currentFileName, fileType: 'constellation', oldData: oldData, newData: updatedData });
            fileOutputs.set(currentFileName, updatedData);
            saveFilesToLocalStorage();

            if (window.viewSimulation) {
                window.viewSimulation(updatedData);
                setActiveControlButton('startButton');
            }

            updateOutputSidebar(updatedData);
            addFileToResourceSidebar(currentFileName, updatedData, 'constellation');
            updateSatelliteListUI();
            return true;
        }, () => { // Reset function
            document.getElementById('fileNameInput').value = dataToEdit.fileName;
            document.getElementById('altitudeInput').value = dataToEdit.altitude;
            document.getElementById('inclinationInput').value = dataToEdit.inclination;

            if (dataToEdit.eccentricity == 0) {
                document.getElementById('eccentricityCircular').checked = true;
                toggleEccentricityInput('circular');
            } else {
                document.getElementById('eccentricityElliptical').checked = true;
                document.getElementById('eccentricityValueInput').value = formatNumberInput(dataToEdit.eccentricity);
                toggleEccentricityInput('elliptical');
            }
            document.getElementById('raanInput').value = dataToEdit.raan;
            document.getElementById('argumentOfPerigeeInput').value = dataToEdit.argumentOfPerigee || '';
            document.getElementById('trueAnomalyInput').value = dataToEdit.trueAnomaly;

            // For epoch: convert stored UTC epoch to local time
            const storedEpoch = dataToEdit.epoch;
            const utcDate = new Date(storedEpoch + 'Z');
            const localYear = utcDate.getFullYear();
            const localMonth = String(utcDate.getMonth() + 1).padStart(2, '0');
            const localDay = String(utcDate.getDate()).padStart(2, '0');
            const localHour = String(utcDate.getHours()).padStart(2, '0');
            const localMinute = String(utcDate.getMinutes()).padStart(2, '0');
            const localEpochString = `${localYear}-${localMonth}-${localDay}T${localHour}:${localMinute}`;
            document.getElementById('epochInput').value = localEpochString;

            // Set UTC offset to local offset
            const localOffsetMinutes = new Date().getTimezoneOffset();
            const localOffsetHours = -localOffsetMinutes / 60;
            document.getElementById('utcOffsetInput').value = localOffsetHours.toString();

            document.getElementById('beamwidthInput').value = dataToEdit.beamwidth;

            if (dataToEdit.constellationType === 'walker') {
                document.getElementById('constellationTypeWalker').checked = true;
                toggleConstellationType('walker');
            } else {
                document.getElementById('constellationTypeTrain').checked = true;
                toggleConstellationType('train');
            }

            document.getElementById('numSatellitesInput').value = dataToEdit.numSatellites || '';
            if (dataToEdit.separationType === 'time') {
                document.getElementById('separationTypeTime').checked = true;
            } else {
                document.getElementById('separationTypeMeanAnomaly').checked = true;
            }
            document.getElementById('separationValueInput').value = dataToEdit.separationValue || '';
            if (dataToEdit.trainDirection === 'backward') {
                document.getElementById('trainDirectionBackward').checked = true;
            } else {
                document.getElementById('trainDirectionForward').checked = true;
            }
            if (dataToEdit.trainStartLocation === 'offset') {
                document.getElementById('trainStartLocationOffset').checked = true;
                toggleTrainOffset(true);
                if (dataToEdit.trainOffsetType === 'trueAnomaly') {
                    document.getElementById('trainOffsetTypeTrueAnomaly').checked = true;
                } else if (dataToEdit.trainOffsetType === 'time') {
                    document.getElementById('trainOffsetTypeTime').checked = true;
                } else {
                    document.getElementById('trainOffsetTypeMeanAnomaly').checked = true;
                }
                document.getElementById('trainOffsetValue').value = dataToEdit.trainOffsetValue || '';
            } else {
                document.getElementById('trainStartLocationSame').checked = true;
                toggleTrainOffset(false);
            }

            document.getElementById('numPlanesInput').value = dataToEdit.numPlanes || '';
            document.getElementById('satellitesPerPlaneInput').value = dataToEdit.satellitesPerPlane || '';
            document.getElementById('raanSpreadInput').value = dataToEdit.raanSpread || '';
            document.getElementById('phasingFactorInput').value = dataToEdit.phasingFactor || '';

            const inputs = document.querySelectorAll('#fileModalBody input');
            inputs.forEach(input => clearInputError(input.id));
        }, fileName, 'constellation');

        // Event listeners for the modal's radio buttons and inputs
        // These need to be re-attached every time the modal is shown because the content is re-rendered
        const setupConstellationEventListeners = () => {
            document.getElementById('eccentricityCircular').addEventListener('change', () => toggleEccentricityInput('circular'));
            document.getElementById('eccentricityElliptical').addEventListener('change', () => toggleEccentricityInput('elliptical'));
            document.getElementById('constellationTypeTrain').addEventListener('change', () => toggleConstellationType('train'));
            document.getElementById('constellationTypeWalker').addEventListener('change', () => toggleConstellationType('walker'));
            document.getElementById('trainStartLocationSame').addEventListener('change', () => toggleTrainOffset(false));
            document.getElementById('trainStartLocationOffset').addEventListener('change', () => toggleTrainOffset(true));
        };

        // Call setup after showModal
        // This is a common pattern when modal content is dynamically injected
        const modalElement = document.getElementById('fileModal');
        modalElement.addEventListener('shown.bs.modal', setupConstellationEventListeners, { once: true }); // Use { once: true } to prevent multiple listeners

        // Populate modal with existing data
        document.getElementById('fileNameInput').value = dataToEdit.fileName;
        document.getElementById('altitudeInput').value = dataToEdit.altitude;
        document.getElementById('inclinationInput').value = dataToEdit.inclination;
        document.getElementById('raanInput').value = dataToEdit.raan;
        document.getElementById('trueAnomalyInput').value = dataToEdit.trueAnomaly;
        document.getElementById('beamwidthInput').value = dataToEdit.beamwidth;

        // For epoch: convert stored UTC epoch to local time
        const storedEpoch = dataToEdit.epoch;
        const utcDate = new Date(storedEpoch + 'Z');
        const localYear = utcDate.getFullYear();
        const localMonth = String(utcDate.getMonth() + 1).padStart(2, '0');
        const localDay = String(utcDate.getDate()).padStart(2, '0');
        const localHour = String(utcDate.getHours()).padStart(2, '0');
        const localMinute = String(utcDate.getMinutes()).padStart(2, '0');
        const localEpochString = `${localYear}-${localMonth}-${localDay}T${localHour}:${localMinute}`;
        document.getElementById('epochInput').value = localEpochString;

        // Set UTC offset to local offset
        const localOffsetMinutes = new Date().getTimezoneOffset();
        const localOffsetHours = -localOffsetMinutes / 60;
        document.getElementById('utcOffsetInput').value = localOffsetHours.toString();

        if (dataToEdit.eccentricity == 0) {
            document.getElementById('eccentricityCircular').checked = true;
            toggleEccentricityInput('circular');
        } else {
            document.getElementById('eccentricityElliptical').checked = true;
            document.getElementById('eccentricityValueInput').value = formatNumberInput(dataToEdit.eccentricity);
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
            if (dataToEdit.trainDirection === 'backward') {
                document.getElementById('trainDirectionBackward').checked = true;
            } else {
                document.getElementById('trainDirectionForward').checked = true;
            }
            if (dataToEdit.trainStartLocation === 'offset') {
                document.getElementById('trainStartLocationOffset').checked = true;
                toggleTrainOffset(true);
                if (dataToEdit.trainOffsetType === 'trueAnomaly') {
                    document.getElementById('trainOffsetTypeTrueAnomaly').checked = true;
                } else if (dataToEdit.trainOffsetType === 'time') {
                    document.getElementById('trainOffsetTypeTime').checked = true;
                } else {
                    document.getElementById('trainOffsetTypeMeanAnomaly').checked = true;
                }
                document.getElementById('trainOffsetValue').value = dataToEdit.trainOffsetValue;
            } else {
                document.getElementById('trainStartLocationSame').checked = true;
                toggleTrainOffset(false);
            }
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
            if (window.addOrUpdateGroundStationInScene) {
                window.addOrUpdateGroundStationInScene(updatedData);
            }
            updateOutputSidebar(updatedData);
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
        const dataToEdit = linkBudgetAnalyses.get(name);
        if (!dataToEdit) { showCustomAlert("Link Budget Analysis data not found."); return; }

        // Re-use the NewLinkBudgetMenu modal body and logic
        NewLinkBudgetMenu(); // This will open the modal with the generic input fields

        // Populate the modal fields with existing data
        document.getElementById('lbNameInput').value = dataToEdit.name;
        document.getElementById('lbNameInput').readOnly = true; // Name should be read-only when editing
        document.getElementById('transmitPowerInput').value = dataToEdit.transmitPower;
        document.getElementById('txAntennaGainInput').value = dataToEdit.txAntennaGain;
        document.getElementById('rxAntennaGainInput').value = dataToEdit.rxAntennaGain;
        document.getElementById('frequencyInput').value = dataToEdit.frequency;
        document.getElementById('distanceInput').value = dataToEdit.distance;
        document.getElementById('bandwidthInput').value = dataToEdit.bandwidth;
        document.getElementById('noiseFigureInput').value = dataToEdit.noiseFigure;
        document.getElementById('atmosphericLossInput').value = dataToEdit.atmosphericLoss;
        document.getElementById('orbitHeightInput').value = dataToEdit.orbitHeight;
        document.getElementById('elevationAngleInput').value = dataToEdit.elevationAngle;
        document.getElementById('targetAreaInput').value = dataToEdit.targetArea;
        document.getElementById('minimumSNRInput').value = dataToEdit.minimumSNR;
        document.getElementById('orbitInclinationInput').value = dataToEdit.orbitInclination;
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

// Placeholder for deleteFile function if not defined elsewhere
if (typeof window.deleteFile === 'undefined') {
    window.deleteFile = function(fileName, fileType) {
        showCustomConfirmation(`Are you sure you want to delete ${fileName}?`, 'Confirm Delete', 'Delete', () => {
            if (fileType === 'single' || fileType === 'constellation') {
                const dataToDelete = fileOutputs.get(fileName);
                if (dataToDelete && dataToDelete.satellites) {
                    // If it's a constellation, remove all its satellites from activeSatellites
                    dataToDelete.satellites.forEach(satId => {
                        window.removeObjectFromScene(satId, 'satellite');
                    });
                } else if (fileType === 'single') {
                    // If it's a single satellite, remove it from activeSatellites
                    window.removeObjectFromScene(fileName, 'satellite');
                }
                fileOutputs.delete(fileName);
            } else if (fileType === 'groundStation') {
                window.removeObjectFromScene(fileName, 'groundStation');
                groundStations.delete(fileName);
            } else if (fileType === 'linkBudget') {
                linkBudgetAnalyses.delete(fileName);
            }
            saveFilesToLocalStorage();
            updateSatelliteListUI(); // Refresh UI after deletion
            // If the deleted item was selected, clear the output sidebar
            if (window.selectedItemType === fileType && (window.selectedSatelliteId === fileName || window.selectedGroundStationId === fileName)) {
                window.selectedSatelliteId = null;
                window.selectedGroundStationId = null;
                window.selectedItemType = null;
                updateOutputSidebar(null);
            }
        }, true);
    };
}
