<?php
session_start();
if (!isset($_SESSION['e_id'])) {
    header("Location: ../e_portal/employee_login.php"); // Redirect to login if not logged in
    exit();
}

include '../db/db_conn.php';

// Fetch user info
$employeeId = $_SESSION['e_id'];
$sql = "SELECT firstname, middlename, lastname, email, role FROM employee_register WHERE e_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error); // Display error if statement preparation fails
}

$stmt->bind_param("i", $employeeId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error executing query: " . $stmt->error); // Display error if query execution fails
}

$employeeInfo = $result->fetch_assoc();
$stmt->close();
$conn->close();

$profilePicture = !empty($employeeInfo['profile_picture']) ? $employeeInfo['profile_picture'] : '../img/defaultpfp.png';
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  </head>

<body class="sb-nav-fixed bg-dark">
    <nav class="sb-topnav navbar navbar-expand navbar-dark border-bottom border-1 border-warning bg-dark">
        <a class="navbar-brand ps-3 text-light" href="../e_portal/employee_dashboard.php">Employee Portal</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars text-light"></i></button>
        
        <div class="d-flex ms-auto me-0 me-md-3 my-2 my-md-0 align-items-center">
            <div class="text-light me-3 p-2 rounded shadow-sm bg-gradient" id="currentTimeContainer" 
            style="background: linear-gradient(45deg, #333333, #444444); border-radius: 5px;">
                <span class="d-flex align-items-center">
                    <span class="pe-2">
                        <i class="fas fa-clock"></i> 
                        <span id="currentTime">00:00:00</span>
                    </span>
                    <button class="btn btn-outline-warning btn-sm ms-2" type="button" onclick="toggleCalendar()">
                        <i class="fas fa-calendar-alt"></i>
                        <span id="currentDate">00/00/0000</span>
                    </button>
                </span>
            </div>
        </div>
    </nav>
    
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion bg-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading text-center text-muted">Your Profile</div>
                        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                            <li class="nav-item dropdown text-center">
                                <a class="nav-link dropdown-toggle text-light d-flex justify-content-center" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo $profilePicture; ?>" class="rounded-circle border border-dark" width="120" height="120" />
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="../e_portal/e_profile.php">Profile</a></li>
                                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                                    <li><hr class="dropdown-divider" /></li>
                                    <li><a class="dropdown-item" href="../e_portal/employee_login.php" onclick="confirmLogout(event)">Logout</a></li>
                                </ul>
                            </li>
                            <li class="nav-item text-light text-center">
                                <span class="big text-light mb-1">
                                    <?php echo htmlspecialchars($employeeInfo['firstname'] . ' ' . $employeeInfo['middlename'] . ' ' . $employeeInfo['lastname']); ?>
                                </span>
                                <span class="big text-light"><?php echo htmlspecialchars($employeeInfo['role']); ?></span>
                            </li>
                        </ul>
                        
                        <div class="sb-sidenav-menu-heading text-center text-muted border-top border-1 border-warning mt-3">Employee Dashboard</div>
                        
                        <a class="nav-link text-light" href="../e_portal/employee_dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapseTAD" aria-expanded="false" aria-controls="collapseTAD">
                            <div class="sb-nav-link-icon"><i class="fa fa-address-card"></i></div>
                            Time and Attendance
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseTAD" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link text-light" href="../main/tad_display.php">QR for Attendance</a>
                                <a class="nav-link text-light" href="../main/tad_timesheet.php">View Record Attendance</a>
                                <a class="nav-link text-light" href="../main/timeout.php">Out</a>
                            </nav>
                        </div>
                        <!-- Other collapsible sections (Leave Management, Performance Management, etc.) go here following the same structure -->
                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLM" aria-expanded="false" aria-controls="collapseLM">
                            <div class="sb-nav-link-icon "><i class="fas fa-calendar-times"></i></div>
                            Leave Management
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLM" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link text-light" href="../e_portal/leave_update.php">File Leave Request</a>
                                <a class="nav-link text-light" href="../e_portal/leave_balance.php">View Remaining Leave</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePM" aria-expanded="false" aria-controls="collapsePM">
                            <div class="sb-nav-link-icon"><i class="fas fa-line-chart"></i></div>
                            Performance Management
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePM" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link text-light" href="../e_portal/employee_department.php">Evaluation</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSR" aria-expanded="false" aria-controls="collapseSR">
                            <div class="sb-nav-link-icon"><i class="fa fa-address-card"></i></div>
                            Social Recognition
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseSR" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link text-light" href="../e_portal/e_recognition.php">View Your Rating</a>
                            </nav>
                        </div> 
                    </div>
                </div>
                <div class="sb-sidenav-footer text-center">
                    <div class="small text-light">Logged in as: <?php echo htmlspecialchars($employeeInfo['firstname'] . ' ' . $employeeInfo['lastname']); ?></div>
                </div>
            </nav>
        </div>
        
        <div id="layoutSidenav_content">
            <main>
                <!-- Main content goes here -->
            </main>
            <footer class="py-4 bg-dark mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Microfinance 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a> &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/e_dashboard.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../src/assets/demo/chart-area-demo.js"></script>
    <script src="../src/assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="../js/datatables-simple-demo.js"></script>
</body>

</html>
