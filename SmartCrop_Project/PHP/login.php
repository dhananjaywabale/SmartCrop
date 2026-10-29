<?php

// ==========================================
// SmartCrop - Login System
// ==========================================

session_start();

require_once "db.php";


// Check POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}


// Get form data
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";


// Validate input
if ($email === "" || $password === "") {
    die("Please enter email and password.");
}


// Check email
$stmt = $conn->prepare(
    "SELECT id, full_name, email, role, password
     FROM users
     WHERE email = ?"
);

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();


// Check user exists
if ($result->num_rows !== 1) {

    $stmt->close();
    $conn->close();

    echo "
    <script>
        alert('Invalid email or password.');
        window.location.href = '../html/login.html';
    </script>
    ";

    exit;
}


// Get user data
$user = $result->fetch_assoc();


// Verify hashed password
if (!password_verify($password, $user["password"])) {

    $stmt->close();
    $conn->close();

    echo "
    <script>
        alert('Invalid email or password.');
        window.location.href = '../html/login.html';
    </script>
    ";

    exit;
}


// Store user information in session
$_SESSION["user_id"] = $user["id"];
$_SESSION["full_name"] = $user["full_name"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = $user["role"];


// Close connection
$stmt->close();
$conn->close();


// Login successful
echo "
<script>
    alert('Login successful!');
    window.location.href = '../html/dashboard.php';
</script>
";

?>