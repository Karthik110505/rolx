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
        // Step 2: Retrieve the product_id and buyer from the cart table
        $stmt2 = $conn->prepare("SELECT product_id, buyer_id FROM cart WHERE cart_id = :cart_id");
        $stmt2->bindParam(':cart_id', $cart_id);
        $stmt2->execute();
        $cartData = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($cartData) {
            $product_id = $cartData['product_id'];
            $buyer_id = $cartData['buyer_id']; // Fetching buyer correctly

            // Debugging line to check if buyer_id is fetched correctly
            error_log("DEBUG: Buyer ID fetched from cart: " . $buyer_id);

            // Step 3: Update the products table to set status = 'SOLD' and assign buyer
            $stmt3 = $conn->prepare("UPDATE products SET status = 'SOLD', buyer = :buyer_id WHERE product_id = :product_id");
            $stmt3->bindParam(':buyer_id', $buyer_id);
            $stmt3->bindParam(':product_id', $product_id);
            $stmt3->execute();

            if ($stmt3->rowCount() > 0) {
                echo json_encode(["success" => true, "message" => "Sale confirmed and buyer updated"]);
            } else {
                echo json_encode(["success" => false, "error" => "Failed to update product with buyer"]);
            }
            exit();
        } else {
            echo json_encode(["success" => false, "error" => "No product found for this cart"]);
            exit();
        }
    }

    echo json_encode(["success" => false, "error" => "Cart update failed"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
