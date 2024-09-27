<?php
include 'koneksi.php';
include 'header.php';
include 'sidebar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sasaran Strategis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <style>
        .btn-delete {
            color: red;
        }
    </style>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                swal({
                    title: "Apakah kamu yakin?",
                    text: "PERINGATAN: Penghapusan data tidak dapat dibatalkan.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Iyaa, hapus itu!",
                    cancelButtonText: "Tidak, batal!",
                    closeOnConfirm: false
                }, function() {
                    $.ajax({
                        url: 'delete_sasaran.php',
                        type: 'POST',
                        data: { id: id },
                        success: function(response) {
                            if (response === 'success') {
                                swal("Terhapus!", "Data berhasil dihapus.", "success");
                                $('button[data-id="' + id + '"]').closest('tr').remove();
                            } else {
                                swal("Error!", "Gagal untuk menghapus. Silahkan coba lagi.", "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error occurred:', error);
                            swal("Error!", "An error occurred: " + error, "error");
                        }
                    });
                });
            });
        });
    </script>
</head>
<body>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
      <!-- Topbar Search -->
      <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">

          <div class="input-group-append">
            
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
            <h1 class="h3 mb-4 text-gray-800">Sasaran Strategis</h1>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sasaran List</h6>
                    <a href="tambah/add_sasaran.php" class="btn btn-primary">Add Data</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Keterangan</th>
                                    <th>Tahun</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
    <?php
    $query = "SELECT * FROM sasaran ORDER BY id DESC"; // Fetch data in descending order
    $result = $mysqli->query($query);
    $no = 1;

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_ss']) . "</td>";
        echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
        echo "<td>" . htmlspecialchars($row['tahun_anggaran']) . "</td>";
        echo "<td>
                <a href='edit/edit_sasaran.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-warning btn-sm'>Edit</a>
                <button class='btn btn-danger btn-sm btn-delete' data-id='" . htmlspecialchars($row['id']) . "'>Delete</button>
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
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>
