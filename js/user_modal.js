// Get the modals
var settingsModal = document.getElementById("settingsModal");
var userInfoModal = document.getElementById("myModal");

// Get the open settings button
var openSettings = document.getElementById("openSettings");

// Get the user-circle element
var userCircle = document.querySelector(".profile");

function closeSettingsModal() {
    settingsModal.style.display = "none";
}

function closeModal() {
    userInfoModal.style.display = "none";
}

// When the user clicks on the cog icon, toggle the settings modal
openSettings.onclick = function() {
    if (settingsModal.style.display === "flex") {
        closeSettingsModal();
    } else {
        settingsModal.style.display = "flex";
        // Ensure the user info modal is closed when opening settings modal
        closeModal();
    }
}

// When the user clicks on the user-circle, open the user info modal
userCircle.onclick = function() {
    userInfoModal.style.display = "block";
    // Ensure the settings modal is closed when opening user info modal
    closeSettingsModal();
}

// When the user clicks anywhere outside of the modal content, close it
window.onclick = function(event) {
    if (event.target == settingsModal) {
        closeSettingsModal();
    }
    if (event.target == userInfoModal) {
        closeModal();
    }
}
