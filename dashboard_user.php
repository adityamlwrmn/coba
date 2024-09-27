<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous">
    <style>
        /* Your existing styles */
        
        /* Styles for Settings Panel */
        #settings-panel {
            position: fixed;
            top: 0;
            right: 0;
            width: 320px;
            height: 100%;
            background-color: #f8f9fa;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
            padding: 20px;
            display: none;
            overflow-y: auto;
        }

        #settings-panel h3 {
            margin-top: 0;
        }

        #settings-panel label {
            display: block;
            margin-bottom: 10px;
        }

        #settings-panel input[type="color"],
        #settings-panel input[type="range"] {
            width: 100%;
            border: none;
            padding: 5px;
        }

        #settings-panel .close-btn {
            display: block;
            margin: 10px 0;
            cursor: pointer;
            font-size: 24px;
        }

        #settings-panel .section {
            margin-bottom: 20px;
        }

        #settings-panel .section h4 {
            margin-bottom: 10px;
        }
    </style>
</head>

<body class="bg-content">
    <main class="dashboard d-flex">
        <!-- Start sidebar -->
        <?php include 'koneksi.php'; // Ensure this file establishes $mysqli
        include 'header.php';
        include 'sidebar_user.php'; ?>
        <!-- End sidebar -->

        <!-- Start content page -->
        <div class="container-fluid px-4">

            <!-- Button section -->
            <div class="student-list-header d-flex justify-content-between align-items-center py-2">
                <!-- Title -->
                <div class="title h6 fw-bold">Dashboard</div>
                <!-- Settings Button -->
                <button id="settings-btn" class="btn btn-secondary">Settings</button>
            </div>
            <!-- End button section -->

            <!-- Display Current Day -->
            <h1 class="h3 mb-4 text-gray-800 text-center">Welcome To The Dashboard</h1>
            <h3 class="h3 mb-4 text-gray-800 text-center"></h3>
            <p class="h4 text-gray-600 text-center">
                <?php
                // Define days in Indonesian
                $days = array(
                    "Monday" => "Senin",
                    "Tuesday" => "Selasa",
                    "Wednesday" => "Rabu",
                    "Thursday" => "Kamis",
                    "Friday" => "Jumat",
                    "Saturday" => "Sabtu",
                    "Sunday" => "Minggu"
                );
                // Get the current day in English
                $day_in_english = date("l");
                // Translate to Indonesian
                $day_in_indonesian = isset($days[$day_in_english]) ? $days[$day_in_english] : $day_in_english;
                echo "Hari ini adalah Hari " . $day_in_indonesian;
                ?>
            </p>

            <!-- Circular Clock -->
            <div id="clock" class="text-center">
                <div class="clock-face">
              
                    <div class="hand hour-hand"></div>
                    <div class="hand minute-hand"></div>
                    <div class="hand second-hand"></div>
                    <div class="center-circle"></div>
                </div>
            </div>

            <!-- Digital Clock -->
            <div class="digital-clock">
                <span id="digital-time"></span>
            </div>

            <!-- Settings Panel -->
            <div id="settings-panel">
                <span class="close-btn">&times;</span>
                <h3>Settings</h3>
                
                <!-- Background and Text Color -->
                <div class="section">
                    <h4>Appearance</h4>
                    <label for="bg-color">Background Color:</label>
                    <input type="color" id="bg-color" value="#ffffff">
                    <label for="text-color">Text Color:</label>
                    <input type="color" id="text-color" value="#000000">
                </div>

                <!-- Clock Colors -->
                <div class="section">
                    <h4>Clock Colors</h4>
                    <label for="clock-bg-color">Clock Background Color:</label>
                    <input type="color" id="clock-bg-color" value="#f0f0f0">
                    <label for="clock-hand-color">Clock Hand Color:</label>
                    <input type="color" id="clock-hand-color" value="#333333">
                </div>

                <!-- Font Customization -->
                <div class="section">
                    <h4>Font Settings</h4>
                    <label for="font-family">Font Family:</label>
                    <select id="font-family">
                        <option value="Arial">Arial</option>
                        <option value="'Courier New', Courier">Courier New</option>
                        <option value="Georgia">Georgia</option>
                        <option value="'Times New Roman', Times">Times New Roman</option>
                        <option value="Verdana">Verdana</option>
                    </select>
                    <label for="font-size">Font Size:</label>
                    <input type="range" id="font-size" min="12" max="24" value="16">
                    <span id="font-size-value">16px</span>
                </div>

                <!-- Theme Toggle -->
                <div class="section">
                    <h4>Theme</h4>
                    <label for="theme-toggle">Dark Mode:</label>
                    <input type="checkbox" id="theme-toggle">
                </div>
            </div>

            <!-- JavaScript for Circular and Digital Clock -->
            <script>
                function updateClock() {
                    const now = new Date();
                    const hours = now.getHours();
                    const minutes = now.getMinutes();
                    const seconds = now.getSeconds();

                    // Calculate degrees for each hand
                    const hourDegrees = (hours % 12) * 30 + (minutes / 60) * 30; // 360 degrees / 12 hours
                    const minuteDegrees = minutes * 6; // 360 degrees / 60 minutes
                    const secondDegrees = seconds * 6; // 360 degrees / 60 seconds

                    // Update hand rotations
                    document.querySelector('.hour-hand').style.transform = `translateX(-50%) translateY(-100%) rotate(${hourDegrees}deg)`;
                    document.querySelector('.minute-hand').style.transform = `translateX(-50%) translateY(-100%) rotate(${minuteDegrees}deg)`;
                    document.querySelector('.second-hand').style.transform = `translateX(-50%) translateY(-100%) rotate(${secondDegrees}deg)`;

                    // Update Digital Clock
                    const digitalTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    document.getElementById('digital-time').textContent = digitalTime;
                }

                setInterval(updateClock, 1000);
                updateClock(); // Initialize clock immediately

                // Settings panel functionality
                const settingsBtn = document.getElementById('settings-btn');
                const settingsPanel = document.getElementById('settings-panel');
                const closeBtn = settingsPanel.querySelector('.close-btn');

                settingsBtn.addEventListener('click', () => {
                    settingsPanel.style.display = settingsPanel.style.display === 'none' ? 'block' : 'none';
                });

                closeBtn.addEventListener('click', () => {
                    settingsPanel.style.display = 'none';
                });

                // Apply saved settings
                const applySettings = () => {
                    document.body.style.backgroundColor = localStorage.getItem('bg-color') || '#ffffff';
                    document.body.style.color = localStorage.getItem('text-color') || '#000000';
                    document.getElementById('clock').style.backgroundColor = localStorage.getItem('clock-bg-color') || '#f0f0f0';
                    const clockHandColor = localStorage.getItem('clock-hand-color') || '#333333';
                    document.querySelectorAll('.hand').forEach(hand => hand.style.backgroundColor = clockHandColor);
                    document.querySelectorAll('.number').forEach(number => number.style.color = clockHandColor);
                    document.querySelector('.center-circle').style.backgroundColor = clockHandColor;
                    document.body.style.fontFamily = localStorage.getItem('font-family') || 'Arial';
                    document.body.style.fontSize = localStorage.getItem('font-size') || '16px';
                    document.body.classList.toggle('dark-mode', localStorage.getItem('dark-mode') === 'true');
                };

                // Handle color changes
                document.getElementById('bg-color').addEventListener('input', (e) => {
                    const color = e.target.value;
                    document.body.style.backgroundColor = color;
                    localStorage.setItem('bg-color', color);
                });

                document.getElementById('text-color').addEventListener('input', (e) => {
                    const color = e.target.value;
                    document.body.style.color = color;
                    localStorage.setItem('text-color', color);
                });

                document.getElementById('clock-bg-color').addEventListener('input', (e) => {
                    const color = e.target.value;
                    document.getElementById('clock').style.backgroundColor = color;
                    localStorage.setItem('clock-bg-color', color);
                });

                document.getElementById('clock-hand-color').addEventListener('input', (e) => {
                    const color = e.target.value;
                    document.querySelectorAll('.hand').forEach(hand => hand.style.backgroundColor = color);
                    document.querySelectorAll('.number').forEach(number => number.style.color = color);
                    document.querySelector('.center-circle').style.backgroundColor = color;
                    localStorage.setItem('clock-hand-color', color);
                });

                document.getElementById('font-family').addEventListener('change', (e) => {
                    const font = e.target.value;
                    document.body.style.fontFamily = font;
                    localStorage.setItem('font-family', font);
                });

                document.getElementById('font-size').addEventListener('input', (e) => {
                    const fontSize = e.target.value + 'px';
                    document.body.style.fontSize = fontSize;
                    document.getElementById('font-size-value').textContent = fontSize;
                    localStorage.setItem('font-size', fontSize);
                });

                document.getElementById('theme-toggle').addEventListener('change', (e) => {
                    const isDarkMode = e.target.checked;
                    document.body.classList.toggle('dark-mode', isDarkMode);
                    localStorage.setItem('dark-mode', isDarkMode);
                });

                // Initialize settings
                applySettings();
            </script>
        </div>
        <!-- End content page -->
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>
