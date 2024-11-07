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
        /* Remove border-radius to prevent rounded corners */
        #layoutSidenav_nav {
            border-right: 2px solid #b38b00; /* Dark yellow border on the right side */
            border-top: none;
            border-bottom: none;
            border-left: none;
        }

        /* Remove the left corner border but keep internal borders */
        .sb-sidenav {
            border-left: 2px solid #b38b00; /* Dark yellow border on the left side */
            border-top: none;
            border-bottom: none;
        }

        /* Add some spacing and structure inside the sidebar items */
        .sb-sidenav-menu .nav-item {
            margin-bottom: 15px;
            border-bottom: 1px solid #b38b00;  /* Dark yellow border between items */
        }

        /* Remove border-radius from the sidebar menu items */
        .sb-sidenav-menu .nav-item .nav-link {
            padding: 12px 20px;  /* Add space inside the button */
            border-radius: 0;  /* Remove the rounded corners */
            transition: background-color 0.3s ease;
        }

        /* Keep the collapsible dropdown visually distinct */
        .sb-sidenav-menu .nav-item .collapse .nav-link {
            border-top: 1px solid #b38b00;  /* Add border above nested items */
            padding-left: 30px;  /* Indent nested items */
        }

        /* Styling for the individual profile section */
        .nav-item .dropdown-menu {
            border: 2px solid #b38b00; /* Dark yellow border */
            padding: 10px;
        }

        /* Button hover effect */
        .btn-raise {
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-raise:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        /* Optionally, add padding around each sidebar link to improve spacing */
        .sb-sidenav-menu .nav-item .nav-link {
            padding: 12px 20px;  /* Add space inside the button */
            border-radius: 0.25rem;  /* Slightly round corners */
            transition: background-color 0.3s ease;
        }

        /* Make the collapsible dropdown more visually distinct */
        .sb-sidenav-menu .nav-item .collapse .nav-link {
            border-top: 1px solid #b38b00;  /* Add border above nested items */
            padding-left: 30px;  /* Indent nested items */
        }
    </style>
  </head>

  <body class="sb-nav-fixed bg-dark">
    <!-- Navigation Bar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-light">
        <a class="navbar-brand ps-3 text-muted" href="../e_portal/employee_dashboard.php">Leave Request</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars text-dark"></i></button>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <!-- Profile section removed here -->
        </ul>
    </nav>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <!-- Sidebar Menu -->
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <!-- Profile Section Inside Sidebar -->
                        <div class="sb-sidenav-menu-heading text-center text-light">Your Profile</div>
                        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                            <li class="nav-item dropdown text-light d-flex justify-content-center ms-4">
                                <a class="nav-link dropdown-toggle text-light" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="../img/defaultpfp.png" class="rounded-circle border border-dark" width="120" height="120" />
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="../e_portal/e_profile.php">Profile</a></li>
                                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                                    <li><hr class="dropdown-divider" /></li>
                                    <li><a class="dropdown-item" href="../e_portal/employee_login.php" onclick="confirmLogout(event)">Logout</a></li>
                                </ul>
                            </li>
                            <li class="nav-item text-light d-flex ms-3 flex-column align-items-center text-center">
                                <span class="big text-light mb-1">
                                    <?php echo htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']); ?>
                                </span>      
                                <span class="big text-light">
                                    <?php echo htmlspecialchars($employee['role']); ?>
                                </span>
                            </li>
                        </ul>
                        
                        <div class="sb-sidenav-menu-heading text-center text-muted mt-3">Dashboard</div>
                        <a class="nav-link text-light" href="../e_portal/employee_dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>

                        <!-- Other Sidebar Links -->
                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapseTAD" aria-expanded="false" aria-controls="collapseTAD">
                            <div class="sb-nav-link-icon"><i class="fa fa-address-card"></i></div>
                            Time and Attendance
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseTAD" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="../main/tad_display.php">QR for Attendance</a>
                                <a class="nav-link" href="../main/tad_timesheet.php">View Record Attendance</a>
                                <a class="nav-link" href="../main/timeout.php">Out</a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLM" aria-expanded="false" aria-controls="collapseLM">
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

                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePM" aria-expanded="false" aria-controls="collapsePM">
                            <div class="sb-nav-link-icon"><i class="fas fa-line-chart"></i></div>
                            Performance Management
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePM" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="../e_portal/employee_department.php">Evaluation</a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSR" aria-expanded="false" aria-controls="collapseSR">
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
