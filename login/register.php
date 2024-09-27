<?php
session_start();
include 'koneksi1.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // Mengambil password tanpa hashing

    // Cek apakah username atau email sudah ada
    $checkQuery = "SELECT * FROM `users` WHERE Email = ? OR username = ?";
    $checkStmt = $con->prepare($checkQuery);
    $checkStmt->execute([$email, $username]);
    $existingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $_SESSION['error_message'] = 'Username atau email sudah terdaftar.';
    } else {
        $query = "INSERT INTO `users` (Email, password, status, username) VALUES (?, ?, 1, ?)";
        $stmt = $con->prepare($query);

        if ($stmt->execute([$email, $password, $username])) { // Simpan password tanpa hashing
            $_SESSION['success_message'] = 'Akun berhasil dibuat. Silakan login.';
            header("Location: register.php");
            exit();
        } else {
            $_SESSION['error_message'] = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
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
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus,
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
        <h2>Registrasi</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Buat Akun">
            </div>
            <div class="error-message">
                <?php if (isset($_SESSION['error_message'])) {
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                } ?>
                <?php if (isset($_SESSION['success_message'])) {
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                } ?>
            </div>
        </form>
        <div class="create-account">
            <p>Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script>
        document.querySelector('form').onsubmit = function() {
            var inputs = this.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
            var valid = true;

            inputs.forEach(input => {
                if (input.value.trim() === '') {
                    valid = false;
                    swal("Error!", `${input.previousElementSibling.innerText} harus diisi!`, "error");
                }
            });

            return valid; // Izinkan atau cegah pengiriman formulir
        }
    </script>
</body>
</html>
