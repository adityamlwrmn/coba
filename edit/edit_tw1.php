<?php
// Start session
session_start();

// Start output buffering
ob_start();

// Input sanitization function
function input($data) {
    return htmlspecialchars(trim($data));
}

// Include necessary files
include "conixion.php";
include 'header.php';
include 'sidebar.php';

// Check if connection is successful
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch data for indicators
$dropdown_query = "SELECT ik.nama_indikator, ik.keterangan, s.nama_ss, u.nama_user, ik.tahun_anggaran, ik.target 
                   FROM `indikator kinerja` ik
                   JOIN `sasaran` s ON ik.nama_ss = s.id 
                   JOIN `user` u ON ik.nama_user = u.id_user";

$dropdown_result = mysqli_query($con, $dropdown_query);
if (!$dropdown_result) {
    die("Query failed: " . mysqli_error($con));
}

$indikator_options = [];
while ($row = mysqli_fetch_assoc($dropdown_result)) {
    $indikator_options[] = $row;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = input($_POST["id"]);

    // Handle file deletion
    if (isset($_POST['delete_file'])) {
        $file_to_delete = input($_POST['delete_file']);
        $existing_files = explode(',', $_POST['existing_files']);
        $new_file_list = [];

        foreach ($existing_files as $existing_file) {
            if ($existing_file !== $file_to_delete) {
                $new_file_list[] = $existing_file;
            } else {
                // Delete file from server
                $file_path = "uploads/" . $existing_file;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }

        $file_data_dukung = implode(',', $new_file_list);

        // Update database with new file list
        $sql = "UPDATE `tw1` SET file_data_dukung = '$file_data_dukung' WHERE id = '$id'";
        mysqli_query($con, $sql);

        // Reload the current page to show updated data
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit();
    }

    // Handle other form data
    $nama_ik = input($_POST["nama_indikator"]);
    $keterangan = input($_POST["keterangan"]);
    $nama_user = input($_POST["nama_user"]);
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

    // Handle file uploads
    $upload_directory = "uploads/";
    $file_data_dukung = !empty($_POST['existing_files']) ? $_POST['existing_files'] : '';

    // Check if any new files are being uploaded
    if (!empty($_FILES['file_data_dukung']['name'][0])) {
        foreach ($_FILES['file_data_dukung']['name'] as $key => $file_name) {
            $file_tmp = $_FILES['file_data_dukung']['tmp_name'][$key];
            $file_size = $_FILES['file_data_dukung']['size'][$key];
            $file_error = $_FILES['file_data_dukung']['error'][$key];

            if ($file_error === UPLOAD_ERR_OK) {
                $file_path = $upload_directory . basename($file_name);

                // Check file size (limit to 5MB)
                if ($file_size > 5 * 1024 * 1024) {
                    echo "<div class='alert alert-danger'>File $file_name terlalu besar. Maksimal 5MB.</div>";
                    exit();
                }

                // Move file to the upload directory
                if (move_uploaded_file($file_tmp, $file_path)) {
                    // Tambahkan file baru ke dalam list file yang ada
                    $file_data_dukung .= ($file_data_dukung ? ',' : '') . $file_name;
                } else {
                    echo "<div class='alert alert-danger'>Gagal mengunggah file $file_name.</div>";
                    exit();
                }
            } else {
                echo "Error uploading file: " . $file_error . "<br>";
            }
        }
    }

    // Update data in the database
    $sql = "UPDATE `tw1` SET
            nama_ik = '$nama_ik',
            keterangan = '$keterangan',
            nama_user = '$nama_user', 
            sasaran = '$sasaran',
            target = '$target',
            realisasi = '$realisasi',
            satuan = '$satuan',
            bobot = '$bobot',
            capaian = '$capaian',
            penjelasan = '$penjelasan',
            progress_kegiatan = '$progress_kegiatan',
            kendala_permasalahan = '$kendala_permasalahan',
            strategi_tindak_lanjut = '$strategi_tindak_lanjut',
            tahun_anggaran = '$tahun_anggaran',
            file_data_dukung = '$file_data_dukung'
            WHERE id = '$id'";

    if (mysqli_query($con, $sql)) {
        // Set session variable for success
        $_SESSION['update_success'] = true;

        // Redirect to tw1.php after update
        header("Location: tw1.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Data gagal diperbarui. Error: " . mysqli_error($con) . "</div>";
    }
}

// Retrieve existing data for editing
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sqlEdit = "SELECT * FROM `tw1` WHERE id = $id";
    $resultEdit = mysqli_query($con, $sqlEdit);

    if ($resultEdit && mysqli_num_rows($resultEdit) > 0) {
        $editData = mysqli_fetch_assoc($resultEdit);
    } else {
        die("Data tidak ditemukan.");
    }
} else {
    die("ID tidak valid.");
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Data</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .form-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        .form-group label {
            flex: 0 0 200px;
            margin-right: 10px;
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
<body>
    <main>
        <div class="container">
            <h1>Edit Data Triwulan 1</h1>
            <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($editData['id']); ?>" />
                    <input type="hidden" name="existing_files" value="<?php echo htmlspecialchars($editData['file_data_dukung']); ?>" />

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Indikator </label>
                            <select name="nama_indikator" id="nama_indikator" class="form-control">
                                <?php
                                foreach ($indikator_options as $option) {
                                    $value = htmlspecialchars($option['nama_indikator']);
                                    $selected = ($value == htmlspecialchars($editData['nama_ik'])) ? 'selected' : '';
                                    echo "<option value='$value' $selected>$value</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Keterangan </label>
                            <input type="text" name="keterangan" id="keterangan" class="form-control" value="<?php echo htmlspecialchars($editData['keterangan']); ?>" readonly />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Sasaran</label>
                            <input type="text" name="nama_ss" id="nama_ss" class="form-control" value="<?php echo htmlspecialchars($editData['sasaran']); ?>" readonly />
                        </div>
                        <div class="form-group">
                            <label>Tahun Anggaran</label>
                            <input type="text" name="tahun_anggaran" id="tahun_anggaran" class="form-control" value="<?php echo htmlspecialchars($editData['tahun_anggaran']); ?>" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama User</label>
                            <input type="text" name="nama_user" id="nama_user" class="form-control" value="<?php echo htmlspecialchars($editData['nama_user']); ?>" readonly />
                        </div>
                        <div class="form-group">
                            <label>Target</label>
                            <input type="text" name="target" id="target" class="form-control" value="<?php echo htmlspecialchars($editData['target']); ?>" />
                        </div>
                    </div>

                    <!-- Additional fields -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Realisasi</label>
                            <input type="text" name="realisasi" id="realisasi" class="form-control" value="<?php echo htmlspecialchars($editData['realisasi']); ?>" />
                        </div>
                        <div class="form-group">
                            <label>Satuan</label>
                            <input type="text" name="satuan" id="satuan" class="form-control" value="<?php echo htmlspecialchars($editData['satuan']); ?>" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Bobot</label>
                            <input type="text" name="bobot" id="bobot" class="form-control" value="<?php echo htmlspecialchars($editData['bobot']); ?>" />
                        </div>
                        <div class="form-group">
                            <label>Capaian</label>
                            <input type="text" name="capaian" id="capaian" class="form-control" value="<?php echo htmlspecialchars($editData['capaian']); ?>" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Penjelasan</label>
                            <textarea name="penjelasan" id="penjelasan" class="form-control"><?php echo htmlspecialchars($editData['penjelasan']); ?></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Progress Kegiatan</label>
                            <textarea name="progress_kegiatan" id="progress_kegiatan" class="form-control"><?php echo htmlspecialchars($editData['progress_kegiatan']); ?></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Kendala Permasalahan</label>
                            <textarea name="kendala_permasalahan" id="kendala_permasalahan" class="form-control"><?php echo htmlspecialchars($editData['kendala_permasalahan']); ?></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Strategi Tindak Lanjut</label>
                            <textarea name="strategi_tindak_lanjut" id="strategi_tindak_lanjut" class="form-control"><?php echo htmlspecialchars($editData['strategi_tindak_lanjut']); ?></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <label>File Data Dukung:</label>
                        <?php
                        $existing_files = explode(',', $editData['file_data_dukung']);
                        foreach ($existing_files as $file) {
                            echo "<div class='form-group'>
                                    <span>$file</span>
                                    <button type='submit' name='delete_file' value='" . htmlspecialchars($file) . "' class='btn btn-danger'>Hapus</button>
                                  </div>";
                        }
                        ?>
                        <input type="file" name="file_data_dukung[]" multiple />
                    </div>

                    <button type="submit" class="btn btn-primary">Update Data</button>
                    <a href="tw1.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
