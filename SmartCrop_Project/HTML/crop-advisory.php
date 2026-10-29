<?php
session_start();
require_once "../php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$message = "";
$recommended_crop = "";
$advice = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id = $_SESSION["user_id"];

    $soil_type = trim($_POST["soil_type"] ?? "");
    $temperature = floatval($_POST["temperature"] ?? 0);
    $soil_moisture = floatval($_POST["soil_moisture"] ?? 0);

    // Crop Recommendation
    if ($soil_type === "Black") {
        $recommended_crop = "Cotton";
        $advice = "Black soil is suitable for Cotton. Maintain proper irrigation and monitor pests.";
    }
    elseif ($soil_type === "Red") {
        $recommended_crop = "Groundnut";
        $advice = "Red soil is suitable for Groundnut. Maintain soil moisture and use proper fertilizers.";
    }
    elseif ($soil_type === "Alluvial") {
        $recommended_crop = "Wheat";
        $advice = "Alluvial soil is suitable for Wheat. Ensure adequate irrigation and nutrients.";
    }
    else {
        $recommended_crop = "Carrot";
        $advice = "Carrot can be grown in this soil. Maintain good drainage and proper moisture.";
    }

    // Save data into database
    $stmt = $conn->prepare("
        INSERT INTO crop_advisory
        (user_id, soil_type, temperature, soil_moisture, recommended_crop, advice)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isddss",
        $user_id,
        $soil_type,
        $temperature,
        $soil_moisture,
        $recommended_crop,
        $advice
    );

    if ($stmt->execute()) {
        $message = "Crop advisory saved successfully!";
    } else {
        $message = "Error while saving crop advisory.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Crop Advisory - SmartCrop</title>

    <link rel="stylesheet"
          href="../css/style.css">

    <style>

        .advisory-header {
            background: #e8f5e9;
            padding: 45px 6%;
        }

        .advisory-header h1 {
            color: #1b5e20;
            margin-bottom: 8px;
        }

        .advisory-container {
            width: 90%;
            max-width: 800px;
            margin: 40px auto;
        }

        .advisory-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .advisory-box h2 {
            color: #2e7d32;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .submit-btn {
            width: 100%;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .success-message {
            background: #e8f5e9;
            color: #1b5e20;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .result-box {
            margin-top: 25px;
            padding: 20px;
            background: #f1f8e9;
            border-left: 5px solid #2e7d32;
            border-radius: 6px;
        }

        .result-box h3 {
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .result-box p {
            margin-bottom: 8px;
        }

    </style>

</head>

<body>

    <!-- Navbar -->

    <nav class="navbar">

        <div class="logo">
            🌱 SmartCrop
        </div>

        <div class="nav-links">

            <a href="index.html">Home</a>

            <a href="dashboard.php">Dashboard</a>

            <a href="crop-advisory.php">Crop Advisory</a>

            <a href="crop-health.html">Crop Health</a>

            <a href="marketplace.html">Marketplace</a>

            <a href="weather.html">Weather</a>

            <a href="profile.html">Profile</a>

        </div>

    </nav>


    <!-- Header -->

    <section class="advisory-header">

        <h1>🌾 Crop Advisory</h1>

        <p>
            Enter your soil and weather details to get
            a suitable crop recommendation.
        </p>

    </section>


    <!-- Main Container -->

    <div class="advisory-container">

        <div class="advisory-box">

            <h2>Get Crop Recommendation</h2>

            <?php if ($message !== ""): ?>

                <div class="success-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <form method="POST"
                  action="crop-advisory.php">

                <div class="form-group">

                    <label for="soil_type">
                        Soil Type
                    </label>

                    <select id="soil_type"
                            name="soil_type"
                            required>

                        <option value="">
                            Select Soil Type
                        </option>

                        <option value="Black">
                            Black Soil
                        </option>

                        <option value="Red">
                            Red Soil
                        </option>

                        <option value="Alluvial">
                            Alluvial Soil
                        </option>

                        <option value="Other">
                            Other
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="temperature">
                        Temperature (°C)
                    </label>

                    <input type="number"
                           id="temperature"
                           name="temperature"
                           step="0.1"
                           placeholder="Enter temperature"
                           required>

                </div>


                <div class="form-group">

                    <label for="soil_moisture">
                        Soil Moisture (%)
                    </label>

                    <input type="number"
                           id="soil_moisture"
                           name="soil_moisture"
                           min="0"
                           max="100"
                           step="0.1"
                           placeholder="Enter soil moisture"
                           required>

                </div>


                <button type="submit"
                        class="btn submit-btn">

                    🌱 Get Crop Advice

                </button>

            </form>


            <?php if ($recommended_crop !== ""): ?>

                <div class="result-box">

                    <h3>🌾 Recommendation</h3>

                    <p>
                        <strong>Recommended Crop:</strong>
                        <?php echo htmlspecialchars($recommended_crop); ?>
                    </p>

                    <p>
                        <strong>Advice:</strong>
                        <?php echo htmlspecialchars($advice); ?>
                    </p>

                </div>

            <?php endif; ?>

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