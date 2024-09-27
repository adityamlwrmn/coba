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
      $new_status = ($current_status === 'Active') ? 'Inactive' : 'Active';

      // Update status in database
      $update_query = "UPDATE tahun_anggaran SET status_ta = ? WHERE Id = ?";
      if ($update_stmt = $mysqli->prepare($update_query)) {
        $update_stmt->bind_param('si', $new_status, $id);
        $update_stmt->execute();
        $update_stmt->close();
      }
    }
  }

  // Redirect back to the list page
  header('Location: list_tahun.php');
  exit();
}
