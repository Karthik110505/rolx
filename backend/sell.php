<?php
session_start();
include("db.php"); // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to sell a product.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $product_name = htmlspecialchars($_POST['product_name']);
        $description = htmlspecialchars($_POST['description']);

        // Get owner details from session
        $owner_id = $_SESSION['user_id'];
        $owner_phone = $_SESSION['phone'];

        // Handle file upload
        $target_dir = "products/";
        $product_image = basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $product_image;

        if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            die("Error uploading product image.");
        }

        // Insert into database
        $sql = "INSERT INTO products (product_name, description, owner_id, owner_phone, product_image)
                VALUES (:product_name, :description, :owner_id, :owner_phone, :product_image)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->bindParam(':owner_phone', $owner_phone);
        $stmt->bindParam(':product_image', $product_image);

        if ($stmt->execute()) {
            echo "Product listed successfully!";
        } else {
            echo "Error: Could not add product.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
