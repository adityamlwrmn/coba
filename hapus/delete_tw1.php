<?php
include 'koneksi.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Prepare the delete statement
    $stmt = $mysqli->prepare("DELETE FROM tw1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
}
?>
