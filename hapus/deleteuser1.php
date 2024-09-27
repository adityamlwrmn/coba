<?php
include "conixion.php";

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$file_name = isset($_POST['file_name']) ? mysqli_real_escape_string($con, $_POST['file_name']) : '';

if ($id > 0 && !empty($file_name)) {
    // Retrieve existing file data
    $sql = "SELECT file_data_dukung FROM `tw1` WHERE id = $id";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $existingFiles = $row['file_data_dukung'];

        $files = explode(',', $existingFiles);
        $files = array_filter($files, function($file) use ($file_name) {
            return $file !== $file_name;
        });
        $updatedFiles = implode(',', $files);

        // Delete the file from the server
        $file_path = "uploads/" . $file_name;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Update the database
        $update_sql = "UPDATE `tw1` SET file_data_dukung = '$updatedFiles' WHERE id = $id";
        if (mysqli_query($con, $update_sql)) {
            echo "<div class='alert alert-success'>File berhasil dihapus!</div>";
        } else {
            echo "<div class='alert alert-danger'>Gagal menghapus file dari database. Error: " . mysqli_error($con) . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Data tidak ditemukan.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>ID atau nama file tidak valid.</div>";
}

mysqli_close($con);
header("Location: edit_tw1user.php?id=$id");
exit();
?>
