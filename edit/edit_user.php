<?php
include 'koneksi.php'; // Ensure this file establishes $mysqli

$showAlert = false; // Variable for controlling alert

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $id_user = $_POST['id_user'];
    $username = $_POST['username'];
    $nama = $_POST['nama_user'];
    $password = $_POST['password'];
    $keterangan = $_POST['keterangan'];
    $status = isset($_POST['status']) ? 1 : 0; // Check if status is set

    // Prepare an SQL statement for updating user data
    $stmt = $mysqli->prepare("UPDATE user SET username = ?, nama_user = ?, password = ?, keterangan = ?, status = ? WHERE id_user = ?");
    $stmt->bind_param('ssssii', $username, $nama, $password, $keterangan, $status, $id_user);

    if ($stmt->execute()) {
        $showAlert = true; // Set variable to show alert
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

    if ($showAlert) {
        echo "<script>setTimeout(function(){ window.location.href = 'user.php'; }, 2000);</script>"; // Redirect after alert
    }
} else {
    // Fetch user data for the form
    if (isset($_GET['id_user'])) {
        $id_user = $_GET['id_user'];
        $stmt = $mysqli->prepare("SELECT * FROM user WHERE id_user = ?");
        $stmt->bind_param('i', $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "No user ID provided.";
        exit();
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
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
            <!-- Your navbar content -->
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <h1 class="h3 mb-4 text-gray-800">Edit User</h1>

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit User Details</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($user['id_user'] ?? ''); ?>">
                        
                        <div class="form-group row">
                            <label for="username" class="col-md-2 col-form-label">Username:</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="nama_user" class="col-md-2 col-form-label">Nama:</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="nama_user" name="nama_user" value="<?php echo htmlspecialchars($user['nama_user'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="password" class="col-md-2 col-form-label">Password:</label>
                            <div class="col-md-10">
                                <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($user['password'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="keterangan" class="col-md-2 col-form-label">Keterangan:</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?php echo htmlspecialchars($user['keterangan'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="status" class="col-md-2 col-form-label">Status:</label>
                            <div class="col-md-10">
                                <div style="display: flex; align-items: center;">
                                    <input type="checkbox" id="status" name="status" value="1" <?php echo (isset($user['status']) && $user['status'] ? 'checked' : ''); ?>>
                                    <label for="status" class="ml-2">Active</label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="user.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

<?php include 'footer.php'; ?>

<?php if ($showAlert): ?>
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data pengguna telah berhasil diperbarui.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'user.php'; // Redirect to user list after alert
        });
    </script>
<?php endif; ?>

<!-- Add your JavaScript includes here -->
</body>
</html>
