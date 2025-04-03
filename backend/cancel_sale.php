<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

if (!isset($_POST['cart_id'])) {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

$cart_id = $_POST['cart_id'];

try {
    // Update cart status to "CANCELLED"
    $stmt = $conn->prepare("UPDATE cart SET status = 'CANCELLED' WHERE cart_id = :cart_id");
    $stmt->bindParam(':cart_id', $cart_id);
    $stmt->execute();

    echo json_encode(["success" => "Sale cancelled"]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
