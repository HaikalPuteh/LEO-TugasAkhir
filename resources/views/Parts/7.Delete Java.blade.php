<script>
    
window.deleteFile = deleteFile;
        // --------------------------------------------- DELETE FUNCTION -------------------------------------------

        function deleteFile(fileName, fileType) {
            showCustomConfirmation(
                `Are you sure you want to delete "${fileName}"?`,
                "Konfirmasi Penghapusan",
                "OK",
                () => {
                    let fileDataToDelete;
                    if (fileType === 'single' || fileType === 'constellation') {
                        fileDataToDelete = { ...fileOutputs.get(fileName) };
                        fileOutputs.delete(fileName);
                        if (window.removeObjectFromScene) window.removeObjectFromScene(fileName, 'satellite');
                    } else if (fileType === 'groundStation') {
                        fileDataToDelete = { ...groundStations.get(fileName) };
                        groundStations.delete(fileName);
                        if (window.removeObjectFromScene) window.removeObjectFromScene(fileName, 'groundStation');
                    } else if (fileType === 'linkBudget') {
                        fileDataToDelete = { ...linkBudgetAnalyses.get(fileName) };
                        linkBudgetAnalyses.delete(fileName);
                        // No 3D object to remove for link budget
                    }

                    recordAction({
                        type: 'deleteFile',
                        fileName: fileName,
                        fileType: fileType,
                        fileData: fileDataToDelete
                    });

                    saveFilesToLocalStorage();

                    const listItem = document.querySelector(`li[data-file-name="${fileName}"][data-file-type="${fileType}"]`);
                    if (listItem) {
                        listItem.remove();
                    }

                    const outputMenu = document.getElementById('output-menu').querySelector('ul');
                    const displayedFileNameElement = outputMenu.querySelector('.output-file-name');
                    if (displayedFileNameElement && displayedFileNameElement.textContent.includes(fileName)) {
                        updateOutputSidebar(null); // Clear displayed data if it was the deleted item
                    }
                    updateSatelliteListUI(); // Re-render satellite list if any changes (e.g., if deleted selected one)
                },
                true // Show cancel button
            );
        }
// --------------------------------------------- END DELETE FUNCTION -------------------------------------------