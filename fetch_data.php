<?php
include 'koneksi.php';

$tahun = $_POST['tahun'] ?? '';
$indikator = $_POST['indikator'] ?? '';
$sasaran = $_POST['sasaran'] ?? '';
$page = $_POST['page'] ?? 1;
$entries = $_POST['entries'] ?? '';
$search = $_POST['search'] ?? ''; // Get the search parameter

// Build your query here
$query = "SELECT * FROM tw1 WHERE 1=1"; // Base query

// Filter by year if provided
if ($tahun) {
    $query .= " AND tahun_anggaran = '$tahun'";
}

// Filter by indicator if provided
if ($indikator) {
    $query .= " AND nama_ik = '$indikator'";
}

// Filter by target if provided
if ($sasaran) {
    $query .= " AND sasaran = '$sasaran'";
}

// Filter by search term if provided
if ($search) {
    $search = $mysqli->real_escape_string($search); // Sanitize input
    $query .= " AND (nama_ik LIKE '%$search%' OR 
                      sasaran LIKE '%$search%' OR 
                      keterangan LIKE '%$search%')";
}

// Sort by ID in descending order to get the latest entries first
$query .= " ORDER BY id DESC"; // Adjust 'id' to your relevant timestamp field if available

// Pagination
$totalResults = $mysqli->query($query)->num_rows;
$totalPages = ceil($totalResults / $entries);
$offset = ($page - 1) * $entries;

// Limit results for pagination
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
                    <a href="edit_tw1.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-warning btn-sm">Edit</a>
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
