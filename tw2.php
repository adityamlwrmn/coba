<?php
include 'koneksi.php';
include 'header.php';
include 'sidebar.php';

// Fetch options for filters from the database
$tahun_query = "SELECT DISTINCT tahun_anggaran FROM tw2";
$tahun_result = $mysqli->query($tahun_query);
$tahun_options = [];
while ($row = $tahun_result->fetch_assoc()) {
    $tahun_options[] = $row['tahun_anggaran'];
}

$indikator_query = "SELECT DISTINCT nama_ik FROM tw2";
$indikator_result = $mysqli->query($indikator_query);
$indikator_options = [];
while ($row = $indikator_result->fetch_assoc()) {
    $indikator_options[] = $row['nama_ik'];
}

$sasaran_query = "SELECT DISTINCT sasaran FROM tw2";
$sasaran_result = $mysqli->query($sasaran_query);
$sasaran_options = [];
while ($row = $sasaran_result->fetch_assoc()) {
    $sasaran_options[] = $row['sasaran'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triwulan Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <ul class="navbar-nav ml-auto"></ul>
            </nav>

            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Triwulan 2</h1>
                <a href="tambah/add_tw2.php" class="btn btn-primary">Tambah Data</a>
                <a href="export.php" class="btn btn-success">Export</a>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Triwulan</h6>
                        <div id="filterContainer">
                            <select id="tahunDropdown" class="form-select">
                                <option value="">Pilih Tahun</option>
                                <?php foreach ($tahun_options as $tahun): ?>
                                    <option value="<?php echo htmlspecialchars($tahun); ?>"><?php echo htmlspecialchars($tahun); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="indikatorDropdown" class="form-select">
                                <option value="">Pilih Indikator</option>
                                <?php foreach ($indikator_options as $indikator): ?>
                                    <option value="<?php echo htmlspecialchars($indikator); ?>"><?php echo htmlspecialchars($indikator); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="sasaranDropdown" class="form-select">
                                <option value="">Pilih Sasaran</option>
                                <?php foreach ($sasaran_options as $sasaran): ?>
                                    <option value="<?php echo htmlspecialchars($sasaran); ?>"><?php echo htmlspecialchars($sasaran); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="entriesContainer" class="mt-2">
                            <label for="entriesSelect">Show entries:</label>
                            <select id="entriesSelect" class="form-select" style="display:inline-block; width:auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Indikator</th>
                                        <th>Keterangan Indikator</th>
                                        <th>Nama Sasaran</th>
                                        <th>Tahun Anggaran</th>
                                        <th>Nama User</th>
                                        <th>Target</th>
                                        <th>Realisasi</th>
                                        <th>Satuan</th>
                                        <th>Bobot</th>
                                        <th>Capaian</th>
                                        <th>Penjelasan</th>
                                        <th>Progress Kegiatan</th>
                                        <th>Kendala Permasalahan</th>
                                        <th>Strategi Tindak Lanjut</th>
                                        <th class="file-data-column">File Data Dukung</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody"></tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <button id="prevButton" class="btn btn-secondary" disabled>Previous</button>
                            <div id="paginationContainer" class="text-center"></div>
                            <button id="nextButton" class="btn btn-secondary">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'footer.php'; ?>

            <script>
                $(document).ready(function() {
                    let currentPage = 1;
                    let entriesPerPage = 10;
                    let totalPages = 1;

                    function fetchData(page = 1, entries = 10) {
                        const tahun = $('#tahunDropdown').val();
                        const indikator = $('#indikatorDropdown').val();
                        const sasaran = $('#sasaranDropdown').val();

                        $.ajax({
                            url: 'fetch_data2.php',
                            type: 'POST',
                            data: {
                                tahun: tahun,
                                indikator: indikator,
                                sasaran: sasaran,
                                page: page,
                                entries: entries
                            },
                            success: function(response) {
                                const result = JSON.parse(response);
                                $('#tableBody').html(result.data);
                                totalPages = result.totalPages;
                                setupPagination();
                                updateButtons();
                            }
                        });
                    }

                    function setupPagination() {
                        let paginationHtml = '';
                        for (let i = 1; i <= totalPages; i++) {
                            paginationHtml += `<button class="btn btn-link pagination-button" data-page="${i}">${i}</button>`;
                        }
                        $('#paginationContainer').html(paginationHtml);
                    }

                    function updateButtons() {
                        $('#prevButton').prop('disabled', currentPage === 1);
                        $('#nextButton').prop('disabled', currentPage === totalPages);
                    }

                    $('#tahunDropdown, #indikatorDropdown, #sasaranDropdown, #entriesSelect').change(function() {
                        entriesPerPage = $('#entriesSelect').val();
                        fetchData(currentPage, entriesPerPage);
                    });

                    $('#paginationContainer').on('click', '.pagination-button', function() {
                        currentPage = $(this).data('page');
                        fetchData(currentPage, entriesPerPage);
                    });

                    $('#prevButton').click(function() {
                        if (currentPage > 1) {
                            currentPage--;
                            fetchData(currentPage, entriesPerPage);
                        }
                    });

                    $('#nextButton').click(function() {
                        if (currentPage < totalPages) {
                            currentPage++;
                            fetchData(currentPage, entriesPerPage);
                        }
                    });


                    $('#tableBody').on('click', '.delete-button', function() {
                        const id = $(this).data('id');
                        Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: 'Anda tidak akan bisa membatalkan ini!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, hapus ini!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: 'delete_tw2.php',
                                    type: 'POST',
                                    data: {
                                        id: id
                                    },
                                    success: function(response) {
                                        if (response === "success") {
                                            Swal.fire('Dihapus!', 'Data Anda telah dihapus.', 'success');
                                            fetchData(currentPage, entriesPerPage);
                                        } else {
                                            Swal.fire('Gagal!', 'Gagal menghapus data.', 'error');
                                        }
                                    },
                                    error: function() {
                                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                                    }
                                });
                            }
                        });
                    });


                    fetchData(currentPage, entriesPerPage);
                });
            </script>
        </div>
    </div>
</body>

</html>