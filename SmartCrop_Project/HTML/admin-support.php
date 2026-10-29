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


/* Fetch Support Requests */

$sql = "SELECT
            support_requests.id,
            support_requests.name,
            support_requests.email,
            support_requests.message,
            support_requests.status,
            support_requests.created_at
        FROM support_requests
        ORDER BY support_requests.id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Support Requests - SmartCrop</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        .support-admin-section {
            min-height: 80vh;
            padding: 50px 20px;
        }

        .support-admin-container {
            max-width: 1100px;
            margin: auto;
        }

        .support-admin-title {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .support-admin-subtitle {
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

        .support-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        .support-table th {
            background: #2e7d32;
            color: white;
            padding: 14px;
            text-align: left;
        }

        .support-table td {
            padding: 13px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        .support-table tr:hover {
            background: #f5f5f5;
        }

        .message-cell {
            max-width: 300px;
            line-height: 1.5;
        }

        .status {
            font-weight: bold;
        }

        .back-button {
            text-align: center;
            margin-top: 30px;
        }

        .back-button a {
            text-decoration: none;
        }

        .no-requests {
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

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="../php/logout.php" class="login-btn">
                Logout
            </a>

        </div>

    </nav>

</header>


<section class="support-admin-section">

    <div class="support-admin-container">

        <h1 class="support-admin-title">
            📩 Support Requests
        </h1>

        <p class="support-admin-subtitle">
            View support requests submitted by SmartCrop users
        </p>


        <div class="table-card">

            <?php if ($result && $result->num_rows > 0): ?>

                <table class="support-table">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Name</th>

                            <th>Email</th>

                            <th>Message</th>

                            <th>Status</th>

                            <th>Created At</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php while ($request = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars($request["id"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($request["name"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($request["email"]);
                                    ?>
                                </td>

                                <td class="message-cell">
                                    <?php
                                    echo htmlspecialchars($request["message"]);
                                    ?>
                                </td>

                                <td class="status">
                                    <?php
                                    echo htmlspecialchars($request["status"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($request["created_at"]);
                                    ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="no-requests">
                    No support requests found.
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