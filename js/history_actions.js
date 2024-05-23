document.querySelector('.clear-button').addEventListener('click', function() {
    if (confirm('Are you sure you want to clear all history? This action cannot be undone.')) {
        fetch('php/clear_history.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ action: 'clear_history' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('All history cleared successfully.');
                refreshHistoryTable();
            } else {
                alert('Failed to clear history.');
            }
        })
        .catch(error => console.error('Error:', error));
    }
});


