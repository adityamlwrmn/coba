<?php
include 'koneksi.php'; // Ensure this file establishes $mysqli

// Check if the ID is provided in the URL
if (isset($_GET['Id'])) {
    $id = intval($_GET['Id']);

    // Prepare the SQL query to delete the record
    $query = "DELETE FROM tahun_anggaran WHERE Id = ?";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('i', $id);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to the list page after successful deletion
            header('Location: tahun.php');
            exit();
        } else {
            // Error
            echo "Error deleting record: " . $mysqli->error;
        }

        $stmt->close();
    } else {
        // Error preparing the query
        echo "Error preparing query: " . $mysqli->error;
    }
} else {
    // No ID provided, redirect to the list page
    header('Location: tahun.php');
    exit();
}

$mysqli->close();
?>
