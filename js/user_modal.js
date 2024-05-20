// Get the modals
var settingsModal = document.getElementById("settingsModal");
var userInfoModal = document.getElementById("myModal");
var editFileModal = document.getElementById("editFileModal");

// Get the open settings button
const openSettings = document.getElementById("openSettings");

// When the user clicks on the cog icon, toggle the settings modal
openSettings.addEventListener("click", function () {
    if (settingsModal.style.display === "block") {
        settingsModal.style.display = "none";
    } else {
        settingsModal.style.display = "block";
    }
});

// Get the user-circle element
var userCircle = document.querySelector(".profile");

// Function to close the edit file modal
function closeEditFileModal() {
    editFileModal.style.display = 'none';
}

// Function to close the settings modal
function closeSettingsModal() {
    settingsModal.style.display = 'none';
}

// Function to close both modals
function closeModal() {
    settingsModal.style.display = 'none';
    userInfoModal.style.display = 'none';
    closeEditFileModal();
}



// When the user clicks on the user-circle, open the user info modal
userCircle.onclick = function() {
    userInfoModal.style.display = "block";
    // Ensure the settings modal is closed when opening user info modal
    closeSettingsModal();
    closeEditFileModal();
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    if (event.target == settingsModal || event.target == userInfoModal || event.target == editFileModal) {
        closeModal();
    }
}
