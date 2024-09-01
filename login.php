<?php
session_start();

if(isset($_SESSION["user_id"])) {
    header("location: dashboard.php");
    exit;
}

?>
<style>
     .grid-background {
         position: absolute;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         z-index: -1;
         background-size: 200px 200px;
         background-color: rgb(244, 244, 245);
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
     .form-btn input {
        background-color: rgb(0, 0, 0);
        border: none;
        width: 20vh;
     }
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login-TrackWave</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="login.css">
</head>
<body class="grid-background">
    <div class="container" style="background-color: rgb(244, 244, 245); border: 1px solid gray; box-shadow: rgba(10, 10, 92, 0) 0px 7px 29px 0px;">
        <h1>Login</h1>
        <?php
        if(isset($_POST["login"])) {
            $email = $_POST["email"];
            $password = $_POST["password"];
            require_once "database.php";
            
            // Prepare and execute SQL query
            $sql = "SELECT * FROM users WHERE email ='$email'";
            $result = mysqli_query($conn_login, $sql);

            if($result) {
                $user = mysqli_fetch_assoc($result);
                
                if($user && password_verify($password, $user["password"])) {
                    // Set user_id in session
                    $_SESSION["user_id"] = $user["id"];
                    header("location: dashboard.php");
                    exit;
                } else {
                    echo "<div class='alert alert-danger'>Invalid email or password.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error executing query: " . mysqli_error($conn_login) . "</div>";
            }
            
            // Close connection
            mysqli_close($conn_login);
        }
        ?>
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="email" placeholder="Enter your email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter your password" name="password" class="form-control">
            </div>
            <div class="form-btn">
                <input type="submit" value="Login" name="login" class="btn btn-primary" style="background-color: black;">
            </div>
        </form>
        <div class="tt"><p>Don't have an account? <a href="registration.php">Register</a></p></div>
        <div class="tt"><p>Go <a href="index.html">home</a></p></div>
    </div>
</body>
</html>
