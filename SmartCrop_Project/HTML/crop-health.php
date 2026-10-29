<?php
session_start();
require_once "../php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$message = "";
$health_status = "";
$risk_level = "";
$growth_stage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id = $_SESSION["user_id"];

    $crop_name = trim($_POST["crop_name"] ?? "");
    $health_status = trim($_POST["health_status"] ?? "");
    $soil_moisture = floatval($_POST["soil_moisture"] ?? 0);
    $risk_level = trim($_POST["risk_level"] ?? "");
    $growth_stage = trim($_POST["growth_stage"] ?? "");

    $stmt = $conn->prepare("
        INSERT INTO crop_health
        (user_id, crop_name, health_status, soil_moisture, risk_level, growth_stage)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issdss",
        $user_id,
        $crop_name,
        $health_status,
        $soil_moisture,
        $risk_level,
        $growth_stage
    );

    if ($stmt->execute()) {
        $message = "Crop health information saved successfully!";
    } else {
        $message = "Error while saving crop health information.";
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

    <title>Crop Health - SmartCrop</title>

    <link rel="stylesheet"
          href="../css/style.css">

    <style>

        .health-header {
            background: #e8f5e9;
            padding: 45px 6%;
        }

        .health-header h1 {
            color: #1b5e20;
            margin-bottom: 8px;
        }

        .health-container {
            width: 90%;
            max-width: 800px;
            margin: 40px auto;
        }

        .health-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .health-box h2 {
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

        .health-result {
            margin-top: 25px;
            padding: 20px;
            background: #f1f8e9;
            border-left: 5px solid #2e7d32;
            border-radius: 6px;
        }

        .health-result h3 {
            color: #2e7d32;
            margin-bottom: 12px;
        }

        .health-result p {
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

            <a href="crop-health.php">Crop Health</a>

            <a href="marketplace.html">Marketplace</a>

            <a href="weather.html">Weather</a>

            <a href="profile.html">Profile</a>

        </div>

    </nav>


    <!-- Header -->

    <section class="health-header">

        <h1>🌱 Crop Health Monitoring</h1>

        <p>
            Enter your crop health information
            and monitor your crop condition.
        </p>

    </section>


    <!-- Main -->

    <div class="health-container">

        <div class="health-box">

            <h2>Crop Health Details</h2>

            <?php if ($message !== ""): ?>

                <div class="success-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <form method="POST"
                  action="crop-health.php">


                <!-- Crop Name -->

                <div class="form-group">

                    <label for="crop_name">
                        Crop Name
                    </label>

                    <input type="text"
                           id="crop_name"
                           name="crop_name"
                           placeholder="Enter crop name"
                           required>

                </div>


                <!-- Health Status -->

                <div class="form-group">

                    <label for="health_status">
                        Health Status
                    </label>

                    <select id="health_status"
                            name="health_status"
                            required>

                        <option value="">
                            Select Health Status
                        </option>

                        <option value="Healthy">
                            Healthy
                        </option>

                        <option value="Moderate">
                            Moderate
                        </option>

                        <option value="Poor">
                            Poor
                        </option>

                    </select>

                </div>


                <!-- Soil Moisture -->

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


                <!-- Risk Level -->

                <div class="form-group">

                    <label for="risk_level">
                        Risk Level
                    </label>

                    <select id="risk_level"
                            name="risk_level"
                            required>

                        <option value="">
                            Select Risk Level
                        </option>

                        <option value="Low">
                            Low Risk
                        </option>

                        <option value="Medium">
                            Medium Risk
                        </option>

                        <option value="High">
                            High Risk
                        </option>

                    </select>

                </div>


                <!-- Growth Stage -->

                <div class="form-group">

                    <label for="growth_stage">
                        Growth Stage
                    </label>

                    <select id="growth_stage"
                            name="growth_stage"
                            required>

                        <option value="">
                            Select Growth Stage
                        </option>

                        <option value="Seedling">
                            Seedling
                        </option>

                        <option value="Vegetative">
                            Vegetative
                        </option>

                        <option value="Flowering">
                            Flowering
                        </option>

                        <option value="Harvesting">
                            Harvesting
                        </option>

                    </select>

                </div>


                <button type="submit"
                        class="btn submit-btn">

                    🌱 Save Crop Health

                </button>

            </form>


            <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $message !== "" && $health_status !== ""): ?>

                <div class="health-result">

                    <h3>📊 Crop Health Summary</h3>

                    <p>
                        <strong>Crop:</strong>
                        <?php echo htmlspecialchars($crop_name); ?>
                    </p>

                    <p>
                        <strong>Health:</strong>
                        <?php echo htmlspecialchars($health_status); ?>
                    </p>

                    <p>
                        <strong>Soil Moisture:</strong>
                        <?php echo htmlspecialchars($soil_moisture); ?>%
                    </p>

                    <p>
                        <strong>Risk Level:</strong>
                        <?php echo htmlspecialchars($risk_level); ?>
                    </p>

                    <p>
                        <strong>Growth Stage:</strong>
                        <?php echo htmlspecialchars($growth_stage); ?>
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