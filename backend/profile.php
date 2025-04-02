<?php
session_start();
include 'db.php'; // Database connection

if (!isset($_SESSION['user']['email'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$email = $_SESSION['user']['email'];

try {
    $stmt = $conn->prepare("SELECT user_id, username, email, full_name, phone, profile_picture, department, year_of_study 
                            FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode($user); // Return user details as JSON
    } else {
        echo json_encode(["error" => "User not found"]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
