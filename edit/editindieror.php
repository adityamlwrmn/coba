<?php
include 'koneksi.php'; // Ensure this file establishes $mysqli
include 'header.php';
include 'sidebar.php';

// Check if ID is provided in the URL
if (!isset($_GET['id_ik']) || empty($_GET['id_ik'])) {
    echo "Invalid ID.";
    exit;
}

$id_ik = intval($_GET['id_ik']);

// Fetch the current data for the given ID
$query = "SELECT * FROM `indikator kinerja` WHERE id_ik = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_ik);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "No data found.";
    exit;
}

// Fetch the list of sasaran for the dropdown
$sasaran_query = "SELECT id, nama_ss, tahun_anggaran, keterangan FROM `sasaran`";
$sasaran_result = $mysqli->query($sasaran_query);
$sasaran_options = [];
while ($row = $sasaran_result->fetch_assoc()) {
    $sasaran_options[] = $row;
}

// Fetch the list of users for the dropdown
$user_query = "SELECT id_user, nama_user FROM `user`";
$user_result = $mysqli->query($user_query);
$user_options = [];
while ($row = $user_result->fetch_assoc()) {
    $user_options[] = $row;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_indikator = $_POST['nama_indikator'];
    $nama_user_id = $_POST['nama_user']; // Use user ID
    $sasaran = $_POST['nama_ss'];
    $keterangan = $_POST['keterangan'];
    $tahun_anggaran = $_POST['tahun_anggaran'];
    $target = $_POST['target'];

    $update_query = "UPDATE `indikator kinerja` SET nama_indikator=?, nama_user=?, nama_ss=?, keterangan=?, tahun_anggaran=?, target=? WHERE id_ik=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("ssssssi", $nama_indikator, $nama_user_id, $sasaran, $keterangan, $tahun_anggaran, $target, $id_ik);

    if ($update_stmt->execute()) {
        echo "<script>alert('Data updated successfully.'); window.location.href='indikator.php';</script>";
    } else {
        echo "<script>alert('Failed to update data.');</script>";
    }

    $update_stmt->close();
}

$mysqli->close();
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
        <!-- Add navbar items here -->
      </ul>
    </nav>
    <!-- End of Topbar -->

    <!-- Begin Page Content -->
    <div class="container-fluid">
      <h1 class="h3 mb-4 text-gray-800">Edit Indikator Kinerja</h1>

      <!-- Edit Form -->
      <form action="" method="POST">
        <div class="form-group">
          <label for="nama_indikator">Nama Indikator</label>
          <input type="text" class="form-control" id="nama_indikator" name="nama_indikator" value="<?php echo htmlspecialchars($data['nama_indikator']); ?>" required>
        </div>

        <div class="form-group">
          <label for="nama_ss">Sasaran</label>
          <select class="form-control" name="nama_ss" id="nama_ss" required onchange="updateFields()">
            <option value="">Select Sasaran</option>
            <?php foreach ($sasaran_options as $sasaran): ?>
              <option value="<?php echo htmlspecialchars($sasaran['id']); ?>" 
                      <?php echo ($data['nama_ss'] == $sasaran['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($sasaran['nama_ss']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="tahun_anggaran">Tahun</label>
          <input type="number" class="form-control" id="tahun_anggaran" name="tahun_anggaran" value="<?php echo htmlspecialchars($data['tahun_anggaran']); ?>" readonly>
        </div>

        <div class="form-group">
          <label for="keterangan">Keterangan</label>
          <textarea class="form-control" id="keterangan" name="keterangan" rows="3" readonly><?php echo htmlspecialchars($data['keterangan']); ?></textarea>
        </div>

        <div class="form-group">
          <label for="nama_user">Nama User</label>
          <select class="form-control" name="nama_user" required>
            <option value="">Select a User</option>
            <?php foreach ($user_options as $user): ?>
              <option value="<?php echo htmlspecialchars($user['id_user']); ?>" 
                      <?php echo (htmlspecialchars($data['nama_user']) == htmlspecialchars($user['nama_user'])) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($user['nama_user']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="target">Target</label>
          <input type="text" class="form-control" id="target" name="target" value="<?php echo htmlspecialchars($data['target']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="indikator.php" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
    <!-- /.container-fluid -->

  </div>
  <!-- End of Main Content -->

  <script>
    function updateFields() {
        const sasaranDropdown = document.getElementById('nama_ss');
        const selectedSasaranId = sasaranDropdown.value;

        // Predefined options mapping for simplicity
        const sasaranData = {
            <?php foreach ($sasaran_options as $sasaran): ?>
                "<?php echo htmlspecialchars($sasaran['id']); ?>": {
                    tahun_anggaran: "<?php echo htmlspecialchars($sasaran['tahun_anggaran']); ?>",
                    keterangan: "<?php echo htmlspecialchars($sasaran['keterangan']); ?>"
                },
            <?php endforeach; ?>
        };

        if (sasaranData[selectedSasaranId]) {
            document.getElementById('tahun_anggaran').value = sasaranData[selectedSasaranId].tahun_anggaran;
            document.getElementById('keterangan').value = sasaranData[selectedSasaranId].keterangan;
        } else {
            document.getElementById('tahun_anggaran').value = '';
            document.getElementById('keterangan').value = '';
        }
    }
  </script>

<?php include 'footer.php'; ?>
