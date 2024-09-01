<?php
session_start();

if (isset($_SESSION["admin"])) {
    header("location: admin_dashboard.php");
    exit;
}

if (isset($_POST["submit"])) {
    // Database connection
    require_once "database.php"; // Ensure this file includes your database connection

    // Sanitize and validate inputs
    $fullname = mysqli_real_escape_string($conn_login, $_POST["fullname"]);
    $email = mysqli_real_escape_string($conn_login, $_POST["email"]);
    $password = mysqli_real_escape_string($conn_login, $_POST["password"]);
    $passwordRepeat = mysqli_real_escape_string($conn_login, $_POST["repeat_password"]);

    // Hash password
    $passwordhash = password_hash($password, PASSWORD_DEFAULT);

    // Validation and error handling
    $errors = array();

    if (empty($fullname) || empty($email) || empty($password) || empty($passwordRepeat)) {
        array_push($errors, "All fields are required");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid");
    }
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }
    if ($password !== $passwordRepeat) {
        array_push($errors, "Passwords do not match");
    }

    // Check if email already exists
    $sql_check_email = "SELECT * FROM admins WHERE email = '$email'";
    $result_check_email = mysqli_query($conn_login, $sql_check_email);

    if (mysqli_num_rows($result_check_email) > 0) {
        array_push($errors, "Email already exists");
    }

    // If no errors, insert user into database
    if (count($errors) === 0) {
        $sql_insert_user = "INSERT INTO admins (full_name, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn_login);
        if (mysqli_stmt_prepare($stmt, $sql_insert_user)) {
            mysqli_stmt_bind_param($stmt, "sss", $fullname, $email, $passwordhash);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Redirect to admin login after successful registration
            header("location: admin_login.php");
            exit;
        } else {
            die("Error: Unable to prepare statement");
        }
    }

    // Close database connection
    mysqli_close($conn_login);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - TrackWave</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<style>
     .grid-background{
    padding: 50px;
    background-color: rgba(243, 243, 243, 1);

}
.container{
    font-family: 'MADE Outer Sans';
    font-weight: 300;
    font-style: normal;
    max-width: 600px;
    margin: auto;
    padding: 50px;
    box-shadow: rgba(10, 10, 92, 0)0px 7px 29px 0px;
    background-color: rgb(255, 255, 255);
    border-radius: 5px;
   


}
.container h1{
    font-family: 'MADE Outer Sans Alt';
    font-weight: bold;
    font-style: normal;
}
.form-btn input{
    background-color: rgb(0, 0, 0);
    border: none;
    width: 20vh;

}
.form-group{
    margin-bottom: 30px;
}
.form-btn{
    
}
</style>
<body class="container">
    <div class="container" style="background-color: rgb(244, 244, 245); border: 1px solid gray; box-shadow: rgba(10, 10, 92, 0) 0px 7px 29px 0px;">
        <h1>Admin Sign Up</h1>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
        ?>
        <form action="admin_registration.php" method="post" class="container">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name" value="<?php if(isset($_POST['fullname'])) echo $_POST['fullname']; ?>">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit" style="background-color: black; border: none;">
            </div>
        </form>
        <div class="tt">Already registered? <a href="admin_login.php">Login</a></div>
        <div class="tt"><p>Go <a href="admin.html">home</a></p></div>
    </div>
</body>
</html>
