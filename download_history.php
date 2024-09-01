<?php
session_start();
include 'database.php';

$user_id = $_SESSION['user_id'];

// Fetch session history for the logged-in user
$sql_fetch_history = "SELECT * FROM sessions WHERE user_id = '$user_id' ORDER BY start_time DESC";
$result_history = mysqli_query($conn_attendance, $sql_fetch_history);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="session_history.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, array('Session Name', 'Start Time', 'End Time', 'Attendees'));

if ($result_history && mysqli_num_rows($result_history) > 0) {
    while ($row = mysqli_fetch_assoc($result_history)) {
        $session_id = $row['session_id'];
        $session_name = $row['session_name'];
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];

        // Fetch attendees for the session
        $sql_fetch_attendees = "SELECT * FROM attendance WHERE timestamp BETWEEN '$start_time' AND '$end_time'";
        $result_attendees = mysqli_query($conn_attendance, $sql_fetch_attendees);
        
        $attendees = [];
        if ($result_attendees && mysqli_num_rows($result_attendees) > 0) {
            while ($attendee = mysqli_fetch_assoc($result_attendees)) {
                $attendees[] = $attendee['rfid_tag'];
            }
        } else {
            $attendees[] = "No attendees present";
        }

        fputcsv($output, array($session_name, $start_time, $end_time, implode(', ', $attendees)));
    }
} else {
    fputcsv($output, array('No session history found.'));
}

fclose($output);
exit();
?>
