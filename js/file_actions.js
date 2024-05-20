function refreshTable() {
    // Code to refresh the file table content
    location.reload();
}


//PREVIEW
function previewFile(fileId) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    var fileType = response.fileType;
                    var fileContent = response.fileContent;

                    if (fileType.includes('image')) {
                        // If it's an image, display it in an image tag
                        var previewImg = document.createElement('img');
                        previewImg.src = 'data:' + fileType + ';base64,' + fileContent;
                        previewImg.style.maxWidth = '100%';
                        previewImg.style.maxHeight = '100%';
                        var previewWindow = window.open('', '_blank');
                        previewWindow.document.body.appendChild(previewImg);
                    } else if (fileType === 'video/mp4') {
                        // If it's an mp4 video, embed it in a video tag
                        var videoSrc = 'data:' + fileType + ';base64,' + fileContent;
                        var videoTag = '<video controls><source src="' + videoSrc + '" type="video/mp4"></video>';
                        var previewWindow = window.open('', '_blank');
                        previewWindow.document.body.innerHTML = videoTag;
                    } else if (fileType === 'audio/mp3') {
                        // If it's an mp3 audio, embed it in an audio tag
                        var audioSrc = 'data:' + fileType + ';base64,' + fileContent;
                        var audioTag = '<audio controls><source src="' + audioSrc + '" type="audio/mp3"></audio>';
                        var previewWindow = window.open('', '_blank');
                        previewWindow.document.body.innerHTML = audioTag;
                    } else {
                        // For other file types, display a download link
                        var downloadLink = document.createElement('a');
                        downloadLink.href = 'data:' + fileType + ';base64,' + fileContent;
                        downloadLink.download = 'file.' + fileType.split('/')[1];
                        downloadLink.textContent = 'Download';
                        var previewWindow = window.open('', '_blank');
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
                    // Refresh the file table to reflect the changes
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
    }
}

// DOWNLOAD
function downloadFile(id) {
    window.location.href = 'php/download_file.php?file_id=' + id;
}