<?php
session_start();

require_once "../php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

if ($_SESSION["role"] !== "Admin") {
    echo "<script>
            alert('Access denied. Admin only.');
            window.location.href = 'dashboard.php';
          </script>";
    exit;
}

/* Statistics */

$user_count = 0;
$product_count = 0;
$support_count = 0;
$advisory_count = 0;

$result = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($result) {
    $user_count = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM marketplace");
if ($result) {
    $product_count = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM support_requests");
if ($result) {
    $support_count = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM crop_advisory");
if ($result) {
    $advisory_count = $result->fetch_assoc()["total"];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Panel - SmartCrop</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        .admin-section {
            min-height: 80vh;
            padding: 50px 20px;
        }

        .admin-container {
            max-width: 1100px;
            margin: auto;
        }

        .admin-title {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .admin-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 35px;
        }

        .admin-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .admin-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.10);
        }

        .admin-card h2 {
            font-size: 32px;
            color: #2e7d32;
            margin-bottom: 8px;
        }

        .admin-card p {
            color: #666;
            margin: 0;
        }

        .admin-menu {
            margin-top: 35px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .admin-menu a {
            text-decoration: none;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.10);
            color: #333;
        }

        .admin-menu a:hover {
            transform: translateY(-3px);
        }

        .admin-menu h3 {
            color: #2e7d32;
            margin-bottom: 8px;
        }

        .admin-menu p {
            color: #666;
            margin: 0;
        }

        .admin-logout {
            text-align: center;
            margin-top: 35px;
        }

        @media (max-width: 800px) {

            .admin-cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .admin-menu {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 500px) {

            .admin-cards {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>


<header>

    <nav class="navbar">

        <div class="logo">
            SmartCrop Admin
        </div>

        <div class="nav-links">

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="profile.php">
                Profile
            </a>

            <a href="settings.php">
                Settings
            </a>

            <a href="support.php">
                Support
            </a>

            <a href="../php/logout.php" class="login-btn">
                Logout
            </a>

        </div>

    </nav>

</header>


<section class="admin-section">

    <div class="admin-container">

        <h1 class="admin-title">
            👨‍💼 Admin Panel
        </h1>

        <p class="admin-subtitle">
            SmartCrop System Administration
        </p>


        <!-- Statistics -->

        <div class="admin-cards">

            <div class="admin-card">

                <h2>
                    <?php echo $user_count; ?>
                </h2>

                <p>
                    Total Users
                </p>

            </div>


            <div class="admin-card">

                <h2>
                    <?php echo $product_count; ?>
                </h2>

                <p>
                    Marketplace Products
                </p>

            </div>


            <div class="admin-card">

                <h2>
                    <?php echo $support_count; ?>
                </h2>

                <p>
                    Support Requests
                </p>

            </div>


            <div class="admin-card">

                <h2>
                    <?php echo $advisory_count; ?>
                </h2>

                <p>
                    Crop Advisory Records
                </p>

            </div>

        </div>


        <!-- Admin Menu -->

        <div class="admin-menu">

            <a href="admin-users.php">

                <h3>
                    👥 Manage Users
                </h3>

                <p>
                    View and manage registered users.
                </p>

            </a>


            <a href="admin-products.php">

                <h3>
                    🌾 Manage Marketplace
                </h3>

                <p>
                    View marketplace products and sellers.
                </p>

            </a>


            <a href="admin-support.php">

                <h3>
                    📩 Support Requests
                </h3>

                <p>
                    View user support requests.
                </p>

            </a>


            <a href="admin-advisory.php">

                <h3>
                    🌱 Crop Advisory Records
                </h3>

                <p>
                    View crop advisory information.
                </p>

            </a>

        </div>


        <div class="admin-logout">

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