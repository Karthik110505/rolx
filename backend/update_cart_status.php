<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

if (!isset($_POST['cart_id']) || !isset($_POST['action'])) {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

$cart_id = $_POST['cart_id'];
$product_id = $_POST['product_id'] ?? null;
$buyer_id = $_POST['buyer_id'] ?? null;
$action = $_POST['action'];

try {
    if ($action === "confirm") {
        // Update the cart status to "BUYED" and update the buyer_id in the products table
        $stmt = $conn->prepare("UPDATE cart SET status = 'CONFIRMED' WHERE cart_id = :cart_id");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();

        // Update the product's buyer_id in the products table
        $stmt = $conn->prepare("UPDATE products SET buyer_id = :buyer_id WHERE product_id = :product_id");
        $stmt = $conn->prepare("UPDATE products SET status = 'SOLD' WHERE product_id = :product_id");
        $stmt->bindParam(':buyer_id', $buyer_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
    } elseif ($action === "cancel") {
        // Update the cart status to "CANCELLED"
        $stmt = $conn->prepare("UPDATE cart SET status = 'CANCELLED' WHERE cart_id = :cart_id");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();
    }

    echo json_encode(["success" => "Status updated successfully"]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
