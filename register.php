<?php
// Database connection (make sure to replace with your own credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "schema";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    echo "Form submitted successfully!<br>";
    echo "Firstname: " . htmlspecialchars($_POST['firstname']) . "<br>";
    echo "Email: " . htmlspecialchars($_POST['email']) . "<br>";
    echo "Role: " . htmlspecialchars($_POST['role']) . "<br>";

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password
    $role = $_POST['role'];

    
    $sql = "INSERT INTO Users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $password, $role);

    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
