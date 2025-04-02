<?php
session_start();
include 'db.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT user_id, username, email, password, full_name, phone, profile_picture, department, year_of_study 
                                FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Store user details in the session
            $_SESSION['user'] = [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'phone' => $user['phone'],
                'profile_picture' => $user['profile_picture'],
                'department' => $user['department'],
                'year_of_study' => $user['year_of_study']
            ];
            header("Location: ../frontend/home.html"); // Redirect to dashboard
            exit();
        } else {
            echo "<script>alert('Invalid email or password'); window.location.href='login.html';</script>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
