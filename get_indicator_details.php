<?php
include "conixion.php";

if (isset($_GET['nama_indikator'])) {
    $nama_indikator = mysqli_real_escape_string($con, $_GET['nama_indikator']);
    
    $query = "SELECT ik.keterangan, s.nama_ss, u.nama_user, ik.tahun_anggaran, ik.target 
              FROM `indikator kinerja` ik
              JOIN `sasaran` s ON ik.nama_ss = s.id 
              JOIN `user` u ON ik.nama_user = u.id_user
              WHERE ik.nama_indikator = '$nama_indikator'";
    
    $result = mysqli_query($con, $query);
    
    if ($result) {
        $data = mysqli_fetch_assoc($result);
        echo json_encode($data);
    } else {
        echo json_encode(null);
    }
} else {
    echo json_encode(null);
}

mysqli_close($con);
?>
