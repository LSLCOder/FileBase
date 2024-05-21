
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

                    progressBar.value = totalSizeMB;
                    progressText.textContent = `${totalSizeMB.toFixed(2)}MB/25MB`;
                    if (totalSizeMB > 25) {
                        alert('Total file size exceeds 25MB limit.');
                    }
                } else {
                    console.error(response.message);
                }
            } else {
                console.error('Failed to fetch total size');
            }
        }
    };
    xhr.open('GET', 'php/fetch_total_size.php', true);
    xhr.send();
}

updateProgressBar();
