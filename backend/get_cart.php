<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$buyer_id = $_SESSION['user']['user_id'];

try {
    // Fetch only the products added to the cart by the logged-in buyer but NOT the ones they own
    $stmt = $conn->prepare("
        SELECT 
            cart.cart_id, 
            cart.product_id,  
            products.product_name, 
            products.product_image, 
            products.description, 
            users.user_id AS owner_id, 
            users.username AS owner_name, 
            users.phone AS owner_phone, 
            cart.status 
        FROM cart 
        JOIN products ON cart.product_id = products.product_id 
        JOIN users ON products.owner_id = users.user_id
        WHERE cart.buyer_id = :buyer_id 
          AND products.owner_id != :buyer_id  -- Exclude products where user is the owner
    ");

    // ✅ FIX: Bind as a STRING instead of an integer
    $stmt->bindParam(':buyer_id', $buyer_id, PDO::PARAM_STR);
    $stmt->execute();
    
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($cartItems);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
