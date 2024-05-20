<?php
include 'database.php';

if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];

    // Perform the search query here
    $query = "SELECT * FROM file WHERE fileName LIKE '%$searchTerm%'";
    $result = mysqli_query($connection, $query);

    // Fetch and return search results as JSON
    $searchResults = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $searchResults[] = $row;
    }

    echo json_encode($searchResults);
    exit();
}
?>
