<?php
session_start();
include('koneksi.php'); // Include the database connection

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['status'] = "Email and password cannot be empty";
        header("Location: login.php");
        exit();
    }

    // Prepare SQL query
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $connection->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $connection->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables or any other login actions
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['status'] = "Incorrect password";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "No user found with that email";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
