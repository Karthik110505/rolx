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
               COALESCE(c.cart_id, NULL) AS cart_id, 
               COALESCE(c.status, 'AVAILABLE') AS cart_status, 
               COALESCE(c.buyer_id, NULL) AS buyer_id
        FROM products p
        LEFT JOIN cart c ON p.product_id = c.product_id
        WHERE p.owner_id = :owner_id
        GROUP BY p.product_id
    ");
    $stmt->bindParam(':owner_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process each product and determine its state
    foreach ($products as &$product) {
        switch ($product['cart_status']) {
            case 'CONFIRMED':
                $product['status'] = 'SOLD';
                break;
            case 'PENDING':
                $product['status'] = 'PENDING';
                break;
            default:
                $product['status'] = 'AVAILABLE';
                break;
        }
    }

    echo json_encode($products);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
