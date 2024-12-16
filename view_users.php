<?php
session_start(); // Start session for authentication

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "schema";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle "Add User" form submission
$message = ""; // To display success or error messages
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password
    $role = $_POST['role'];

    $sql = "INSERT INTO Users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $password, $role);

    if ($stmt->execute()) {
        $message = "User registered successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all users
$sql = "SELECT id, firstname, lastname, email, role, created_at FROM Users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - View Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Users</h1>
    <a href="dashboard.html" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <!-- Display success or error messages -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- Add User Form -->
    <div class="mb-5">
        <h2>Add User</h2>
        <form method="POST" action="">
            <div class="form-group mb-3">
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" class="form-control" placeholder="Enter first name" required>
            </div>
            <div class="form-group mb-3">
                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname" class="form-control" placeholder="Enter last name" required>
            </div>
            <div class="form-group mb-3">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter email" required>
            </div>
            <div class="form-group mb-3">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            <div class="form-group mb-3">
                <label for="role">Role:</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="Member">Member</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
        </form>
    </div>

    <!-- Users Table -->
    <h2>Existing Users</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Date Created</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
