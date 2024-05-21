function refreshTable() {
    // Code to refresh the file table content
    location.reload();
}


// PREVIEW
function previewFile(fileId) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    var fileType = response.fileType;
                    var fileName = response.fileName;
                    var fileContent = response.fileContent;

                    var previewWindow = window.open('', '_blank');

                    if (fileType.includes('image')) {
                        // If it's an image, display it in an image tag
                        var previewImg = document.createElement('img');
                        previewImg.src = 'data:' + fileType + ';base64,' + fileContent;
                        previewImg.style.maxWidth = '100%';
                        previewImg.style.maxHeight = '100%';
                        previewWindow.document.body.appendChild(previewImg);
                    } else if (fileType === 'video/mp4') {
                        // If it's an mp4 video, embed it in a video tag
                        var videoSrc = 'data:' + fileType + ';base64,' + fileContent;
                        var videoTag = '<video controls style="width:100%;height:auto;"><source src="' + videoSrc + '" type="video/mp4"></video>';
                        previewWindow.document.body.innerHTML = videoTag;
                    } else if (fileType === 'audio/mp3') {
                        // If it's an mp3 audio, embed it in an audio tag
                        var audioSrc = 'data:' + fileType + ';base64,' + fileContent;
                        var audioTag = '<audio controls style="width:100%;"><source src="' + audioSrc + '" type="audio/mp3"></audio>';
                        previewWindow.document.body.innerHTML = audioTag;
                    } else if (fileType === 'application/pdf') {
                        // If it's a pdf, embed it in an iframe
                        var pdfSrc = 'data:' + fileType + ';base64,' + fileContent;
                        var pdfTag = '<iframe src="' + pdfSrc + '" type="application/pdf" style="width:100%;height:100%;border:none;"></iframe>';
                        previewWindow.document.body.innerHTML = pdfTag;
                    } else if (fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                        // If it's a docx, offer it as a download or view
                        var docxSrc = 'data:' + fileType + ';base64,' + fileContent;
                        var docxTag = '<iframe src="https://docs.google.com/viewerng/viewer?url=' + encodeURIComponent('data:' + fileType + ';base64,' + fileContent) + '&embedded=true" style="width:100%;height:100%;border:none;"></iframe>';
                        previewWindow.document.body.innerHTML = docxTag;
                    } else {
                        // For other file types, display a download link
                        var downloadLink = document.createElement('a');
                        downloadLink.href = 'data:' + fileType + ';base64,' + fileContent;
                        downloadLink.download = fileName;
                        downloadLink.textContent = 'Download ' + fileName;
                        previewWindow.document.body.appendChild(downloadLink);
                    }
                } else {
                    alert(response.message);
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