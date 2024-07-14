<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoteLock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 50px;
        }
        .img-fluid {
            max-width: 100%;
            height: auto;
        }
        .faq {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="row text-center">
            <div class="col-sm-1"></div>
            <div class="col-sm-4">
                <img src="images/Mainpage.png" alt="DataSecure-image" class="img-fluid" />
            </div>
            <div class="col-sm-5">
                <h1>Welcome to <strong>NoteLock</strong></h1>
                <h6>Store Your Notes and Passwords in encrypted form</h6>
            </div>
        </div>
        <br/>
        <div class="container border mb-3">
            <br/>
            <p class="mark lead">Frequently Asked Questions?</p>
            <div class="container faq">
                <p>
                    <button class="btn " type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        Why NoteLock?
                    </button>
                </p>
                <div class="collapse" id="collapseExample">
                    <div class="card card-body">
                        NoteLock is a secure note-taking web application that stores your notes and passwords in encrypted form.
                        It uses AES-256 encryption to encrypt your notes and passwords.
                    </div>
                </div>
            </div>
            <hr/>
            <div class="container faq">
                <p>
                    <button class="btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample2" aria-expanded="false" aria-controls="collapseExample2">
                        Can I trust NoteLock?
                    </button>
                </p>
                <div class="collapse" id="collapseExample2">
                    <div class="card card-body">
                    Yes, NoteLock is 100% secure. We use AES-256 encryption to encrypt your notes and passwords.
                </div>
                </div>
            </div>
            <hr/>
            <div class="container faq">
                <p>
                    <button class="btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample3" aria-expanded="false" aria-controls="collapseExample3">
                    can we see your data?
                    </button>
                </p>
                <div class="collapse" id="collapseExample3">
                    <div class="card card-body">
                    No, we do not store any of your data. We use AES-256 encryption to encrypt your notes and passwords.
                    </div>
                </div>
            </div>
            <hr/>   
            <div class="container faq">
                <p>
                    <button class="btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample4" aria-expanded="false" aria-controls="collapseExample4">
                    can we see your password that is stored in password manager?
                    </button>
                </p>
                <div class="collapse" id="collapseExample4">
                    <div class="card card-body">
                        No, we can't see your password that is stored in password manager. We use AES-256 encryption to encrypt your notes and passwords.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
