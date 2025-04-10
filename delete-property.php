<?php
$host = "localhost";
$username = "root";
$password = "";
$db_name = "property_portal";

$conn = new mysqli($host, $username, $password, $db_name);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$property_id = intval($_GET['id'] ?? 0);

if ($property_id > 0) {
    // Delete property images
    $conn->query("DELETE FROM property_images WHERE property_id = $property_id");

    // Delete price history
    $conn->query("DELETE FROM price_history WHERE property_id = $property_id");

    // Delete property
    $conn->query("DELETE FROM properties WHERE id = $property_id");

    echo "✅ Property deleted successfully.";
} else {
    echo "❌ Invalid property ID.";
}
?>
