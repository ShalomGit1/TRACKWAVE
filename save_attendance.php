<?php
include('database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rfid_tag = $_POST['rfid_tag'];
    $timestamp = $_POST['timestamp']; // Should be in 'Y-m-d H:i:s' format

    // Insert data into the 'attendance' table
    $sql = "INSERT INTO attendance (rfid_tag, timestamp) VALUES (?, ?)";

    $stmt = $conn_attendance->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ss", $rfid_tag, $timestamp);
        if ($stmt->execute()) {
            echo "Data inserted successfully";
        } else {
            error_log("Error executing statement: " . $stmt->error);
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        error_log("Error preparing statement: " . $conn_attendance->error);
        echo "Error: " . $conn_attendance->error;
    }
}

$conn_attendance->close();
?>
