<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting Indikator Kinerja</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
        integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        .table-responsive {
            max-width: 100%;
            overflow-x: auto;
        }

        .form-group {
            margin-bottom: 0;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
</head>

<body class="bg-content">
    <main class="dashboard d-flex">
        <?php include 'koneksi.php';
        include 'header.php';
        include 'sidebar.php'; ?>

        <div class="container-fluid px-4">
            <div class="student-list-header d-flex justify-content-between align-items-center py-2">
                <div class="title h6 fw-bold">Setting Indikator Kinerja</div>
                <div class="btn-add d-flex gap-3 align-items-center">
                    <div class="short">
                        <i class="far fa-sort"></i>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered indikator">
                    <thead>
                        <tr class="align-middle">
                            <th>No</th>
                            <th style="width: 15%;">Nama Indikator</th>
                            <th>Keterangan Indikator</th>
                            <th>Nama Sasaran</th>
                            <th>Nama User</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include 'koneksi1.php';
                        $query = "SELECT ik.*, u.nama_user, s.nama_ss 
                                  FROM `indikator kinerja` ik 
                                  LEFT JOIN sasaran s ON ik.nama_ss = s.id
                                  LEFT JOIN user u ON ik.nama_user = u.id_user";
                        $result = $con->query($query);
                        $count = 1;
                        foreach ($result as $value):
                        ?>
                            <tr class="bg-white align-middle">
                                <td><?php echo $count; ?></td>
                                <td style="width: 15%;"><?php echo htmlspecialchars($value['nama_indikator']); ?></td>
                                <td><?php echo $value['status'] ? 'Aktif' : 'Nonaktif'; ?></td>
                                <td><?php echo htmlspecialchars($value['nama_ss'] ?? 'N/A'); ?></td>
                                <td>
                                    <div class="form-group">
                                        <select name="nama_user" class="form-control">
                                            <option value="">Pilih Nama User</option>
                                            <?php
                                            $queryUser = $con->query("SELECT * FROM `user`");
                                            while ($rowUser = $queryUser->fetch(PDO::FETCH_ASSOC)) {
                                                $selected = ($rowUser['nama_user'] == $value['nama_user']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($rowUser['nama_user']) . "' $selected>" . htmlspecialchars($rowUser['nama_user']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-<?php echo $value['status'] ? 'warning' : 'success'; ?>"
                                        onclick="confirmStatusChange(<?php echo $value['id_ik']; ?>, <?php echo $value['status']; ?>)">
                                        <?php echo $value['status'] ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php
                            $count++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="../js/script.js"></script>
    <script src="../js/bootstrap.bundle.js"></script>
    <script>
        function confirmStatusChange(id, currentStatus) {
            const action = currentStatus ? 'nonaktifkan' : 'aktifkan';
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: `Kamu akan ${action} indikator ini.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    const newStatus = currentStatus ? 0 : 1; // Toggle status
                    updateStatus(id, newStatus);
                }
            });
        }

        function updateStatus(id, status) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "", true); // Submit to the same page
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload(); // Reload the page on success
                }
            };
            xhr.send("id_ik=" + id + "&status=" + status); // Send the data
        }
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_ik = $_POST['id_ik'];
        $status = $_POST['status'];
        
        // Update the status in the database
        $updateQuery = "UPDATE `indikator kinerja` SET status = :status WHERE id_ik = :id";
        $stmt = $con->prepare($updateQuery);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id_ik);
        $stmt->execute();
    }
    ?>

    <?php include 'footer.php'; ?>
</body>

</html>
