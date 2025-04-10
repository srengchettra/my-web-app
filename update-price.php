<?php
$host = "localhost";
$username = "root";
$password = "";
$db_name = "property_portal";

// Connect to DB
$conn = new mysqli($host, $username, $password, $db_name);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

if (isset($_POST['id']) && isset($_POST['price'])) {
    $id = (int)$_POST['id'];
    $price = (float)$_POST['price'];

    // Update price
    $updateSql = "UPDATE properties SET price = '$price' WHERE id = '$id'";
    $logSql = "INSERT INTO price_history (property_id, price) VALUES ('$id', '$price')";

    if ($conn->query($updateSql) === TRUE && $conn->query($logSql) === TRUE) {
        echo "✅ Price updated and logged!";
    } else {
        echo "❌ Failed: " . $conn->error;
    }
} else {
    echo "❌ Missing property ID or price.";
}

$conn->close();
?>
