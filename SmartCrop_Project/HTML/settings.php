<?php
session_start();

require_once "../php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION["user_id"];
$message = "";

/* Save Settings */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $notification_type = $_POST["notification_type"] ?? "In-app alerts";
    $alert_frequency = $_POST["alert_frequency"] ?? "Daily";
    $two_factor = isset($_POST["two_factor"]) ? 1 : 0;
    $language = $_POST["language"] ?? "English";
    $area_unit = $_POST["area_unit"] ?? "Acres";
    $weight_unit = $_POST["weight_unit"] ?? "Kg";
    $currency = $_POST["currency"] ?? "INR";
    $profile_visibility = $_POST["profile_visibility"] ?? "Public";
    $location_sharing = isset($_POST["location_sharing"]) ? 1 : 0;
    $third_party_access = isset($_POST["third_party_access"]) ? 1 : 0;

    $stmt = $conn->prepare(
        "INSERT INTO settings
        (user_id, notification_type, alert_frequency, two_factor,
         language, area_unit, weight_unit, currency,
         profile_visibility, location_sharing, third_party_access)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        notification_type = VALUES(notification_type),
        alert_frequency = VALUES(alert_frequency),
        two_factor = VALUES(two_factor),
        language = VALUES(language),
        area_unit = VALUES(area_unit),
        weight_unit = VALUES(weight_unit),
        currency = VALUES(currency),
        profile_visibility = VALUES(profile_visibility),
        location_sharing = VALUES(location_sharing),
        third_party_access = VALUES(third_party_access)"
    );

    $stmt->bind_param(
        "ississsssii",
        $user_id,
        $notification_type,
        $alert_frequency,
        $two_factor,
        $language,
        $area_unit,
        $weight_unit,
        $currency,
        $profile_visibility,
        $location_sharing,
        $third_party_access
    );

    if ($stmt->execute()) {
        $message = "Settings saved successfully!";
    } else {
        $message = "Failed to save settings.";
    }

    $stmt->close();
}


/* Get Current Settings */

