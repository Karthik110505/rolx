function loadSection(section) {
    const content = document.getElementById("content");

    if (section === "buy") {
        content.innerHTML = `
            <h2>Buy Items</h2>
            <p>All items available for purchase will be displayed here.</p>
        `;
    } else if (section === "sell") {
        content.innerHTML = `
            <h2>Sell Items</h2>
            <p>List items you want to sell here.</p>
            <button onclick="sellItem()">Sell an Item</button>
        `;
    } else if (section === "cart") {
        content.innerHTML = `
            <h2>Cart</h2>
            <p>View items in your cart.</p>
        `;
    }
}

function loadProfile() {
    fetch("../backend/profile.php")
        .then(response => response.json())
        .then(user => {
            if (user.error) {
                document.getElementById("content").innerHTML = `<p>Error: ${user.error}</p>`;
                return;
            }

            const content = document.getElementById("content");
            content.innerHTML = `
                <h2>Profile</h2>
                <div id="profile-details">
                    <h3>Welcome, ${user.full_name} (${user.username})</h3>
                    <img src="uploads/${user.profile_picture}" alt="Profile Picture" width="100" height="100">
                    <p><strong>Email:</strong> ${user.email}</p>
                    <p><strong>Phone:</strong> ${user.phone}</p>
                    <p><strong>Department:</strong> ${user.department}</p>
                    <p><strong>Year of Study:</strong> ${user.year_of_study}</p>
                </div>
                <button onclick="logout()">Logout</button>
            `;
        })
        .catch(error => {
            console.error("Error fetching profile:", error);
            document.getElementById("content").innerHTML = "<p>Error loading profile.</p>";
        });
}

function logout() {
    fetch("../backend/logout.php")
        .then(() => {
            window.location.href = "login.html";
        })
        .catch(error => console.error("Logout failed:", error));
}
