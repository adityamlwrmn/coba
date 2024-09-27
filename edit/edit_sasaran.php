<?php
include 'koneksi.php'; // Pastikan file ini ada dan benar
include 'header.php';
include 'sidebar.php';

// Cek jika ID ada di URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data untuk ID tertentu
if ($id > 0) {
    $query = "SELECT * FROM sasaran WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
} else {
    // Redirect ke halaman list jika tidak ada ID
    header('Location: buttons.php');
    exit();
}

// Menambahkan SweetAlert untuk pemberitahuan
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data berhasil diupdate.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'buttons.php'; // Redirect ke tabel setelah mengklik OK
            }
        });
    </script>";
}
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
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Edit Sasaran</h1>

            <!-- Edit Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Sasaran Data</h6>
                </div>
                <div class="card-body">
                    <form action="update_sasaran.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['id']); ?>">
                        <div class="form-group row">
                            <label for="nama" class="col-md-3 col-form-label">Nama</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($data['nama_ss']); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="keterangan" class="col-md-3 col-form-label">Keterangan</label>
                            <div class="col-md-9">
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required><?php echo htmlspecialchars($data['keterangan']); ?></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tahun" class="col-md-3 col-form-label">Tahun</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="tahun" name="tahun" value="<?php echo htmlspecialchars($data['tahun_anggaran']); ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="buttons.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
        <!-- End of Content Wrapper -->

    </div>

    <?php include 'footer.php'; ?>
</div>
