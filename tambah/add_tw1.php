<?php
// Start output buffering and session
ob_start();
session_start();

// Include necessary files
include "conixion.php";
include 'header.php';
include 'sidebar.php';

// Check database connection
if (!$con) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Fetch data for dropdown
$dropdown_query = "SELECT ik.id_ik, ik.nama_indikator, ik.keterangan, u.username AS nama_user, ik.tahun_anggaran, ik.target, s.nama_ss 
                   FROM `indikator kinerja` ik
                   JOIN `sasaran` s ON ik.nama_ss = s.id 
                   JOIN `user` u ON ik.nama_user = u.id_user";
$dropdown_result = mysqli_query($con, $dropdown_query);

if (!$dropdown_result) {
    die("Query gagal: " . mysqli_error($con));
}

$indikator_options = [];
$indikatorDetails = [];
while ($row = mysqli_fetch_assoc($dropdown_result)) {
    $indikator_options[] = $row;
    $indikatorDetails[$row['nama_indikator']] = [
        'nama_ss' => $row['nama_ss'],
        'target' => $row['target'],
        'keterangan' => $row['keterangan'],
        'nama_user' => $row['nama_user'],
        'tahun_anggaran' => $row['tahun_anggaran']
    ];
}

// Function to sanitize input
function input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $nama_ik = input($_POST["nama_indikator"]);
    $keterangan = input($_POST["keterangan"]);
    $sasaran = input($_POST["nama_ss"]);
    $target = input($_POST["target"]);
    $realisasi = input($_POST["realisasi"]);
    $satuan = input($_POST["satuan"]);
    $bobot = input($_POST["bobot"]);
    $capaian = input($_POST["capaian"]);
    $penjelasan = input($_POST["penjelasan"]);
    $progress_kegiatan = input($_POST["progress_kegiatan"]);
    $kendala_permasalahan = input($_POST["kendala_permasalahan"]);
    $strategi_tindak_lanjut = input($_POST["strategi_tindak_lanjut"]);
    $tahun_anggaran = input($_POST["tahun_anggaran"]);
    
    // Get username based on selected indicator
    $nama_user = $indikatorDetails[$nama_ik]['nama_user'] ?? '';

    // Handle file uploads
    $file_data_dukung = [];
    $upload_directory = "uploads/";

    if (!is_dir($upload_directory)) {
        mkdir($upload_directory, 0777, true);
    }

    if (!empty($_FILES['file_data_dukung']['name'][0])) {
        foreach ($_FILES['file_data_dukung']['name'] as $key => $file_name) {
            $file_tmp = $_FILES['file_data_dukung']['tmp_name'][$key];
            $file_size = $_FILES['file_data_dukung']['size'][$key];
            $file_error = $_FILES['file_data_dukung']['error'][$key];

            if ($file_error === UPLOAD_ERR_OK) {
                $file_path = $upload_directory . basename($file_name);

                // Check file size (max 5MB)
                if ($file_size > 5 * 1024 * 1024) {
                    echo "<div class='alert alert-danger'>File $file_name terlalu besar. Maksimal 5MB.</div>";
                    exit();
                }

                // Move uploaded file
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $file_data_dukung[] = $file_name;
                } else {
                    echo "<div class='alert alert-danger'>Gagal mengunggah file $file_name.</div>";
                    exit();
                }
            }
        }

        $file_data_dukung = implode(',', $file_data_dukung);
    }

    // Insert data into the database
    $sql = "INSERT INTO `tw1` (nama_ik, keterangan, nama_user, sasaran, target, realisasi, satuan, bobot, capaian, penjelasan, progress_kegiatan, kendala_permasalahan, strategi_tindak_lanjut, tahun_anggaran, file_data_dukung)
            VALUES ('$nama_ik', '$keterangan', '$nama_user', '$sasaran', '$target', '$realisasi', '$satuan', '$bobot', '$capaian', '$penjelasan', '$progress_kegiatan', '$kendala_permasalahan', '$strategi_tindak_lanjut', '$tahun_anggaran', '$file_data_dukung')";

    if (mysqli_query($con, $sql)) {
        $_SESSION['success_message'] = "Data berhasil ditambahkan.";
        header("Location: tw1.php"); // Redirect to data view page
        exit();
    } else {
        echo "<div class='alert alert-danger'>Data gagal disimpan. Error: " . mysqli_error($con) . "</div>";
    }
}

// End output buffering and flush output
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Triwulan 1</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
       .form-row {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
}

.form-group {
    margin-bottom: 15px;
    display: flex;
    flex-direction: row; /* Change to row for left alignment */
    align-items: center; /* Align items vertically centered */
}

