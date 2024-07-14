<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'Database.php';

$db = new Database();
$conn = $db->getConnection();

$username = $_SESSION['username'];

// Fetch user id
$userQuery = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();
$user_id = $user['id'];

// Defining Encryption and Decryption Functions
function encrypt($data) {
    // $data = str_replace(["\r\n","\n","\r"], " ", $data);
    $cipher = "AES-256-CBC";
    $key = "mysecretkey";
    $iv = openssl_random_pseudo_bytes(16); // 16 bytes iv
    $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
    return base64_encode($encrypted . '::' . base64_encode($iv));
}

function decrypt($data) {
    $cipher = "AES-256-CBC";
    $key = "mysecretkey";
    $decodedData = base64_decode($data);
    if (strpos($decodedData, '::') !== false) {
        list($encrypted_data, $iv) = explode('::', $decodedData, 2);
        $iv = base64_decode($iv);
        if (strlen($iv) !== 16) {
            return "Invalid IV length.";
        }
        $decrypted = openssl_decrypt($encrypted_data, $cipher, $key, 0, $iv);
        if ($decrypted === false) {
            return "Error decrypting data.";
        }
        // return nl2br(str_replace(["<<space>>","<<newline>>, <<tabspace>>"], [" ","\n","\r"], $decrypted));
        return $decrypted;
    }
    return "Invalid data format.";
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['note'])) {
        // $note = $conn->real_escape_string($_POST['note']);
        $note = $_POST['note'];
        $encrypted_note = encrypt($note);

        $stmt = $conn->prepare("INSERT INTO notes (user_id, content) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $encrypted_note);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $delete_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        $edit_note = $_POST['edit_note'];
        // $edit_note = $conn->real_escape_string($edit_note);
        $encrypted_edit_note = encrypt($edit_note);

        $stmt = $conn->prepare("UPDATE notes SET content = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $encrypted_edit_note, $edit_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch notes
$notesQuery = $conn->prepare("SELECT * FROM notes WHERE user_id = ?");
$notesQuery->bind_param("i", $user_id);
$notesQuery->execute();
$notesResult = $notesQuery->get_result();
$notes = [];
while ($row = $notesResult->fetch_assoc()) {
    $notes[] = $row;
}
$notesQuery->close();
$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1>Notes</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="note" class="form-label">New Note</label>
                <textarea class="form-control" id="note" name="note" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Note</button>
        </form>

        <h2 class="mt-5">Your Notes</h2>
        <div class="list-group">
            <?php foreach ($notes as $note): ?>
                <div class="list-group-item">
                    <!-- /**
                     * Displays the content of a note in a formatted way.
                     * The note content is decrypted and HTML-escaped to prevent XSS attacks.
                     * Newlines in the note content are preserved using the nl2br() function.
                     *
                     * @param array $note The note data, including the encrypted content.
                     * @return void
                     */ -->
                    <p><?php echo nl2br(htmlspecialchars(decrypt($note['content']))); ?></p>
                    <form method="POST" action="" class="d-inline-block">
                        <input type="hidden" name="delete_id" value="<?php echo $note['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    <button class="btn btn-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#editForm_<?php echo $note['id']; ?>">Edit</button>
                    <form method="POST" action="" class="collapse mt-3" id="editForm_<?php echo $note['id']; ?>">
                        <input type="hidden" name="edit_id" value="<?php echo $note['id']; ?>">
                        <div class="mb-3">
                            <label for="edit_note_<?php echo $note['id']; ?>" class="form-label">Edit Note</label>
                            <textarea class="form-control" id="edit_note_<?php echo $note['id']; ?>" name="edit_note" rows="3" required><?php echo htmlspecialchars(decrypt($note['content'])); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
