<?php
$host = "localhost";
$username = "root";
$password = "";
$db_name = "property_portal";

$conn = new mysqli($host, $username, $password, $db_name);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

if (!isset($_POST['property_id']) || !isset($_FILES['kml_file'])) {
    http_response_code(400);
    echo "❌ Invalid request.";
    exit;
}

$property_id = intval($_POST['property_id']);
$uploadDir = "kml_files/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$kmlFile = $_FILES['kml_file'];
$ext = strtolower(pathinfo($kmlFile['name'], PATHINFO_EXTENSION));

if ($ext !== 'kml') {
    echo "❌ Only KML files are allowed.";
    exit;
}

$filename = time() . "_" . basename($kmlFile['name']);
$targetFile = $uploadDir . $filename;

if (move_uploaded_file($kmlFile['tmp_name'], $targetFile)) {
    // Save file path in database
    $stmt = $conn->prepare("UPDATE properties SET kml_file = ? WHERE id = ?");
    $stmt->bind_param("si", $targetFile, $property_id);

    if ($stmt->execute()) {
        echo "✅ KML file uploaded and linked successfully.";
    } else {
        echo "❌ Failed to update database.";
    }

    $stmt->close();
} else {
    echo "❌ Failed to upload the KML file.";
}

$conn->close();
?>
