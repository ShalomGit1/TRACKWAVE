<?php
session_start();
include 'database.php';


$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session after login

// Fetch user profile data
// Retrieve user information based on user_id
$user_id = $_SESSION["user_id"];
$sql_user = "SELECT * FROM users WHERE id = $user_id";
$result_user = mysqli_query($conn_login, $sql_user);

if ($result_user) {
    $user = mysqli_fetch_assoc($result_user);
    if ($user) {
        $user_name = $user['full_name'];
        $user_email = $user['email'];
    } else {
        echo "User not found.";
    }
} else {
    echo "Error: " . mysqli_error($conn_login);
}

// Fetch current active session for the logged-in user
$sql_fetch_current_session = "SELECT * FROM sessions WHERE NOW() BETWEEN start_time AND end_time AND user_id = '$user_id' ORDER BY start_time DESC LIMIT 1";
$result_current_session = mysqli_query($conn_attendance, $sql_fetch_current_session);

// Fetch session history for the logged-in user
$sql_fetch_history = "SELECT * FROM sessions WHERE user_id = '$user_id' ORDER BY start_time DESC";
$result_history = mysqli_query($conn_attendance, $sql_fetch_history);

// Fetch total number of students
$sql_total_students = "SELECT COUNT(DISTINCT rfid_tag) as total_students FROM attendance";
$result_total_students = mysqli_query($conn_attendance, $sql_total_students);
$total_students = mysqli_fetch_assoc($result_total_students)['total_students'];

