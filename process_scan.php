<?php

// // Include the database connection
// include 'database.php';

// // Check if the form is submitted
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $rfid_tag = mysqli_real_escape_string($conn_attendance, $_POST['rfid_tag']);
//     $timestamp = mysqli_real_escape_string($conn_attendance, $_POST['timestamp']);

//     // Insert data into the attendance table
//     $query = "INSERT INTO attendance (rfid_tag, timestamp) VALUES ('$rfid_tag', '$timestamp')";
//     if (mysqli_query($conn_attendance, $query)) {
//         echo "Attendance recorded successfully.";
//     } else {
//         echo "Error: " . mysqli_error($conn_attendance);
//     }

//     // Close the database connection
//     mysqli_close($conn_attendance);
// } else {
//     echo "Invalid request.";
// }
?>
