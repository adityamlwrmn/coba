<?php
include 'koneksi.php'; // Ensure this connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the ID from the POST request
    $id_ik = isset($_POST['id_ik']) ? intval($_POST['id_ik']) : 0;

    // Check if ID is valid
    if ($id_ik > 0) {
        // Prepare the SQL statement
        $stmt = $mysqli->prepare("DELETE FROM `indikator kinerja` WHERE id_ik = ?");
        $stmt->bind_param('i', $id_ik); // Bind the ID parameter

        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                echo 'success';
            } else {
                echo 'failed'; // No rows deleted (maybe the ID doesn't exist)
            }
        } else {
            echo 'error'; // Execution failed
        }

        $stmt->close();
    } else {
        echo 'invalid'; // Invalid ID
    }
}

$mysqli->close();
?>
