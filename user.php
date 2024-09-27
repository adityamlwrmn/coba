<?php
include 'koneksi.php'; // Pastikan file ini ada dan benar

// Inisialisasi query pencarian
$search_query = '';
$showAlert = false; // Variabel untuk mengontrol alert
$showDeleteAlert = false; // Variabel untuk mengontrol alert penghapusan

// Cek untuk penambahan yang berhasil
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $showAlert = true; // Set variabel untuk menunjukkan alert
    $statusChangeMessage = isset($_GET['message']) ? $_GET['message'] : '';
}

// Cek jika penghapusan berhasil
if (isset($_GET['delete']) && $_GET['delete'] == 'success') {
    $showDeleteAlert = true;
}

// Cek input pencarian
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search_query = $mysqli->real_escape_string(trim($_POST['search']));
}

// Cek jika permintaan penghapusan ada
if (isset($_POST['delete_user'])) {
    $id_user = $_POST['id_user'];

    // Siapkan dan eksekusi pernyataan hapus
    $stmt = $mysqli->prepare("DELETE FROM user WHERE id_user = ?");
    $stmt->bind_param('i', $id_user);

    if ($stmt->execute()) {
        header("Location: user.php?delete=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Tangani pembaruan status
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    // Siapkan dan eksekusi query SQL untuk memperbarui status pengguna
    $query = "UPDATE `user` SET `status` = ? WHERE `id_user` = ?";
    $stmt = $mysqli->prepare($query);
    $newStatus = ($action == 'aktifkan') ? 1 : 0; // 1 untuk aktif, 0 untuk non-aktif
    $stmt->bind_param('ii', $newStatus, $id);
    $stmt->execute();

    // Redirect dengan pesan status
    $message = ($action == 'aktifkan') ? 'User berhasil diaktifkan' : 'User berhasil dinonaktifkan';
    header("Location: user.php?status=success&message=" . urlencode($message));
    exit();
}

// Ambil pengguna dari database, diurutkan berdasarkan id_user menurun
$query = "SELECT * FROM user ORDER BY id_user DESC";
if ($search_query) {
    $query .= " WHERE username LIKE '%$search_query%' OR nama_user LIKE '%$search_query%'";
}
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <form class="d-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="POST">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Cari..." name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </form>
            </nav>

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Daftar Pengguna</h1>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Pengguna</h6>
                        <a href="tambah/add_user.php" class="btn btn-success">Tambah Data</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Password</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1; // Inisialisasi penghitung
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                                        <td><?php echo htmlspecialchars($row['password']); ?></td>
                                        <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $row['status'] ? 'badge-success' : 'badge-danger'; ?>">
                                                <?php echo $row['status'] ? 'Aktif' : 'Nonaktif'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="edit/edit_user.php?id_user=<?php echo $row['id_user']; ?>" class="btn btn-sm btn-info">Edit</a>
                                            <button class="btn btn-sm btn-<?php echo $row['status'] ? 'warning' : 'success'; ?>" onclick="confirmStatusChange(<?php echo $row['id_user']; ?>, '<?php echo $row['status'] ? 'nonaktifkan' : 'aktifkan'; ?>')">
                                                <?php echo $row['status'] ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $row['id_user']; ?>)">Hapus</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End of Content Wrapper -->
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Anda tidak dapat mengembalikan data ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iyaa, hapus itu!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form untuk mengirim permintaan hapus
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'user.php';
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id_user';
                    input.value = id;
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_user';
                    deleteInput.value = '1';
                    form.appendChild(input);
                    form.appendChild(deleteInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function confirmStatusChange(id, action) {
            const actionText = action === 'aktifkan' ? 'aktifkan' : 'nonaktifkan';
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: `Kamu akan ${actionText} user ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Iyaa, ${actionText} itu!`
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke halaman yang sama dengan action dan id
                    window.location.href = `user.php?action=${action}&id=${id}`;
                }
            });
        }
    </script>

    <?php if ($showAlert): ?>
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data telah berhasil diubah.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>

    <?php if ($showDeleteAlert): ?>
        <script>
            Swal.fire({
                title: 'Dihapus!',
                text: 'User telah berhasil dihapus.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>

</body>

</html>
