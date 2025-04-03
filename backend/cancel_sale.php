<?php
session_start();
include 'db.php'; // Ensure database connection

header("Content-Type: application/json");

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['cart_id'])) {
    echo json_encode(["success" => false, "error" => "Cart ID is missing"]);
    exit();
}

$cart_id = $data['cart_id'];

try {
    // Update cart table: Set status to CANCELLED
    $stmt = $conn->prepare("UPDATE cart SET status = 'CANCELLED' WHERE cart_id = :cart_id");
    $stmt->bindParam(':cart_id', $cart_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Sale cancelled successfully"]);
        exit();
    }

    echo json_encode(["success" => false, "error" => "Failed to update cart status"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
