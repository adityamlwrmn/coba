<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']); // Sanitize input

    // Prepare and execute the delete statement
    $query = "DELETE FROM sasaran WHERE id = ?";
    
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    } else {
        echo 'error';
    }
} else {
    echo 'invalid request';
}

$mysqli->close();
?>
