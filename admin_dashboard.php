<?php
// Database connection for login_register database (users)
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "login_register";

$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Database connection for student_attendance database
$dbAttendanceName = "student_attendance";
$conn_attendance = mysqli_connect($hostName, $dbUser, $dbPassword, $dbAttendanceName);
if (!$conn_attendance) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetching student attendance data
$sql_students = "SELECT rfid_tag, COUNT(*) as attendance_count 
                 FROM attendance
                 GROUP BY rfid_tag";

$result_students = mysqli_query($conn_attendance, $sql_students);
if (!$result_students) {
    die("Error fetching students: " . mysqli_error($conn_attendance));
}

// Fetching all users from login_register database
$sql_users = "SELECT * FROM users";
$result_users = mysqli_query($conn, $sql_users);
if (!$result_users) {
    die("Error fetching users: " . mysqli_error($conn));
}

// Count number of students
$num_students = mysqli_num_rows($result_students);

// Count number of teachers (users)
$num_users = mysqli_num_rows($result_users);


// Fetching sessions and the number of attendees for each session
$sql_sessions = "SELECT session_name, COUNT(attendance.id) as attendee_count 
                 FROM sessions 
                 LEFT JOIN attendance 
                 ON attendance.timestamp BETWEEN sessions.start_time AND sessions.end_time
                 GROUP BY sessions.session_id, sessions.session_name";
$result_sessions = mysqli_query($conn_attendance, $sql_sessions);
$sessions_data = [];
if ($result_sessions && mysqli_num_rows($result_sessions) > 0) {
    while ($row_sessions = mysqli_fetch_assoc($result_sessions)) {
        $sessions_data[] = $row_sessions;
    }
}

// Prepare data for Chart.js
$sessions_names = [];
$sessions_attendee_counts = [];
foreach ($sessions_data as $session) {
    $sessions_names[] = $session['session_name'];
    $sessions_attendee_counts[] = $session['attendee_count'];
}



// Fetch active sessions and student counts
$sql_active_sessions = "SELECT session_name, COUNT(DISTINCT attendance.rfid_tag) as student_count
                        FROM sessions
                        LEFT JOIN attendance ON attendance.timestamp BETWEEN sessions.start_time AND sessions.end_time
                        WHERE NOW() BETWEEN sessions.start_time AND sessions.end_time
                        GROUP BY sessions.session_id, sessions.session_name";

$result_active_sessions = mysqli_query($conn_attendance, $sql_active_sessions);
$active_sessions_data = [];

