<?php
session_start();

require_once "../php/db.php";

/* Admin Login Check */

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


/* Fetch All Users */

$sql = "SELECT id, full_name, email, mobile, role, created_at
        FROM users
        ORDER BY id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Users - SmartCrop</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        .users-section {
            min-height: 80vh;
            padding: 50px 20px;
        }

        .users-container {
            max-width: 1100px;
            margin: auto;
        }

        .users-title {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .users-subtitle {
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

        .users-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        .users-table th {
            background: #2e7d32;
            color: white;
            padding: 14px;
            text-align: left;
        }

        .users-table td {
            padding: 13px;
            border-bottom: 1px solid #ddd;
        }

        .users-table tr:hover {
            background: #f5f5f5;
        }

        .role {
            font-weight: bold;
        }

        .back-button {
            text-align: center;
            margin-top: 30px;
        }

        .back-button a {
            text-decoration: none;
        }

        .no-users {
            text-align: center;
            color: #666;
            padding: 30px;
        }

        @media (max-width: 700px) {

            .table-card {
                padding: 15px;
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

            <a href="admin.php">
                Admin Panel
            </a>

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="profile.php">
                Profile
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


<section class="users-section">

    <div class="users-container">

        <h1 class="users-title">
            👥 Manage Users
        </h1>

        <p class="users-subtitle">
            View all registered SmartCrop users
        </p>


        <div class="table-card">

            <?php if ($result && $result->num_rows > 0): ?>

                <table class="users-table">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Full Name</th>

                            <th>Email</th>

                            <th>Mobile</th>

                            <th>Role</th>

                            <th>Created At</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php while ($user = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars($user["id"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($user["full_name"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($user["email"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($user["mobile"]);
                                    ?>
                                </td>

                                <td class="role">
                                    <?php
                                    echo htmlspecialchars($user["role"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($user["created_at"]);
                                    ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="no-users">
                    No users found.
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