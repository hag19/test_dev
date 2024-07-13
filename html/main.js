document.addEventListener("DOMContentLoaded", function() {
    fetchSessionData();
});

async function fetchSessionData() {
    try {
        const response = await fetch('delete.php?action=getsession');
        const data = await response.json();

        const contentElement = document.getElementById('content');

        if (data.role === 'admin') {
            contentElement.innerHTML = `
                <form>
                    <button id="getusers">Get Info</button>
                </form>
                <form action="delete.php" method="POST">
                    <input type="text" name="name" placeholder="your name" required>
                    <input type="email" name="email" placeholder="your email" required>
                    <input type="password" name="password" placeholder="your password" required>
                    <input type="submit" value="Send Request">
                </form>
            `;

            document.getElementById("getusers").addEventListener("click", function(event) {
                event.preventDefault(); // Prevent default behavior
                getlist(); // Call your function to fetch data
            });
        } else {
            contentElement.innerHTML = `
                <p>Want to log in?</p>
                <form action="delete.php" method="POST">
                    <input type="email" name="email" placeholder="your email" required>
                    <input type="password" name="password" placeholder="your password" required>
                    <input type="submit" value="Send Request">
                </form>
            `;
        }
    } catch (error) {
        console.error("Error fetching session data:", error);
        const resultElement = document.getElementById("result");
        resultElement.innerHTML = "An error occurred, please try again later.";
    }
}

async function getlist() {
    try {
        const response = await fetch("delete.php?action=getlist", {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            }
        });

        if (response.ok) {
            const data = await response.json();
            console.log(data); // Log data to verify

            const resultElement = document.getElementById("result");
            let html = "";

            data.forEach(user => {
                html += `
                    <div>
                        <p>ID: ${user.id}</p>
                        <button onclick="getinfo(${user.id})">Get Info</button>
                        <button onclick="deleteU(${user.id})">delete user</button> 
                    </div>
                    <hr>
                `;
            });

            resultElement.innerHTML = html;
        } else {
            console.error("Error fetching data:", response.statusText);
            const resultElement = document.getElementById("result");
            resultElement.innerHTML = "Error fetching data";
        }
    } catch (error) {
        console.error("Error:", error);
        const resultElement = document.getElementById("result");
        resultElement.innerHTML = "An error occurred, please try again later.";
    }
}

async function deleteU(id) {
    try {
        const response = await fetch(`delete.php?id=${id}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        });

        if (response.ok) {
            console.log("User deleted successfully");
            getlist(); // Fetch updated data
        } else {
            console.error("Error deleting user:", response.statusText);
        }
    } catch (error) {
        console.error("Error:", error);
    }
}

async function getinfo(id) {
    try {
        const response = await fetch(`delete.php?action=getuser&id=${id}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            }
        });

        if (response.ok) {
            const user = await response.json();
            console.log(user); // Log user data to verify

            const resultElement = document.getElementById("result");
            let html = `
                <div>
                    <p>ID: ${user.id}</p>
                    <p>Name: ${user.name}</p>
                    <p>Email: ${user.email}</p>
                    <button onclick="changeInfo(${user.id}, '${user.name}', '${user.email}')">Change Information</button>
                </div>
            `;
            resultElement.innerHTML = html;
        } else {
            console.error("Error fetching user info:", response.statusText);
            const resultElement = document.getElementById("result");
            resultElement.innerHTML = "Error fetching user info";
        }
    } catch (error) {
        console.error("Error:", error);
        const resultElement = document.getElementById("result");
        resultElement.innerHTML = "An error occurred, please try again later.";
    }
}

async function changeInfo(id, name, email) {
    const resultElement = document.getElementById("result");
    let html = `
        <form id="updateForm">
            <input type="hidden" name="id" value="${id}" required>
            <input type="text" name="name" value="${name}" required>
            <input type="password" name="password" placeholder="password" required>
            <input type="email" name="email" value="${email}" required>
            <input type="submit" value="Update Information">
        </form>
    `;
    resultElement.innerHTML = html;

    document.getElementById("updateForm").addEventListener("submit", async function(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData.entries());
        try {
            const response = await fetch("delete.php", {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                console.log("User information updated successfully");
                getinfo(data.id);
                // Optionally, you can reload or fetch the updated data here
            } else {
                console.error("Error updating user information:", response.statusText);
            }
        } catch (error) {
            console.error("Error:", error);
        }
    });
}
