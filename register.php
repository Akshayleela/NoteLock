<?php
include 'Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new Database();
    $conn = $db->getConnection();

    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $cpassword = $conn->real_escape_string($_POST['cpassword']);

    if ($password !== $cpassword){
        $message = "Passwords do not match.";
    }else{
         // Check if username already exists
         $stmt = $db->conn->prepare('SELECT id FROM users WHERE username = ?');
         $stmt->bind_param('s', $username);
         $stmt->execute();
         $stmt->store_result();
 
         if ($stmt->num_rows > 0) {
             $message = 'Username already exists. Try different one';
            //  header("Location:register.php"); //no need to relocate the page
         }else{ 
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
           
            // $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
            // if ($conn->query($sql) === TRUE) {
            //     $message = "Registration Successful";
            // } else {
            //     echo "Error: " . $sql . "<br>" . $conn->error;
            // } or the following method
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param('ss', $username, $hashed_password);
            if ($stmt->execute()) {
                $message = "Registration Successful !! Don't forget your username and password. You can't retrieve this if you forget. Please Login Now!!";
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
    $db->closeConnection();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet" >
    <style>
        input[type="text"], input[type="password"], textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 0px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container centered-form mt-5">
        <h2>Register</h2>
        <hr/>
        <?php if (isset($message)): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php unset($message) ?>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="" required="required">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required />
                    <span class="btn btn-outline-secondary password_show" type="button" tabindex="-1" ><i class="fa fa-eye-slash"></i></span>
                </div>
            </div>
            <div class="mb-3">
                <label for="cpassword" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="cpassword" name="cpassword" required>
                    <span class="btn btn-outline-secondary password_show" type="button" tabindex="-1" ><i class="fa fa-eye-slash"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    var passwordToggles = document.querySelectorAll('.password_show');
    
    passwordToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            var input = this.previousElementSibling;
            var curType = input.getAttribute('type');
            if (curType === 'password') {
                this.innerHTML = "<i class='fa fa-eye'></i>";
                input.setAttribute('type', 'text');
                input.focus();
            } else {
                this.innerHTML = "<i class='fa fa-eye-slash'></i>";
                input.setAttribute('type', 'password');
                input.focus();
            }
        });
    });
});
</script>
</html>
