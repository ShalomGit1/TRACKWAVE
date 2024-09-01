<?php
// session_start();
// require_once "database.php"; // Adjust the path as per your file structure

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // $start_time = $_POST["start_time"];
    // $end_time = $_POST["end_time"];
    // $session_name = $_POST["session_name"];
    // $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session during login

    // Insert session into sessions table with user_id
    // $sql = "INSERT INTO student_attendance.sessions (start_time, end_time, session_name, user_id) VALUES ('$start_time', '$end_time', '$session_name', '$user_id')";
    
    // if (mysqli_query($conn_attendance, $sql)) {
    //     echo "<script>alert('Session created successfully');</script>";
    // } else {
    //     echo "Error: " . mysqli_error($conn_attendance);
    // }
// }
?>


<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $session_name = $_POST['session_name'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session after login

    $sql_create_session = "INSERT INTO sessions (session_name, start_time, end_time, user_id) VALUES ('$session_name', '$start_time', '$end_time', '$user_id')";

    if (mysqli_query($conn_attendance, $sql_create_session)) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $sql_create_session . "<br>" . mysqli_error($conn_attendance);
    }
}
?>

