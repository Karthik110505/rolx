<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user']['user_id'];
$phone = $_SESSION['user']['phone'];
$product_name = $_POST['product_name'];
$description = $_POST['description'];

if ($_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
    // Ensure 'products' directory exists
    $uploadDir = __DIR__ . "/products/"; // Newly added line
    if (!is_dir($uploadDir)) { // Newly added line
        mkdir($uploadDir, 0777, true); // Newly added line
    }

    $imagePath = "products/" . basename($_FILES['product_image']['name']);
    move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadDir . basename($_FILES['product_image']['name'])); // Newly added line
} else {
    echo json_encode(["error" => "Error uploading image"]);
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO products (product_name, description, owner_id, owner_phone, product_image) 
                            VALUES (:product_name, :description, :owner_id, :owner_phone, :product_image)");
    $stmt->bindParam(':product_name', $product_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':owner_id', $user_id);
    $stmt->bindParam(':owner_phone', $phone);
    $stmt->bindParam(':product_image', $imagePath);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => "Product added successfully"]);
    } else {
        echo json_encode(["error" => "Failed to add product"]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
