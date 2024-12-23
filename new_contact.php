<?php
session_start();
include 'db_connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login_index.html");
    exit();
}





$error_message = "";
$success_message = "";
$users = [];

// Fetch all users
$sqlUsers = "SELECT id, firstname, lastname, email, role, created_at FROM Users";
$resultUsers = $conn->query($sqlUsers);
if ($resultUsers->num_rows > 0) {
    while ($row = $resultUsers->fetch_assoc()) {
        $users[] = $row;
    }}



if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $title = $_POST['title'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $company = $_POST['company'];
    $type = $_POST['type'];
    $assigned_to = (int)$_POST['assigned_to'];
    $created_by = $_SESSION['user_id'];}

    // Validate required fields
    if (!$firstname || !$lastname || !$email || !$company || !$assigned_to) {
        $error_message = "All fields marked as required must be filled.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error_message = "Invalid email address.";
    } elseif (!preg_match("/^\+?[0-9\s\-]+$/", $telephone)) {
        $error_message = "Invalid phone number.";
    
    } else {
        // Insert contact into the database 
        $stmt = $conn->prepare("
            INSERT INTO Contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        if (!$stmt) {
            die('Prepare failed: ' . $conn->error);
        }

        if ($stmt->bind_param("ssssssiii", $title, $firstname, $lastname, $email, $telephone, $company, $type, $assigned_to, $created_by)) {
            if ($stmt->execute()) {
                header("Location: index.php"); // Redirect to homepage after success
                exit();
            } else {
                $error_message = "Database error: " . $stmt->error;
            }
        } else {
            $error_message = "Failed to bind parameters.";
        }
        $stmt->close();
    }
    
?>
<!DOCTYPE html>
<html lang="en"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Add New Contact</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php elseif (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <form method="POST" action="new_contact.php">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Title</label>
                        <select name="title" id="title" class="form-control" required>
                            <option value="Mr.">Mr.</option>
                            <option value="Mrs.">Mrs.</option>
                            <option value="Ms.">Ms.</option>
                            <option value="Dr.">Dr.</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="Sales Lead">Sales Lead</option>
                            <option value="Support">Support</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" name="firstname" id="firstname" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" name="lastname" id="lastname" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="telephone" class="form-label">Telephone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="company" class="form-label">Company</label>
                        <input type="text" name="company" id="company" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="assigned_to" class="form-label">Assigned To (User ID)</label>
                        <select name="assigned_to" id="assigned_to" class="form-control" required>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo $user['firstname'] . ' ' . $user['lastname']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Save Contact</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>