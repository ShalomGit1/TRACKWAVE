<?php
// Database connection
include 'database.php';

// Fetch user ID from URL
$user_id = $_GET['id'];

// Fetch user details
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt = $conn_login->prepare($sql_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();

// Fetch user attendance history based on user_id
// $sql_history = "SELECT hs.session_id, s.session_name, hs.start_time, hs.end_time
//                 FROM history_sessions hs
//                 JOIN sessions s ON hs.session_id = s.session_id
//                 WHERE hs.user_id = ?";
// $stmt = $conn_attendance->prepare($sql_history);
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $result_history = $stmt->get_result();
// Fetch session history for the logged-in user
$sql_fetch_history = "SELECT * FROM sessions WHERE user_id = '$user_id' ORDER BY start_time DESC";
$result_history = mysqli_query($conn_attendance, $sql_fetch_history);


// Fetch active session based on user_id and current time
$sql_active_session = "SELECT * FROM sessions WHERE user_id = ? AND end_time > NOW()";
$stmt = $conn_attendance->prepare($sql_active_session);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_active_session = $stmt->get_result();
$active_session = $result_active_session->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="preload" href="MADEOuterSansAlt-Light.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Black.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Bold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Thin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSans-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSans-Light.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Thin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/da3601086d.js" crossorigin="anonymous"></script>
</head>
<style>
    body{
        font-family: 'MADE Outer Sans';
    font-weight: 300;
    font-style: normal;
    background-color:#F2F2F2;
    }
</style>
<body>
<div class="container mt-5">
<div class="d-grid gap-2 d-md-flex justify-content-md-start">
    <a class="btn btn-dark me-md-2" href="admin_dashboard.php" role="button"><i class="fa-solid fa-arrow-left-long"></i>Back</a>
</div>

    <h1>User Profile: <?php echo htmlspecialchars($user['full_name']); ?></h1>

    <div class="row">
        <div class="col-6">
            <h3>Profile Details</h3>
            <ul class="list-group">
                <li class="list-group-item">Full Name: <?php echo htmlspecialchars($user['full_name']); ?></li>
                <li class="list-group-item">Email: <?php echo htmlspecialchars($user['email']); ?></li>
            </ul>
        </div>
        <div class="col-6 ">
            
            <h3>Active Session</h3>
            <?php if ($active_session): ?>
                <ul class="list-group">
                    <li class="list-group-item">Session ID: <?php echo htmlspecialchars($active_session['session_id']); ?></li>
                    <li class="list-group-item">Session Name: <?php echo htmlspecialchars($active_session['session_name']); ?></li>
                    <li class="list-group-item">Start Time: <?php echo htmlspecialchars($active_session['start_time']); ?></li>
                    <li class="list-group-item">End Time: <?php echo htmlspecialchars($active_session['end_time']); ?></li>
                </ul>
            <?php else: ?>
                <p>No active sessions.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="mt-4">
        <h3> <?php echo htmlspecialchars($user['full_name']); ?>, History</h3>
        <div class="d-flex justify-content-end mb-2">
        <a href="download_history.php" class="btn btn-success"><i class="fa-solid fa-download"></i>   Download History</a>
    </div>
        <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Session Name</th>
                                    <th scope="col">Start Time</th>
                                    <th scope="col">End Time</th>
                                    <th scope="col">Attendees</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result_history && mysqli_num_rows($result_history) > 0) {
                                    while ($row = mysqli_fetch_assoc($result_history)) {
                                        $session_id = $row['session_id'];
                                        $session_name = $row['session_name'];
                                        $start_time = $row['start_time'];
                                        $end_time = $row['end_time'];

                                        // Fetch attendees for the session
                                        $sql_fetch_attendees = "SELECT * FROM attendance WHERE timestamp BETWEEN '$start_time' AND '$end_time'";
                                        $result_attendees = mysqli_query($conn_attendance, $sql_fetch_attendees);

                                        echo "<tr>
                                            <td>{$session_name}</td>
                                            <td>{$start_time}</td>
                                            <td>{$end_time}</td>
                                            <td>";

                                        if ($result_attendees && mysqli_num_rows($result_attendees) > 0) {
                                            while ($attendee = mysqli_fetch_assoc($result_attendees)) {
                                                echo $attendee['rfid_tag'] . "<br>";
                                            }
                                        } else {
                                            echo "No attendees present .";
                                        }

                                        echo "</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No session history found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
    </div>
</div>


</body>
</html>

<?php
// Close database connections
$conn_login->close();
$conn_attendance->close();
?>
