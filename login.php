<?php
require_once 'assets/php/connections/pdo.php';
session_start();

if (isset($_SESSION['name'])) {
    header('Location: index.php');
    return;
}


if (isset($_POST['email']) && isset($_POST['password'])) {
    unset($_SESSION['name']);
    unset($_SESSION['user_id']);

    if (strlen($_POST['email']) < 1 || strlen($_POST['password']) < 1) {
        $_SESSION['error'] = "Email and password are required";
        header("Location: login.php");
        return;
    }
    if ((strpos($_POST['email'], '@') === false || strpos($_POST['email'], '.') === false) || (strpos($_POST['email'], '@') === false && strpos($_POST['email'], '.') === false)) {
        $_SESSION['failure'] = "Email must have an at-sign (@) and a dot (.)";
        $failure = true;
        header("Location: login.php");
        return;
    }


    $salt = 'XyZzy12*_';
    $check = hash('md5', $salt . $_POST['password']);
    $stmt = $conn->prepare('SELECT id, first_name FROM users WHERE email = :em AND password = :pw');
    $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false) {
        $_SESSION['name'] = $row['first_name'];
        $_SESSION['user_id'] = $row['id'];
        error_log("Login success " . $_POST['email']);
        header('Location: index.php');
        return;
    } else {
        $_SESSION['error'] = 'Incorrect password or email.';
        error_log("Login fail " . $_POST['email'] . " $check");
        header('Location: login.php');
        return;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Login</title>
    <style>
        /* Center the row vertically within its parent container */
        .vertical-center {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Set a minimum height for vertical centering */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row vertical-center">
            <div class="col-sm-4"></div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h1>VideoVerse Login</h1>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mt-3">
                                <?php
                                if (isset($_SESSION['error'])) {
                                    echo ('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
                                    unset($_SESSION['error']);
                                }
                                if (isset($_SESSION['success'])) {
                                    echo ('<p style="color: green;">' . htmlentities($_SESSION['success']) . "</p>\n");
                                    unset($_SESSION['success']);
                                }
                                ?>
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" name="email" id="email" class="form-control mb-2 text-bg-light" placeholder="Enter email">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control mb-2 text-bg-light" placeholder="Enter password">
                                </div>
                                <div class="form-group">
                                    <div class="container">
                                        <div class="row">
                                            <button value="login" type="submit" class="btn btn-lg btn-primary col-sm-4 mt-3">LOGIN</button>
                                            <div class="col-sm-4"></div>
                                            <button value="cancel" type="submit" class="btn btn-lg btn-danger col-sm-4 mt-3">CANCEL</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-sm-2"></div>
                                            <button type="submit" class="btn btn-lg btn-primary  col-sm-12 mt-3">SIGN UP</button>
                                            <div class="col-sm-2"></div>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
                <div class="col-sm-4"></div>
            </div>
        </div>

</body>

</html>