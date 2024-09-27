<?php
// Database connection parameters
$host = 'localhost'; // Replace with your database host
$user = 'root'; // Replace with your database username
$password = ''; // Replace with your database password
$database = 'input'; // Replace with your database name

// Create connection
$mysqli = new mysqli($host, $user, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
?>
