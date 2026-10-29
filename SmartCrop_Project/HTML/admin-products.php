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


/* Fetch Marketplace Products */

$sql = "SELECT 
            marketplace.id,
            marketplace.product_name,
            marketplace.category,
            marketplace.price,
            marketplace.unit,
            marketplace.location,
            marketplace.quality_rating,
            marketplace.stock_status,
            marketplace.description,
            marketplace.created_at,
            users.full_name AS seller_name
        FROM marketplace
        LEFT JOIN users
        ON marketplace.seller_id = users.id
        ORDER BY marketplace.id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Marketplace - SmartCrop</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        .products-section {
            min-height: 80vh;
            padding: 50px 20px;
        }

        .products-container {
            max-width: 1200px;
            margin: auto;
        }

        .products-title {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .products-subtitle {
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

        .products-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        .products-table th {
            background: #2e7d32;
            color: white;
            padding: 14px;
            text-align: left;
        }

        .products-table td {
            padding: 13px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        .products-table tr:hover {
            background: #f5f5f5;
        }

        .price {
            font-weight: bold;
            color: #2e7d32;
        }

        .stock {
            font-weight: bold;
        }

        .description {
            max-width: 220px;
        }

        .back-button {
            text-align: center;
            margin-top: 30px;
        }

        .back-button a {
            text-decoration: none;
        }

        .no-products {
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

            <a href="dashboard.php">
                Dashboard
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


<section class="products-section">

    <div class="products-container">

        <h1 class="products-title">
            🌾 Manage Marketplace
        </h1>

        <p class="products-subtitle">
            View all marketplace products and sellers
        </p>


        <div class="table-card">

            <?php if ($result && $result->num_rows > 0): ?>

                <table class="products-table">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Product</th>

                            <th>Category</th>

                            <th>Seller</th>

                            <th>Price</th>

                            <th>Unit</th>

                            <th>Location</th>

                            <th>Quality</th>

                            <th>Stock</th>

                            <th>Description</th>

                            <th>Created At</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php while ($product = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars($product["id"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($product["product_name"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($product["category"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($product["seller_name"] ?? "Unknown");
                                    ?>
                                </td>

                                <td class="price">
                                    ₹<?php
                                    echo htmlspecialchars($product["price"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($product["unit"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($product["location"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($product["quality_rating"]);
                                    ?>/5
                                </td>

                                <td class="stock">
                                    <?php
                                    echo htmlspecialchars($product["stock_status"]);
                                    ?>
                                </td>

                                <td class="description">
                                    <?php
                                    echo htmlspecialchars($product["description"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($product["created_at"]);
                                    ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="no-products">
                    No marketplace products found.
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