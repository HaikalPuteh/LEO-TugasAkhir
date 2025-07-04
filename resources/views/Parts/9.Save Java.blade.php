<script>

// ------------------------------------- SAVE MENU FUNCTIONS ------------------------------------------------
window.showSavePopup            = showSavePopup;
window.generateAndSaveSelected = generateAndSaveSelected;

// Show a popup with checkboxes for every “single” or “constellation” in fileOutputs
function showSavePopup() {
        document.querySelectorAll('.custom-popup').forEach(el=>el.remove());
        const popup = document.createElement('div');
        popup.className = 'custom-popup';
        Object.assign(popup.style, {
            position: 'absolute', left: '50%', top: '50%',
            transform: 'translate(-50%,-50%)',
            background: '#fff', padding: '20px',
            border: '1px solid #ccc', 'zIndex':1000
        });

        let html = `<h5>Select items to save:</h5>
            <ul style="list-style:none;padding:0;">`;

        let hasSingle = false, hasConst = false;
        fileOutputs.forEach(data=>{
            if (data.fileType==='single')       hasSingle      = true;
            if (data.fileType==='constellation') hasConst       = true;
        });
        if (hasSingle) {
            html += `<li><strong>Single Satellites</strong>
            <ul style="padding-left:20px;">`;
            fileOutputs.forEach((data,name)=>{
            if (data.fileType==='single') {
                html += `<li>
                <label>
                    <input type="checkbox" data-type="single" value="${name}">
                    ${name}
                </label>
                </li>`;
            }
            });
            html += `</ul></li>`;
        }
        if (hasConst) {
            html += `<li><strong>Constellations</strong>
            <ul style="padding-left:20px;">`;
            fileOutputs.forEach((data,name)=>{
            if (data.fileType==='constellation') {
                html += `<li>
                <label>
                    <input type="checkbox" data-type="constellation" value="${name}">
                    ${name}
                </label>
                </li>`;
            }
            });
            html += `</ul></li>`;
        }
        html += `</ul>
            <div style="text-align:right;margin-top:1em">
            <button onclick="document.querySelector('.custom-popup').remove()">Close</button>
            <button onclick="generateAndSaveSelected()">Save</button>
            </div>`;

        popup.innerHTML = html;
        document.body.appendChild(popup);
}

// Main save/export routine
function generateAndSaveSelected() {
        const picked = [];
        document.querySelectorAll('.custom-popup input:checked').forEach(ch=>{
            picked.push({ name: ch.value, type: ch.dataset.type });
        });
        if (!picked.length) {
            alert("Select at least one item.");
            return;
        }

        // Build a single text blob: for TLE-equipped sats we dump TLE lines, 
        // others we generate a minimal Two-Line format from their orbital params 
        let txt = '';
        picked.forEach(item=>{
            const data = fileOutputs.get(item.name);
            if (!data) return;

            if (item.type==='single' && data.tleLine1 && data.tleLine2) {
            // real TLE
            txt += data.tleLine1 + '\n' + data.tleLine2 + '\n';
            }
            else if (item.type==='single') {
            // fallback "fake TLE" from elements
            txt += makePseudoTle(item.name, data) + '\n';
            }
            else if (item.type==='constellation' && data.satellites) {
            data.satellites.forEach(satName=>{
                const sat = fileOutputs.get(satName);
                if (sat && sat.tleLine1 && sat.tleLine2) {
                txt += sat.tleLine1 + '\n' + sat.tleLine2 + '\n';
                }
                else if (sat) {
                txt += makePseudoTle(satName, sat) + '\n';
                }
            });
            }
        });

        const blob = new Blob([txt], {type:'text/plain'});
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = 'satellite_orbits.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        document.querySelector('.custom-popup').remove();
        alert("Orbit data saved as satellite_orbits.txt");
}

// Build a very simple, pseudo‐TLE two-line element set from Keplerian inputs.
// You can refine field widths & checksums as needed.
function makePseudoTle(name, d) {
        // Line 0: name
        let L0 = name.padEnd(24).slice(0,24);

        // Line 1 placeholder: NORAD cat=0, epoch YYDDD.DDDDD
        const dt = new Date(d.epoch);
        const year = dt.getUTCFullYear()%100;
        const start = new Date(Date.UTC(dt.getUTCFullYear(),0,1));
        const doy = ((dt - start)/(1000*86400)) + 1;
        const epochStr = year.toString().padStart(2,'0')
            + doy.toFixed(5).padStart(8,'0');

        let line1 = `1 ${'00000'}U 00000   ${epochStr} .00000000  00000-0  00000-0 0  9991`;
        // Line 2: i, Ω, e, ω, M, n
        const i  = (d.inclination||0).toFixed(4).padStart(8,' ');
        const raan = (d.raan||0).toFixed(4).padStart(8,' ');
        const e  = ((d.eccentricity||0)*1e7).toFixed(0).padStart(7,'0');
        const ap = (d.argumentOfPerigee||0).toFixed(4).padStart(8,' ');
        const M  = (d.trueAnomaly||0).toFixed(4).padStart(8,' ');
        // mean motion: n = sqrt(μ/a^3)*86400/(2π)
        const a_km  = (d.altitude || 0) + 6378.137;
        const μ = 398600.4418;
        const n = (Math.sqrt(μ/Math.pow(a_km,3))*86400/(2*Math.PI)).toFixed(8).padStart(11,' ');
        let line2 = `2 00000 ${i} ${raan} ${e} ${ap} ${M} ${n}    00`;

        return L0 + '\n' + line1 + '\n' + line2;
}
// ------------------------------------- END SAVE MENU FUNCTIONS ---------------------------------------------