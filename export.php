<?php
include 'koneksi1.php';

$result = [];
$count = 1;

// Query untuk mendapatkan data
$query = "SELECT * FROM `tw2`";
$stmt = $con->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mendapatkan tahun anggaran yang unik
$yearQuery = "SELECT DISTINCT tahun_anggaran FROM `tw2` ORDER BY tahun_anggaran DESC";
$yearStmt = $con->prepare($yearQuery);
$yearStmt->execute();
$years = $yearStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Triwulan 2</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
    <style>
        /* Custom styles can be added here */
    </style>
</head>
<body>
    <div class="container mt-4">
        <a href="tw2.php" class="btn btn-primary mb-2">Kembali</a>
        <h2>Data Triwulan 2</h2>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    
    <script>
        $(document).ready(function() {
            var table = $('#mauexport').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        text: 'Ekspor PDF',
                        filename: 'Data_Triwulan_2',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        customize: function(doc) {
                            var table = doc.content[1].table;

                            // Set custom widths for columns
                            table.widths = [15, 30, 10, 30, 20, 20, 10, 10, 10, 10, 20, 20, 20, 20, 20, 0];

                            // Adjust the font size
                            doc.styles.tableHeader.fontSize = 8;
                            doc.styles.tableBodyEven.fontSize = 6;
                            doc.styles.tableBodyOdd.fontSize = 6;
                            
                            // Fix column splitting issue
                            var body = [];
                            var headers = table.body[0].slice();
                            body.push(headers); // Add headers to the body

                            // Add rows to the body
                            for (var i = 1; i < table.body.length; i++) {
                                var row = table.body[i].slice();
                                body.push(row);
                            }

                            doc.content = [{
                                table: {
                                    body: body
                                },
                                layout: 'noBorders'
                            }];
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Ekspor Excel',
                        filename: 'Data_Triwulan_2',
                        title: 'Data Triwulan 2',
                        exportOptions: {
                            columns: ':visible' // Export only visible columns
                        }
                    }
                ]
            });

            // Filter berdasarkan tahun
            $('#yearFilter').on('change', function() {
                var year = $(this).val();
                table.column(5).search(year).draw(); // Kolom 5 adalah tahun
            });

            // Filter berdasarkan kolom yang dipilih
            $('input[type="checkbox"]').on('change', function() {
                var column = table.column($(this).data('column'));
                column.visible(!column.visible());
            });
        });
    </script>
</body>
</html>

