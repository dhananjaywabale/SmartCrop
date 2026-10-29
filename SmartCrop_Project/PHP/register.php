<?php

// SmartCrop - User Registration

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

// Get form data
$full_name = trim($_POST["full_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$role = trim($_POST["role"] ?? "Farmer");
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";

// Basic validation
if (
    $full_name === "" ||
    $email === "" ||
    $mobile === "" ||
    $password === ""
) {
    die("Please fill all required fields.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}

if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

if (strlen($password) < 6) {
    die("Password must contain at least 6 characters.");
}

// Check whether email already exists
$check = $conn->prepare(
    "SELECT id FROM users WHERE email = ?"
);

$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {

    $check->close();

    die("This email is already registered.");

}

$check->close();

// Secure password hashing
$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);

// Insert user into database
$stmt = $conn->prepare(
    "INSERT INTO users
    (full_name, email, mobile, role, password)
    VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sssss",
    $full_name,
    $email,
    $mobile,
    $role,
    $hashed_password
);

if ($stmt->execute()) {

    echo "
    <script>
        alert('Registration successful!');
        window.location.href = '../html/login.html';
    </script>
    ";

} else {

    echo "
    <script>
        alert('Registration failed. Please try again.');
        window.location.href = '../html/register.html';
    </script>
    ";

}

$stmt->close();
$conn->close();

?>