<?php
$host = "localhost";
$username = "root";
$password = "";
$db_name = "property_portal";

// Connect to database
$conn = new mysqli($host, $username, $password, $db_name);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Gather owner info
$owner_type = $_POST['owner_type'];
$owner_name = $_POST['owner_name'] ?? '';
$owner_phone = $_POST['owner_phone'];

// Insert owner
$owner_sql = "INSERT INTO owners (type, name, phone) VALUES ('$owner_type', '$owner_name', '$owner_phone')";
if (!$conn->query($owner_sql)) {
    die("❌ Owner insert failed: " . $conn->error);
}
$owner_id = $conn->insert_id;

// Gather property info
$property_type = $_POST['property_type'];
$purpose = $_POST['purpose'];
$price = $_POST['price'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];
$size = $_POST['size'];
$province = $_POST['province'];
$khan = $_POST['khan'];
$sangkat = $_POST['sangkat'];
$title_deed_type = $_POST['title_deed_type'] ?? '';
$title_deed_number = $_POST['title_deed_number'] ?? '';
$road_type = $_POST['road_type'] ?? '';
$width = $_POST['width'] ?? '';
$length = $_POST['length'] ?? '';
$building_size = $_POST['building_size'] ?? '';
$description = $_POST['description'] ?? '';
$title = "$property_type for $purpose";

// Insert property
$property_sql = "INSERT INTO properties 
(title, property_type, purpose, price, lat, lng, size, province, khan, sangkat, 
 title_deed_type, title_deed_number, road_type, width, length, building_size, description, owner_id)
VALUES (
 '$title', '$property_type', '$purpose', '$price', '$lat', '$lng', '$size', 
 '$province', '$khan', '$sangkat', '$title_deed_type', '$title_deed_number', 
 '$road_type', '$width', '$length', '$building_size', '$description', '$owner_id')";

if (!$conn->query($property_sql)) {
    die("❌ Property insert failed: " . $conn->error);
}
$property_id = $conn->insert_id;

function saveFiles($files, $property_id, $type, $upload_dir, $conn) {
    foreach ($files['tmp_name'] as $key => $tmp_name) {
        if ($tmp_name) {
            $filename = time() . "_" . basename($files['name'][$key]);
            $targetPath = $upload_dir . "/" . $filename;
            if (move_uploaded_file($tmp_name, $targetPath)) {
                $sql = "INSERT INTO property_images (property_id, image_url, type) VALUES ('$property_id', '$targetPath', '$type')";
                if (!$conn->query($sql)) {
                    echo "⚠️ Failed to insert $type image: " . $conn->error . "<br>";
                }
            } else {
                echo "⚠️ Failed to move $type file.<br>";
            }
        }
    }
}

// Upload property photos
if (isset($_FILES['property_images'])) {
    saveFiles($_FILES['property_images'], $property_id, 'property', 'property_images', $conn);
}

// Upload title deed images
if (isset($_FILES['title_images'])) {
    saveFiles($_FILES['title_images'], $property_id, 'title', 'title_images', $conn);
}

// Upload KML
if (!empty($_FILES['kml_file']['tmp_name'])) {
    $kml_filename = time() . "_" . basename($_FILES['kml_file']['name']);
    $kml_path = "kml_files/" . $kml_filename;
    if (move_uploaded_file($_FILES['kml_file']['tmp_name'], $kml_path)) {
        $conn->query("UPDATE properties SET kml_file = '$kml_path' WHERE id = '$property_id'");
    } else {
        echo "⚠️ Failed to upload KML file.<br>";
    }
}

echo "<script>
alert('✅ Property added successfully!');
window.location.href = 'listing.html';
</script>";
?>
