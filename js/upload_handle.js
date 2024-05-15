function handleFileUpload(event) {
    const file = event.target.files[0];
    if (file) {
        const validTypes = ["application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/pdf", "image/png", "image/jpeg", "audio/mpeg", "video/mp4"];
        if (!validTypes.includes(file.type)) {
            alert('Invalid file type. Please upload a doc, docx, pdf, png, jpg, jpeg, mp3, or mp4 file.');
            return;
        }

        const fileName = file.name;
        const fileSize = (file.size / 1024).toFixed(2) + ' KB';
        const fileType = file.type;
        const uploadDate = new Date().toLocaleDateString();

        const formData = new FormData();
        formData.append('fileName', fileName);
        formData.append('fileSize', file.size);
        formData.append('fileType', fileType);
        formData.append('uploadDate', uploadDate);
        formData.append('file', file);

        $.ajax({
            url: 'php/upload.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                const tableBody = document.getElementById('file-table-body');
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${fileName}</td>
                    <td>${fileSize}</td>
                    <td>${fileType}</td>
                    <td>${uploadDate}</td>
                    <td class="action-buttons">
                        <div class="btn view"><i class="fa fa-eye"></i></div>
                        <div class="btn download"><i class="fa fa-download"></i></div>
                        <div class="btn edit"><i class="fa fa-edit"></i></div>
                        <div class="btn delete"><i class="fa fa-trash"></i></div>
                    </td>
                `;
                tableBody.appendChild(newRow);
                alert('File uploaded successfully!');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Error uploading file.');
            }
        });
    }
}
