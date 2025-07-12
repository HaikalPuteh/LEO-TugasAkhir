<script>


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
  const step    = parseInt(popup.querySelector('#saveStep').value,10)*1000;
  const fmt     = popup.querySelector('input[name=saveFormat]:checked').value;
  const fileExt = popup.querySelector('#saveFileExt')?.value || 'csv';

  if (!satId || isNaN(endTs) || endTs < startTs) {
    return alert('Please pick a valid end time (≥ start time).');
  }
  const sat = window.activeSatellites.get(satId);
  if (!sat) return alert('Satellite not found.');

  // ----- TLE export -----
  if (fmt === 'tle') {
    let txt = '';
    if (sat.tleLine1 && sat.tleLine2) {
      txt = sat.tleLine1 + "\n" + sat.tleLine2 + "\n";
    } else {
      // fallback pseudo‐TLE (ensure you have makePseudoTle in scope)
      txt = makePseudoTle(sat.name, {
        epoch:             sat.initialEpochUTC,
        inclination:       sat.params.inclinationRad*(180/Math.PI),
        raan:              sat.currentRAAN*(180/Math.PI),
        eccentricity:      sat.params.eccentricity,
        argumentOfPerigee: sat.params.argPerigeeRad*(180/Math.PI),
        trueAnomaly:       sat.currentTrueAnomaly*(180/Math.PI),
        altitude:          (sat.mesh.position.length()*EarthRadius/SCENE_EARTH_RADIUS) - EarthRadius
      }) + "\n";
    }
    downloadText(`sat_${sat.name}.tle`, txt);
    popup.remove();
    return;
  }

  // ----- Coordinates export -----
  const lines = [];
  const altKm = (sat.mesh.position.length()*EarthRadius/SCENE_EARTH_RADIUS) - EarthRadius;
  const { orbitalPeriod } = calculateDerivedOrbitalParameters(
    (sat.params.semiMajorAxis - SCENE_EARTH_RADIUS)*(EarthRadius/SCENE_EARTH_RADIUS),
    sat.params.eccentricity
  );

  if (fileExt === 'txt') {
    // tab-delimited header
    lines.push(`Satellite Name:\t${sat.name}`);
    lines.push(`Start Time:\t${new Date(startTs).toISOString()}`);
    lines.push(`Stop Time:\t${new Date(endTs).toISOString()}`);
    lines.push(`UTC Offset:\t0`);
    lines.push(`Altitude (km):\t${altKm.toFixed(3)}`);
    lines.push(`Inclination (°):\t${(sat.params.inclinationRad*180/Math.PI).toFixed(3)}`);
    lines.push(`Orbital Period (min):\t${(orbitalPeriod/60).toFixed(3)}`);
    lines.push(`Orbit Type:\t${sat.params.eccentricity<1e-3?'Circular':'Elliptical'}`);
    lines.push('');
    lines.push(`Longitude\tLatitude\t\tTime (UTC)\tElapsed(s)`);
  } else {
    // comma-separated CSV
    lines.push(`Satellite Name:,${sat.name}`);
    lines.push(`Start Time:,${new Date(startTs).toISOString()}`);
    lines.push(`Stop Time:,${new Date(endTs).toISOString()}`);
    lines.push(`UTC Offset:,0`);
    lines.push(`Altitude (km):,${altKm.toFixed(3)}`);
    lines.push(`Inclination (°):,${(sat.params.inclinationRad*180/Math.PI).toFixed(3)}`);
    lines.push(`Orbital Period (min):,${(orbitalPeriod/60).toFixed(3)}`);
    lines.push(`Orbit Type:,${sat.params.eccentricity<1e-3?'Circular':'Elliptical'}`);
    lines.push('');
    lines.push(`Longitude,Latitude,Time (UTC),Elapsed(s)`);
  }

  // step through the simulation
  const core     = window.getSimulationCoreObjects();
  const oldTime  = core.totalSimulatedTime;
  const oldEpoch = core.currentEpochUTC;

  for (let t = startTs; t <= endTs; t += step) {
    const simSec = (t - sat.initialEpochUTC)/1000;
    core.setTotalSimulatedTime(simSec);
    core.setCurrentEpochUTC(sat.initialEpochUTC);
    sat.updatePosition(simSec, 0);

    const { latitudeDeg: lat, longitudeDeg: lon } = sat;
    const elapsed = Math.round((t - startTs)/1000);

    if (fileExt === 'txt') {
      lines.push(`${lon.toFixed(6)}\t${lat.toFixed(6)}\t${new Date(t).toISOString()}\t${elapsed}`);
    } else {
      lines.push(`${lon.toFixed(6)},${lat.toFixed(6)},${new Date(t).toISOString()},${elapsed}`);
    }
  }

  // restore simulation state
  core.setTotalSimulatedTime(oldTime);
  core.setCurrentEpochUTC(oldEpoch);
  sat.updatePosition(oldTime, 0);

  // download
  const ext = fmt==='coordinates' ? `.${fileExt}` : '.tle';
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