
document.getElementById('file-upload').addEventListener('change', function() {
    var form = document.getElementById('file-upload-form');
    var formData = new FormData(form);

    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();
    // Define a callback function to handle the response
    xhr.onreadystatechange = function() {
        // Check if the request is complete
        if (xhr.readyState === XMLHttpRequest.DONE) {
            // Check if the request was successful
            if (xhr.status === 200) {
                // Parse the JSON response
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Display a success message
                    alert(response.message);
                    // Refresh the table and update the progress bar
                    refreshTable();
                    updateProgressBar();
                } else {
                    // Display an error message
                    alert(response.message);
                }
            } else {
                // Display an error message for upload failure
                alert('Upload failed: ' + xhr.status);
            }
        }
    };

    // Open a POST request to the upload PHP script
    xhr.open('POST', 'php/upload.php', true);
    // Send the FormData
    xhr.send(formData);
});

// Function to refresh the table by fetching updated file data
function refreshTable() {
    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();
    // Define a callback function to handle the response
    xhr.onreadystatechange = function() {
        // Check if the request is complete
        if (xhr.readyState === XMLHttpRequest.DONE) {
            // Check if the request was successful
            if (xhr.status === 200) {
                // Replace the content of the table body with the updated HTML
                document.getElementById('file-table-body').innerHTML = xhr.responseText;
            } else {
                // Log an error message if fetching the data fails
                console.error('Failed to fetch updated file data');
            }
        }
    };
    // Open a GET request to the fetch files PHP script
    xhr.open('GET', 'php/fetch_files.php', true);
    // Send the request
    xhr.send();
}

// Function to update the progress bar
function updateProgressBar() {
    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();
    // Define a callback function to handle the response
    xhr.onreadystatechange = function() {
        // Check if the request is complete
        if (xhr.readyState === XMLHttpRequest.DONE) {
            // Check if the request was successful
            if (xhr.status === 200) {
                // Parse the JSON response
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Get the total size in KB and convert to MB
                    var totalSizeKB = response.totalSizeKB;
                    var totalSizeMB = totalSizeKB / 1024;
                    // Get the progress bar and progress text elements
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
