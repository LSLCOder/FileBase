function refreshTable() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                document.getElementById('file-table-body').innerHTML = xhr.responseText;
            } else {
                console.error('Failed to fetch updated file data');
            }
        }
    };
    xhr.open('GET', 'php/fetch_files.php', true);
    xhr.send();
}


function refreshHistoryTable() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                document.getElementById('history-table-body').innerHTML = xhr.responseText;
            } else {
                alert('Failed to refresh history table: ' + xhr.status);
            }
        }
    };
    xhr.open('GET', 'php/fetch_history.php', true);
    xhr.send();
}



//PREVIEW
function previewFile(fileId) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        var fileContent = response.fileContent;
                        var fileType = response.fileType;
                        var fileName = response.fileName;

                        var previewWindow = window.open("", "_blank");
                        previewWindow.document.title = fileName;

                        // FOR IMAGES
                        if (fileType === 'png' || fileType === 'jpg' || fileType === 'gif') {
                            var previewImg = document.createElement('img');
                            previewImg.src = 'data:' + fileType + ';base64,' + fileContent;
                            previewImg.style.maxWidth = '80%'; 
                            previewImg.style.maxHeight = '80%'; 
                            previewImg.style.display = 'block'; 
                            previewImg.style.margin = 'auto'; 
                            previewImg.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 1)';
                            var blurredBackground = document.createElement('div');
                            blurredBackground.style.position = 'fixed';
                            blurredBackground.style.top = '0';
                            blurredBackground.style.left = '0';
                            blurredBackground.style.width = '100%';
                            blurredBackground.style.height = '100%';
                            blurredBackground.style.backgroundImage = 'url(data:' + fileType + ';base64,' + fileContent + ')';
                            blurredBackground.style.backgroundSize = 'cover';
                            blurredBackground.style.filter = 'blur(10px)';
                            blurredBackground.style.zIndex = '-1';
                            previewWindow.document.body.appendChild(blurredBackground);
                            previewWindow.document.body.style.backgroundColor = 'transparent';
                            previewWindow.document.body.style.display = 'flex';
                            previewWindow.document.body.style.alignItems = 'center';
                            previewWindow.document.body.style.justifyContent = 'center';
                            previewWindow.document.body.appendChild(previewImg);
                        }
                        // FOR VIDEO
                        else if (fileType === 'mp4') {
                            var videoTag = '<video controls style="width:75%;height:auto;transform: scale(0.75);transform-origin: center;margin: auto;display: block;"><source src="data:' + fileType + ';base64,' + fileContent + '" type="video/mp4"></video>';
                            previewWindow.document.body.style.backgroundColor = 'black';
                            previewWindow.document.body.innerHTML = videoTag;

                        } 
                        // FOR MUSIC
                        else if (fileType === 'mp3') {
                            var audioTag = '<audio controls style="width:70%;display:block;margin:auto;"><source src="data:' + fileType + ';base64,' + fileContent + '" type="audio/mpeg"></audio>';
                            previewWindow.document.body.style.backgroundColor = 'black';
                            previewWindow.document.body.style.display = 'flex'; 
                            previewWindow.document.body.style.alignItems = 'center'; 
                            previewWindow.document.body.style.justifyContent = 'center'; 
                            previewWindow.document.body.innerHTML = audioTag;
                        
                        }
                        // FOR PDF
                        else if (fileType === 'pdf') {
                            var pdfSrc = 'data:application/pdf;base64,' + fileContent;
                            var pdfTag = '<embed src="' + pdfSrc + '" type="application/pdf" style="width:100%;height:100%;" />';
                            previewWindow.document.body.innerHTML = pdfTag;
                        }
                        // FOR DOCX
                        else if (fileType === 'docx') {
                            var docxSrc = 'data:application/vnd.openxmlformats-officedocument.wordprocessingml.document;base64,' + fileContent;
                            
                            // Fetch the DOCX file as a Blob
                            fetch(docxSrc)
                                .then(response => response.blob())
                                .then(blob => {
                                    var reader = new FileReader();
                                    reader.onload = function(event) {
                                        var arrayBuffer = reader.result;
                    
                                        // Use Mammoth to convert the arrayBuffer to HTML
                                        mammoth.convertToHtml({ arrayBuffer: arrayBuffer })
                                            .then(function(result) {
                                                var html = result.value; // The generated HTML
                                                previewWindow.document.body.innerHTML = html;
                                            })
                                            .catch(function(error) {
                                                console.error('Error converting DOCX file:', error);
                                            });
                                    };
                                    reader.readAsArrayBuffer(blob);
                                })
                                .catch(error => {
                                    console.error('Error fetching DOCX file:', error);
                                });
                        }
                        //OTHER FILE
                        else {
                            var downloadLink = document.createElement('a');
                            downloadLink.href = 'data:' + fileType + ';base64,' + fileContent;
                            downloadLink.download = fileName;
                            downloadLink.textContent = 'Download ' + fileName;
                            previewWindow.document.body.appendChild(downloadLink);
                        }
                    } else {
                        alert(response.message);
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    console.error('Response text:', xhr.responseText);
                }
            } else {
                alert('Failed to fetch file data: ' + xhr.status);
            }
        }
    };
    xhr.open('GET', 'php/preview_file.php?file_id=' + fileId, true);
    xhr.send();
}


// EDIT (RENAME)
var editFileModal = document.getElementById('editFileModal');

function editFile(fileId) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Extract file name and extension
                    var originalFileName = response.fileName;
                    var fileNameWithoutExtension = originalFileName.split('.').slice(0, -1).join('.');
                    var fileExtension = originalFileName.split('.').pop();

                    // Populate the text fields with the file name and extension
                    document.getElementById('file-name').value = fileNameWithoutExtension;
                    document.getElementById('file-id').value = fileId;
                    document.getElementById('file-extension').value = fileExtension;

                    document.getElementById('editFileModal').style.display = 'block';
                } else {
                    alert(response.message);
                }
            } else {
                alert('Failed to fetch file data: ' + xhr.status);
            }
        }
    };
    xhr.open('GET', 'php/edit_file.php?file_id=' + fileId, true);
    xhr.send();
}

// Function to handle form submission for editing file name
document.getElementById('edit-file-form').addEventListener('submit', function(event) {
    event.preventDefault();

    var fileId = document.getElementById('file-id').value;
    var newFileName = document.getElementById('file-name').value;

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert('File name updated successfully');
                    closeEditFileModal();
                    refreshTable();
                } else {
                    alert(response.message);
                }
            } else {
                alert('Failed to update file name: ' + xhr.status);
            }
        }
    };
    xhr.open('POST', 'php/edit_file.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('file_id=' + encodeURIComponent(fileId) + '&new_file_name=' + encodeURIComponent(newFileName));
    xhr.setRequestHeader(window.location.reload());
});


// Function to close the edit file modal
function closeEditFileModal() {
    editFileModal.style.display = 'none';
}


// DELETE
function deleteFile(id) {
    if (confirm('Are you sure you want to delete this file?')) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert(response.message);
                        refreshTable();
                        refreshHistoryTable();
                        updateProgressBar();
                    } else {
                        alert(response.message);
                    }
                } else {
                    alert('Failed to delete file: ' + xhr.status);
                }
            }
        };
        xhr.open('POST', 'php/delete_file.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('file_id=' + id);
        xhr.setRequestHeader(window.location.reload());
    }
}

// DOWNLOAD
function downloadFile(id) {
    window.location.href = 'php/download_file.php?file_id=' + id;
}

