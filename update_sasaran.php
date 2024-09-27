<?php
include 'koneksi.php'; // Pastikan file ini ada dan benar

// Cek apakah data POST ada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $keterangan = $_POST['keterangan'];
    $tahun = $_POST['tahun'];

    // Persiapkan dan eksekusi query update
    $stmt = $mysqli->prepare("UPDATE sasaran SET nama_ss = ?, keterangan = ?, tahun_anggaran = ? WHERE id = ?");
    $stmt->bind_param('ssii', $nama, $keterangan, $tahun, $id);

    if ($stmt->execute()) {
        // Redirect dengan status sukses
        header('Location: edit_sasaran.php?id=' . $id . '&status=success');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
