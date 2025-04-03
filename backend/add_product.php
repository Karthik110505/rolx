<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$buyer_id = $_SESSION['user']['user_id'];
$product_id = $_POST['product_id'];

try {
    // Step 1: Get owner_id from products table using product_id
    $stmt = $conn->prepare("SELECT owner_id FROM products WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(["error" => "Product not found"]);
        exit();
    }

    $owner_id = $product['owner_id'];

    // Step 2: Insert into cart table
    $stmt = $conn->prepare("INSERT INTO cart (product_id, owner_id, buyer_id, status) 
                           VALUES (:product_id, :owner_id, :buyer_id, :status)");
    
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':owner_id', $owner_id);
    $stmt->bindParam(':buyer_id', $buyer_id);
    $stmt->bindValue(':status', 'pending');  // Default status
    
    if ($stmt->execute()) {
        echo json_encode(["success" => "Product added to cart!"]);
    } else {
        echo json_encode(["error" => "Insert failed"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
