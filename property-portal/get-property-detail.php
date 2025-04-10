<?php
$host = "localhost";
$username = "root";
$password = "";
$db_name = "property_portal";

// Connect to database
$conn = new mysqli($host, $username, $password, $db_name);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Missing property ID"]);
    exit;
}

$propertyId = (int)$_GET['id'];

// Fetch property and owner info
$sql = "SELECT 
            p.*, 
            o.type AS owner_type, 
            o.phone AS owner_phone
        FROM properties p
        LEFT JOIN owners o ON p.owner_id = o.id
        WHERE p.id = $propertyId
        LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Property not found"]);
    exit;
}

$property = $result->fetch_assoc();

// Fetch property images
$imageQuery = "SELECT image_url, type FROM property_images WHERE property_id = $propertyId";
$imageResult = $conn->query($imageQuery);

$propertyImages = [];
$titleImages = [];

while ($row = $imageResult->fetch_assoc()) {
    if ($row['type'] === 'property') {
        $propertyImages[] = $row['image_url'];
    } elseif ($row['type'] === 'title') {
        $titleImages[] = $row['image_url'];
    }
}

$property['property_images'] = $propertyImages;
$property['title_images'] = $titleImages;

// ✅ Fetch price history
$historyQuery = "SELECT price, updated_at FROM price_history WHERE property_id = $propertyId ORDER BY updated_at DESC";
$historyResult = $conn->query($historyQuery);

$priceHistory = [];
while ($row = $historyResult->fetch_assoc()) {
    $priceHistory[] = $row;
}

$property['price_history'] = $priceHistory;

echo json_encode($property);
$conn->close();
?>
