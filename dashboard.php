<?php
include 'koneksi.php'; // Ensure this file establishes $mysqli
include 'header.php';
include 'sidebar.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Polban</title>
    <!-- Include your CSS files here -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0; /* Remove default margin */
            overflow: hidden; /* Prevent scrolling */
        }
        .background-image {
            position: absolute;
            top: 5;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; /* Send to back */
            object-fit: cover; /* Cover the entire area */
        }
        h1 {
            font-size: 3em;
            /* Large font size for welcome message */
            margin: 0;
            color: orange;
            position: relative; /* Ensure h1 is above the image */
            z-index: 1; /* Bring h1 to the front */
        }
    </style>
</head>

<body>
    <img src="Gedung-H2.jpg" alt="Background" class="background-image"> <!-- Update the path -->

  <h1>Welcome To Dashboard</h1>

    <!-- Include footer here -->
    <?php include 'footer.php'; ?>
    </div>
    <!-- End of Content Wrapper -->
</body>

</html>
