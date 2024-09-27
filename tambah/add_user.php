<?php
include 'koneksi.php'; // Ensure this file exists and is correct

// Handling form submission
$showAlert = false; // Variable for controlling alert

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $keterangan = $_POST['keterangan'];
    $status = isset($_POST['status']) ? 1 : 0; // Check if status is set

    // Check if the username already exists
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM user WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();

    if ($count > 0) {
        // Username already exists, show error message
        $error_message = "Username '$username' sudah ada. Silakan gunakan username yang berbeda.";
    } else {
        // Close the statement before preparing a new one
        $stmt->close();

        // Prepare an SQL statement for inserting user data
        $stmt = $mysqli->prepare("INSERT INTO user (username, nama_user, password, keterangan, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssi', $username, $nama, $password, $keterangan, $status);

        if ($stmt->execute()) {
            $stmt->close(); // Make sure to close the statement
            header("Location: user.php?status=success"); // Redirect with status
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Close the statement
    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <style>
        .error-message {
            color: red; /* Red color for error message */
            margin-bottom: 20px; /* Spacing below the message */
        }
        .form-group {
            margin-bottom: 1rem; /* Spacing between form groups */
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
            <!-- Navbar content... -->
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Users</h1>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambahkan User</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="form-group row">
                            <label for="username" class="col-md-2 col-form-label">Username:</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama" class="col-md-2 col-form-label">Nama:</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-md-2 col-form-label">Password:</label>
                            <div class="col-md-10">
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="keterangan" class="col-md-2 col-form-label">Keterangan:</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-md-2 col-form-label">Status:</label>
                            <div class="col-md-10">
                                <div style="display: flex; align-items: center;">
                                    <input type="checkbox" id="status" name="status" value="1">
                                    <label for="status" class="ml-2">Active</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add User</button>
                        <a href="user.php" class="btn btn-secondary">Batal</a>
                    </form>
                    <?php
                    // Display error message if exists
                    if (isset($error_message)) {
                        echo "<div class='error-message'>$error_message</div>";
                    }
                    ?>
                </div>
            </div>

            <?php include 'footer.php'; ?>
        </div>
        <!-- End of Content Wrapper -->
    </div>
</div>

</body>
</html>
