<?php
session_start();
include 'db.php';

error_log("add_to_cart.php: Start"); // Debugging: Start of the script

if (!isset($_SESSION['user']['user_id'])) {
    error_log("add_to_cart.php: User not logged in"); // Debugging
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$buyer_id = $_SESSION['user']['user_id'];
error_log("add_to_cart.php: buyer_id = " . $buyer_id); // Debugging

// Retrieve product_id and owner_id from JSON request body
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['product_id']) || !isset($data['owner_id'])) {
    echo json_encode(["error" => "Invalid input data"]);
    exit();
}

$product_id = $data['product_id'];
$owner_id = $data['owner_id'];

error_log("add_to_cart.php: product_id = " . $product_id . ", owner_id = " . $owner_id); // Debugging

try {
    // 🔹 Step 1: Check if the product already exists in the cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE product_id = :product_id AND owner_id = :owner_id AND buyer_id = :buyer_id");
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':owner_id', $owner_id);
    $stmt->bindParam(':buyer_id', $buyer_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // If product already exists in the cart
        error_log("add_to_cart.php: Product already in cart"); // Debugging
        echo json_encode(["error" => "Product already in cart!"]);
        exit();
    }

    // 🔹 Step 2: Insert product into cart if not already added
    $stmt = $conn->prepare("INSERT INTO cart (product_id, owner_id, buyer_id, status) 
                            VALUES (:product_id, :owner_id, :buyer_id, 'pending')");
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':owner_id', $owner_id);
    $stmt->bindParam(':buyer_id', $buyer_id);

    if ($stmt->execute()) {
        error_log("add_to_cart.php: Product added to cart successfully"); // Debugging
        echo json_encode(["success" => "Product added to cart!"]);
    } else {
        error_log("add_to_cart.php: Insert failed"); // Debugging
        echo json_encode(["error" => "Insert failed"]);
    }
} catch (PDOException $e) {
    error_log("add_to_cart.php: PDOException - " . $e->getMessage()); // Debugging
    echo json_encode(["error" => $e->getMessage()]);
}

error_log("add_to_cart.php: End"); // Debugging: End of the script
?>
