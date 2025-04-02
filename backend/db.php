<?php
// Database configuration
$servername = "localhost"; // Your MySQL server (usually localhost)
$username = "root";        // MySQL username
$password = "";            // MySQL password (empty for localhost by default)
$dbname = "colx";          // Database name (use your database name here)

try {
    // Create connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If connection fails, print the error message
    echo "Connection failed: " . $e->getMessage();
}
?>
