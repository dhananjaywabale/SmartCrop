<?php
session_start();

require_once "../php/db.php";
require_once "../php/config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION["user_id"];

$locations = [
    "Kopargaon" => ["lat" => 19.8826, "lon" => 74.4762],
    "Shirdi"    => ["lat" => 19.7667, "lon" => 74.4778],
    "Nashik"    => ["lat" => 19.9975, "lon" => 73.7898],
    "Pune"      => ["lat" => 18.5204, "lon" => 73.8567]
];

$selected_location = $_POST["location"] ?? "Kopargaon";

if (!isset($locations[$selected_location])) {
    $selected_location = "Kopargaon";
}

$lat = $locations[$selected_location]["lat"];
$lon = $locations[$selected_location]["lon"];

$temperature = 0;
$humidity = 0;
$rainfall = 0;
$wind_speed = 0;
$weather_description = "Loading...";
$alert_type = "Normal";
$alert_message = "Weather information loaded successfully.";

$api_url = "https://api.openweathermap.org/data/2.5/weather"
         . "?lat=" . $lat
         . "&lon=" . $lon
         . "&appid=" . WEATHER_API_KEY
         . "&units=metric";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);

if ($response === false) {

    $weather_description = "API connection failed";
    $alert_type = "Error";
    $alert_message = "Unable to connect to weather service.";

} else {

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $weather_data = json_decode($response, true);

    if ($http_code == 200 && isset($weather_data["main"])) {

        $temperature = $weather_data["main"]["temp"];
        $humidity = $weather_data["main"]["humidity"];

        $wind_speed = isset($weather_data["wind"]["speed"])
            ? round($weather_data["wind"]["speed"] * 3.6, 2)
            : 0;

        $rainfall = isset($weather_data["rain"]["1h"])
            ? $weather_data["rain"]["1h"]
            : 0;

        $weather_description = ucfirst(
            $weather_data["weather"][0]["description"] ?? "Normal"
        );

        if ($rainfall > 0) {

            $alert_type = "Rain Alert";
            $alert_message = "Rainfall detected. Plan farming activities carefully.";

        } elseif ($temperature >= 35) {

            $alert_type = "High Temperature";
            $alert_message = "High temperature detected. Provide sufficient water to crops.";

        } elseif ($temperature <= 10) {

            $alert_type = "Cold Alert";
            $alert_message = "Low temperature detected. Protect sensitive crops.";

        } else {

            $alert_type = "Normal";
            $alert_message = "Weather conditions are normal.";

        }

        // Save weather data into database
        $stmt = $conn->prepare(
            "INSERT INTO weather_alerts
            (user_id, location, temperature, humidity, rainfall, wind_speed, alert_message, alert_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if ($stmt) {

            $stmt->bind_param(
                "isddddss",
                $user_id,
                $selected_location,
                $temperature,
                $humidity,
                $rainfall,
                $wind_speed,
                $alert_message,
                $alert_type
            );

            $stmt->execute();
            $stmt->close();
        }

    } else {

        $weather_description = "API Error";
        $alert_type = "Error";

        if (isset($weather_data["message"])) {
            $alert_message = $weather_data["message"];
        } else {
            $alert_message = "Unable to get weather data.";
        }
    }
}

curl_close($ch);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Weather Alerts - SmartCrop</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        .weather-section {
            padding: 50px 20px;
            min-height: 80vh;
        }

        .weather-container {
            max-width: 1000px;
            margin: auto;
        }

        .weather-container h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .weather-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .weather-form {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .weather-form label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        .weather-form select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .weather-form button {
            width: 100%;
            border: none;
            cursor: pointer;
        }

        .weather-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .weather-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .weather-card h3 {
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .weather-value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .weather-alert {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .weather-alert h2 {
            color: #2e7d32;
        }

        .api-info {
            background: #e8f5e9;
            padding: 20px;
            border-radius: 10px;
        }

        @media (max-width: 800px) {

            .weather-cards {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 500px) {

            .weather-cards {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<header>

    <nav class="navbar">

        <div class="logo">
            SmartCrop
        </div>

        <div class="nav-links">

            <a href="index.html">Home</a>

            <a href="crop-advisory.php">
                Crop Advisory
            </a>

            <a href="marketplace.php">
                Marketplace
            </a>

            <a href="weather.php">
                Weather
            </a>

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="../php/logout.php"
               class="login-btn">
                Logout
            </a>

        </div>

    </nav>

</header>


<section class="weather-section">

    <div class="weather-container">

        <h1>Weather Alerts</h1>

        <p class="weather-subtitle">
            Live weather information powered by OpenWeather REST API
        </p>


        <form method="POST" class="weather-form">

            <label for="location">
                Select Location
            </label>

            <select name="location" id="location">

                <option value="Kopargaon"
                    <?php if ($selected_location == "Kopargaon") echo "selected"; ?>>
                    Kopargaon
                </option>

                <option value="Shirdi"
                    <?php if ($selected_location == "Shirdi") echo "selected"; ?>>
                    Shirdi
                </option>

                <option value="Nashik"
                    <?php if ($selected_location == "Nashik") echo "selected"; ?>>
                    Nashik
                </option>

                <option value="Pune"
                    <?php if ($selected_location == "Pune") echo "selected"; ?>>
                    Pune
                </option>

            </select>

            <button type="submit" class="btn">
                Check Live Weather
            </button>

        </form>


        <div class="weather-cards">

            <div class="weather-card">

                <h3>Temperature</h3>

                <div class="weather-value">
                    <?php echo htmlspecialchars($temperature); ?> °C
                </div>

            </div>


            <div class="weather-card">

                <h3>Humidity</h3>

                <div class="weather-value">
                    <?php echo htmlspecialchars($humidity); ?> %
                </div>

            </div>


            <div class="weather-card">

                <h3>Rainfall</h3>

                <div class="weather-value">
                    <?php echo htmlspecialchars($rainfall); ?> mm
                </div>

            </div>


            <div class="weather-card">

                <h3>Wind Speed</h3>

                <div class="weather-value">
                    <?php echo htmlspecialchars($wind_speed); ?> km/h
                </div>

            </div>

        </div>


        <div class="weather-alert">

            <h2>
                <?php echo htmlspecialchars($selected_location); ?>
            </h2>

            <p>
                <strong>Weather:</strong>
                <?php echo htmlspecialchars($weather_description); ?>
            </p>

            <p>
                <strong>Alert Type:</strong>
                <?php echo htmlspecialchars($alert_type); ?>
            </p>

            <p>
                <strong>Message:</strong>
                <?php echo htmlspecialchars($alert_message); ?>
            </p>

        </div>


        <div class="api-info">

            <h3>REST API Integration</h3>

            <p>
                SmartCrop uses the OpenWeather REST API to retrieve
                live weather information such as temperature,
                humidity, rainfall and wind speed.
            </p>

        </div>

    </div>

</section>


<footer>

    <p>
        © <span class="current-year"></span>
        SmartCrop. All Rights Reserved.
    </p>

</footer>


<script src="../js/script.js"></script>

</body>

</html>