if ($result_active_sessions && mysqli_num_rows($result_active_sessions) > 0) {
    while ($row_active_sessions = mysqli_fetch_assoc($result_active_sessions)) {
        $active_sessions_data[] = $row_active_sessions;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="preload" href="MADEOuterSansAlt-Light.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Black.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Bold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Thin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSans-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSans-Light.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="MADEOuterSansAlt-Thin.woff2" as="font" type="font/woff2" crossorigin>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/da3601086d.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            /* background-color: #f8f9fa; */
            font-family: 'MADE Outer Sans';
    font-weight: 300;
    font-style: normal;
    background-color:#F2F2F2;
        }
      
        .sidebar {
    position: fixed; /* Fixed position */
    top: 0; /* Position at the top */
    left: 0; /* Position at the left */
    bottom: 0; /* Extend to the bottom of the viewport */
    width: 250px; /* Adjust width as needed */
    background-color: #141414; /* Dark background color */
    padding-top: 20px;
    z-index: 1000; /* Ensure sidebar is above other content */
    overflow-y: auto; /* Enable vertical scrolling if content exceeds sidebar height */
}

.sidebar .list-group-item {
    border: none;
    color: black; /* White text color */
    background-color: transparent; /* Transparent background */
    transition: all 0.3s ease;
    padding: 12px 20px;
}

.sidebar .list-group-item:hover {
    background-color: #515151; /* Darken background on hover */
    color:white;
}

.sidebar .list-group-item.active {
    background-color: #505050; /* Active item background color */
}

.sidebar .list-group-item.active:hover {
    background-color: white; /* Darken active item background on hover */
}

.content {
    margin-left: 250px; /* Ensure content doesn't overlap sidebar */
    padding: 20px;
}




    </style>
    
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=YOUR_TRACKING_ID"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'YOUR_TRACKING_ID');
    </script>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 bg-light sidebar d-flex flex-column">
    <div class="list-group">
    <a href="#reports" class="list-group-item list-group-item-action active" data-bs-toggle="pill" role="tab"> <i class="fa-solid fa-chart-line"></i>   Dashboard</a>

        <a href="#students" class="list-group-item list-group-item-action " data-bs-toggle="pill" role="tab"><i class="fa-solid fa-graduation-cap"></i>   Students</a>
        <a href="#users" class="list-group-item list-group-item-action" data-bs-toggle="pill" role="tab"><i class="fa-solid fa-users-viewfinder"></i>   Users</a>
        <a href="#settings" class="list-group-item list-group-item-action disabled" data-bs-toggle="pill" role="tab"><i class="fa-solid fa-users-gear"></i>   Settings</a>
        <a href="logout.php" class="list-group-item list-group-item-action"><i class="fa-solid fa-right-from-bracket"></i>   Logout</a>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
      
</div>

       
     
    </div>
    <div class="mt-auto"></div> <!-- Pushes content to the bottom -->
</div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 content">
            <div class="tab-content">
                <!-- Students Tab -->
                <div id="students" class="tab-pane fade ">
                    <h2>Students Attendance</h2>
                    <input type="text" id="studentSearch" class="form-control mb-3" placeholder="Search student...">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>RFID Tag</th>
                                <th>Attendance Count</th>
                                <th>Attendance Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_students = mysqli_fetch_assoc($result_students)) {
                                $rfid_tag = $row_students['rfid_tag'];
                                $attendance_count = $row_students['attendance_count'];
                                // Calculate attendance percentage (dummy calculation)
                                $attendance_percentage = ($attendance_count > 0) ? ($attendance_count / 50) * 100 : 0;
                                echo "<tr>";
                                echo "<td>$rfid_tag</td>";
                                echo "<td>$attendance_count</td>";
                                echo "<td>$attendance_percentage%</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Users Tab -->
                <div id="users" class="tab-pane fade">
                    <h2>System Users</h2>
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_users = mysqli_fetch_assoc($result_users)) {
                                $user_id = $row_users['id'];
                                $user_fullname = $row_users['full_name'];
                                $user_email = $row_users['email'];
                                echo "<tr>";
                                echo "<td>$user_id</td>";
                                echo "<td>$user_fullname</td>";
                                echo "<td>$user_email</td>";
                                echo "<td><a href='user_profile.php?id=$user_id'>View Profile</a> | <a href='delete_user.php?id=$user_id'>Delete</a></td>";

                                // echo "<td><a href='user_profile.php?id=$user_id'>View Profile</a> | <a href='delete_user.php'>Delete</a></td>";

                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Settings Tab -->
                <div id="settings" class="tab-pane fade">
                    <h2>Settings</h2>
                    <div id="settings" class="tab-pane fade">
    <h2>Settings</h2>
    <h2></h2>
    <div class="accordion " id="settingsAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingProfile">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProfile" aria-expanded="true" aria-controls="collapseProfile">
                    Profile Settings
                </button>
            </h2>
            <div id="collapseProfile" class="accordion-collapse collapse show" aria-labelledby="headingProfile" data-bs-parent="#settingsAccordion">
                <div class="accordion-body">
                    <!-- Profile settings content -->
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSystem">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSystem" aria-expanded="false" aria-controls="collapseSystem">
                    System Settings
                </button>
            </h2>
            <div id="collapseSystem" class="accordion-collapse collapse" aria-labelledby="headingSystem" data-bs-parent="#settingsAccordion">
                <div class="accordion-body">
                    
                    <!-- System settings content -->
                </div>
            </div>
        </div>
        <!-- Additional settings sections -->
    </div>
</div>

                </div>

              <!-- Reports Tab -->
<div id="reports" class="tab-pane fade show active">
    <h2>Admin Dashboard</h2>
    <div class="row">
    <div class="col-md-4 mb-4">
        <div class="p-3 bg-white shadow-sm d-flex justify-content-between align-items-center rounded custom-card custom-card2">
            <div>
                <h3 class="fs-2"> <?php echo $num_students; ?></h3>
                <p class="fs-5">Students</p>
            </div>
            <i class="fa-solid fa-school fs-1 l p-4"></i>
          
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="p-3 bg-white shadow-sm d-flex justify-content-between align-items-center rounded custom-card custom-card2">
            <div>
                <h3 class="fs-2">  <?php echo $num_users; ?></h3>
                <p class="fs-5">Lecturers</p>
            </div>
            <i class="fa-solid fa-person-chalkboard fs-1 l p-4"></i>
         
        </div>
    </div>
    <div class="col-md-4 mb-4 position-relative">
    <div class="p-3 bg-white shadow-sm d-flex justify-content-between align-items-center rounded custom-card custom-card2">
        <div>
            <h3 class="fs-2"> <?php echo count($active_sessions_data); ?></h3>
            <p class="fs-5">Active session</p>
        </div>
        <i class="fa-solid fa-toggle-on fs-1 l p-4"></i>
        <span class="position-absolute top-0 end-0 translate-middle p-2 bg-success border border-light rounded-circle">
            <span class="visually-hidden">Active</span>
        </span>
    </div>
</div>


</div>

    

    
                   
    <canvas id="userChart" width="400" height="200"></canvas>
    <script>
        const ctx = document.getElementById('userChart').getContext('2d');
        const userChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Students', 'Teachers', 'Active Sessions'],
                datasets: [{
                    label: '# of Users',
                    data: [
                        <?php echo $num_students; ?>,
                        <?php echo $num_users; ?>,
                        <?php echo count($active_sessions_data); ?> // Number of active sessions
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</div>

                    </script>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript for student search functionality
    document.getElementById('studentSearch').addEventListener('input', function () {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('#students tbody tr');
        rows.forEach(row => {
            const rfidTag = row.children[0].textContent.toLowerCase();
            row.style.display = rfidTag.includes(searchText) ? '' : 'none';
        });
    });

   

</script>


</body>
</html>

<?php
// Close database connections
mysqli_close($conn);
mysqli_close($conn_attendance);
?>
