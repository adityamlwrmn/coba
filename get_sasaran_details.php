<?php
include 'koneksi.php'; // Include the database connection

$sasaran_id = (int)$_GET['sasaran_id'];

// Validate the input
if ($sasaran_id <= 0) {
    echo json_encode(['error' => 'Invalid ID']);
    exit();
}

// Prepare and execute the query
$stmt = $mysqli->prepare("SELECT tahun_anggaran, keterangan FROM `sasaran` WHERE id = ?");
$stmt->bind_param('i', $sasaran_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode([]);
}

$stmt->close();
$mysqli->close();
?>
