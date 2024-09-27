<?php
include 'koneksi.php'; // Ensure this file establishes $mysqli

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);

        // Fetch current status
        $query = "SELECT status_ta FROM tahun_anggaran WHERE Id = ?";
        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($current_status);
            $stmt->fetch();
            $stmt->close();

            // Determine new status
            $new_status = ($current_status == 1) ? 0 : 1;

            // Update status in the database
            $update_query = "UPDATE tahun_anggaran SET status_ta = ? WHERE Id = ?";
            if ($update_stmt = $mysqli->prepare($update_query)) {
                $update_stmt->bind_param('ii', $new_status, $id);
                if ($update_stmt->execute()) {
                    header('Location: list_tahun.php'); // Redirect to the list page
                } else {
                    echo "Error updating record: " . $mysqli->error;
                }
                $update_stmt->close();
            } else {
                echo "Error preparing statement: " . $mysqli->error;
            }
        } else {
            echo "Error preparing statement: " . $mysqli->error;
        }

        $mysqli->close();
    }
}
?>
