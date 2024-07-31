<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../settings/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the total number of accepted matches for each origin-destination combo
$sql = "SELECT mt.origin_id, mt.destination_id, 
        c1.country_name AS origin_country, c2.country_name AS destination_country, 
        COUNT(mt.match_id) AS total_matches
        FROM Matches mt
        JOIN Countries c1 ON mt.origin_id = c1.country_id
        JOIN Countries c2 ON mt.destination_id = c2.country_id
        WHERE mt.match_id LIKE :user_id_pattern AND mt.status = 'accepted'
        GROUP BY mt.origin_id, mt.destination_id
        ORDER BY total_matches DESC";

try {
    $stmt = $conn->prepare($sql);
    $user_id_pattern = '%' . $user_id . '%';
    $stmt->bindParam(':user_id_pattern', $user_id_pattern, PDO::PARAM_STR);
    $stmt->execute();
    $matches_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Query failed: ' . $e->getMessage();
    exit();
}

// Calculate the total number of trips
$total_trips = array_sum(array_column($matches_summary, 'total_matches'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Analysis</title>
    <style>
        :root {
            --primary-color: #007BFF;
            --background-color: #f5f7fa;
            --dark-background-color: #121212;
            --text-color: #333;
            --dark-text-color: #f5f7fa;
            --card-bg-color: white;
            --dark-card-bg-color: #1e1e1e;
        }
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            scroll-behavior: smooth;
            background-color: var(--background-color);
            color: var(--text-color);
        }
        .dark-mode {
            background-color: var(--dark-background-color);
            color: var(--dark-text-color);
        }
        .dark-mode .header,
        .dark-mode .sidebar,
        .dark-mode .section,
        .dark-mode .footer {
            background-color: var(--dark-background-color);
            color: var(--dark-text-color);
        }
        .dark-mode .header .dashboard,
        .dark-mode .header .create-trip,
        .dark-mode .header .view-travelers,
        .dark-mode .header .dark-mode-toggle {
            background: rgba(255, 255, 255, 0.1);
        }
        .dark-mode .header .dashboard:hover,
        .dark-mode .header .create-trip:hover,
        .dark-mode .header .view-travelers:hover,
        .dark-mode .header .dark-mode-toggle:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        .dark-mode .sidebar a {
            color: var(--dark-text-color);
        }
        .dark-mode .sidebar a:hover {
            background-color: #1e1e1e;
        }
        .dark-mode .card {
            background-color: var(--dark-card-bg-color);
        }
        .dark-mode .card .btn {
            background-color: var(--primary-color);
        }
        .dark-mode .card .btn:hover {
            background-color: #0056b3;
        }
        .dark-mode .card p, .dark-mode .card h3 {
            color: var(--dark-text-color);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .header .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        .header .user-menu {
            display: flex;
            align-items: center;
            position: relative;
        }
        .header .dashboard, .header .view-travelers, .header .dark-mode-toggle {
            color: white;
            font-weight: bold;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
            padding: 10px 20px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        .header .dashboard:hover, .header .view-travelers:hover, .header .dark-mode-toggle:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        .header .username {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            position: relative;
        }
        .header .dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            display: none;
            border-radius: 5px;
            overflow: hidden;
            min-width: 150px;
        }
        .header .dropdown a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }
        .header .dropdown a:hover {
            background-color: #f1f1f1;
        }
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: var(--primary-color);
            padding-top: 20px;
            color: white;
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #0056b3;
        }
        .main-content {
            margin-left: 250px;
            padding: 80px 20px 20px;
            transition: filter 0.3s;
        }
        .section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: var(--card-bg-color);
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 50px;
        }
        .dark-mode .section {
            background: var(--dark-card-bg-color);
        }
        .section h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: var(--text-color);
            cursor: pointer; /* Make the header look clickable */
        }
        .dark-mode .section h2 {
            color: var(--dark-text-color);
        }
        .section p {
            font-size: 18px;
            color: #666;
        }
        .dark-mode .section p {
            color: var(--dark-text-color);
        }
        .dashboard-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .data-table th {
            background-color: #007BFF;
            color: white;
        }
        .total-trips {
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
        }
        .chart-container {
            margin-top: 50px;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
        }
        .footer a {
            color: var(--primary-color);
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="header">
        <div class="logo">TravelPal</div>
        <?php if (isset($_SESSION['username'])): ?>
            <div class="user-menu">
                <a href="HomePage.php" class="dashboard">HomePage</a>
                <a href="view_travelers.php" class="view-travelers">View All Travelers</a>
                <button class="dark-mode-toggle" onclick="toggleDarkMode()">Toggle Dark Mode</button>
                <div class="username" onclick="toggleDropdown()">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></div>
                <div class="dropdown">
                    <a href="Dashboard.php">Profile</a>
                    <a href="/MyTravelPal/action/logout.php">Log Out</a>
                </div>
            </div>
        <?php else: ?>
            <a href="../LandingPage.php"></a>
        <?php endif; ?>
    </div>
    <div class="sidebar">
        <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
        <a href="Dashboard.php"><i class="fas fa-user"></i>Dashboard</a>
    </div>
    <div class="main-content">
        <div class="section">
            <h2>Matches Summary</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Origin Country</th>
                        <th>Destination Country</th>
                        <th>Total Matches</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches_summary as $summary): ?>
                        <tr>
                            <td><?= htmlspecialchars($summary['origin_country']) ?></td>
                            <td><?= htmlspecialchars($summary['destination_country']) ?></td>
                            <td><?= htmlspecialchars($summary['total_matches']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total-trips">
                Total Number of matched Trips: <?= $total_trips ?>
            </div>
            <div class="chart-container">
                <canvas id="matchesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('matchesChart').getContext('2d');
        var matchesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_map(function($item) {
                    return $item['origin_country'] . ' to ' . $item['destination_country'];
                }, $matches_summary)) ?>,
                datasets: [{
                    label: 'Total Matches',
                    data: <?= json_encode(array_column($matches_summary, 'total_matches')) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            // Save dark mode preference in local storage
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.setItem('darkMode', 'disabled');
            }
        }

        // Apply dark mode preference on page load
        document.addEventListener('DOMContentLoaded', (event) => {
            if (localStorage.getItem('darkMode') === 'enabled') {
                document.body.classList.add('dark-mode');
            }
        });

        function toggleDropdown() {
            var dropdown = document.querySelector('.dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.username')) {
                var dropdowns = document.querySelectorAll('.dropdown');
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === 'block') {
                        openDropdown.style.display = 'none';
                    }
                }
            }
        }
    </script>
    <div class="footer">
        <p>&copy; 2024 TravelPal. All rights reserved. | <a href="/view/privacy-policy.php">Privacy Policy</a> | <a href="/view/terms-of-service.php">Terms of Service</a></p>
    </div>
</body>
</html>
