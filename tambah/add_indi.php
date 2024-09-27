<?php
include 'koneksi.php'; // Pastikan file ini menginisialisasi $mysqli

// Fetch data for dropdowns
$sasaran_query = $mysqli->query("SELECT id, nama_ss FROM `sasaran`");
$user_query = $mysqli->query("SELECT id_user, nama_user FROM `user`");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and ensure proper data types
    $nama_indikator = $_POST['nama_indikator'] ?? '';
    $nama_ss = (int)($_POST['nama_ss'] ?? 0);
    $nama_user = (int)($_POST['nama_user'] ?? 0);
    $tahun_anggaran = (int)($_POST['tahun_anggaran'] ?? 0);
    $keterangan = $_POST['keterangan'] ?? '';
    $target = (int)($_POST['target'] ?? 0);

    // Prepare SQL statement for inserting data
    $stmt = $mysqli->prepare("INSERT INTO `indikator kinerja` (nama_indikator, nama_ss, nama_user, tahun_anggaran, keterangan, target) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($mysqli->error));
    }

    // Bind parameters
    $stmt->bind_param('siiiss', $nama_indikator, $nama_ss, $nama_user, $tahun_anggaran, $keterangan, $target);

    if ($stmt->execute()) {
        header("Location: indikator.php?add=success");
        exit();
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
    <title>Add Indikator</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#nama_ss').change(function() {
                var sasaranId = $(this).val();
                if (sasaranId) {
                    $.ajax({
                        url: 'get_sasaran_details.php',
                        type: 'GET',
                        data: { sasaran_id: sasaranId },
                        dataType: 'json',
                        success: function(response) {
                            if (response) {
                                $('#tahun_anggaran').val(response.tahun_anggaran);
                                $('#keterangan').val(response.keterangan);
                            } else {
                                $('#tahun_anggaran').val('');
                                $('#keterangan').val('');
                            }
                        },
                        error: function() {
                            alert('Error retrieving data.');
                        }
                    });
                } else {
                    $('#tahun_anggaran').val('');
                    $('#keterangan').val('');
                }
            });
        });
    </script>
</head>
<body>
<?php include 'header.php'; include 'sidebar.php'; ?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Indikator Kinerja</h1>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambahkan Indikator</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="form-group row">
                            <label for="nama_indikator" class="col-md-3 col-form-label">Nama Indikator:</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="nama_indikator" name="nama_indikator" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_ss" class="col-md-3 col-form-label">Nama Sasaran:</label>
                            <div class="col-md-9">
                                <select class="form-control" id="nama_ss" name="nama_ss" required>
                                    <option value="">Select Sasaran</option>
                                    <?php while ($row = $sasaran_query->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['nama_ss']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tahun_anggaran" class="col-md-3 col-form-label">Tahun Anggaran:</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="tahun_anggaran" name="tahun_anggaran" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="keterangan" class="col-md-3 col-form-label">Keterangan:</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="keterangan" name="keterangan" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_user" class="col-md-3 col-form-label">Nama User:</label>
                            <div class="col-md-9">
                                <select class="form-control" id="nama_user" name="nama_user" required>
                                    <option value="">Select User</option>
                                    <?php while ($row = $user_query->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($row['id_user']); ?>"><?php echo htmlspecialchars($row['nama_user']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="target" class="col-md-3 col-form-label">Target:</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="target" name="target" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Indikator</button>
                        <a href="indikator.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['add']) && $_GET['add'] == 'success'): ?>
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data telah berhasil ditambahkan.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    </script>
<?php endif; ?>

<?php include 'footer.php'; ?>
</body>
</html>
