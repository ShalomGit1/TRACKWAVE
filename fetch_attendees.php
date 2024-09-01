<?php
// Database connection parameters
$server = 'localhost';
$username = 'root';
$password = '';
$database = 'student_attendance';

// Create connection
$connection = new mysqli($server, $username, $password, $database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Fetch attendees for the selected session
if (isset($_GET['session_id'])) {
    $sessionId = $_GET['session_id'];
    $query = "SELECT rfid_tag, timestamp FROM attendance WHERE session_id = '$sessionId'";
    $result = $connection->query($query);

    if ($result->num_rows > 0) {
        echo '<ul>';
        while ($row = $result->fetch_assoc()) {
            echo '<li>' . $row['rfid_tag'] . ' - ' . $row['timestamp'] . '</li>';
        }
        echo '</ul>';
    } else {
        echo 'No attendees found for this session.';
    }
}

// Close connection
$connection->close();
?>
