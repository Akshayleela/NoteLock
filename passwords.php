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

// Encryption and Decryption Functions
function encrypt($string){
    $cipher = "AES-256-CBC";
    $key = "CSInternship";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    $encrypted = openssl_encrypt($string, $cipher, $key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv); // base64_encode helps to encrypt the data with MIME base64
}

function decrypt($string){
    $cipher = "AES-256-CBC";
    $key = "CSInternship";
    $decodedData = base64_decode($string);
    if (strpos($decodedData , '::') !== false) {
        list($encrypted_data, $iv) = explode('::', $decodedData, 2);
        $decrypted = openssl_decrypt($encrypted_data, $cipher, $key, 0, $iv);
        if ($decrypted === false) {
            return "Error decrypting data";
        }
        return $decrypted;
    }
    return "Invalid data format";
}
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['sitename']) && isset($_POST['password']) && isset($_POST['description']) && isset($_POST['siteurl'])) {
        $sitename = $_POST['sitename'];
        $password = $_POST['password'];
        $description = $_POST['description'];
        $siteurl = $_POST['siteurl'];

        $encrypted_sitename = encrypt($sitename);
        $encrypted_password = encrypt($password);
        $encrypted_description = encrypt($description);
        $encrypted_siteurl = encrypt($siteurl);
        $stmt = $conn->prepare("INSERT INTO passwords (user_id, sitename, password, description, siteurl) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $encrypted_sitename, $encrypted_password, $encrypted_description, $encrypted_siteurl);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM passwords WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $delete_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        $edit_sitename = $_POST['edit_sitename'];
        $edit_password = $_POST['edit_password'];
        $edit_description = $_POST['edit_description'];
        $edit_siteurl = $_POST['edit_siteurl'];

        $encrypted_edit_sitename = encrypt($edit_sitename);
        $encrypted_edit_password = encrypt($edit_password);
        // $encrypted_edit_description = encrypt($conn->real_escape_string($edit_description));
        $encrypted_edit_description = encrypt($edit_description);
        $encrypted_edit_siteurl = encrypt($edit_siteurl);

        $stmt = $conn->prepare("UPDATE passwords SET sitename = ?, password = ?, description = ?, siteurl = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssssii", $encrypted_edit_sitename, $encrypted_edit_password, $encrypted_edit_description, $encrypted_edit_siteurl, $edit_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch passwords
$passwordsQuery = $conn->prepare("SELECT * FROM passwords WHERE user_id = ?");
$passwordsQuery->bind_param("i", $user_id);
$passwordsQuery->execute();
$passwordsResult = $passwordsQuery->get_result();
$passwords = [];
while ($row = $passwordsResult->fetch_assoc()) {
    $passwords[] = $row;
}
$passwordsQuery->close();
$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passwords</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1>Passwords</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="sitename" class="form-label">Site Name</label>
                <input type="text" class="form-control" id="sitename" name="sitename" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="text" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea type="text" class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="siteurl" class="form-label">Site URL</label>
                <input type="url" class="form-control" id="siteurl" name="siteurl" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Password</button>
        </form>

        <h2 class="mt-5">Stored Passwords</h2>
        <div class="list-group">
            <?php foreach ($passwords as $password): ?>
                <div class="list-group-item">
                    <p><strong>Sitename:</strong><?php echo htmlspecialchars(decrypt($password['sitename'])); ?></p>
                    <p><strong>Password:</strong> <?php echo htmlspecialchars(decrypt($password['password'])); ?></p>
                    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars(decrypt($password['description']))); ?></p>
                    <p><strong>Site URL:</strong> <a href="<?php echo htmlspecialchars(decrypt($password['siteURL'])); ?>" target="_blank"><?php echo htmlspecialchars(decrypt($password['siteURL'])); ?></a></p>
                    <form method="POST" action="" class="d-inline-block">
                        <input type="hidden" name="delete_id" value="<?php echo $password['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    <button class="btn btn-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#editForm_<?php echo $password['id']; ?>">Edit</button>
                    <form method="POST" action="" class="collapse mt-3" id="editForm_<?php echo $password['id']; ?>">
                        <input type="hidden" name="edit_id" value="<?php echo $password['id']; ?>">
                        <div class="mb-3">
                            <label for="edit_sitename_<?php echo $password['id']; ?>" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="edit_sitename_<?php echo $password['id']; ?>" name="edit_sitename" value="<?php echo htmlspecialchars(decrypt($password['sitename'])); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password_<?php echo $password['id']; ?>" class="form-label">Password</label>
                            <input type="text" class="form-control" id="edit_password_<?php echo $password['id']; ?>" name="edit_password" value="<?php echo htmlspecialchars(decrypt($password['password'])); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description_<?php echo $password['id']; ?>" class="form-label">Description</label>
                            <textarea type="text" class="form-control" id="edit_description_<?php echo $password['id']; ?>" name="edit_description" value="" required><?php echo htmlspecialchars(decrypt($password['description'])); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_siteurl_<?php echo $password['id']; ?>" class="form-label">Site URL</label>
                            <input type="url" class="form-control" id="edit_siteurl_<?php echo $password['id']; ?>" name="edit_siteurl" value="<?php echo htmlspecialchars(decrypt($password['siteURL'])); ?>" required>
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
