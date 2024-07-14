<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>Welcome, <?php echo $username; ?></h2>
        <br> 
        <div class="row">
           <div class="mt-2 col-sm-6">
             <h4 class="text-center text-muted">Access Your Notes Here</h4>
             <hr>
             <p><a class="text-decoration-none" href="notes.php"><img class="img-fluid" src="images/clipboard-1.png" alt="Notes-icon"/></a></p>
           </div>
           <div class="mt-2 col-sm-6">
             <h4 class="text-center text-muted">Manage Your Passwords Here</h4>
             <hr>
             <p><a class="text-decoration-none" href="passwords.php"><img class="img-fluid" src="images/secure.png" alt="passwords-icon"/></a></p>
           </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // let tabCount = 1;
        // document.getElementById('add-tab').addEventListener('click', function () {
        //     tabCount++;
        //     const newTabId = 'tab-' + tabCount;
        //     const newContentId = 'content-' + tabCount;

        //     const newTab = document.createElement('li');
        //     newTab.className = 'nav-item';
        //     newTab.role = 'presentation';
        //     newTab.innerHTML = `
        //         <button class="nav-link" id="${newTabId}" data-bs-toggle="tab" data-bs-target="#${newContentId}" type="button" role="tab" aria-controls="${newContentId}" aria-selected="false">Tab ${tabCount}</button>
        //     `;
        //     const addTab = document.getElementById('add-tab').parentElement;
        //     addTab.before(newTab);

        //     const newContent = document.createElement('div');
        //     newContent.className = 'tab-pane fade';
        //     newContent.id = newContentId;
        //     newContent.role = 'tabpanel';
        //     newContent.setAttribute('aria-labelledby', newTabId);
        //     newContent.innerHTML = '<textarea class="form-control mt-3" rows="10"></textarea>';
        //     document.getElementById('myTabContent').appendChild(newContent);

        //     const newTabButton = newTab.querySelector('button');
        //     newTabButton.click();
        // });
    </script>
</body>
</html>
