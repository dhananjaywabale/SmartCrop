<?php
session_start();

require_once "../php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION["user_id"];
$message = "";

/* Get user information */

$stmt = $conn->prepare(
    "SELECT full_name, email
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();


/* Submit Support Request */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $support_message = trim($_POST["support_message"] ?? "");

    if ($support_message === "") {

        $message = "Please enter your message.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO support_requests
            (user_id, name, email, message, status)
            VALUES (?, ?, ?, ?, 'Pending')"
        );

        $stmt->bind_param(
            "isss",
            $user_id,
            $user["full_name"],
            $user["email"],
            $support_message
        );

        if ($stmt->execute()) {
            $message = "Support request submitted successfully!";
        } else {
            $message = "Unable to submit your request.";
        }

        $stmt->close();
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Help & Support - SmartCrop</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        .support-section {
            min-height: 80vh;
            padding: 50px 20px;
        }

        .support-container {
            max-width: 900px;
            margin: auto;
        }

        .support-container h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .support-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .support-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.10);
            margin-bottom: 25px;
        }

        .support-card h2 {
            color: #2e7d32;
            margin-bottom: 20px;
        }

        .faq-item {
            background: #f8f9f8;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .faq-item h3 {
            color: #333;
            margin-bottom: 8px;
        }

        .faq-item p {
            color: #666;
            margin: 0;
            line-height: 1.6;
        }

        .support-form {
            display: flex;
            flex-direction: column;
        }

        .support-form label {
            font-weight: bold;
            color: #333;
            margin-bottom: 7px;
            margin-top: 12px;
        }

        .support-form input,
        .support-form textarea {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .support-form input:focus,
        .support-form textarea:focus {
            outline: none;
            border-color: #2e7d32;
        }

        .support-form textarea {
            min-height: 130px;
            resize: vertical;
        }

        .support-form button {
            border: none;
            cursor: pointer;
            margin-top: 20px;
            font-size: 16px;
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

        .support-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .support-option {
            background: #f8f9f8;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
        }

        .support-option h3 {
            color: #2e7d32;
            margin-bottom: 8px;
        }

        .support-option p {
            color: #666;
            margin: 0;
        }

        .support-actions {
            text-align: center;
            margin-top: 25px;
        }

        .support-actions a {
            text-decoration: none;
        }

        @media (max-width: 700px) {

            .support-options {
                grid-template-columns: 1fr;
            }

            .support-card {
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

            <a href="support.php">
                Help & Support
            </a>

            <a href="../php/logout.php" class="login-btn">
                Logout
            </a>

        </div>

    </nav>

</header>


<section class="support-section">

    <div class="support-container">

        <h1>🆘 Help & Support</h1>

        <p class="support-subtitle">
            Get help with SmartCrop services and features
        </p>


        <?php if ($message !== ""): ?>

            <div class="success-message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <!-- FAQ -->

        <div class="support-card">

            <h2>❓ Frequently Asked Questions</h2>


            <div class="faq-item">

                <h3>
                    How does Crop Advisory work?
                </h3>

                <p>
                    SmartCrop uses soil and weather information
                    to provide suitable crop recommendations.
                </p>

            </div>


            <div class="faq-item">

                <h3>
                    How can I check live weather?
                </h3>

                <p>
                    Open the Weather page, select your location
                    and click Check Live Weather.
                </p>

            </div>


            <div class="faq-item">

                <h3>
                    How can I add a marketplace product?
                </h3>

                <p>
                    Open Marketplace, fill in the product details
                    and click Add Product.
                </p>

            </div>


            <div class="faq-item">

                <h3>
                    Can I update my farm details?
                </h3>

                <p>
                    Yes. Open your Profile page and update
                    the Farm Details section.
                </p>

            </div>

        </div>


        <!-- Contact Support -->

        <div class="support-card">

            <h2>📩 Contact Support</h2>

            <form method="POST" class="support-form">

                <label>
                    Your Name
                </label>

                <input
                    type="text"
                    value="<?php echo htmlspecialchars($user["full_name"]); ?>"
                    readonly
                >


                <label>
                    Email Address
                </label>

                <input
                    type="email"
                    value="<?php echo htmlspecialchars($user["email"]); ?>"
                    readonly
                >


                <label for="support_message">
                    Your Message
                </label>

                <textarea
                    id="support_message"
                    name="support_message"
                    placeholder="Describe your problem or question..."
                    required
                ></textarea>


                <button type="submit" class="btn">
                    Submit Support Request
                </button>

            </form>

        </div>


        <!-- Support Options -->

        <div class="support-card">

            <h2>📞 Support Options</h2>

            <div class="support-options">

                <div class="support-option">

                    <h3>💬 Live Chat</h3>

                    <p>
                        Chat with our support team.
                    </p>

                </div>


                <div class="support-option">

                    <h3>📚 Tutorials</h3>

                    <p>
                        Learn how to use SmartCrop.
                    </p>

                </div>


                <div class="support-option">

                    <h3>📧 Email Support</h3>

                    <p>
                        Get assistance through email.
                    </p>

                </div>

            </div>

        </div>


        <div class="support-actions">

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