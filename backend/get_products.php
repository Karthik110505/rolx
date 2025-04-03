<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user']['user_id'];

try {
    // Fetch products along with owner's username
    $stmt = $conn->prepare("
        SELECT p.*, u.username 
        FROM products p
        JOIN users u ON p.owner_id = u.user_id
        WHERE p.owner_id != :user_id
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($products);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
