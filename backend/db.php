<?php
// Database configuration for InfinityFree
$servername = "sql313.infinityfree.com"; // InfinityFree MySQL Hostname
$username = "if0_38735546";              // Your MySQL username
$password = "vE2mwxtj2f87HC";            // Your MySQL password
$dbname = "if0_38735546_rolx";           // Your database name

try {
    // Create connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: echo "Connected successfully"; // Only for debugging
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
