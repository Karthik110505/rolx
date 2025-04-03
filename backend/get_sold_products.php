<?php
session_start();
include 'db.php'; // Database connection

if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user']['user_id'];

try {
    $stmt = $conn->prepare("SELECT product_id, product_name, description, owner_phone, product_image 
                            FROM products WHERE owner_id = :owner_id");
    $stmt->bindParam(':owner_id', $user_id);
    $stmt->execute();
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
