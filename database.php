<?php
$host ='localhost';
$user ='root';
$password ='';
$database='filebase';
$port='3306';
$connection = mysqli_connect($host,$user,$password,$database,$port);

if ($connection->connect_error) {
    die ("Connection failed: " . $connection->connect_error);
}
?>