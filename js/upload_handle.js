
document.getElementById('file-upload').addEventListener('change', function() {
    var form = document.getElementById('file-upload-form');
    var formData = new FormData(form);
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
                alert('Upload failed: ' + xhr.status);
            }
        }
    };

    xhr.open('POST', 'php/upload.php', true);
    xhr.send(formData);
});

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

function updateProgressBar() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    var totalSizeKB = response.totalSizeKB;
                    var totalSizeMB = totalSizeKB / 1024;
                    var progressBar = document.querySelector('.box-info progress');
                    var progressText = document.querySelector('.box-info p');

                    // Update the progress bar value and text
                    progressBar.value = totalSizeMB;
                    progressText.textContent = `${totalSizeMB.toFixed(2)}MB/10MB`;

                    // Display an alert if the total size exceeds 10MB
                    if (totalSizeMB > 10) {
                        alert('Total file size exceeds 10MB limit.');
                    }
                } else {
                    // Log an error message if fetching the total size fails
                    console.error(response.message);
                }
            } else {
                // Log an error message if the request fails
                console.error('Failed to fetch total size');
            }
        }
    };
    // Open a GET request to the fetch total size PHP script
    xhr.open('GET', 'php/fetch_total_size.php', true);
    // Send the request
    xhr.send();
}

// Initial load to update the progress bar
updateProgressBar();
