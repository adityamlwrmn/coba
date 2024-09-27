<?php
session_start();
include 'koneksi1.php';

// Fetch years from the database
function getYearsFromDatabase($pdo) {
    $query = "SELECT DISTINCT tahun_anggaran FROM sasaran";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Check if POST data is received
$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $selected_year = $_POST['selected_year'];

    $query = "SELECT * FROM `user` WHERE `username` = ? AND `password` = ?";
    if (isset($con)) {
        $stmt = $con->prepare($query);
        $stmt->execute([$username, $password]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['status'] == 0) {
                echo "<script>alert('Maaf, akun Anda saat ini non-aktif. Silakan hubungi administrator untuk informasi lebih lanjut.');</script>";
            } else {
                $_SESSION['username'] = $username;
                $_SESSION['nama'] = $user['nama_user'];
                $_SESSION['selected_year'] = $selected_year;

                header("Location: dashboard_user.php");
                exit();
            }
        } else {
            $error_message = 'Username atau password salah. Silakan coba lagi.';
        }
    } else {
        echo "Database connection not established. Check your connection settings.";
    }
}

$years = getYearsFromDatabase($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 360px;
            position: relative;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input[type="text"],
        .form-group input[type="password"],
        .form-group select {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group input[type="submit"] {
            padding: 10px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
            display: block;
            width: 100%;
        }
        .form-group input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .form-group .select-year-container {
            margin-top: 15px;
            text-align: center;
        }
        .form-group .select-year-container select,
        .form-group .select-year-container button {
            font-size: 16px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            cursor: pointer;
            display: block;
            margin-top: 5px;
            width: 100%;
            box-sizing: border-box;
        }
        .form-group .select-year-container select {
            margin-bottom: 10px;
        }
        .form-group .select-year-container button {
            background-color: #28a745;
            color: #fff;
            border: none;
            transition: background-color 0.3s;
        }
        .form-group .select-year-container button:hover {
            background-color: #218838;
        }
        .form-group .selected-year {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
            text-align: center;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            text-align: center;
        }
        .login-container a {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .login-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login For User</h2>
        <div class="form-group">
            <div id="selected-year" class="selected-year">Select a year</div>
            <div class="select-year-container">
                <select id="year-select">
                    <?php
                    foreach ($years as $year) {
                        echo "<option value='$year'>$year</option>";
                    }
                    ?>
                </select>
                <button type="button" onclick="handleYearSelection()">Pilih Tahun</button>
            </div>
        </div>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="hidden" id="selected_year_input" name="selected_year" value="">
            <div class="form-group">
                <input type="submit" value="Login">
            </div>
            <div class="error-message" id="error-message"></div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script>
        function handleYearSelection() {
            var year = document.getElementById('year-select').value;
            document.getElementById('selected-year').innerText = "Selected Year: " + year;
            document.getElementById('selected_year_input').value = year;
        }

        <?php if ($error_message): ?>
            // Show SweetAlert on error
            swal({
                title: "Error!",
                text: "<?php echo addslashes($error_message); ?>",
                type: "error",
                confirmButtonText: "OK"
            });
        <?php endif; ?>

        document.querySelector('form').onsubmit = function() {
            var username = document.getElementById('username').value.trim();
            var password = document.getElementById('password').value.trim();

            if (username === '' || password === '') {
                document.getElementById('error-message').innerText = 'Username dan password harus diisi';
                return false; // Prevent form submission
            } else {
                return true; // Allow form submission
            }
        }
    </script>
</body>
</html>
