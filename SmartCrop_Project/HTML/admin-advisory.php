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


/* Fetch Crop Advisory Records */

$sql = "SELECT
            crop_advisory.id,
            users.full_name,
            users.email,
            crop_advisory.soil_type,
            crop_advisory.temperature,
            crop_advisory.soil_moisture,
            crop_advisory.recommended_crop,
            crop_advisory.advice,
            crop_advisory.created_at
        FROM crop_advisory
        LEFT JOIN users
        ON crop_advisory.user_id = users.id
        ORDER BY crop_advisory.id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Crop Advisory Records - SmartCrop</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        .advisory-section {
            min-height: 80vh;
            padding: 50px 20px;
        }

        .advisory-container {
            max-width: 1200px;
            margin: auto;
        }

        .advisory-title {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .advisory-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .table-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.10);
            overflow-x: auto;
        }

        .advisory-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        .advisory-table th {
            background: #2e7d32;
            color: white;
            padding: 14px;
            text-align: left;
        }

        .advisory-table td {
            padding: 13px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        .advisory-table tr:hover {
            background: #f5f5f5;
        }

        .recommended {
            font-weight: bold;
            color: #2e7d32;
        }

        .advice-cell {
            max-width: 280px;
            line-height: 1.5;
        }

        .back-button {
            text-align: center;
            margin-top: 30px;
        }

        .back-button a {
            text-decoration: none;
        }

        .no-records {
            text-align: center;
            color: #666;
            padding: 30px;
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

            <a href="admin.php">
                Admin Panel
            </a>

            <a href="admin-users.php">
                Users
            </a>

            <a href="admin-products.php">
                Marketplace
            </a>

            <a href="admin-support.php">
                Support
            </a>

            <a href="../php/logout.php" class="login-btn">
                Logout
            </a>

        </div>

    </nav>

</header>


<section class="advisory-section">

    <div class="advisory-container">

        <h1 class="advisory-title">
            🌱 Crop Advisory Records
        </h1>

        <p class="advisory-subtitle">
            View crop recommendations submitted by users
        </p>


        <div class="table-card">

            <?php if ($result && $result->num_rows > 0): ?>

                <table class="advisory-table">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>User</th>

                            <th>Email</th>

                            <th>Soil Type</th>

                            <th>Temperature</th>

                            <th>Soil Moisture</th>

                            <th>Recommended Crop</th>

                            <th>Advice</th>

                            <th>Created At</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php while ($record = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars($record["id"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $record["full_name"] ?? "Unknown"
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $record["email"] ?? "N/A"
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $record["soil_type"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $record["temperature"]
                                    );
                                    ?> °C
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $record["soil_moisture"]
                                    ); ?> %
                                </td>

                                <td class="recommended">
                                    <?php
                                    echo htmlspecialchars(
                                        $record["recommended_crop"]
                                    );
                                    ?>
                                </td>

                                <td class="advice-cell">
                                    <?php
                                    echo htmlspecialchars(
                                        $record["advice"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $record["created_at"]
                                    );
                                    ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="no-records">
                    No crop advisory records found.
                </div>

            <?php endif; ?>

        </div>


        <div class="back-button">

            <a href="admin.php" class="btn">
                ← Back to Admin Panel
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

<?php

$conn->close();

?>