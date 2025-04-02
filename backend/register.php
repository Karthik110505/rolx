<?php
// Start session
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file (use absolute path)
include(__DIR__ . '/db.php'); 

// Check if the database connection was successful
if (!isset($conn)) {
    die("Database connection failed.");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Collect form data and sanitize it
        $user_id = htmlspecialchars($_POST['user_id']);
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $full_name = htmlspecialchars($_POST['full_name']);
        $phone = htmlspecialchars($_POST['phone']);
        $department = htmlspecialchars($_POST['department']);
        $year_of_study = htmlspecialchars($_POST['year_of_study']);

        // Hash the password before storing it in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Handle profile picture upload
        $profile_picture = NULL;
        if (!empty($_FILES['profile_picture']['name'])) {
            $target_dir = "uploads/";
            $profile_picture = basename($_FILES["profile_picture"]["name"]);
            $target_file = $target_dir . $profile_picture;

            // Move the uploaded file
            if (!move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                die("Error uploading profile picture.");
            }
        }

        // Prepare SQL statement to insert data into the users table
        $sql = "INSERT INTO users (user_id, username, email, password, full_name, phone, profile_picture, department, year_of_study)
                VALUES (:user_id, :username, :email, :password, :full_name, :phone, :profile_picture, :department, :year_of_study)";
        
        // Prepare the statement
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':profile_picture', $profile_picture);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':year_of_study', $year_of_study);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to home page after successful registration
            header("Location: ../frontend/login.html");
            exit();
        } else {
            echo "Error: Could not register the user.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

