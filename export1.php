<?php
include 'koneksi1.php';

$result = [];
$count = 1;

// Query untuk mendapatkan data
$query = "SELECT * FROM `tw1`";
$stmt = $con->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mendapatkan tahun anggaran yang unik
$yearQuery = "SELECT DISTINCT tahun_anggaran FROM `tw1` ORDER BY tahun_anggaran DESC";
$yearStmt = $con->prepare($yearQuery);
$yearStmt->execute();
$years = $yearStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Triwulan 1</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
</head>
<body>
    <div class="container mt-4">
        <a href="tw1.php" class="btn btn-primary mb-2">Kembali</a>
        <h2>Data Triwulan 1</h2>
        <div id="export-info" style="margin-bottom: 15px;"></div>
        <div class="form-group">
            <label for="yearFilter">Filter Tahun Anggaran:</label>
            <select id="yearFilter" class="form-control">
                <option value="">Pilih Tahun</option>
                <?php foreach ($years as $year) : ?>
                    <option value="<?php echo htmlspecialchars($year); ?>"><?php echo htmlspecialchars($year); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="columnFilter">Pilih Kolom:</label>
            <div id="columnFilter">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col0" data-column="0" checked>
                            <label class="form-check-label" for="col0">No</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col1" data-column="1" checked>
                            <label class="form-check-label" for="col1">Nama Indikator</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col2" data-column="2" checked>
                            <label class="form-check-label" for="col2">Keterangan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col3" data-column="3" checked>
                            <label class="form-check-label" for="col3">Sasaran</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col4" data-column="4" checked>
                            <label class="form-check-label" for="col4">User</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col5" data-column="5" checked>
                            <label class="form-check-label" for="col5">Tahun</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col6" data-column="6" checked>
                            <label class="form-check-label" for="col6">Target</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col7" data-column="7" checked>
                            <label class="form-check-label" for="col7">Realisasi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col8" data-column="8" checked>
                            <label class="form-check-label" for="col8">Satuan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col9" data-column="9" checked>
                            <label class="form-check-label" for="col9">Bobot</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col10" data-column="10" checked>
                            <label class="form-check-label" for="col10">Capaian</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col11" data-column="11" checked>
                            <label class="form-check-label" for="col11">Penjelasan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col12" data-column="12" checked>
                            <label class="form-check-label" for="col12">Progres Kegiatan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col13" data-column="13" checked>
                            <label class="form-check-label" for="col13">Kendala Masalah</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col14" data-column="14" checked>
                            <label class="form-check-label" for="col14">Strategi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="col15" data-column="15" checked>
                            <label class="form-check-label" for="col15">File Data Dukung</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="data-tables datatable-dark">
            <table class="table table-bordered" id="mauexport">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>Nama Indikator</th>
                        <th>Keterangan</th>
                        <th>Sasaran</th>
                        <th>User</th>
                        <th>Tahun</th>
                        <th>Target</th>
                        <th>Realisasi</th>
                        <th>Satuan</th>
                        <th>Bobot</th>
                        <th>Capaian</th>
                        <th>Penjelasan</th>
                        <th>Progres Kegiatan</th>
                        <th>Kendala Masalah</th>
                        <th>Strategi</th>
                        <th>File Data Dukung</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $row) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($count++); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_ik']); ?></td>
                            <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                            <td><?php echo htmlspecialchars($row['sasaran']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                            <td><?php echo htmlspecialchars($row['tahun_anggaran']); ?></td>
                            <td><?php echo htmlspecialchars($row['target']); ?></td>
                            <td><?php echo htmlspecialchars($row['realisasi']); ?></td>
                            <td><?php echo htmlspecialchars($row['satuan']); ?></td>
                            <td><?php echo htmlspecialchars($row['bobot']); ?></td>
                            <td><?php echo htmlspecialchars($row['capaian']); ?></td>
                            <td><?php echo htmlspecialchars($row['penjelasan']); ?></td>
                            <td><?php echo htmlspecialchars($row['progress_kegiatan']); ?></td>
                            <td><?php echo htmlspecialchars($row['kendala_permasalahan']); ?></td>
                            <td><?php echo htmlspecialchars($row['strategi_tindak_lanjut']); ?></td>
                            <td class="file-data-dukung"><?php echo htmlspecialchars($row['file_data_dukung']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.4/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.4/vfs_fonts.js"></script>
    
    <script>
        $(document).ready(function() {
            var table = $('#mauexport').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        text: 'Ekspor PDF',
                        filename: 'Data_Triwulan_1',
                        title: 'Data Triwulan 1',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function(doc) {
                            doc.content.unshift({
                                text: '',
                                fontSize: 16,
                                bold: true,
                                margin: [0, 0, 0, 10],
                                alignment: 'center'
                            });
                            doc.styles.tableHeader.fontSize = 8;
                            doc.styles.tableBodyEven.fontSize = 6;
                            doc.styles.tableBodyOdd.fontSize = 6;
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Ekspor Excel',
                        filename: 'Data_Triwulan_1',
                        title: 'Data Triwulan 1',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });

            $('#yearFilter').on('change', function() {
                var year = $(this).val();
                table.column(5).search(year).draw();
            });

            $('input[type="checkbox"]').on('change', function() {
                var column = table.column($(this).data('column'));
                column.visible(!column.visible());
            });
        });
    </script>
</body>
</html>
