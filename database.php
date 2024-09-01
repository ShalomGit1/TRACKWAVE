





<?php
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName_login = "login_register";
$dbName_attendance = "student_attendance";

// Connect to login_register database
$conn_login = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName_login);
if (!$conn_login) {
    die("Connection to login_register database failed: " . mysqli_connect_error());
}

// Connect to student_attendance database
$conn_attendance = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName_attendance);
if (!$conn_attendance) {
    die("Connection to student_attendance database failed: " . mysqli_connect_error());
}

// Optionally, you can close the connections if you're only establishing them here
// mysqli_close($conn_login);
// mysqli_close($conn_attendance);
?>
