document.addEventListener("DOMContentLoaded", function() {
    const loginBtns = document.querySelectorAll(".button-27"); 
    const signupBtns = document.querySelectorAll(".button-28");
    const loginPopup = document.querySelector(".Login-form-container");
    const signupPopup = document.querySelector(".Signup-form-container");
    const heroSection = document.getElementById("hero-section");
    const navBar = document.querySelector(".header-landingpage");

    const signupLinkInLogin = loginPopup.querySelector(".links a");
    const loginLinkInSignup = signupPopup.querySelector(".links a");

    function clearInputFields() {
        const inputFields = document.querySelectorAll('input[type="text"], input[type="password"]');
        inputFields.forEach(function(input) {
            input.value = ''; // Set value to empty string
        });
    }

    // Function to open login popup
    function openLoginPopup() {
        clearInputFields(); 
        openPopup(loginPopup);
    }

    // Function to open signup popup
    function openSignupPopup() {
        clearInputFields(); 
        openPopup(signupPopup);
    }

    loginBtns.forEach(function(btn) {
        btn.addEventListener("click", openLoginPopup);
    });

    signupBtns.forEach(function(btn) {
        btn.addEventListener("click", openSignupPopup);
    });

    // Event listener for "Sign up now" link inside login popup
    signupLinkInLogin.addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default link behavior
        closePopup(loginPopup);
        openSignupPopup();
    });

    // Event listener for "Login now" link inside signup popup
    loginLinkInSignup.addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default link behavior
        closePopup(signupPopup);
        openLoginPopup();
    });


    // Function to open login popup
    loginBtns.forEach(function(btn) {
        btn.addEventListener("click", function() {
            openPopup(loginPopup);
        });
    });

    // Function to open signup popup
    signupBtns.forEach(function(btn) {
        btn.addEventListener("click", function() {
            openPopup(signupPopup);
        });
    });

    // Function to close popup
    function closePopup(popup) {
        popup.style.display = "none";
        heroSection.classList.remove("blurred");
        navBar.classList.remove("blurred");
        document.body.style.overflow = ""; 
    }

    // Function to open popup
    function openPopup(popup) {
        popup.style.display = "block";
        heroSection.classList.add("blurred");
        navBar.classList.add("blurred");
        document.body.style.overflow = "hidden";
        closeNavbar();
        // window.scrollTo({
        //     top: 0,
        //     behavior: "smooth"
        // });
    }

    // Event listener for "Sign up now" link inside login popup
    signupLinkInLogin.addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default link behavior
        closePopup(loginPopup);
        openPopup(signupPopup);
    });

    // Event listener for "Login now" link inside signup popup
    loginLinkInSignup.addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default link behavior
        closePopup(signupPopup);
        openPopup(loginPopup);
    });

    // Function to check if click is inside popup
    function isInsidePopup(target, popup) {
        return popup.contains(target);
    }

    // Event listener to close popups when clicking outside
    window.addEventListener('click', function(event) {
        const isClickInsideLoginPopup = isInsidePopup(event.target, loginPopup);
        const isClickInsideSignupPopup = isInsidePopup(event.target, signupPopup);
        if (!isClickInsideLoginPopup && !isClickInsideSignupPopup && !event.target.classList.contains("button-27") && !event.target.classList.contains("button-28")) {
            closePopup(loginPopup);
            closePopup(signupPopup);
        }
    });
});
