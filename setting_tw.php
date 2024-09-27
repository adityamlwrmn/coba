<?php
session_start();
include 'koneksi1.php';  // Ensure this file establishes $con as the PDO connection
include 'header.php';
include 'sidebar.php';

// Check for action and source parameters
if (isset($_GET['action']) && isset($_GET['id']) && isset($_GET['source'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];
    $source = $_GET['source'];

    // Validate source to be one of the expected values
    $valid_sources = ['tw1', 'tw2', 'tw3', 'tw4'];
    if (!in_array($source, $valid_sources)) {
        die("Invalid source specified.");
    }

    if ($action == 'aktifkan') {
        // Deactivate all previous active triwulan
        foreach ($valid_sources as $table) {
            $updateStatusQuery = "UPDATE `$table` SET `status_triwulan` = 0";
            $con->exec($updateStatusQuery);
        }

        // Activate the new triwulan
        $query = "UPDATE `$source` SET `status_triwulan` = 1 WHERE `Id` = ?";
        $stmt = $con->prepare($query);
        $stmt->execute([$id]);

        // Store a message in the session to display later
        $_SESSION['message'] = 'Triwulan berhasil diaktifkan';
        echo "<script>window.location.href = 'setting_tw.php';</script>"; // Redirect back to the settings page
    } elseif ($action == 'nonaktifkan') {
        // Deactivate the triwulan
        $query = "UPDATE `$source` SET `status_triwulan` = 0 WHERE `Id` = ?";
        $stmt = $con->prepare($query);
        $stmt->execute([$id]);

        // Store a message in the session to display later
        $_SESSION['message'] = 'Triwulan berhasil dinonaktifkan';
        echo "<script>window.location.href = 'setting_tw.php';</script>"; // Redirect back to the settings page
    }
}

// List of triwulan sources
$tw_sources = ['tw1', 'tw2', 'tw3', 'tw4'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting Triwulan</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
          integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous">
</head>
<body>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McCarthy</span>
                        <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                            Settings
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                            Activity Log
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <h1 class="h3 mb-4 text-gray-800">Setting Triwulan</h1>

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Triwulan</h6>
                </div>

                <!-- Indikator list table -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Source</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Iterate through each triwulan source
                                $no = 1;
                                foreach ($tw_sources as $source) {
                                    $query = "SELECT Id, status_triwulan FROM `$source` LIMIT 1";
                                    $stmt = $con->query($query);
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    if ($row) {
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($source); ?></td>
                                            <td>
                                                <?php echo ($row['status_triwulan'] == 1) ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>'; ?>
                                            </td>
                                            <td>
                                                <?php if ($row['status_triwulan'] == 1): ?>
                                                    <a href="?action=nonaktifkan&id=<?php echo htmlspecialchars($row['Id']); ?>&source=<?php echo htmlspecialchars($source); ?>" onclick="return confirm('Apakah Anda yakin ingin menonaktifkan triwulan ini?')" class="btn btn-sm btn-warning"><i class="fas fa-user-times"></i> Nonaktifkan</a>
                                                <?php else: ?>
                                                    <a href="#" data-toggle="modal" data-target="#confirmModal" data-id="<?php echo htmlspecialchars($row['Id']); ?>" data-source="<?php echo htmlspecialchars($source); ?>" class="btn btn-sm btn-success activate-btn"><i class="fas fa-user-check"></i> Aktifkan</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    } else {
                                        echo "<tr><td colspan='4'>Tidak ada data untuk $source.</td></tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End of content page -->
        </div>
        <!-- End of Main Content -->

        <!-- Confirmation Modal -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin mengaktifkan triwulan ini?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" id="confirmActivate" class="btn btn-success">Aktifkan</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Sukses</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php echo isset($_SESSION['message']) ? htmlspecialchars($_SESSION['message']) : ''; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container my-auto">
                <div class="text-center my-auto">
                    <span>© <?php echo date("Y"); ?> Your Company. All Rights Reserved.</span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../js/bootstrap.bundle.js"></script>
<script>
    $(document).ready(function() {
        let activateId;
        let activateSource;

        // Set the ID and source when the modal is triggered
        $('.activate-btn').click(function() {
            activateId = $(this).data('id');
            activateSource = $(this).data('source');
        });

        // On confirmation, navigate to the URL to activate
        $('#confirmActivate').click(function() {
            window.location.href = `?action=aktifkan&id=${activateId}&source=${activateSource}`;
        });

        // Show the success modal if there is a message
        <?php if (isset($_SESSION['message'])): ?>
            $('#successModal').modal('show');
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    });
</script>
</body>
</html>
