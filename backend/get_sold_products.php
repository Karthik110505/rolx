<?php
session_start();
include 'db.php'; // Database connection

if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user']['user_id'];

try {
    // Fetch all products owned by the user
    $stmt = $conn->prepare("
        SELECT p.product_id, p.product_name, p.description, p.owner_phone, p.product_image, 
               c.cart_id, c.status AS cart_status, c.buyer_id
        FROM products p
        LEFT JOIN cart c ON p.product_id = c.product_id
        WHERE p.owner_id = :owner_id
    ");
    $stmt->bindParam(':owner_id', $user_id);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process each product and determine its state
    foreach ($products as &$product) {
        if ($product['cart_id']) {
            if ($product['cart_status'] === 'CONFIRMED') {
                $product['status'] = 'SOLD';
            } elseif ($product['cart_status'] === 'PENDING') {
                $product['status'] = 'PENDING';
            } else {
                $product['status'] = 'AVAILABLE';
            }
        } else {
            $product['status'] = 'AVAILABLE';
        }
    }

    echo json_encode($products);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
