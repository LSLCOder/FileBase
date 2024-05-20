document.addEventListener("DOMContentLoaded", function () {
    const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');
    const sections = document.querySelectorAll('main > div');

    allSideMenu.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const sectionId = item.getAttribute('data-section');
            console.log(`Clicked section: ${sectionId}`);  // Debugging step
            allSideMenu.forEach(i => {
                i.parentElement.classList.remove('active');
            });
            item.parentElement.classList.add('active');
            showSection(sectionId);
        });
    });

    function showSection(sectionId) {
        console.log(`Showing section: ${sectionId}`);  // Debugging step
        sections.forEach(section => {
            if (section.id === sectionId) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    }

    const menuBar = document.querySelector('#content nav .bx.bx-menu');
    const sidebar = document.getElementById('sidebar');

    menuBar.addEventListener('click', function () {
        sidebar.classList.toggle('hide');
    });

    const searchForm = document.querySelector('.unique-search-form'); // Use unique class for the search form
    const searchInput = document.getElementById('search-input');
    const tableRows = document.querySelectorAll("#file-table-body tr");

    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const searchTerm = searchInput.value.trim().toLowerCase();
    
        tableRows.forEach(function (row) {
            const fileName = row.querySelector("td:first-child").textContent.trim().toLowerCase();
            if (fileName.startsWith(searchTerm)) {
                row.style.display = ""; // Show matching rows
            } else {
                row.style.display = "none"; // Hide non-matching rows
            }
        });
    });

    searchInput.addEventListener('input', function () {
        const searchTerm = searchInput.value.trim().toLowerCase();
        tableRows.forEach(function (row) {
            const fileName = row.querySelector("td:first-child").textContent.trim().toLowerCase();
            if (fileName.startsWith(searchTerm)) {
                row.style.display = ""; // Show matching rows
            } else {
                row.style.display = "none"; // Hide non-matching rows
            }
        });
    });

    const searchButton = document.querySelector('.unique-search-form .form-input button');
    const searchButtonIcon = document.querySelector('.unique-search-form .form-input button .bx');

    searchButton.addEventListener('click', function (e) {
        if (window.innerWidth < 576) {
            e.preventDefault();
            searchForm.classList.toggle('show');
            if (searchForm.classList.contains('show')) {
                searchButtonIcon.classList.replace('bx-search', 'bx-x');
            } else {
                searchButtonIcon.classList.replace('bx-x', 'bx-search');
            }
        }
    });

    const profile = document.querySelector('#content nav .profile');
    const dropdownProfile = document.querySelector('#content nav .profile .profile-link');

    profile.addEventListener('click', function () {
        dropdownProfile.classList.toggle('show');
    });

    window.addEventListener('click', function (e) {
        if (e.target !== profile) {
            if (dropdownProfile.classList.contains('show')) {
                dropdownProfile.classList.remove('show');
            }
        }
    });
});
