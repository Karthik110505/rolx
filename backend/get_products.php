<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user']['user_id'];

try {
    // Fetch products along with owner's username, product_id, and owner_id
    $stmt = $conn->prepare("
        SELECT 
            p.product_id,
            p.owner_id,
            p.product_name,
            p.description,
            p.product_image,
            p.owner_phone,
            u.username
        FROM products p
        JOIN users u ON p.owner_id = u.user_id
        WHERE p.owner_id != :user_id AND p.status != 'SOLD'

    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
