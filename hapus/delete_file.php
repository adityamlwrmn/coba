<?php
// Include database connection
include "conixion.php";

// Function to sanitize input
function input($data) {
    return htmlspecialchars(trim($data));
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the ID and file name from the POST request
    $id = input($_POST["id"]);
    $file_name = input($_POST["file_name"]);
    $file_path = 'uploads/' . $file_name; // Path to the file

    // Check if the file exists on the server
    if (file_exists($file_path)) {
        // Try to delete the file
        if (unlink($file_path)) {
            // File deleted successfully
        } else {
            echo "Error deleting file from server.<br>";
        }
    } else {
        echo "File does not exist on the server.<br>";
    }

    // Update the database to remove the file reference
    $sql = "SELECT file_data_dukung FROM `tw1` WHERE id = '$id'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $existing_files = $row['file_data_dukung'];

        // Remove the file from the list
        $files_array = explode(',', $existing_files);
        $updated_files_array = array_diff($files_array, [$file_name]);
        $updated_files = implode(',', $updated_files_array);

        // Update the database
        $update_sql = "UPDATE `tw1` SET file_data_dukung = '$updated_files' WHERE id = '$id'";
        if (mysqli_query($con, $update_sql)) {
            // Redirect back to the edit page
            header("Location: edit_tw1.php?id=$id");
            exit();
        } else {
            echo "Error updating database: " . mysqli_error($con);
        }
    } else {
        echo "Error fetching data from database: " . mysqli_error($con);
    }
}

// Close the database connection
mysqli_close($con);
?>
