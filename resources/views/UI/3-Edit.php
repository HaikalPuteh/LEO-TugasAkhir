<script>

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