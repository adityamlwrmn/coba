<?php
// Start output buffering
ob_start();

// Input sanitization function
function input($data)
{
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
        $sql = "UPDATE `tw3` SET file_data_dukung = '$file_data_dukung' WHERE id = '$id'";
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
    $file_data_dukung = '';

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

    // If there are existing files, concatenate with new files
    $existing_files = !empty($editData['file_data_dukung']) ? $editData['file_data_dukung'] : '';
    $file_data_dukung = !empty($file_data_dukung) ? ($existing_files ? $existing_files . ',' . $file_data_dukung : $file_data_dukung) : $existing_files;

    // Update data in the database
    $sql = "UPDATE `tw3` SET
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
        echo "<div class='alert alert-success'>Data berhasil diperbarui!</div>";

        // Redirect to tw3.php after update
        header("Location: tw3.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Data gagal diperbarui. Error: " . mysqli_error($con) . "</div>";
    }
}

// Retrieve existing data for editing
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sqlEdit = "SELECT * FROM `tw3` WHERE id = $id";
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

<body>
    <main>
        <div class="container">
            <h1>Edit Data Triwulan 3</h1>
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
                            <input type="text" name="tahun_anggaran" id="tahun_anggaran" class="form-control" value="<?php echo htmlspecialchars($editData['tahun_anggaran']); ?>" readonly/>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama User</label>
                            <input type="text" id="nama_user" name="nama_user" class="form-control" value="<?php echo htmlspecialchars($editData['nama_user']); ?>" readonly />
                        </div>
                        <div class="form-group">
                            <label>Target</label>
                            <input type="text" name="target" id="target" class="form-control" value="<?php echo htmlspecialchars($editData['target']); ?>" readonly/>
                        </div>
                    </div>

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
                            <input type="text" name="penjelasan" id="penjelasan" class="form-control" value="<?php echo htmlspecialchars($editData['penjelasan']); ?>" />
                        </div>
                        <div class="form-group">
                            <label>Progress Kegiatan</label>
                            <input type="text" name="progress_kegiatan" id="progress_kegiatan" class="form-control" value="<?php echo htmlspecialchars($editData['progress_kegiatan']); ?>" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Kendala Permasalahan</label>
                            <input type="text" name="kendala_permasalahan" id="kendala_permasalahan" class="form-control" value="<?php echo htmlspecialchars($editData['kendala_permasalahan']); ?>" />
                        </div>
                        <div class="form-group">
                            <label>Strategi Tindak Lanjut</label>
                            <input type="text" name="strategi_tindak_lanjut" id="strategi_tindak_lanjut" class="form-control" value="<?php echo htmlspecialchars($editData['strategi_tindak_lanjut']); ?>" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>File Data Dukung :</label>
                            <input type="file" name="file_data_dukung[]" multiple />
                            <p>File yang sudah ada:
                                <?php
                                if (!empty($editData['file_data_dukung'])) {
                                    $files = explode(',', $editData['file_data_dukung']);
                                    foreach ($files as $file) {
                                        echo htmlspecialchars($file) . " <button type='button' class='delete-file' data-file='" . htmlspecialchars($file) . "'>Hapus</button><br>";
                                    }
                                } else {
                                    echo "Tidak ada file yang diunggah.";
                                }
                                ?>
                            </p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Data</button>
                    <a href="tw3.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.delete-file').on('click', function() {
                var fileToDelete = $(this).data('file');
                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan menghapus file: " + fileToDelete,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'delete_file',
                            value: fileToDelete
                        }).appendTo(form);
                        form.submit(); // Submit the form to delete the file
                    }
                });
            });

            $('#nama_indikator').change(function() {
                var namaIndikator = $(this).val();

                if (namaIndikator) {
                    $.ajax({
                        url: 'get_indicator_details.php',
                        type: 'GET',
                        data: {
                            nama_indikator: namaIndikator
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data) {
                                $('#keterangan').val(data.keterangan);
                                $('#nama_ss').val(data.nama_ss);
                                $('#nama_user').val(data.nama_user);
                                $('#tahun_anggaran').val(data.tahun_anggaran);
                                $('#target').val(data.target);
                            } else {
                                $('#keterangan').val('');
                                $('#nama_ss').val('');
                                $('#nama_user').val('');
                                $('#tahun_anggaran').val('');
                                $('#target').val('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error: ', xhr.responseText);
                            alert('Error retrieving data: ' + xhr.status + ' ' + error);
                        }
                    });
                } else {
                    $('#keterangan').val('');
                    $('#nama_ss').val('');
                    $('#nama_user').val('');
                    $('#tahun_anggaran').val('');
                    $('#target').val('');
                }
            });
        });
    </script>
</body>

</html>