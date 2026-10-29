<?php
session_start();
require_once "../php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION["user_id"];
$full_name = $_SESSION["full_name"];

/* Crop Health */
$health = "Good";
$health_query = $conn->prepare("
    SELECT health_status
    FROM crop_health
    WHERE user_id = ?
    ORDER BY id DESC
    LIMIT 1
");
$health_query->bind_param("i", $user_id);
$health_query->execute();
$health_result = $health_query->get_result();

if ($health_result->num_rows > 0) {
    $health_data = $health_result->fetch_assoc();
    $health = $health_data["health_status"];
}

/* Soil Moisture */
$soil_moisture = 68;

$soil_query = $conn->prepare("
    SELECT soil_moisture
    FROM crop_health
    WHERE user_id = ?
    ORDER BY id DESC
    LIMIT 1
");
$soil_query->bind_param("i", $user_id);
$soil_query->execute();
$soil_result = $soil_query->get_result();

if ($soil_result->num_rows > 0) {
    $soil_data = $soil_result->fetch_assoc();
    $soil_moisture = $soil_data["soil_moisture"];
}

/* Weather */
$temperature = 28;

$weather_query = $conn->prepare("
    SELECT temperature
    FROM weather_alerts
    WHERE user_id = ?
    ORDER BY id DESC
    LIMIT 1
");
$weather_query->bind_param("i", $user_id);
$weather_query->execute();
$weather_result = $weather_query->get_result();

if ($weather_result->num_rows > 0) {
    $weather_data = $weather_result->fetch_assoc();
    $temperature = $weather_data["temperature"];
}

/* Market Price */
$market_price = 2400;

$market_query = $conn->prepare("
    SELECT price
    FROM marketplace
    ORDER BY id DESC
    LIMIT 1
");
$market_query->execute();
$market_result = $market_query->get_result();

if ($market_result->num_rows > 0) {
    $market_data = $market_result->fetch_assoc();
    $market_price = $market_data["price"];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard - SmartCrop</title>

    <link rel="stylesheet"
          href="../css/style.css">

    <style>

        .dashboard-header {
            background: #e8f5e9;
            padding: 45px 6%;
        }

        .dashboard-header h1 {
            color: #1b5e20;
            margin-bottom: 8px;
        }

        .dashboard-container {
            width: 88%;
            max-width: 1200px;
            margin: 35px auto;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .metric-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .metric-card .icon {
            font-size: 35px;
            margin-bottom: 10px;
        }

        .metric-card h2 {
            color: #2e7d32;
            margin-bottom: 5px;
        }

        .metric-card p {
            color: #666;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .dashboard-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .dashboard-box h2 {
            color: #2e7d32;
            margin-bottom: 20px;
        }

        .alert-item {
            padding: 14px;
            margin-bottom: 12px;
            border-left: 5px solid #2e7d32;
            background: #f5f9f5;
            border-radius: 5px;
        }

        .market-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #ddd;
        }

        .price {
            color: #2e7d32;
            font-weight: bold;
        }

        .quick-actions {
            margin-top: 25px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .action-btn {
            display: block;
            text-align: center;
            text-decoration: none;
            background: #2e7d32;
            color: white;
            padding: 15px;
            border-radius: 7px;
            font-weight: bold;
        }

        .action-btn:hover {
            background: #1b5e20;
        }

        .logout-btn {
            background: #c62828;
        }

        .logout-btn:hover {
            background: #8e0000;
        }

        @media (max-width: 900px) {

            .metric-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {

            .metric-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }
        }

    </style>

</head>

<body>

    <!-- Navigation -->

    <nav class="navbar">

        <div class="logo">
            🌱 SmartCrop
        </div>

        <div class="nav-links">

            <a href="index.html">Home</a>

            <a href="crop-advisory.php">
                Crop Advisory
            </a>

            <a href="crop-health.html">
                Crop Health
            </a>

            <a href="marketplace.php">
                Marketplace
            </a>

            <a href="weather.php">
                Weather
            </a>

            <a href="profile.html">
                Profile
            </a>

        </div>

    </nav>


    <!-- Dashboard Header -->

    <section class="dashboard-header">

        <h1>
            Welcome, <?php echo htmlspecialchars($full_name); ?> 👋
        </h1>

        <p>
            Monitor your crops, weather and marketplace activity
            from one place.
        </p>

    </section>


    <!-- Dashboard Content -->

    <div class="dashboard-container">


        <!-- Metric Cards -->

        <div class="metric-grid">


            <div class="metric-card">

                <div class="icon">
                    🌱
                </div>

                <h2>
                    <?php echo htmlspecialchars($health); ?>
                </h2>

                <p>
                    Crop Health
                </p>

            </div>


            <div class="metric-card">

                <div class="icon">
                    💧
                </div>

                <h2>
                    <?php echo htmlspecialchars($soil_moisture); ?>%
                </h2>

                <p>
                    Soil Moisture
                </p>

            </div>


            <div class="metric-card">

                <div class="icon">
                    🌦️
                </div>

                <h2>
                    <?php echo htmlspecialchars($temperature); ?>°C
                </h2>

                <p>
                    Current Weather
                </p>

            </div>


            <div class="metric-card">

                <div class="icon">
                    💰
                </div>

                <h2>
                    ₹<?php echo number_format($market_price); ?>
                </h2>

                <p>
                    Market Price / Quintal
                </p>

            </div>


        </div>


        <!-- Dashboard Grid -->

        <div class="dashboard-grid">


            <!-- Recent Alerts -->

            <div class="dashboard-box">

                <h2>
                    🚨 Recent Alerts
                </h2>

                <div class="alert-item">

                    <strong>
                        Rain Alert
                    </strong>

                    <p>
                        Moderate rainfall expected today.
                    </p>

                </div>


                <div class="alert-item">

                    <strong>
                        Soil Moisture
                    </strong>

                    <p>
                        Soil moisture level is currently
                        <?php echo htmlspecialchars($soil_moisture); ?>%.
                    </p>

                </div>


                <div class="alert-item">

                    <strong>
                        Temperature
                    </strong>

                    <p>
                        Temperature is
                        <?php echo htmlspecialchars($temperature); ?>°C.
                    </p>

                </div>

            </div>


            <!-- Marketplace Activity -->

            <div class="dashboard-box">

                <h2>
                    🛒 Marketplace Activity
                </h2>

                <div class="market-item">

                    <div>

                        <strong>
                            Wheat
                        </strong>

                        <p>
                            Kopargaon
                        </p>

                    </div>

                    <span class="price">
                        ₹2,400
                    </span>

                </div>


                <div class="market-item">

                    <div>

                        <strong>
                            Potato
                        </strong>

                        <p>
                            Shirdi
                        </p>

                    </div>

                    <span class="price">
                        ₹1,800
                    </span>

                </div>


                <div class="market-item">

                    <div>

                        <strong>
                            Tomato
                        </strong>

                        <p>
                            Nashik
                        </p>

                    </div>

                    <span class="price">
                        ₹2,000
                    </span>

                </div>

            </div>

        </div>


        <!-- Quick Actions -->

        <div class="dashboard-box quick-actions">

            <h2>
                ⚡ Quick Actions
            </h2>

            <div class="action-buttons">

                <a href="crop-advisory.html"
                   class="action-btn">
                    🌾 Add New Crop
                </a>

                <a href="marketplace.html"
                   class="action-btn">
                    🛒 Post a Listing
                </a>

                <a href="weather.html"
                   class="action-btn">
                    🌦️ Check Weather
                </a>

                <a href="../php/logout.php"
                   class="action-btn logout-btn">
                    🚪 Logout
                </a>

            </div>

        </div>


    </div>


    <!-- Footer -->

    <footer>

        <p>
            © 2026 SmartCrop. All Rights Reserved.
        </p>

        <p>
            Smart Crop Advisory & Marketplace Platform
        </p>

    </footer>


    <script src="../js/script.js"></script>

</body>

</html>