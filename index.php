<?php
require_once('assets/php/html_strings/nav_bar.php');

// session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Index</title>
    <style>
        /* Center the row vertically within its parent container */
        .vertical-center {
            min-height: 100%; /* Fallback for browsers do NOT support vh unit */
            min-height: 100vh; /* These two lines are counted as one :-)       */
            display: flex;
            align-items: center;
        }

        .user-initials {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #007bff;
            color: #fff;
            text-align: center;
            line-height: 40px;
        }
        
        /* Custom CSS for the landing page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .landing-container {
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        h1 {
            font-size: 3em;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        p {
            font-size: 1.5em;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<?php echo $nav; ?>
    <div class="landing-container">
        <h1>Welcome to VideoVerse</h1>
        <p>Your very own video hub</p>
        <button type="button" href="explore.php" class="btn btn-primary btn-lg">Explore</button>
    </div>
</body>
</html>