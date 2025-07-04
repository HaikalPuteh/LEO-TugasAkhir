<script>
  // ------------------------------------- LOAD TLE FUNCTION ------------------------------------------------
  // assumes parseTle is globally available (imported in your 3D code)
  function LoadTLE() {
    // 1) collect inputs
    const nameInput   = document.getElementById('tle-name').value.trim();
    const line1       = document.getElementById('tle-line1').value.trim();
    const line2       = document.getElementById('tle-line2').value.trim();

    if (!line1 || !line2) {
      return showCustomAlert("You must supply both TLE Line 1 and Line 2.");
    }

    // 2) quick length‐check (optional)
    if (line1.length<65 || line2.length<65) {
      showCustomAlert(
        "Warning: TLE lines are usually 69 chars long.  Proceeding anyway…"
      );
    }

    // 3) parse it right now to validate & grab its epoch
    let parsed;
    try {
      parsed = parseTle(line1, line2);
    } catch(err) {
      return showCustomAlert("Invalid TLE: " + err.message);
    }

    // 4) decide on a name
    const satName = nameInput || `TLE_${parsed.satelliteNumber}`;

    // 5) clear out any existing objects
    window.clearSimulationScene();

    // 6) add it back
    window.addOrUpdateSatelliteInScene({
      fileType:  'single',
      id:        satName,
      name:      satName,
      tleLine1:  line1,
      tleLine2:  line2
      // NO need to pass altitude, inclination, etc.
      // SGP4 will drive position from the TLE itself.
    });

    // 7) start animating
    window.isAnimating = true;

    showCustomAlert(`Loaded TLE for “${satName}” (epoch ${new Date(parsed.epochTimestamp).toUTCString()})`);
  }
    // ------------------------------------- END LOAD TLE FUNCTION ------------------------------------------------
