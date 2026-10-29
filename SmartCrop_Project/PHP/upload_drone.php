<?php

// Show errors for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Database connection
$host = "localhost";
$username = "root";
$password = "Pass@123";
$database = "smartcrop_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("DATABASE CONNECTION ERROR: " . $conn->connect_error);
}


// Check POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("ERROR: Invalid request method.");
}


// Get form data
$farmer_name = $_POST["farmer_name"] ?? "";
$gut_number = $_POST["gut_number"] ?? "";
$field_name = $_POST["field_name"] ?? "";
$crop_name = $_POST["crop_name"] ?? "";

$crop_health = $_POST["crop_health"] ?? "Healthy";
$water_stress = $_POST["water_stress"] ?? "Low";
$pest_risk = $_POST["pest_risk"] ?? "Low";
$disease_risk = $_POST["disease_risk"] ?? "Low";
$dry_area = $_POST["dry_area"] ?? "Low";
$recommendation = $_POST["recommendation"] ?? "";


// GPS Location
$latitude = $_POST["latitude"] ?? null;
$longitude = $_POST["longitude"] ?? null;


// Check required information
if ($farmer_name === "") {
    die("ERROR: Farmer name missing.");
}

if ($gut_number === "") {
    die("ERROR: Gut number missing.");
}

if ($field_name === "") {
    die("ERROR: Field name missing.");
}

if ($crop_name === "") {
    die("ERROR: Crop name missing.");
}


// Check GPS
if ($latitude === null || $longitude === null) {
    die("ERROR: GPS location was not received.");
}


// Check image
if (!isset($_FILES["drone_image"])) {
    die("ERROR: drone_image was not received by PHP.");
}

if ($_FILES["drone_image"]["error"] !== UPLOAD_ERR_OK) {
    die(
        "ERROR: Image upload error code: " .
        $_FILES["drone_image"]["error"]
    );
}


$fileName = $_FILES["drone_image"]["name"];
$fileTmpName = $_FILES["drone_image"]["tmp_name"];
$fileSize = $_FILES["drone_image"]["size"];


$allowedTypes = ["jpg", "jpeg", "png", "webp"];

$fileExtension = strtolower(
    pathinfo($fileName, PATHINFO_EXTENSION)
);


if (!in_array($fileExtension, $allowedTypes)) {
    die("ERROR: Invalid image type.");
}


if ($fileSize > 5 * 1024 * 1024) {
    die("ERROR: Image size is greater than 5 MB.");
}


// Upload folder
$uploadDirectory = "../uploads/";


if (!is_dir($uploadDirectory)) {

    if (!mkdir($uploadDirectory, 0777, true)) {
        die("ERROR: Could not create uploads folder.");
    }
}


// Create unique filename
$newFileName =
    "drone_" .
    time() .
    "_" .
    uniqid() .
    "." .
    $fileExtension;


$uploadPath = $uploadDirectory . $newFileName;


// Move image
if (!move_uploaded_file($fileTmpName, $uploadPath)) {
    die("ERROR: Could not move uploaded image.");
}


// Database image path
$imagePath = "uploads/" . $newFileName;


// Prepare SQL
$sql = "INSERT INTO drone_reports
(
    farmer_name,
    gut_number,
    field_name,
    crop_name,
    image_path,
    crop_health,
    water_stress,
    pest_risk,
    disease_risk,
    dry_area,
    recommendation,
    latitude,
    longitude
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


$stmt = $conn->prepare($sql);


if (!$stmt) {
    die("SQL PREPARE ERROR: " . $conn->error);
}


// Bind parameters
$stmt->bind_param(
    "sssssssssssdd",
    $farmer_name,
    $gut_number,
    $field_name,
    $crop_name,
    $imagePath,
    $crop_health,
    $water_stress,
    $pest_risk,
    $disease_risk,
    $dry_area,
    $recommendation,
    $latitude,
    $longitude
);


// Execute
if (!$stmt->execute()) {

    die("SQL EXECUTE ERROR: " . $stmt->error);

}


// Success
echo "SUCCESS: Drone Report Saved Successfully!";


$stmt->close();
$conn->close();

?>