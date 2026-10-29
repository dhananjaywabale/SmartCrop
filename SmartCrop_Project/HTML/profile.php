<?php
session_start();

require_once __DIR__ . '/../PHP/db.php';

/* Check Login Session */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$message = "";

/* Helper function */
function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/* Update Profile */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $farm_size = trim($_POST['farm_size'] ?? '');
    $farm_location = trim($_POST['farm_location'] ?? '');
    $crop_types = trim($_POST['crop_types'] ?? '');
    $soil_type = trim($_POST['soil_type'] ?? '');
    $irrigation_type = trim($_POST['irrigation_type'] ?? '');

    if ($full_name === '' || $mobile === '') {

        $message = "Please enter your name and mobile number.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE users
             SET full_name = ?,
                 mobile = ?,
                 farm_size = NULLIF(?, ''),
                 farm_location = ?,
                 crop_types = ?,
                 soil_type = ?,
                 irrigation_type = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "sssssssi",
            $full_name,
            $mobile,
            $farm_size,
            $farm_location,
            $crop_types,
            $soil_type,
            $irrigation_type,
            $user_id
        );

        if ($stmt->execute()) {

            $_SESSION['full_name'] = $full_name;

            $message = "Profile updated successfully!";

        } else {

            $message = "Unable to update profile. Please try again.";
        }

        $stmt->close();
    }
}
$stmt = $conn->prepare(
    "SELECT full_name, email, mobile, role, created_at,
            farm_size, farm_location, crop_types,
            soil_type, irrigation_type
     FROM users
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

if (!$user) {
    http_response_code(404);
    exit("User account not found. Please contact the administrator.");
}

$conn->close();

$crops = array_filter(
    array_map('trim', explode(',', $user['crop_types'] ?? ''))
);

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Profile - SmartCrop</title>

    <link rel="stylesheet" href="../CSS/style.css">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f8f5;
            font-family: Arial, sans-serif;
        }

        .profile-header {
            background: #e8f5e9;
            padding: 45px 6%;
        }

        .profile-header h1 {
            color: #1b5e20;
            margin-bottom: 8px;
        }

        .profile-container {
            width: 88%;
            max-width: 1100px;
            margin: 35px auto;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
        }

        .profile-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            min-width: 0;
        }

        .profile-box h2 {
            color: #2e7d32;
            margin-bottom: 20px;
        }

        .profile-avatar {
            width: 110px;
            height: 110px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #c8e6c9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 55px;
        }

        .profile-info {
            text-align: center;
        }

        .profile-info h2 {
            margin-bottom: 5px;
            overflow-wrap: anywhere;
        }

        .profile-info p {
            color: #666;
            margin: 7px 0;
            overflow-wrap: anywhere;
        }

        .role {
            display: inline-block;
            margin-top: 10px;
            padding: 7px 16px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 20px;
            font-weight: bold;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 13px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: bold;
            color: #333;
        }

        .detail-value {
            color: #666;
            text-align: right;
            overflow-wrap: anywhere;
        }

        .crop-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .crop-tag {
            padding: 8px 15px;
            background: #e8f5e9;
            color: #1b5e20;
            border-radius: 20px;
            font-weight: bold;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            margin-bottom: 7px;
            font-weight: bold;
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

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2e7d32;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            background: #2e7d32;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }

        .btn:hover {
            background: #1b5e20;
        }

        .edit-button {
            width: 100%;
            margin-top: 20px;
        }

        .save-btn {
            grid-column: 1 / -1;
            margin-top: 10px;
        }

        .message {
            padding: 15px;
            background: #e8f5e9;
            color: #1b5e20;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .profile-actions {
            text-align: center;
            margin: 25px 0;
        }

        .profile-actions a {
            margin: 5px;
        }

        footer {
            text-align: center;
            padding: 25px;
            background: #e8f5e9;
            color: #1b5e20;
        }

        @media (max-width: 800px) {

            .profile-grid {
                grid-template-columns: 1fr;
            }

            .profile-container {
                width: 94%;
            }

            .profile-box {
                padding: 22px;
            }
        }

        @media (max-width: 500px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full,
            .save-btn {
                grid-column: auto;
            }

            .detail-row {
                flex-direction: column;
                gap: 5px;
            }

            .detail-value {
                text-align: left;
            }
        }

    </style>

</head>

<body>

<nav class="navbar">

    <div class="logo">🌱 SmartCrop</div>

    <div class="nav-links">

        <a href="index.html">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="crop-advisory.php">Crop Advisory</a>
        <a href="marketplace.php">Marketplace</a>
        <a href="weather.php">Weather</a>
        <a href="profile.php">Profile</a>
        <a href="../PHP/logout.php">Logout</a>

    </div>

</nav>

<section class="profile-header">

    <h1>👤 My Profile</h1>

    <p>Manage your personal and farm information.</p>

</section>

<div class="profile-container">

    <?php if ($message !== ''): ?>

        <div class="message">
            <?= e($message) ?>
        </div>

    <?php endif; ?>

    <div class="profile-grid">

        <!-- Account Information -->

        <div>

            <div class="profile-box profile-info">

                <div class="profile-avatar">👨‍🌾</div>

                <h2><?= e($user['full_name']) ?></h2>

                <p><?= e($user['email']) ?></p>

                <p>📱 <?= e($user['mobile']) ?></p>

                <span class="role">
                    <?= e($user['role'] ?: 'Farmer') ?>
                </span>

                <button
                    type="button"
                    class="btn edit-button"
                    onclick="document.getElementById('edit-profile').scrollIntoView({behavior:'smooth'})">

                    ✏️ Edit Profile

                </button>

            </div>

            <!-- Contact Information -->

            <div class="profile-box">

                <h2>📞 Contact Information</h2>

                <div class="detail-row">

                    <span class="detail-label">Email</span>

                    <span class="detail-value">
                        <?= e($user['email']) ?>
                    </span>

                </div>

                <div class="detail-row">

                    <span class="detail-label">Mobile</span>

                    <span class="detail-value">
                        <?= e($user['mobile']) ?>
                    </span>

                </div>

                <div class="detail-row">

                    <span class="detail-label">Location</span>

                    <span class="detail-value">
                        <?= e($user['farm_location'] ?: 'Not provided') ?>
                    </span>

                </div>

            </div>

        </div>

        <!-- Farm Information -->

        <div>

            <div class="profile-box">

                <h2>🌾 Farm Details</h2>

                <div class="detail-row">

                    <span class="detail-label">Farm Size</span>

                    <span class="detail-value">
                        <?= e($user['farm_size'] !== null && $user['farm_size'] !== ''
                            ? $user['farm_size'] . ' Acres'
                            : 'Not provided') ?>
                    </span>

                </div>

                <div class="detail-row">

                    <span class="detail-label">Farm Location</span>

                    <span class="detail-value">
                        <?= e($user['farm_location'] ?: 'Not provided') ?>
                    </span>

                </div>

                <div class="detail-row">

                    <span class="detail-label">User Role</span>

                    <span class="detail-value">
                        <?= e($user['role'] ?: 'Farmer') ?>
                    </span>

                </div>

                <div class="detail-row">

                    <span class="detail-label">Soil Type</span>

                    <span class="detail-value">
                        <?= e($user['soil_type'] ?: 'Not provided') ?>
                    </span>

                </div>

                <div class="detail-row">

                    <span class="detail-label">Irrigation</span>

                    <span class="detail-value">
                        <?= e($user['irrigation_type'] ?: 'Not provided') ?>
                    </span>

                </div>

                <div class="detail-row">

                    <span class="detail-label">Account Created</span>

                    <span class="detail-value">
                        <?= e($user['created_at']) ?>
                    </span>

                </div>

            </div>

            <!-- Crop Types -->

            <div class="profile-box">

                <h2>🌱 Crop Types</h2>

                <div class="crop-tags">

                    <?php if (!empty($crops)): ?>

                        <?php foreach ($crops as $crop): ?>

                            <span class="crop-tag">
                                <?= e($crop) ?>
                            </span>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <p>No crops added yet.</p>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

    <!-- Edit Profile -->

    <div class="profile-box" id="edit-profile">

        <h2>✏️ Edit Profile & Farm Details</h2>

        <form method="POST" action="profile.php" class="form-grid">

            <div class="form-group">

                <label for="full_name">Full Name *</label>

                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    value="<?= e($user['full_name']) ?>"
                    required
                    maxlength="150">

            </div>

            <div class="form-group">

                <label for="mobile">Mobile Number *</label>

                <input
                    type="text"
                    id="mobile"
                    name="mobile"
                    value="<?= e($user['mobile']) ?>"
                    required
                    maxlength="20">

            </div>

            <div class="form-group">

                <label for="farm_size">Farm Size (Acres)</label>

                <input
                    type="number"
                    step="0.01"
                    min="0"
                    id="farm_size"
                    name="farm_size"
                    value="<?= e($user['farm_size']) ?>"
                    placeholder="Example: 5.5">

            </div>

            <div class="form-group">

                <label for="farm_location">Farm Location</label>

                <input
                    type="text"
                    id="farm_location"
                    name="farm_location"
                    value="<?= e($user['farm_location']) ?>"
                    placeholder="Example: Kopargaon">

            </div>

            <div class="form-group full">

                <label for="crop_types">Crop Types</label>

                <input
                    type="text"
                    id="crop_types"
                    name="crop_types"
                    value="<?= e($user['crop_types']) ?>"
                    placeholder="Wheat, Onion, Tomato">

            </div>

            <div class="form-group">

                <label for="soil_type">Soil Type</label>

                <select id="soil_type" name="soil_type">

                    <option value="">Select Soil Type</option>

                    <?php
                    $soil_options = ['Black', 'Red', 'Alluvial', 'Other'];

                    foreach ($soil_options as $option):
                    ?>

                        <option
                            value="<?= e($option) ?>"
                            <?= ($user['soil_type'] === $option) ? 'selected' : '' ?>>

                            <?= e($option) ?> Soil

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group">

                <label for="irrigation_type">Irrigation Type</label>

                <select id="irrigation_type" name="irrigation_type">

                    <option value="">Select Irrigation Type</option>

                    <?php
                    $irrigation_options = [
                        'Drip',
                        'Sprinkler',
                        'Canal',
                        'Borewell',
                        'Rainfed'
                    ];

                    foreach ($irrigation_options as $option):
                    ?>

                        <option
                            value="<?= e($option) ?>"
                            <?= ($user['irrigation_type'] === $option) ? 'selected' : '' ?>>

                            <?= e($option) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <button type="submit" class="btn save-btn">
                💾 Save Profile
            </button>

        </form>

    </div>

    <!-- Transaction History Notice -->

    <div class="profile-box">

        <h2>💰 Transaction History</h2>

        <p>
            Your transaction records will appear here when
            connected to the SmartCrop transaction database.
        </p>

    </div>

    <div class="profile-actions">

        <a href="dashboard.php" class="btn">
            Back to Dashboard
        </a>

        <a href="../PHP/logout.php" class="btn">
            Logout
        </a>

    </div>

</div>

<footer>

    <p>© 2026 SmartCrop. All Rights Reserved.</p>

    <p>Smart Crop Advisory & Marketplace Platform</p>

</footer>

</body>
</html>