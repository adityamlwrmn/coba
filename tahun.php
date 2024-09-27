<?php
include 'koneksi.php'; // Ensure this file establishes $mysqli
include 'header.php';
include 'sidebar.php';
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
        <!-- Add your navbar items here -->
      </ul>
    </nav>
    <!-- End of Topbar -->

    <!-- Begin Page Content -->
    <div class="container-fluid">

      <!-- Page Heading -->
      <h1 class="h3 mb-4 text-gray-800">Tahun Anggaran</h1>
      <a href="tambah/add_tahun.php" class="btn btn-primary">Tambah Data</a>
      
      <!-- DataTales Example -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Tahun Anggaran List</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Keterangan</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Fetch data from the database
                $query = "SELECT * FROM tahun_anggaran";
                $result = $mysqli->query($query);

                $no = 1; // Initialize row number

                while ($row = $result->fetch_assoc()) {
                  $statusText = ($row['status_ta'] == 1) ? 'Aktif' : 'Nonaktif';
                  $statusClass = ($row['status_ta'] == 1) ? 'btn-success' : 'btn-danger';
                  
                  echo "<tr>";
                  echo "<td>" . $no++ . "</td>";
                  echo "<td>" . htmlspecialchars($row['nama_ta']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
                  echo "<td>
                          <form method='post' action='update_status.php' style='display: inline;'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($row['Id']) . "'>
                            <button type='submit' class='btn $statusClass'>" . htmlspecialchars($statusText) . "</button>
                          </form>
                        </td>";
                  echo "<td>
                          <a href='edit/edit_tahun.php?Id=" . htmlspecialchars($row['Id']) . "' class='btn btn-warning btn-sm'>Edit</a>
                          <a href='hapus/delete_tahun.php?Id=" . htmlspecialchars($row['Id']) . "' class='btn btn-danger btn-sm'>Delete</a>
                        </td>";
                  echo "</tr>";
                }

                $result->free();
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
<!-- End of Content Wrapper -->

<!-- Add this style for the status button -->
<style>
  .btn-success {
    background-color: green; /* Green background for "Aktif" status */
    color: white; /* White text */
    border: none; /* Remove borders */
    padding: 0.5rem 1rem; /* Add some padding */
    border-radius: 0.25rem; /* Rounded corners */
    cursor: pointer; /* Pointer cursor on hover */
  }
  .btn-success:hover {
    background-color: darkgreen; /* Darker green on hover */
  }
  .btn-danger {
    background-color: red; /* Red background for "Nonaktif" status */
    color: white; /* White text */
    border: none; /* Remove borders */
    padding: 0.5rem 1rem; /* Add some padding */
    border-radius: 0.25rem; /* Rounded corners */
    cursor: pointer; /* Pointer cursor on hover */
  }
  .btn-danger:hover {
    background-color: darkred; /* Darker red on hover */
  }
</style>

</body>
</html>
