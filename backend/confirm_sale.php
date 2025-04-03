<?php
session_start();
include 'db.php'; // Ensure database connection is included

header("Content-Type: application/json");

// Check if user is logged in
if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit();
}

// Check if cart_id is provided
if (!isset($_POST['cart_id'])) {
    echo json_encode(["success" => false, "error" => "Cart ID is missing"]);
    exit();
}

$cart_id = $_POST['cart_id'];

try {
    // Step 1: Update the cart table to set status = 'CONFIRMED'
    $stmt = $conn->prepare("UPDATE cart SET status = 'CONFIRMED' WHERE cart_id = :cart_id");
    $stmt->bindParam(':cart_id', $cart_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Step 2: Retrieve the product_id and buyer_id from the cart table
        $stmt2 = $conn->prepare("SELECT product_id, buyer_id FROM cart WHERE cart_id = :cart_id");
        $stmt2->bindParam(':cart_id', $cart_id);
        $stmt2->execute();
        $cartData = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($cartData) {
            $product_id = $cartData['product_id'];
            $buyer_id = $cartData['buyer'];

            // Step 3: Update the products table to set status = 'SOLD' and assign buyer_id
            $stmt3 = $conn->prepare("UPDATE products SET status = 'SOLD', buyer = :buyer_id WHERE product_id = :product_id");
            $stmt3->bindParam(':buyer_id', $buyer_id);
            $stmt3->bindParam(':product_id', $product_id);
            $stmt3->execute();

            echo json_encode(["success" => true, "message" => "Sale confirmed"]);
            exit();
        }
    }

    echo json_encode(["success" => false, "error" => "Cart update failed"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
