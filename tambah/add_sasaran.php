<?php
include 'koneksi.php'; // Ensure this file establishes $mysqli

// Initialize variables
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data with default values
    $nama_ss = $_POST['nama_ss'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';
    $tahun_anggaran = $_POST['tahun_anggaran'] ?? 0; // Directly handle as number

    // Prepare an SQL statement for inserting sasaran data
    $stmt = $mysqli->prepare("INSERT INTO sasaran (nama_ss, keterangan, tahun_anggaran) VALUES (?, ?, ?)");
    $stmt->bind_param('ssi', $nama_ss, $keterangan, $tahun_anggaran); // 'ssi' is correct

    if ($stmt->execute()) {
        $success = true; // Set success flag
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Sasaran</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .button-spacing {
            margin-right: 10px; /* Adjust as needed */
        }
    </style>
</head>
<body>
<?php
include 'header.php';
include 'sidebar.php';
?>

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
                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell fa-fw"></i>
                        <span class="badge badge-danger badge-counter">3+</span>
                    </a>
                </li>
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">User</span>
                        <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <h1 class="h3 mb-4 text-gray-800">Sasaran Strategis</h1>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambahkan Sasaran</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="form-group row">
                            <label for="nama_ss" class="col-md-3 col-form-label">Nama Sasaran:</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="nama_ss" name="nama_ss" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="keterangan" class="col-md-3 col-form-label">Keterangan:</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tahun_anggaran" class="col-md-3 col-form-label">Tahun Anggaran:</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="tahun_anggaran" name="tahun_anggaran" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary button-spacing">Add Sasaran</button>
                        <a href="buttons.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

<?php include 'footer.php'; ?>

<!-- SweetAlert notification -->
<?php if ($success): ?>
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Sasaran berhasil ditambahkan.',
            icon: 'success'
        }).then(() => {
            window.location.href = 'buttons.php';
        });
    </script>
<?php endif; ?>

</body>
</html>