$stmt = $conn->prepare(
    "SELECT notification_type, alert_frequency, two_factor,
            language, area_unit, weight_unit, currency,
            profile_visibility, location_sharing, third_party_access
     FROM settings
     WHERE user_id = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$settings = $result->fetch_assoc();

$stmt->close();
$conn->close();


/* Default values */

if (!$settings) {

    $settings = [
        "notification_type" => "In-app alerts",
        "alert_frequency" => "Daily",
        "two_factor" => 0,
        "language" => "English",
        "area_unit" => "Acres",
        "weight_unit" => "Kg",
        "currency" => "INR",
        "profile_visibility" => "Public",
        "location_sharing" => 0,
        "third_party_access" => 0
    ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Settings - SmartCrop</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        .settings-section {
            min-height: 80vh;
            padding: 50px 20px;
        }

        .settings-container {
            max-width: 850px;
            margin: auto;
        }

        .settings-container h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .settings-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .settings-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.10);
            margin-bottom: 25px;
        }

        .settings-card h2 {
            color: #2e7d32;
            margin-bottom: 20px;
        }

        .setting-group {
            margin-bottom: 20px;
        }

        .setting-group label {
            display: block;
            font-weight: bold;
            color: #333;
            margin-bottom: 7px;
        }

        .setting-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .setting-group select:focus {
            outline: none;
            border-color: #2e7d32;
        }

        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .toggle-row:last-child {
            border-bottom: none;
        }

        .toggle-row label {
            margin: 0;
        }

        .toggle-row input {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .save-settings {
            width: 100%;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: bold;
        }

        .settings-actions {
            text-align: center;
            margin-top: 25px;
        }

        .settings-actions a {
            text-decoration: none;
        }

        @media (max-width: 600px) {

            .settings-card {
                padding: 22px;
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

            <a href="profile.php">
                Profile
            </a>

            <a href="settings.php">
                Settings
            </a>

            <a href="../php/logout.php" class="login-btn">
                Logout
            </a>

        </div>

    </nav>

</header>


<section class="settings-section">

    <div class="settings-container">

        <h1>⚙️ Settings</h1>

        <p class="settings-subtitle">
            Customize your SmartCrop preferences
        </p>


        <?php if ($message !== ""): ?>

            <div class="success-message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form method="POST">


            <!-- Notification Settings -->

            <div class="settings-card">

                <h2>🔔 Notification Preferences</h2>

                <div class="setting-group">

                    <label>
                        Notification Type
                    </label>

                    <select name="notification_type">

                        <option value="Push notifications"
                            <?php if ($settings["notification_type"] == "Push notifications") echo "selected"; ?>>
                            Push Notifications
                        </option>

                        <option value="SMS"
                            <?php if ($settings["notification_type"] == "SMS") echo "selected"; ?>>
                            SMS
                        </option>

                        <option value="Email"
                            <?php if ($settings["notification_type"] == "Email") echo "selected"; ?>>
                            Email
                        </option>

                        <option value="In-app alerts"
                            <?php if ($settings["notification_type"] == "In-app alerts") echo "selected"; ?>>
                            In-App Alerts
                        </option>

                    </select>

                </div>


                <div class="setting-group">

                    <label>
                        Alert Frequency
                    </label>

                    <select name="alert_frequency">

                        <option value="Instant"
                            <?php if ($settings["alert_frequency"] == "Instant") echo "selected"; ?>>
                            Instant
                        </option>

                        <option value="Daily"
                            <?php if ($settings["alert_frequency"] == "Daily") echo "selected"; ?>>
                            Daily
                        </option>

                        <option value="Weekly"
                            <?php if ($settings["alert_frequency"] == "Weekly") echo "selected"; ?>>
                            Weekly
                        </option>

                    </select>

                </div>

            </div>


            <!-- Security -->

            <div class="settings-card">

                <h2>🔐 Account Security</h2>

                <div class="toggle-row">

                    <label>
                        Enable Two-Factor Authentication
                    </label>

                    <input
                        type="checkbox"
                        name="two_factor"
                        <?php if ($settings["two_factor"]) echo "checked"; ?>
                    >

                </div>

            </div>


            <!-- Language & Units -->

            <div class="settings-card">

                <h2>🌐 Language & Units</h2>

                <div class="setting-group">

                    <label>
                        Language
                    </label>

                    <select name="language">

                        <option value="English"
                            <?php if ($settings["language"] == "English") echo "selected"; ?>>
                            English
                        </option>

                        <option value="Marathi"
                            <?php if ($settings["language"] == "Marathi") echo "selected"; ?>>
                            Marathi
                        </option>

                        <option value="Hindi"
                            <?php if ($settings["language"] == "Hindi") echo "selected"; ?>>
                            Hindi
                        </option>

                        <option value="Japanese"
                            <?php if ($settings["language"] == "Japanese") echo "selected"; ?>>
                            Japanese
                        </option>

                    </select>

                </div>


                <div class="setting-group">

                    <label>
                        Area Unit
                    </label>

                    <select name="area_unit">

                        <option value="Acres"
                            <?php if ($settings["area_unit"] == "Acres") echo "selected"; ?>>
                            Acres
                        </option>

                        <option value="Hectares"
                            <?php if ($settings["area_unit"] == "Hectares") echo "selected"; ?>>
                            Hectares
                        </option>

                    </select>

                </div>


                <div class="setting-group">

                    <label>
                        Weight Unit
                    </label>

                    <select name="weight_unit">

                        <option value="Kg"
                            <?php if ($settings["weight_unit"] == "Kg") echo "selected"; ?>>
                            Kilogram (Kg)
                        </option>

                        <option value="Quintal"
                            <?php if ($settings["weight_unit"] == "Quintal") echo "selected"; ?>>
                            Quintal
                        </option>

                        <option value="Ton"
                            <?php if ($settings["weight_unit"] == "Ton") echo "selected"; ?>>
                            Ton
                        </option>

                    </select>

                </div>


                <div class="setting-group">

                    <label>
                        Currency
                    </label>

                    <select name="currency">

                        <option value="INR"
                            <?php if ($settings["currency"] == "INR") echo "selected"; ?>>
                            Indian Rupee (₹)
                        </option>

                        <option value="USD"
                            <?php if ($settings["currency"] == "USD") echo "selected"; ?>>
                            US Dollar ($)
                        </option>

                        <option value="JPY"
                            <?php if ($settings["currency"] == "JPY") echo "selected"; ?>>
                            Japanese Yen (¥)
                        </option>

                    </select>

                </div>

            </div>


            <!-- Privacy -->

            <div class="settings-card">

                <h2>🔒 Privacy Settings</h2>

                <div class="setting-group">

                    <label>
                        Profile Visibility
                    </label>

                    <select name="profile_visibility">

                        <option value="Public"
                            <?php if ($settings["profile_visibility"] == "Public") echo "selected"; ?>>
                            Public
                        </option>

                        <option value="Private"
                            <?php if ($settings["profile_visibility"] == "Private") echo "selected"; ?>>
                            Private
                        </option>

                    </select>

                </div>


                <div class="toggle-row">

                    <label>
                        Allow Location Sharing
                    </label>

                    <input
                        type="checkbox"
                        name="location_sharing"
                        <?php if ($settings["location_sharing"]) echo "checked"; ?>
                    >

                </div>


                <div class="toggle-row">

                    <label>
                        Allow Third-Party Access
                    </label>

                    <input
                        type="checkbox"
                        name="third_party_access"
                        <?php if ($settings["third_party_access"]) echo "checked"; ?>
                    >

                </div>

            </div>


            <button type="submit" class="btn save-settings">
                Save Settings
            </button>

        </form>


        <div class="settings-actions">

            <a href="dashboard.php" class="btn">
                Back to Dashboard
            </a>

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