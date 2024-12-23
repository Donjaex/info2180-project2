<?php
session_start();
include 'db_connect.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_index.html");
    exit();
}

// Fetch contacts and notes (your existing logic)
$contacts = [];
$sqlContacts = "SELECT * FROM Contacts";
$resultContacts = $conn->query($sqlContacts);
if ($resultContacts->num_rows > 0) {
    while ($row = $resultContacts->fetch_assoc()) {
        $contacts[] = $row;
    }
}

// Fetch Notes for Contacts
$notes = [];
$sqlNotes = "SELECT contact_id, comment, created_at FROM Notes";
$resultNotes = $conn->query($sqlNotes);
if ($resultNotes->num_rows > 0) {
    while ($row = $resultNotes->fetch_assoc()) {
        $notes[$row['contact_id']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 bg-dark text-light sidebar">
            <h4 class="text-center py-3">Dolphin CRM</h4>
            <ul class="nav flex-column px-3">
                <li class="nav-item"><a class="nav-link text-light" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="new_contact.php">New Contact</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="view_users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 main">
            <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
                <h1>Welcome to Dolphin CRM!</h1>
                <a href="new_contact.html" class="btn btn-primary">+ Add Contact</a>
            </div>

            <!-- Contacts Table -->
            <div class="table-responsive">
                <h2>All Contacts</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Telephone</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contact['firstname'] . " " . $contact['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                <td><?php echo htmlspecialchars($contact['company']); ?></td>
                                <td><?php echo htmlspecialchars($contact['type']); ?></td>
                                <td><?php echo htmlspecialchars($contact['telephone']); ?></td>
                                <td>
                                    <form action="view_contact.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-primary">View</button>
                                    </form>
                                </td>
                                <td>
                                    <?php if (isset($notes[$contact['id']])): ?>
                                        <ul>
                                            <?php foreach ($notes[$contact['id']] as $note): ?>
                                                <li><?php echo htmlspecialchars($note['comment']); ?> <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($note['created_at']); ?></small>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <em>No comments</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($contacts)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No contacts found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
