<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html"); // Redirect if not logged in
    exit();
}

// Include database connection
include 'db.php';

$email = $_SESSION['user'];
$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Error: User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/home.css">
    <script defer src="js/home.js"></script>
</head>
<body>
    <nav>
        <ul>
            <li><a href="#" onclick="showSection('buy')">Buy</a></li>
            <li><a href="#" onclick="showSection('sell')">Sell</a></li>
            <li><a href="#" onclick="showSection('cart')">Cart</a></li>
            <li><a href="profile.html">Profile</a></li> <!-- Profile Page -->
        </ul>
    </nav>

    <div id="content">
        <section id="buy" class="section active">
            <h2>Buy Items</h2>
            <p>All items available for purchase will be displayed here.</p>
        </section>
        <section id="sell" class="section">
            <h2>Sell Items</h2>
            <p>List items you want to sell here.</p>
        </section>
        <section id="cart" class="section">
            <h2>Cart</h2>
            <p>View items in your cart.</p>
        </section>
        <section id="profile" class="section">
            <h2>Profile</h2>
            <div id="profile-details">
                <h3>Welcome, <?= $user['full_name'] ?> (<?= $user['username'] ?>)</h3>
                <img src="uploads/<?= $user['profile_picture'] ?>" alt="Profile Picture" width="100" height="100">
                <p><strong>Email:</strong> <?= $user['email'] ?></p>
                <p><strong>Phone:</strong> <?= $user['phone'] ?></p>
                <p><strong>Department:</strong> <?= $user['department'] ?></p>
                <p><strong>Year of Study:</strong> <?= $user['year_of_study'] ?></p>
            </div>
            <button onclick="logout()">Logout</button>
        </section>
    </div>

    <script>
        function logout() {
            fetch("logout.php")
                .then(() => {
                    window.location.href = "login.html";
                })
                .catch(error => console.error("Logout failed:", error));
        }
    </script>

</body>
</html>
