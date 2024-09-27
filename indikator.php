<?php
include 'koneksi.php'; // Pastikan file ini terhubung ke database
include 'header.php';
include 'sidebar.php';

// Inisialisasi query pencarian
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

// Pemberitahuan keberhasilan penambahan dan pengeditan data
$successMessage = '';
if (isset($_GET['add']) && $_GET['add'] == 'success') {
    $successMessage = 'Data telah berhasil ditambahkan.';
} elseif (isset($_GET['edit']) && $_GET['edit'] == 'success') {
    $successMessage = 'Data telah berhasil diedit.';
}
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

  <!-- Main Content -->
  <div id="content">

    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
      <!-- Topbar Search -->
      <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="GET" action="">
        <div class="input-group">
          <input type="text" class="form-control bg-light border-0 small" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
          <div class="input-group-append">
            <button class="btn btn-primary" type="submit">
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
      <h1 class="h3 mb-4 text-gray-800">Indikator Kinerja</h1>
      <a href="add_indi.php" class="btn btn-primary">Tambah Data</a>

      <!-- DataTales Example -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Indikator Kinerja List</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Indikator</th>
                  <th>Nama User</th>
                  <th>Sasaran</th>
                  <th>Keterangan</th>
                  <th>Tahun</th>
                  <th>Target</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Query untuk mengambil data dengan pengurutan dan pencarian
                $query = "
                    SELECT ik.*, s.nama_ss, u.nama_user 
                    FROM `indikator kinerja` ik 
                    LEFT JOIN sasaran s ON ik.nama_ss = s.id 
                    LEFT JOIN user u ON ik.nama_user = u.id_user 
                    WHERE ik.nama_indikator LIKE ? OR u.nama_user LIKE ? 
                    OR ik.target LIKE ? OR ik.tahun_anggaran LIKE ? 
                    ORDER BY ik.id_ik DESC"; // Include numeric fields in search

                $stmt = $mysqli->prepare($query);
                $likeQuery = '%' . $searchQuery . '%';
                $stmt->bind_param('ssss', $likeQuery, $likeQuery, $likeQuery, $likeQuery);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result) {
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_indikator'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_user'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_ss'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['keterangan'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['tahun_anggaran'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['target'] ?? '') . "</td>";
                            echo "<td>
                                    <a href='edit_indi.php?id_ik=" . htmlspecialchars($row['id_ik'] ?? '') . "' class='btn btn-warning btn-sm'>Edit</a>
                                    <button class='btn btn-danger btn-sm btn-delete' data-id='" . htmlspecialchars($row['id_ik'] ?? '') . "'>Delete</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No data found.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Error executing query: " . $mysqli->error . "</td></tr>";
                }

                $stmt->close();
                $mysqli->close();
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
    <!-- /.container-fluid -->

  </div>
  <!-- End of Main Content -->

  <?php include 'footer.php'; ?>
</div>

<!-- Include SweetAlert -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script>
  $(document).ready(function() {
    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      var row = $(this).closest('tr');

      swal({
        title: "Apakah kamu yakin?",
        text: "PERINGATAN: Penghapusan catatan ini tidak dapat dibatalkan.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Iya, hapus itu!",
        cancelButtonText: "Tidak, batalkan!",
        closeOnConfirm: false
      }, function() {
        $.ajax({
          url: 'delete_indikator.php',
          type: 'POST',
          data: { id_ik: id },
          success: function(response) {
            if (response === 'success') {
              swal("Terhapus!", "Data berhasil dihapus.", "success");
              row.remove();
            } else {
              swal("Error!", "Failed to delete the record. Please try again.", "error");
            }
          },
          error: function(xhr, status, error) {
            console.error('Error occurred:', error);
            swal("Error!", "An error occurred: " + error, "error");
          }
        });
      });
    });

    <?php if ($successMessage): ?>
      swal("Berhasil!", "<?php echo $successMessage; ?>", "success");
    <?php endif; ?>
  });
</script>
