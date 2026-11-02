<?php
session_start();
require_once "../php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $seller_id = $_SESSION["user_id"];

    $product_name = trim($_POST["product_name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $price = floatval($_POST["price"] ?? 0);
    $unit = trim($_POST["unit"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $quality_rating = floatval($_POST["quality_rating"] ?? 0);
    $stock_status = trim($_POST["stock_status"] ?? "");
    $description = trim($_POST["description"] ?? "");
    
$image_path = "";
$upload_error = "";

if (
    isset($_FILES["product_image"]) &&
    $_FILES["product_image"]["error"] !== UPLOAD_ERR_NO_FILE
) {
    if ($_FILES["product_image"]["error"] !== UPLOAD_ERR_OK) {
        $upload_error = "Image upload failed.";
    } else {
        $file_type = mime_content_type(
            $_FILES["product_image"]["tmp_name"]
        );

        $allowed_types = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];

        if (!isset($allowed_types[$file_type])) {
            $upload_error = "Only JPG, PNG and WEBP images are allowed.";
        } elseif ($_FILES["product_image"]["size"] > 2 * 1024 * 1024) {
            $upload_error = "Image must be smaller than 2 MB.";
        } else {
            $upload_dir = __DIR__ . "/uploads/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_name = bin2hex(random_bytes(16))
                . "." . $allowed_types[$file_type];

            if (move_uploaded_file(
                $_FILES["product_image"]["tmp_name"],
                $upload_dir . $file_name
            )) {
                $image_path = "uploads/" . $file_name;
            } else {
                $upload_error = "Unable to save image.";
            }
        }
    }
}


    if (
        $product_name === "" ||
        $category === "" ||
        $price <= 0 ||
        $unit === "" ||
        $location === "" ||
        !preg_match('/^[0-9]{10}$/', $phone) ||
        $stock_status === ""
    ) {
        $message = "Please fill all required fields.";
    } 
else {

    
$phone_stmt = $conn->prepare(
    "UPDATE users SET phone = ? WHERE id = ?"
);

$phone_stmt->bind_param("si", $phone, $seller_id);
$phone_stmt->execute();
$phone_stmt->close();


    $stmt = $conn->prepare("
        INSERT INTO marketplace
        (
            seller_id,
            product_name,
            category,
            price,
            unit,
            location,
            quality_rating,
            stock_status,
            description,
            image_path
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issdssdsss",
        $seller_id,
        $product_name,
        $category,
        $price,
        $unit,
        $location,
        $quality_rating,
        $stock_status,
        $description,
        $image_path
    );

    if ($stmt->execute()) {
    $message = "Product listed successfully! Image path: " . $image_path;
} else {
    $message = "Database Error: " . $stmt->error;
}

    $stmt->close();
}
}


/* Get marketplace products */

$products = [];

$result = $conn->query("
    SELECT
        m.product_name,
        m.category,
        m.price,
        m.unit,
        m.location,
        m.quality_rating,
        m.stock_status,
        m.description,
        m.image_path,
        u.full_name,
        u.phone
    FROM marketplace m
    LEFT JOIN users u
        ON m.seller_id = u.id
    ORDER BY m.id DESC
");

if ($result) {

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Marketplace - SmartCrop</title>

    <link rel="stylesheet"
          href="../css/style.css">

    <style>

        .market-header {
            background: #e8f5e9;
            padding: 45px 6%;
        }

        .market-header h1 {
            color: #1b5e20;
            margin-bottom: 8px;
        }

        .market-container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .market-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 35px;
        }

        .market-box h2 {
            color: #2e7d32;
            margin-bottom: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            margin-bottom: 5px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .submit-btn {
            width: 100%;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
        }

        .message {
            background: #e8f5e9;
            color: #1b5e20;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .product-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .product-card h3 {
            color: #2e7d32;
            margin-bottom: 8px;
        }

        .product-price {
            color: #1b5e20;
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }

        .product-info {
            color: #666;
            margin-bottom: 7px;
        }

        .stock {
            display: inline-block;
            background: #e8f5e9;
            color: #2e7d32;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: bold;
        }

        @media (max-width: 900px) {

            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: auto;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

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

            <a href="crop-advisory.php">
                Crop Advisory
            </a>

            <a href="crop-health.php">
                Crop Health
            </a>

            <a href="marketplace.php">
                Marketplace
            </a>

            <a href="weather.html">
                Weather
            </a>

            <a href="profile.html">
                Profile
            </a>

        </div>

    </nav>


    <!-- Header -->

    <section class="market-header">

        <h1>🛒 SmartCrop Marketplace</h1>

        <p>
            Buy and sell agricultural products
            directly through SmartCrop.
        </p>

    </section>


    <div class="market-container">


        <!-- Add Product -->

        <div class="market-box">

            <h2>➕ Add New Product</h2>

            <?php if ($message !== ""): ?>

                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>

<form method="POST"
      action="marketplace.php"
      enctype="multipart/form-data">

                <div class="form-grid">


                    <div class="form-group">

                        <label for="product_name">
                            Product Name
                        </label>

                        <input type="text"
                               id="product_name"
                               name="product_name"
                               placeholder="Example: Wheat"
                               required>

                    </div>


                    <div class="form-group">

                        <label for="category">
                            Category
                        </label>

                        <select id="category"
                                name="category"
                                required>

                            <option value="">
                                Select Category
                            </option>

                            <option value="Grains">
                                Grains
                            </option>

                            <option value="Vegetables">
                                Vegetables
                            </option>

                            <option value="Fruits">
                                Fruits
                            </option>

                            <option value="Pulses">
                                Pulses
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="price">
                            Price
                        </label>

                        <input type="number"
                               id="price"
                               name="price"
                               min="1"
                               step="0.01"
                               placeholder="Enter price"
                               required>

                    </div>


                    <div class="form-group">

                        <label for="unit">
                            Unit
                        </label>

                        <select id="unit"
                                name="unit"
                                required>

                            <option value="">
                                Select Unit
                            </option>

                            <option value="Per Quintal">
                                Per Quintal
                            </option>

                            <option value="Per Kg">
                                Per Kg
                            </option>

                            <option value="Per Ton">
                                Per Ton
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="location">
                            Location
                        </label>

                        <input type="text"
                               id="location"
                               name="location"
                               placeholder="Example: Kopargaon"
                               required>

                    </div>

                    
<div class="form-group">
    <label for="phone">Seller Mobile Number</label>
    <input
        type="tel"
        id="phone"
        name="phone"
        placeholder="Enter 10 digit mobile number"
        pattern="[0-9]{10}"
        maxlength="10"
        minlength="10"
        required
    >
</div>



                    <div class="form-group">

                        <label for="quality_rating">
                            Quality Rating
                        </label>

                        <input type="number"
                               id="quality_rating"
                               name="quality_rating"
                               min="0"
                               max="5"
                               step="0.1"
                               value="4.5">

                    </div>


                    <div class="form-group">

                        <label for="stock_status">
                            Stock Status
                        </label>

                        <select id="stock_status"
                                name="stock_status"
                                required>

                            <option value="">
                                Select Stock Status
                            </option>

                            <option value="Available">
                                Available
                            </option>

                            <option value="Limited">
                                Limited Stock
                            </option>

                            <option value="Out of Stock">
                                Out of Stock
                            </option>

                        </select>

                    </div>

<div class="form-group full-width">
    <label for="product_image">Upload Product Image</label>
    <input
        type="file"
        id="product_image"
        name="product_image"
        accept=".jpg,.jpeg,.png,.webp"
    >
    <small>JPG, PNG or WEBP; maximum 2 MB.</small>
</div>


                    <div class="form-group full-width">

                        <label for="description">
                            Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            placeholder="Enter product description"></textarea>

                    </div>

                </div>


                <button type="submit"
                        class="btn submit-btn">

                    🛒 Add Product

                </button>

            </form>

        </div>


        <!-- Products -->

        <div class="market-box">

            <h2>🌾 Available Products</h2>

            <div class="products-grid">

                <?php if (count($products) > 0): ?>

                    <?php foreach ($products as $product): ?>

                        <div class="product-card">

                           
<div class="product-icon">
    <?php if (!empty($product["image_path"])): ?>
        <img
            src="<?php echo htmlspecialchars($product["image_path"], ENT_QUOTES, 'UTF-8'); ?>"
            alt="<?php echo htmlspecialchars($product["product_name"], ENT_QUOTES, 'UTF-8'); ?>"
            class="product-image"
        >
    <?php else: ?>
        🌾
    <?php endif; ?>
</div>


                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $product["product_name"]
                                );
                                ?>
                            </h3>

                            <p class="product-info">

                                Category:
                                <?php
                                echo htmlspecialchars(
                                    $product["category"]
                                );
                                ?>

                            </p>

                            <p class="product-price">

                                ₹<?php
                                echo number_format(
                                    $product["price"]
                                );
                                ?>

                                /
                                <?php
                                echo htmlspecialchars(
                                    $product["unit"]
                                );
                                ?>

                            </p>

                            <p class="product-info">

                                📍
                                <?php
                                echo htmlspecialchars(
                                    $product["location"]
                                );
                                ?>

                            </p>

                            <p class="product-info">

                                ⭐
                                <?php
                                echo htmlspecialchars(
                                    $product["quality_rating"]
                                );
                                ?>/5

                            </p>

                            <p class="product-info">

                                Seller:
                                <?php
                                echo htmlspecialchars(
                                    $product["full_name"] ?? "Unknown"
                                );
                                ?>
                                
<?php
$phone = preg_replace('/\D/', '', $product["phone"] ?? "");

if (strlen($phone) === 10):
    $whatsapp_number = "91" . $phone;
?>
    <p>
        📞 <a href="tel:<?php echo htmlspecialchars($phone); ?>">
            Call Seller
        </a>
    </p>

    <p>
        💬 <a href="https://wa.me/<?php echo $whatsapp_number; ?>"
             target="_blank" rel="noopener noreferrer">
            WhatsApp Seller
        </a>
    </p>
<?php else: ?>
    <p>Seller contact number not available.</p>
<?php endif; ?>


                            </p>

                            <span class="stock">

                                <?php
                                echo htmlspecialchars(
                                    $product["stock_status"]
                                );
                                ?>

                            </span>

                            <?php if (!empty($product["description"])): ?>

                                <p class="product-info"
                                   style="margin-top:12px;">

                                    <?php
                                    echo htmlspecialchars(
                                        $product["description"]
                                    );
                                    ?>

                                </p>

                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p>
                        No products available yet.
                    </p>

                <?php endif; ?>

            </div>

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

<?php
$conn->close();
?>