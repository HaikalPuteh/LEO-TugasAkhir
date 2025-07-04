 
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

            if (is2DViewActive) { // If switching TO 2D
                if (earthContainer) earthContainer.style.display = 'none';
                if (earth2DContainer) {
                    earth2DContainer.style.display = 'flex'; // Make 2D container visible
                    window.resizeCanvas2D(); // <--- CRUCIAL: Resize 2D canvas immediately after making visible
                }
            } else { // If switching TO 3D
                if (earthContainer) earthContainer.style.display = 'flex'; // Make 3D container visible
                if (earth2DContainer) earth2DContainer.style.display = 'none';
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
            if (window.activeSatellites.size === 0) {
                showCustomAlert("No satellites to view in close-up. Please create an orbit first.");
                return;
            }
            
            const core3D = window.getSimulationCoreObjects();
            const selectedSat = window.activeSatellites.get(window.selectedSatelliteId);
            
            // Capture current camera state before changes for undo/redo
            const prevState = {
                position: core3D.camera.position.clone(),
                rotation: core3D.camera.rotation.clone(),
                target: core3D.controls.target.clone(),
                closeView: window.closeViewEnabled // Capture current closeView state
            };

            // Toggle the global flag in Earth3Dsimulation.js
            window.closeViewEnabled = !window.closeViewEnabled;

            // Update button text in UI
            document.getElementById('closeViewButton').textContent = window.closeViewEnabled ? 'Normal View' : 'Close View';

            // Tell Earth3Dsimulation.js to update its active meshes (sphere vs GLB)
            window.activeSatellites.forEach(sat => sat.setActiveMesh(window.closeViewEnabled));

            // Adjust camera immediately with GSAP animation
            // Temporarily disable OrbitControls to allow GSAP to control the camera smoothly
            core3D.controls.enabled = false;
            
            if (window.closeViewEnabled && selectedSat) {
                const currentSatPos = selectedSat.mesh.position.clone();
                const forwardDir = selectedSat.velocity.length() > 0 ? selectedSat.velocity.clone().normalize() : new THREE.Vector3(0, 0, 1);
                const upDir = currentSatPos.clone().normalize();

                // Define camera offset relative to satellite
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
                        // Re-enable controls only if still in closeView mode after animation
                        // This prevents unexpected control re-enabling if toggleCloseView is called quickly again
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

                // 1) record the sat’s current world-position
                prevSatFollowPos.copy( selectedSat.mesh.position );

                // optional: record the fixed camera→sat vector if you want to preserve the zoom-distance
                followOffset
                .copy( core3D.camera.position )
                .sub( selectedSat.mesh.position );

                // lock zoom so they never tumble back to a global Earth view
                core3D.controls.minDistance = SCENE_EARTH_RADIUS * 0.02;  // ~2% of scene-Earth radius
                core3D.controls.maxDistance = SCENE_EARTH_RADIUS * 0.15;  // ~15% of scene-Earth radius

                // allow the user to tilt from straight up (sky) down to the horizon,
                // but never “under” it (so Earth can’t swing overhead)
                core3D.controls.minPolarAngle = 0;          // straight up
                core3D.controls.maxPolarAngle = Math.PI * 0.6; // about 108°
                


            } else { // Exiting close view, return to normal view
                core3D.controls.object.up.set(0, 1, 0); // Reset camera up direction

                gsap.to(core3D.camera.position, {
                    duration: 1.5,
                    x: 0, y: 0, z: 5, // Default normal view position
                    ease: "power2.inOut",
                    onUpdate: () => core3D.controls.update(),
                    onComplete: () => {
                        // Re-enable controls after animation completes
                        core3D.controls.enabled = true;
                    }
                });
                gsap.to(core3D.controls.target, {
                    duration: 1.5,
                    x: 0, y: 0, z: 0, // Default normal view target
                    ease: "power2.inOut",
                    onUpdate: () => core3D.controls.update()
                });

                core3D.controls.minDistance = 0.001; // Restore default limits
                core3D.controls.maxDistance = 1000;
            }
            // Record action for undo/redo after determining new state
            const newState = {
                position: core3D.camera.position.clone(), // Capture final animated position
                rotation: core3D.camera.rotation.clone(),
                target: core3D.controls.target.clone(),
                closeView: window.closeViewEnabled // Capture new closeView state
            };
            recordAction({ type: 'viewToggle', prevState: prevState, newState: newState });
        }

        // ----------------------------------------- END VIEW MENU FUNCTIONS ---------------------------------------------
