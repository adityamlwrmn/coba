<?php
$host = 'localhost'; // Ganti dengan host database Anda
$username = 'root';  // Ganti dengan username database Anda
$password = '';      // Ganti dengan password database Anda
$database = 'input'; // Ganti dengan nama database Anda

// Membuat koneksi
$con = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>
