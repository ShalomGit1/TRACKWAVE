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
    $email = mysqli_real_escape_string($conn_login, $_POST["email"]);
    $password = mysqli_real_escape_string($conn_login, $_POST["password"]);

    // Validation and error handling
    $errors = array();

    if (empty($email) || empty($password)) {
        array_push($errors, "All fields are required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid");
    }

    // Check if email exists
    if (count($errors) === 0) {
        $sql_check_user = "SELECT * FROM admins WHERE email = '$email'";
        $result_check_user = mysqli_query($conn_login, $sql_check_user);

        if (mysqli_num_rows($result_check_user) > 0) {
            $admin = mysqli_fetch_assoc($result_check_user);
            if (password_verify($password, $admin['password'])) {
                // Store user data in session
                $_SESSION["admin"] = $admin["email"];
                $_SESSION["fullname"] = $admin["full_name"];

                // Redirect to admin dashboard after successful login
                header("location: admin_dashboard.php");
                exit;
            } else {
                array_push($errors, "Incorrect password");
            }
        } else {
            array_push($errors, "No account found with that email");
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
    <title>Admin Login - TrackWave</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

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

</style>
<body class="container">
    <div class="container" style="background-color: rgb(244, 244, 245); border: 1px solid gray; box-shadow: rgba(10, 10, 92, 0) 0px 7px 29px 0px;">
        <h1>Admin Login</h1>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
        ?>
        <form action="admin_login.php" method="post" class="container">
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Login" name="submit" style="background-color: black; border: none;">
            </div>
        </form>
        <div class="tt">Don't have an account? <a href="admin_registration.php">Sign Up</a></div>
        <div class="tt"><p>Go <a href="admin.html">home</a></p></div>
    </div>
</body>
</html>
