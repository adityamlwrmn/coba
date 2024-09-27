<?php
session_start();
include 'koneksi3.php';  // Ensure this file initializes $kon correctly
include 'header.php';
include 'sidebar_user.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit();
}

// Check if $kon is set
if (!isset($kon) || !$kon instanceof mysqli) {
    die("Database connection is not established.");
}

// Determine active triwulan
$query = "SELECT status_triwulan FROM tw3 WHERE status_triwulan = 1 LIMIT 1";
$stmt = $kon->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $kon->error);
}
$stmt->execute();
$stmt->store_result();
$status_triwulan_active = $stmt->num_rows > 0;

// Use the year stored in the session
$current_year = $_SESSION['selected_year'];

// Fetch all data from tw3 table for the selected year
$query_all = "SELECT * FROM tw3 WHERE tahun_anggaran = ?";
$stmt_all = $kon->prepare($query_all);
if (!$stmt_all) {
    die("Prepare failed: " . $kon->error);
}
$stmt_all->bind_param("s", $current_year); // Bind the year to the query
$stmt_all->execute();
$result_all = $stmt_all->get_result();
$count = 1;

// Function to display uploaded files as links
function displayUploadedFiles($file_data_dukung)
{
    $files = explode(",", $file_data_dukung);
    $output = '';
    foreach ($files as $file) {
        $file_path = htmlspecialchars(trim($file));
        if (file_exists($file_path)) {
            $output .= '<a href="' . $file_path . '" target="_blank">' . htmlspecialchars($file) . '</a><br>';
        } else {
            $output .= htmlspecialchars($file) . '<br>';
        }
    }
    return $output;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="path/to/your/css/style.css">
    <style>
        .alert {
            padding: 15px;
            margin: 20px 0;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }
    </style>
</head>
<body>
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                        <img class="img-profile rounded-circle" src="img/undraw_profile.svg" alt="User Profile">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <h1 class="h3 mb-4 text-gray-800">Triwulan 3 - Tahun <?php echo htmlspecialchars($current_year); ?></h1>
            
            <?php if (!$status_triwulan_active) : ?>
                <div class="alert alert-warning" role="alert">
                    Triwulan 3 saat ini tidak aktif. Data tidak dapat diedit atau dihapus sampai Triwulan 2 aktif kembali.
                </div>
            <?php endif; ?>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Triwulan 3</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Indikator</th>
                                    <th>Keterangan Indikator</th>
                                    <th>Nama Sasaran</th>
                                    <th>Tahun Anggaran</th>
                                    <th>Nama User</th>
                                    <th>Target</th>
                                    <th>Realisasi</th>
                                    <th>Satuan</th>
                                    <th>Bobot</th>
                                    <th>Capaian</th>
                                    <th>Penjelasan</th>
                                    <th>Progress Kegiatan</th>
                                    <th>Kendala Permasalahan</th>
                                    <th>Strategi Tindak Lanjut</th>
                                    <th class="file-data-column">File Data Dukung</th>
                                    <th class="noExport">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_all->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?php echo $count++; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_ik']); ?></td>
                                        <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['sasaran']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tahun_anggaran']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                                        <td><?php echo htmlspecialchars($row['target']); ?></td>
                                        <td><?php echo htmlspecialchars($row['realisasi']); ?></td>
                                        <td><?php echo htmlspecialchars($row['satuan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['bobot']); ?></td>
                                        <td><?php echo htmlspecialchars($row['capaian']); ?></td>
                                        <td><?php echo htmlspecialchars($row['penjelasan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['progress_kegiatan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['kendala_permasalahan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['strategi_tindak_lanjut']); ?></td>
                                        <td><?php echo displayUploadedFiles($row['file_data_dukung']); ?></td>
                                        <td class="noExport">
                                            <?php if ($row['nama_user'] === $_SESSION['username'] && $status_triwulan_active) : ?>
                                                <a href="edit/edit_tw3user.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            <?php else : ?>
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- End of Content Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
    // Check if the DataTable is already initialized
    if ($.fn.dataTable.isDataTable('#dataTable')) {
        // If it is, destroy it before reinitializing
        $('#dataTable').DataTable().destroy();
    }
    
    // Initialize DataTable with buttons
    $('#dataTable').DataTable({
        dom: 'Bfrtip', // Define where to place the buttons
        buttons: [
            {
                extend: 'pdfHtml5',
                text: 'Export PDF',
                filename: 'Triwulan_3_Data',
                orientation: 'landscape',
                pageSize: 'A3',
                exportOptions: {
                    columns: ':not(.noExport)' // Exclude columns with class 'noExport'
                },
                customize: function (doc) {
                    doc.content[1].margin = [0, 0, 0, 10]; // Add margin to the table
                }
            },
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                filename: 'Triwulan_3_Data',
                title: 'Triwulan 3 Data',
                exportOptions: {
                    columns: ':not(.noExport)' // Exclude columns with class 'noExport'
                }
            }
        ]
    });
});
</script>

</body>
</html>
