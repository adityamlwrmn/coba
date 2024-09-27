<?php
include 'koneksi.php';

$tahun = $_POST['tahun'] ?? '';
$indikator = $_POST['indikator'] ?? '';
$sasaran = $_POST['sasaran'] ?? '';
$page = $_POST['page'] ?? 1;
$entries = $_POST['entries'] ?? 10;

// Build your query here
$query = "SELECT * FROM tw2 WHERE 1=1"; // Adjust according to your needs

if ($tahun) {
    $query .= " AND tahun_anggaran = '$tahun'";
}
if ($indikator) {
    $query .= " AND nama_ik = '$indikator'";
}
if ($sasaran) {
    $query .= " AND sasaran = '$sasaran'";
}

// Sort by ID in descending order to get the latest entries first
$query .= " ORDER BY id DESC"; // Change 'id' to your relevant timestamp field if available

// Pagination
$totalResults = $mysqli->query($query)->num_rows;
$totalPages = ceil($totalResults / $entries);
$offset = ($page - 1) * $entries;

$query .= " LIMIT $offset, $entries";
$result = $mysqli->query($query);

$output = '';
$count = $offset + 1;

while ($row = $result->fetch_assoc()) {
    $output .= '<tr class="align-middle">';
    $output .= '<td>' . htmlspecialchars($count++) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['nama_ik']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['keterangan']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['sasaran']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['tahun_anggaran']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['nama_user']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['target']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['realisasi']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['satuan']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['bobot']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['capaian']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['penjelasan']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['progress_kegiatan']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['kendala_permasalahan']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['strategi_tindak_lanjut']) . '</td>';
    $output .= '<td class="file-data-column">' . htmlspecialchars($row['file_data_dukung']) . '</td>';
    
    $output .= '<td>
                    <a href="edit_tw2.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-warning btn-sm">Edit</a>
                    <button class="btn btn-danger btn-sm delete-button" data-id="' . $row['id'] . '">Delete</button>
                </td>';
    $output .= '</tr>';
}

$response = [
    'data' => $output,
    'totalPages' => $totalPages,
];

echo json_encode($response);
?>