.form-group label {
    flex: 0 0 200px; /* Set a fixed width for labels */
    margin-right: 10px; /* Add spacing between label and input */
}


        .form-group:last-child {
            margin-right: 0;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }

            .form-group {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body class="bg-content">
    <main class="dashboard d-flex">
        <div class="container-fluid px-4">
            <div class="add-indikator-kinerja-form">
                <h2>Tambah Data Triwulan 1</h2>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Nama Indikator:</label>
                        <div class="col-md-8">
                            <select name="nama_indikator" id="nama_indikator" class="form-control" required>
                                <option value="">Pilih Nama IK</option>
                                <?php
                                foreach ($indikator_options as $option) {
                                    $value = htmlspecialchars($option['nama_indikator']);
                                    echo "<option value='$value'>$value</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label small-label">Keterangan:</label>
                        <div class="col-md-8">
                            <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Keterangan otomatis terisi" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label small-label">Nama Sasaran:</label>
                        <div class="col-md-8">
                        <input type="text" id="nama_ss" name="nama_ss" class="form-control" placeholder="Nama sasaran ototmaatis" readonly />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label small-label">Tahun Anggaran:</label>
                        <div class="col-md-8">
                        <input type="text" id="tahun_anggaran" name="tahun_anggaran" class="form-control" placeholder="Tahun otomatis terisi" readonly />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Nama Sasaran:</label>
                        <div class="col-md-8">
                        <input type="text" id="nama_user" name="nama_user" class="form-control" placeholder="Nama user otomatis terisi" value="<?php echo isset($nama_user) ? htmlspecialchars($nama_user) : ''; ?>" readonly />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label small-label">Target:</label>
                        <div class="col-md-8">
                        <input type="number" name="target" id="target" class="form-control" placeholder="Masukkan Target" readonly />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label small-label">Realisasi:</label>
                        <div class="col-md-8">
                            <input type="number" id="realissasi" name="realissasi" class="form-control" placeholder="Masukan realisasi"  />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label small-label">Satuan:</label>
                        <div class="col-md-8">
                            <input type="text" name="satuan" class="form-control" placeholder="Masukkan satuan" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Bobot:</label>
                        <div class="col-md-8">
                        <input type="text" name="bobot" class="form-control" placeholder="Masukkan bobot" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label small-label">Capaian:</label>
                        <div class="col-md-8">
                            <input type="text" name="capaian" class="form-control" placeholder="Masukkan Capaian" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label small-label">Penjelasan:</label>
                        <div class="col-md-8">
                            <input type="text" name="penjelasan" id="penjelasan" class="form-control" placeholder="Masukkan penjelasan" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label small-label">Kendala Permasalahan:</label>
                        <div class="col-md-8">
                            <input type="text" name="kendala_permasalahan" class="form-control" placeholder="Masukkan Kendala Permasalahan"  />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label small-label">Strategi Tindak Lanjut:</label>
                        <div class="col-md-8">
                            <input type="text" name="strategi_tindak_lanjut" class="form-control" placeholder="Masukkan Strategi Tindak Lanjut" required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label small-label">Progress Kegiatan:</label>
                        <div class="col-md-8">
                            <input type="text" name="progress_kegiatan" class="form-control" placeholder="Masukkan Progress Kegiatan" required />
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">File Data Dukung:</label>
                        <div class="col-md-8">
                            <input type="file" name="file_data_dukung[]" class="form-control-file" multiple />
                            <small class="form-text text-muted">Masukan File</small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="tw1.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>

    <script>
        const indikatorDetails = <?php echo json_encode($indikatorDetails); ?>;

        document.getElementById('nama_indikator').addEventListener('change', function() {
            const selectedIndikator = this.value;
            const targetField = document.getElementById('target');
            const tahunAnggaranField = document.getElementById('tahun_anggaran');
            const namaSSField = document.getElementById('nama_ss');
            const namaUserField = document.getElementById('nama_user');
            const keteranganField = document.getElementById('keterangan');

            if (indikatorDetails[selectedIndikator]) {
                const details = indikatorDetails[selectedIndikator];
                namaSSField.value = details.nama_ss;
                targetField.value = details.target;
                tahunAnggaranField.value = details.tahun_anggaran;
                namaUserField.value = details.nama_user;
                keteranganField.value = details.keterangan;
            } else {
                namaSSField.value = '';
                targetField.value = '';
                tahunAnggaranField.value = '';
                namaUserField.value = '';
                keteranganField.value = '';
            }
        });
    </script>
</body>
</html>
