<?php
session_start();
include '../db/db_conn.php';

// Check if the user is logged in and get the employee's ID from the session
if (!isset($_SESSION['e_id'])) {
    die("Error: User is not logged in.");
}

$employeeId = $_SESSION['e_id'];

// Fetch the employee information from the database
$sql = "SELECT firstname, lastname, role, department FROM employee_register WHERE e_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $employeeId);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) {
    die("Error: Employee information not found.");
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .btn-raise {
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-raise:hover {
            transform: translateY(-5px); /* Raise effect */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2); /* Shadow effect */
        }
    </style>
  </head>

  <body class="sb-nav-fixed bg-dark">
    <!-- Navigation Bar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-light">
        <a class="navbar-brand ps-3 text-muted" href="../e_portal/employee_dashboard.php">Leave Request</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars text-dark"></i></button>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item text-dark d-flex flex-column align-items-start">
                <span class="big text-dark mb-1">
                    <?php echo htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']); ?>
                </span>
                <span class="big text-dark">
                    <?php echo htmlspecialchars($employee['role']); ?>
                </span>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../img/defaultpfp.png" class="rounded-circle border border-dark" width="40" height="40" />
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="../e_portal/e_profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="../e_portal/employee_login.php" onclick="confirmLogout(event)">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <!-- Sidebar Menu -->
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading text-center bg-warning text-dark">Logo</div>
                        
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseTAD" aria-expanded="false" aria-controls="collapseTAD">
                            <div class="sb-nav-link-icon"><i class="fa fa-address-card"></i></div>
                            Time and Attendance
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseTAD" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="../main/tad_display.php">QR for Attendance</a>
                                <a class="nav-link" href="../main/tad_timesheet.php">View Record Attendance</a>
                                 <a class="nav-link" href="../main/timeout.php">out</a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLM" aria-expanded="false" aria-controls="collapseLM">
                            <div class="sb-nav-link-icon"><i class="fas fa-calendar-times"></i></div>
                            Leave Management
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLM" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="../e_portal/leave_update.php">File Leave Request</a>
                                <a class="nav-link" href="../e_portal/leave_balance.php">View Remaining Leave</a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePM" aria-expanded="false" aria-controls="collapsePM">
                            <div class="sb-nav-link-icon"><i class="fas fa-line-chart"></i></div>
                            Performance Management
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePM" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="../e_portal/employee_department.php">Evaluation</a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSR" aria-expanded="false" aria-controls="collapseSR">
                            <div class="sb-nav-link-icon"><i class="fa fa-address-card"></i></div>
                            Social Recognition
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseSR" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="../e_portal/e_recognition.php">View Your Rating</a>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as: <?php echo htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']); ?></div>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">
            <!-- Main Content -->
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6 border border-light rounded p-4 mt-5">
                            <form id="leave-request-form" action="../db/leave_conn.php" method="POST">
                                <h2 class="text-center text-light">Leave Request Form</h2>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="first_name" class="text-light">First Name:</label>
                                            <input type="text" class="form-control text-dark" id="first_name" name="first_name" value="<?php echo htmlspecialchars($employee['firstname']); ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="last_name" class="text-light">Last Name:</label>
                                            <input type="text" class="form-control text-dark" id="last_name" name="last_name" value="<?php echo htmlspecialchars($employee['lastname']); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="role" class="text-light">Role:</label>
                                            <input type="text" class="form-control text-dark" id="role" name="role" value="<?php echo htmlspecialchars($employee['role']); ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="department" class="text-light">Department:</label>
                                            <input type="text" class="form-control text-dark" id="department" name="department" value="<?php echo htmlspecialchars($employee['department']); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="start_date" class="text-light">Start Date:</label>
                                            <input type="date" class="form-control text-dark" id="start_date" name="start_date" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_date" class="text-light">End Date:</label>
                                            <input type="date" class="form-control text-dark" id="end_date" name="end_date" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="leave_type" class="text-light">Type of Leave:</label>
                                    <select class="form-control text-dark" id="leave_type" name="leave_type" required>
                                        <option value="">Select a leave type</option>
                                        <option value="Annual Leave">Annual Leave</option>
                                        <option value="Sick Leave">Sick Leave</option>
                                        <option value="Family Leave">Family Leave</option>
                                    </select>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-dark border border-light btn-raise">Submit Leave</button>
                                </div>
                                <div class="text-center mt-3">
                                    <a class="btn btn-dark border border-light btn-raise" href="../e_portal/leave_balance.php">Check Remaining Leave</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="py-4 bg-light mt-auto bg-dark">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/e_dashboard.js"></script>
  </body>
</html>
