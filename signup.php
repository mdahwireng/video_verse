<?php
require_once 'assets/php/connections/pdo.php';
require_once 'assets/php/html_strings/nav_bar.php';
require_once 'assets/php/utils.php';


// session_start();

// if (isset($_POST['first_name'])){
//     print_r($_POST);
//     return;
// }

if (isset($_SESSION['name'])) {
    header('Location: index.php');
    return;
}

$nav = setActiveNav('signup', $nav);

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])) {
    unset($_SESSION['name']);
    unset($_SESSION['user_id']);


    // check for empty entries
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['username']) < 1 || strlen($_POST['password']) < 1 || strlen($_POST['confirm_password']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: signup.php");
        // print_r($_POST);
        return;
    }
    
    // check email validity
    if ((strpos($_POST['email'], '@') === false || strpos($_POST['email'], '.') === false) || (strpos($_POST['email'], '@') === false && strpos($_POST['email'], '.') === false)) {
        $_SESSION['error'] = "Email must have an at-sign (@) and a dot (.)";
        header("Location: signup.php");
        return;
    }

    // check username availability
    $stmt = $conn->prepare('SELECT username FROM users WHERE username = :usrn');
    $stmt->execute(array(':usrn' => $_POST['username']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false) {
        $_SESSION['error'] = "Username already exists";
        header("Location: signup.php");
        return;
    }

    // check password match
    $salt = '&*(yuFT*_';
    $check = hash('sha256', $salt . $_POST['password']);
    $confirm = hash('sha256', $salt . $_POST['confirm_password']);

    // echo $check;
    // return;

    if ($check !== $confirm) {
        $_SESSION['error'] = "Passwords do not match";
        header("Location: signup.php");
        return;
    }

    print_r(array(
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':usrn' => $_POST['username'],
        ':pw' => $check
    ));

   // insert new user into db
    $stmt = $conn -> prepare('INSERT INTO users (first_name, last_name, email, username, password) VALUES (:fn, :ln, :em, :usrn, :pw)');
    $stmt->execute(array(
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':usrn' => $_POST['username'],
        ':pw' => $check
    ));
    $_SESSION['success'] = "Account created successfully";

    $stmt = $conn->prepare('SELECT id, first_name FROM users WHERE email = :em AND password = :pw');
    $stmt->execute(array(
        ':em' => $_POST['email'],
        ':pw' => $check
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false) {
        $_SESSION['name'] = $row['first_name'];
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['initials'] = $row['first_name'][0] . $row['first_name'][1];
        error_log("Sign up success " . $_POST['email']);
        header('Location: index.php');
        return;
    } else {
        $_SESSION['error'] = 'Incorrect password or email.';
        error_log("Sign up fail " . $_POST['email'] . " $check");
        header('Location: signup.php');
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
    <style>
        .btn-group .btn {
            margin-right: 10px; /* Adjust the margin as needed */
        }

        /* Center the row vertically within its parent container */
        .vertical-center {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Set a minimum height for vertical centering */
        }
    </style>
    <title>Login</title>
</head>
<body>
<?php echo $nav; ?>
    <div class="container">
        <div class="row vertical-center">
            <form method="post">
              <div class="container">

              <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="text-danger">' . $_SESSION['error'] . '</p>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<p class="text-success">' . $_SESSION['success'] . '</p>';
                unset($_SESSION['success']);
            }
            ?>
                
              <div class="row">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control mb-2 text-bg-light" placeholder="Enter first name">
                        </div> 
                    </div>
                    <div class="col-sm-2"></div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input name="last_name" id="last_name" class="form-control mb-2 text-bg-light" placeholder="Enter last name">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="text" name="email" id="email" class="form-control mb-2 text-bg-light" placeholder="Enter email">
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control mb-2 text-bg-light" placeholder="Enter username">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control mb-2 text-bg-light" placeholder="Enter password">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password"class="form-control mb-2 text-bg-light" placeholder="Confirm password">
                        </div>
                    </div>
                </div>

                
                <div class="float-end">
                    <div class="btn-group float-right">
                        <button type="submit" value="cancel" class="btn btn-lg btn-danger rounded-pill mt-3">CANCEL</button>
                        <button type="submit" value="signup" class="btn btn-lg btn-primary rounded-pill mt-3">SIGN UP</button>
                    </div>
                </div>
                         
              </div> 
            </form>
        </div>
    </div>
    
</body>
<script src="assets/js/jscript.js"></script>
</html>