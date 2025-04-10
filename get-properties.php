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

$sql = "SELECT 
          p.*, 
          o.type AS owner_type, 
          o.phone AS owner_phone,
          (
            SELECT image_url 
            FROM property_images 
            WHERE property_id = p.id AND type = 'property' 
            ORDER BY id ASC LIMIT 1
          ) AS image
        FROM properties p
        LEFT JOIN owners o ON p.owner_id = o.id";

$result = $conn->query($sql);
$properties = [];

while ($row = $result->fetch_assoc()) {
    $properties[] = $row;
}

header('Content-Type: application/json');
echo json_encode($properties);
?>
