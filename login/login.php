<?php
session_start();
include 'koneksi1.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM `users` WHERE `Email` = ? AND `password` = ?";
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

                $query_tri = "SELECT * FROM `indikator kinerja` WHERE `nama_user` = ?";
                $stmt_tri = $con->prepare($query_tri);
                $stmt_tri->execute([$user['nama_user']]);
                $_SESSION['triwulan2_data'] = $stmt_tri->fetchAll(PDO::FETCH_ASSOC);

                header("Location: dashboard.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = 'Username atau password salah. Silakan coba lagi.';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        echo "Database connection not established. Check your connection settings.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            width: 350px;
            transition: transform 0.3s;
        }

        .login-container:hover {
            transform: scale(1.02);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .form-group input[type="submit"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .form-group input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        .create-account {
            text-align: center;
            margin-top: 20px;
        }

        .create-account a {
            color: #007bff;
            text-decoration: none;
        }

        .create-account a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login For Admin</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Login">
            </div>
            <div class="error-message" id="error-message">
                <?php if (isset($_SESSION['error_message'])) {
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                } ?>
            </div>
        </form>
        <div class="create-account">
            <p>Belum punya akun? <a href="register.php">Buat Akun</a></p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script>
        document.querySelector('form').onsubmit = function() {
            var username = document.getElementById('username').value.trim();
            var password = document.getElementById('password').value.trim();

            if (username === '' || password === '') {
                swal("Error!", "Username dan password harus diisi", "error");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</body>
</html>
