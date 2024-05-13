// Get the modal
var modal = document.getElementById("myModal");

// Get the user-circle element
var userCircle = document.querySelector(".profile");

function closeModal() {
  modal.style.display = "none";
}

// When the user clicks on the user-circle, open the modal
userCircle.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal ) {
    closeModal();
  }
}