// Fetch total number of history sessions for the logged-in user
$sql_total_history_sessions = "SELECT COUNT(*) as total_history_sessions FROM sessions WHERE user_id = '$user_id'";
$result_total_history_sessions = mysqli_query($conn_attendance, $sql_total_history_sessions);
$total_history_sessions = mysqli_fetch_assoc($result_total_history_sessions)['total_history_sessions'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://kit.fontawesome.com/da3601086d.js" crossorigin="anonymous"></script>
    <link rel="preload" href="MADEOuterSansAlt-Light.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Black.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Bold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Thin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSans-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSans-Light.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Thin.woff2" as="font" type="font/woff2" crossorigin>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.4/main.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.4/main.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.4/main.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.4/main.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.4/main.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/2.24.0/moment.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

    <style>
        body {
            /* font-family: 'Arial', sans-serif; */
            background-color: #ffffff;
            font-size: 1rem;
           
            font-family: 'MADE Outer Sans';
            font-weight: 100;
        }

        .sidebar {
            min-height: 100vh;
            border-right: 1px solid #dee2e6;
           

            
        }

        .sidebar-sticky {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: 0.5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .nav-link {
            font-weight: 500;
            color: #333;
        }

        .nav-link:hover {
            color: gray;
        }

      
        .custom-card {
            min-height: 70px;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .custom-card i {
            font-size: 2rem;
            color: #6c757d;
        }


        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
            margin: 10px;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .content {
            margin-top: 20px;
        }

        table {
            margin-top: 20px;
        }

        th, td {
            text-align: center;
        }
        .calendar {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 10px;
            height: 250px; /* Adjust height as needed */
        }
        .time {
            border: 1px solid #ddd;
            padding: 20px;
            height: 100px; /* Adjust height as needed */
        }
        .notepad {
            border: 1px solid #ddd;
            padding: 20px;
            height: 359px; /* Adjust height as needed */
            margin-bottom: 10px;
            /* background-color:gray; */
        }
        .btn-container {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="showDashboard()">
                            <i class="fa-solid fa-house"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showCreateSession()">
                                <i class="fas fa-plus"></i>
                                Create Session
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showHistory()">
                                <i class="fas fa-history"></i>
                                History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showProfile()">
                                <i class="fas fa-user"></i>
                                Profile
                            </a>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSearch()">
                            <i class="fa-solid fa-calendar-day"></i>
                                event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php" onclick="logout()">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div id="content" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="custom-card mt-4">
                    <!-- <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Students</h5>
                            <p class="card-text"></p>
                        </div>
                    </div> -->
                    <div class="col-md-4">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded custom-card custom-card2">
                            <div>
                                <h3 class="fs-2"><?php echo $total_students; ?></h3>
                                <p class="fs-5">Total Students</p>
                            </div>
                            <i class="fa-solid fa-graduation-cap fs-1 l p-4"></i>
                    
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded custom-card custom-card2">
                            <div>
                                <h3 class="fs-2"><?php echo $total_history_sessions; ?></h3>
                                <p class="fs-5"> Sessions Created</p>
                            </div>
                            <i class="fa-solid fa-square-plus fs-1 l p-4"></i>
                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded custom-card custom-card2">
                            <div>
                                <h3 class="fs-2"><?php echo $total_history_sessions; ?></h3>
                                <p class="fs-5"> History</p>
                            </div>
                            <i class="fa-solid fa-clock-rotate-left fs-1 l p-4"></i>
                            <!-- <i class="fas fa-school fs-1 l p-4"></i> -->
                        </div>
                    </div>
                    <!-- <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total History Sessions</h5>
                            <p class="card-text"></p>
                        </div>
                    </div> -->
                </div>

                <!-- Dashboard Content -->
                <div id="dashboard-content" class="content container-fluid px-4">
                    <h2>Active Session</h2>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <?php
                            if ($result_current_session && mysqli_num_rows($result_current_session) > 0) {
                                $current_session = mysqli_fetch_assoc($result_current_session);
                                $session_id = $current_session['session_id'];
                                $session_name = $current_session['session_name'];
                                $start_time = $current_session['start_time'];
                                $end_time = $current_session['end_time'];

                                // Fetch the attendees for the current session
                                $sql_fetch_attendees = "SELECT * FROM attendance WHERE timestamp BETWEEN '$start_time' AND '$end_time'";
                                $result_attendees = mysqli_query($conn_attendance, $sql_fetch_attendees);

                                echo "<thead>
                                    <tr>
                                        <th scope='col'>Session Name</th>
                                        <th scope='col'>Start Time</th>
                                        <th scope='col'>End Time</th>
                                        <th scope='col'>Attendees</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{$session_name}</td>
                                        <td>{$start_time}</td>
                                        <td>{$end_time}</td>
                                        <td>";

                                if ($result_attendees && mysqli_num_rows($result_attendees) > 0) {
                                    while ($row = mysqli_fetch_assoc($result_attendees)) {
                                        echo $row['rfid_tag'] . "<br>";
                                    }
                                } else {
                                    echo "No attendees found.";
                                }

                                echo "</td>
                                    </tr>
                                </tbody>";
                            } else {
                                echo "<tbody><tr><td colspan='4'>No active sessions, create one.</td></tr></tbody>";
                            }
                            ?>
                        </table>
                    </div>
                </div>
             


                <!-- Create Session Content -->
                <div id="create-session-content" class="content container-fluid px-4" style="display: none;">
                    <h2>Create Session</h2>
                    <form method="post" action="create_session.php">
                        <div class="form-group">
                            <label for="session_name">Session Name</label>
                            <input type="text" class="form-control" id="session_name" name="session_name" required>
                        </div>
                        <div class="form-group">
                            <label for="start_time">Start Time</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label for="end_time">End Time</label>
                            <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3" >Create Session</button>
                    </form>
                </div>
                <div id="search-content" class="content container-fluid px-4" style="display: none;">
    <h2>event</h2>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="calendar">
                    <!-- Calendar content here -->
                    <div id="datepicker"></div>
                </div>
                <div class="time">
                    <!-- Time content here -->
                    Time
 <div id="clock" class="text-center fs-1"></div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="notepad">
                    <!-- Notepad content here -->
                    Notepad
 <div class="form-group">
        <label for="session-notepad">.</label>
        <textarea class="form-control" id="session-notepad" rows="5"></textarea>
    </div>
    <div id="notepad-alert" class="alert alert-success mt-2" role="alert" style="display: none;">
        Notepad content saved successfully!
    </div>
                </div>
                
                <div class="btn-container">
                    <button id="save-notepad" class="btn btn-primary">Save Notepad</button>
                </div>
 
            </div>
        </div>
    </div>
    
    <!-- <div class="container mt-4">
    <div class="row">
      <div class="col-md-6">
        <div id="calendar"></div>
      </div>
      <div class="col-md-6">
        <div id="clock" class="text-center fs-1"></div>
      </div>
    </div>
  </div>

    <div class="form-group">
        <label for="session-notepad">Session Notepad</label>
        <textarea class="form-control" id="session-notepad" rows="5"></textarea>
    </div>
    
    <button id="save-notepad" class="btn btn-primary">Save Notepad</button>
    
    <div id="notepad-alert" class="alert alert-success mt-2" role="alert" style="display: none;">
        Notepad content saved successfully!
    </div> -->
</div>



                <!-- History Content -->
                <div id="history-content" class="content container-fluid px-4" style="display: none;">
                    <h2>History</h2>
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

               
               
                

                <!-- Profile Content -->
                <div id="profile-content" class="content container-fluid px-4" style="display: none;">
                    <h2>Profile</h2>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Name: <?php echo $user_name; ?></h5>
                            <p class="card-text">Email: <?php echo $user_email; ?></p>
                        </div>
                    </div>
                </div>

                
               
                </div>

                


                <!-- search  -->


                
            </div>
            
        </div>
    </div>




    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    
    <script>
         $(document).ready(function(){
            $('#datepicker').datepicker();
        });
// $(function () {
//     $('#datetimepicker1').datetimepicker({
//         format: 'YYYY-MM-DD HH:mm:ss'
//     });
// });

$('#save-notepad').on('click', function() {
    var notepadContent = $('#session-notepad').val();
    
    // Save the notepad content to local storage or send it to the server
    localStorage.setItem('sessionNotepad', notepadContent);

    // Show a success message
    $('#notepad-alert').show().delay(3000).fadeOut();
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        }
      });

      calendar.render();
    });

    function updateClock() {
      var now = new Date();
      var hours = now.getHours().toString().padStart(2, '0');
      var minutes = now.getMinutes().toString().padStart(2, '0');
      var seconds = now.getSeconds().toString().padStart(2, '0');
      var timeString = hours + ':' + minutes + ':' + seconds;
      document.getElementById('clock').textContent = timeString;
    }

    setInterval(updateClock, 1000);
    updateClock(); // initial call to display the clock immediately
  </script>


    <script>
    // JavaScript for student search functionality
    const studentSearch = document.getElementById('studentSearch');
    studentSearch.addEventListener('input', function () {
        const searchText = this.value.toLowerCase();
        const studentsTable = document.querySelector('#students table tbody');
        const rows = studentsTable.getElementsByTagName('tr');
        Array.from(rows).forEach(function (row) {
            const rfidTag = row.getElementsByTagName('td')[0].textContent.toLowerCase();
            if (rfidTag.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
    <script>
        function showDashboard() {
            document.getElementById('dashboard-content').style.display = 'block';
            document.getElementById('create-session-content').style.display = 'none';
            document.getElementById('history-content').style.display = 'none';
            document.getElementById('profile-content').style.display = 'none';
            document.getElementById('search-content').style.display = 'none';

        }

        function showCreateSession() {
            document.getElementById('dashboard-content').style.display = 'none';
            document.getElementById('create-session-content').style.display = 'block';
            document.getElementById('history-content').style.display = 'none';
            document.getElementById('profile-content').style.display = 'none';
            document.getElementById('search-content').style.display = 'none';

        }

        function showHistory() {
            document.getElementById('dashboard-content').style.display = 'none';
            document.getElementById('create-session-content').style.display = 'none';
            document.getElementById('history-content').style.display = 'block';
            document.getElementById('profile-content').style.display = 'none';
            document.getElementById('search-content').style.display = 'none';

        }

        function showProfile() {
            document.getElementById('dashboard-content').style.display = 'none';
            document.getElementById('create-session-content').style.display = 'none';
            document.getElementById('history-content').style.display = 'none';
            document.getElementById('profile-content').style.display = 'block';
            document.getElementById('search-content').style.display = 'none';

        }
        function showSearch() {
            document.getElementById('dashboard-content').style.display = 'none';
            document.getElementById('create-session-content').style.display = 'none';
            document.getElementById('history-content').style.display = 'none';
            document.getElementById('profile-content').style.display = 'none';
            document.getElementById('search-content').style.display = 'block';
        }

        function logout() {
            // Add any necessary logout logic here
        }
    </script>
</body>
</html>
