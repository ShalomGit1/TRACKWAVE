<?php
session_start();

// Unset and destroy any existing session data to prevent conflicts
session_unset();
session_destroy();
session_start(); // Start a new session

$errors = [];

if (isset($_POST['submit'])) {
    $full_name = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    if (empty($full_name) || empty($email) || empty($password) || empty($repeat_password)) {
        $errors[] = "All fields are required.";
    }

    if ($password !== $repeat_password) {
        $errors[] = "Passwords do not match.";
    }
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }

    if (empty($errors)) {
        require_once "database.php";
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (full_name, email, password) VALUES ('$full_name', '$email', '$password_hash')";
        if (mysqli_query($conn_login, $sql)) {
            header("Location: login.php");
            exit;
        } else {
            $errors[] = "Error: " . mysqli_error($conn_login);
        }

        mysqli_close($conn_login);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TrackWave</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="signup.css">
    <style>
        .grid-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; /* Ensure grid is behind content */
            background-size: 200px 200px;
            background-color: rgb(244,244,245);
            background-image: linear-gradient(to right, rgba(204, 204, 204, 0.19) 2px, transparent 1px),
                              linear-gradient(to bottom, rgba(204, 204, 204, 0.163) 2px, transparent 1px);
        }
        .tt {
            color: black;
            font-size: 12px;
        }
        .tt a {
            color: black;
            text-decoration: none;
        }
    </style>
</head>
<body class="grid-background">
    <div class="sign-up" style="background-color:rgb(244,244,245); border:1px solid gray; box-shadow: rgba(10, 10, 92, 0)0px 7px 29px 0px;">
        <h1>Sign Up</h1>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
        ?>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name" value="<?php if(isset($_POST['fullname'])) echo htmlspecialchars($_POST['fullname']); ?>">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email" value="<?php if(isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit" style="background-color:black; border:none;">
            </div>
        </form>
        <div class="tt">Already registered? <a href="login.php">Login</a></div>
        <div class="tt"><p>Go <a href="index.html">home</a></p></div>
    </div>
</body>
</html>
