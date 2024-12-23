<?php
session_start();
include 'db_connect.php';

$error_message = "";
$contact = null;
$notes = [];

// Check if contact ID is provided
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['contact_id'])) {
    $contact_id = intval($_POST['contact_id']); // Get the contact ID from POST

    // Handle "Assign to me" action
    if (isset($_POST['assign_to_me'])) {
        $stmt = $conn->prepare("UPDATE Contacts SET assigned_to = ?, updated_at = NOW() WHERE id = ?");
        $user_id = $_SESSION['user_id']; // Assuming the current user ID is stored in session
        $stmt->bind_param("ii", $user_id, $contact_id);
        $stmt->execute();
        $stmt->close();
    }

    // Handle "Switch Type" action
    if (isset($_POST['switch_type'])) {
        $stmt = $conn->prepare("UPDATE Contacts SET type = IF(type = 'Sales Lead', 'Support', 'Sales Lead'), updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $contact_id);
        $stmt->execute();
        $stmt->close();
    }

    // Handle "Add Note" action
    if (isset($_POST['add_note']) && !empty($_POST['note_comment'])) {
        $comment = $_POST['note_comment'];
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO Notes (contact_id, comment, created_by, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("isi", $contact_id, $comment, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Fetch contact details
    $stmt = $conn->prepare("
        SELECT * 
        FROM Contacts 
        WHERE id = ?
    ");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("i", $contact_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $contact = $result->fetch_assoc();
        } else {
            $error_message = "No contact found with the provided ID.";
        }
    } else {
        $error_message = "Error fetching contact details: " . $stmt->error;
    }
    $stmt->close();

    // Fetch notes associated with the contact
    $stmt = $conn->prepare("
        SELECT comment, created_by, created_at 
        FROM Notes 
        WHERE contact_id = ?
        ORDER BY created_at DESC
    ");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("i", $contact_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
    } else {
        $error_message = "Error fetching notes: " . $stmt->error;
    }
    $stmt->close();
} else {
    $error_message = "No contact ID provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Details</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="viewstyles.css" >
</head>
<body>
    <div class="contact-details">
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php elseif ($contact): ?>
            <!-- Header Section -->
            <div class="profile-header">
                <div class="profile-info">
                    <i class="fa-duotone fa-solid fa-circle-user fa-lg" style="--fa-primary-color: #f3f2f2; --fa-secondary-color: #000000;"></i>
                    <div>
                        <h1><?php echo htmlspecialchars($contact['title'] . ' ' . $contact['firstname'] . ' ' . $contact['lastname']); ?></h1>
                        <p>Created on <?php echo htmlspecialchars(date("F j, Y", strtotime($contact['created_at']))); ?> by User ID <?php echo htmlspecialchars($contact['created_by']); ?></p>
                        <p>Updated on <?php echo htmlspecialchars(date("F j, Y", strtotime($contact['updated_at']))); ?></p>
                    </div>
                </div>
                <!-- Buttons on Right -->
                <div class="actions">
                    <form action="view_contact.php" method="POST" style="display:inline;">
                        <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
                        <button type="submit" name="assign_to_me" class="assign">
                            <i class="fa-solid fa-hand"></i> Assign to me
                        </button>
                    </form>
                    <form action="view_contact.php" method="POST" style="display:inline;">
                        <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
                        <button type="submit" name="switch_type" class="switch">
                            <i class="fa-solid fa-arrow-right-arrow-left"></i> Switch to <?php echo htmlspecialchars($contact['type'] === "Sales Lead" ? "Support" : "Sales Lead"); ?>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Info Grid -->
            <div class="info">
                <div class="info-block">
                    <label>Email</label>
                    <p><?php echo htmlspecialchars($contact['email']); ?></p>
                </div>
                <div class="info-block">
                    <label>Telephone</label>
                    <p><?php echo htmlspecialchars($contact['telephone']); ?></p>
                </div>
                <div class="info-block">
                    <label>Company</label>
                    <p><?php echo htmlspecialchars($contact['company']); ?></p>
                </div>
                <div class="info-block">
                    <label>Assigned To</label>
                    <p>User ID <?php echo htmlspecialchars($contact['assigned_to']); ?></p>
                </div>
            </div>
            
            <!-- Notes Section -->
            <section class="notes">
                <h2><i class="fas fa-pen-to-square"></i> Notes</h2>
                <?php if (count($notes) > 0): ?>
                    <?php foreach ($notes as $note): ?>
                        <div class="note">
                            <strong>User ID <?php echo htmlspecialchars($note['created_by']); ?></strong>
                            <p><?php echo htmlspecialchars($note['comment']); ?></p>
                            <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($note['created_at']))); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No notes available for this contact.</p>
                <?php endif; ?>
                
                <!-- Add Note Section -->
                <form action="view_contact.php" method="POST">
                    <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
                    <label for="note">Add a note about <?php echo htmlspecialchars($contact['firstname']); ?></label>
                    <textarea id="note" name="note_comment" placeholder="Enter details here"></textarea>
                    <div class="button-container">
                        <button type="submit" name="add_note" class="add-note">
                            <i class="fas fa-paper-plane"></i> Add Note
                        </button>
                    </div>
                </form>
            </section>
        <?php endif; ?>
    </div>
</body>
</html>
