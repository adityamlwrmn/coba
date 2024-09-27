<?php
include 'koneksi.php'; // Ensure this file establishes $mysqli
include 'header.php';
include 'sidebar.php';

// Check if ID is passed in the URL
$Id = isset($_GET['Id']) ? intval($_GET['Id']) : 0;

// Fetch data for the specific ID
if ($Id > 0) {
    $query = "SELECT * FROM tahun_anggaran WHERE Id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $Id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    if (!$data) {
        // Data not found, redirect to list page
        header('Location: tahun.php');
        exit();
    }
} else {
    // Redirect to the list page if no ID is provided
    header('Location: tahun.php');
    exit();
}
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
  <!-- Main Content -->
  <div id="content">
    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
      <!-- Topbar Search -->
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
      <!-- Topbar Navbar -->
      <ul class="navbar-nav ml-auto">
        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
          <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-bell fa-fw"></i>
            <span class="badge badge-danger badge-counter">3+</span>
          </a>
          <!-- Dropdown - Alerts -->
          <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
            <h6 class="dropdown-header">Alerts Center</h6>
            <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
          </div>
        </li>
        <!-- Nav Item - Messages -->
        <li class="nav-item dropdown no-arrow mx-1">
          <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-envelope fa-fw"></i>
            <span class="badge badge-danger badge-counter">7</span>
          </a>
          <!-- Dropdown - Messages -->
          <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
            <h6 class="dropdown-header">Message Center</h6>
            <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
          </div>
        </li>
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McCarthy</span>
            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
          </a>
          <!-- Dropdown - User Information -->
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
      <h1 class="h3 mb-4 text-gray-800">Edit Tahun</h1>
      <!-- Edit Form -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Edit Tahun</h6>
        </div>
        <div class="card-body">
          <form action="update_tahun.php" method="POST">
            <input type="hidden" name="Id" value="<?php echo htmlspecialchars($data['Id']); ?>">
            <div class="form-group">
              <label for="nama_ta">Nama Tahun</label>
              <input type="text" class="form-control" id="nama_ta" name="nama_ta" value="<?php echo htmlspecialchars($data['nama_ta']); ?>" required>
            </div>
            <div class="form-group">
              <label for="keterangan">Keterangan</label>
              <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required><?php echo htmlspecialchars($data['keterangan']); ?></textarea>
            </div>
            <div class="form-group">
              <label for="status_ta">Status:</label>
              <input type="checkbox" id="status_ta" name="status_ta" value="1" <?php echo $data['status_ta'] == '1' ? 'checked' : ''; ?>> Active
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="tahun.php" class="btn btn-secondary">Cancel</a>
          </form>
        </div>
      </div>
    </div>
    <!-- /.container-fluid -->
  </div>
  <!-- End of Main Content -->
  <?php include 'footer.php'; ?>
</div>
<!-- End of Content Wrapper -->
