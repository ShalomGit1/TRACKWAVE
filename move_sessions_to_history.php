<?php
require_once "database.php"; // Adjust the path as per your file structure

// Function to move sessions to history if end time has passed
function moveExpiredSessionsToHistory($conn_attendance) {
    $current_time = date('Y-m-d H:i:s');
    $sql_move_to_history = "INSERT INTO history_sessions (session_id, start_time, end_time)
                            SELECT session_id, start_time, end_time FROM sessions 
                            WHERE end_time < '$current_time' 
                            AND session_id NOT IN (SELECT session_id FROM history_sessions)";

    if (mysqli_query($conn_attendance, $sql_move_to_history)) {
        // Delete moved sessions from sessions table
        $sql_delete_sessions = "DELETE FROM sessions WHERE end_time < '$current_time'";
        mysqli_query($conn_attendance, $sql_delete_sessions);
        echo "Sessions moved to history successfully.";
    } else {
        echo "Error moving sessions to history: " . mysqli_error($conn_attendance);
    }
}

// Call the function to move expired sessions to history
moveExpiredSessionsToHistory($conn_attendance);

// Close database connection
mysqli_close($conn_attendance);
?>
