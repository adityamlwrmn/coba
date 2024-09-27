<?php
// Start output buffering
ob_start();

// Include necessary files
include "conixion.php";
include 'header.php';
include 'sidebar_user.php';

// Check if connection is successful
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Function to sanitize inputs
function input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data
    $id = input($_POST["id"]);
    $nama_indikator = input($_POST["nama_indikator"]);
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

    // Retrieve existing file data
    $sqlExistingFiles = "SELECT file_data_dukung FROM `tw2` WHERE id = '$id'";
    $resultExistingFiles = mysqli_query($con, $sqlExistingFiles);
    if ($resultExistingFiles && mysqli_num_rows($resultExistingFiles) > 0) {
        $existingFiles = mysqli_fetch_assoc($resultExistingFiles)['file_data_dukung'];
    } else {
        $existingFiles = '';
    }

    // Handle file uploads
    $file_data_dukung = $existingFiles;
    $upload_directory = "uploads/";

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
                    $file_data_dukung .= ($file_data_dukung ? ',' : '') . $file_name;
                } else {
                    echo "<div class='alert alert-danger'>Gagal mengunggah file $file_name.</div>";
                    exit();
                }
            } else {
                echo "Error uploading file: " . $file_error . "<br>"; // Debugging line
            }
        }
    }

    // Update data in the database
    $sql = "UPDATE `tw2` SET
            nama_ik = '$nama_indikator',
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
        echo "<div class='alert alert-success'>Data berhasil diperbarui!</div>";
        header("Location: twUser2.php?id=$id");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Data gagal diperbarui. Error: " . mysqli_error($con) . "</div>";
    }
}

// Retrieve existing data for editing
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sqlEdit = "SELECT * FROM `tw2` WHERE id = $id";
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
</head>
<body>
    <main>
        <div class="container">
            <h1>Edit Data Triwulan 2</h1>
            <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($editData['id']); ?>" readonly />
                    
                    <div class="form-group">
                        <label>Nama Indikator:</label>
                        <input type="text" name="nama_indikator" id="nama_indikator" class="form-control" value="<?php echo htmlspecialchars($editData['nama_ik']); ?>" readonly />
                    </div>

                    <div class="form-group">
                        <label>Keterangan:</label>
                        <input type="text" name="keterangan" id="keterangan" class="form-control" value="<?php echo htmlspecialchars($editData['keterangan']); ?>" readonly />
                    </div>
                    
                    <div class="form-group">
                        <label>Nama User:</label>
                        <input type="text" id="nama_user" name="nama_user" class="form-control" value="<?php echo htmlspecialchars($editData['nama_user']); ?>" readonly />
                    </div>

                    <div class="form-group">
                        <label>Nama Sasaran:</label>
                        <input type="text" id="nama_ss" name="nama_ss" class="form-control" value="<?php echo htmlspecialchars($editData['sasaran']); ?>" readonly />
                    </div>

                    <div class="form-group">
                        <label>Tahun Anggaran:</label>
                        <input type="text" id="tahun_anggaran" name="tahun_anggaran" class="form-control" value="<?php echo htmlspecialchars($editData['tahun_anggaran']); ?>" readonly />
                    </div>

                    <div class="form-group">
                        <label>Target:</label>
                        <input type="text" name="target" id="target" class="form-control" value="<?php echo htmlspecialchars($editData['target']); ?>" readonly />
                    </div>

                    <div class="form-group">
                        <label>Realisasi:</label>
                        <input type="text" name="realisasi" id="realisasi" class="form-control" value="<?php echo htmlspecialchars($editData['realisasi']); ?>" />
                    </div>

                    <div class="form-group">
                        <label>Satuan:</label>
                        <input type="text" name="satuan" id="satuan" class="form-control" value="<?php echo htmlspecialchars($editData['satuan']); ?>" />
                    </div>

                    <div class="form-group">
                        <label>Bobot:</label>
                        <input type="text" name="bobot" id="bobot" class="form-control" value="<?php echo htmlspecialchars($editData['bobot']); ?>" />
                    </div>

                    <div class="form-group">
                        <label>Capaian:</label>
                        <input type="text" name="capaian" id="capaian" class="form-control" value="<?php echo htmlspecialchars($editData['capaian']); ?>" />
                    </div>

                    <div class="form-group">
                        <label>Penjelasan:</label>
                        <textarea name="penjelasan" id="penjelasan" class="form-control"><?php echo htmlspecialchars($editData['penjelasan']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Progress Kegiatan:</label>
                        <textarea name="progress_kegiatan" id="progress_kegiatan" class="form-control"><?php echo htmlspecialchars($editData['progress_kegiatan']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Kendala Permasalahan:</label>
                        <textarea name="kendala_permasalahan" id="kendala_permasalahan" class="form-control"><?php echo htmlspecialchars($editData['kendala_permasalahan']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Strategi Tindak Lanjut:</label>
                        <textarea name="strategi_tindak_lanjut" id="strategi_tindak_lanjut" class="form-control"><?php echo htmlspecialchars($editData['strategi_tindak_lanjut']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Upload File Data Dukung:</label>
                        <input type="file" name="file_data_dukung[]" multiple class="form-control" />
                    </div>

                    <button type="submit" class="btn btn-primary">Update Data</button>
                    <a href="twUser1.php" class="btn btn-secondary">Cancel</a>
                </form>

                <h5>File Data Dukung:</h5>
                <?php
                $files = explode(',', $editData['file_data_dukung']);
                foreach ($files as $file) {
                    if ($file) {
                        echo "<div>
                            <a href='uploads/" . htmlspecialchars($file) . "'>" . htmlspecialchars($file) . "</a>
                            <form action='deleteuser1.php' method='post' style='display:inline;'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($editData['id']) . "' />
                                <input type='hidden' name='file_name' value='" . htmlspecialchars($file) . "' />
                                <button type='submit' class='btn btn-danger btn-sm'>Hapus</button>
                            </form>
                        </div>";
                    }
                }
                ?>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
