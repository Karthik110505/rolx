<?php
session_start();
include 'db.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Check if email exists
        $stmt = $conn->prepare("SELECT user_id, username, email, password, full_name, phone, profile_picture, department, year_of_study 
                                FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // If no user found with this email
            echo "<script>alert('Email not registered. Please register first.'); window.location.href='../frontend/register.html';</script>";
            exit();
        }

        // Email found, now verify password
        if (password_verify($password, $user['password'])) {
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
            echo "<script>alert('Incorrect password.'); window.location.href='../frontend/login.html';</script>";
            exit();
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
