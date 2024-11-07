<?php
session_start();
include '../db/db_conn.php';

// Ensure the employee is logged in
if (!isset($_SESSION['e_id'])) {
    die("Error: You must be logged in.");
}

// Get the logged-in employee's ID from the session
$employee_id = $_SESSION['e_id'];

// Fetch the employee's details from the database
$sql = "SELECT e_id, firstname, lastname, role, department, available_leaves, profile_picture FROM employee_register WHERE e_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the employee's data was found
if ($result->num_rows > 0) {
    $employee = $result->fetch_assoc();
} else {
    die("Error: Employee data not found.");
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
        body {
            background-color: #333;
        }
        .container {
            background-color: #444;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .text-light {
            color: #fff;
        }
        .table {
            color: #fff;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #555;
        }
        .table-striped tbody tr:nth-of-type(even) {
            background-color: #666;
        }
        .form-control {
            background-color: #555;
            color: #fff;
            border: 1px solid #666;
        }
        .form-control:focus {
            background-color: #666;
            border: 1px solid #fff;
        }
        .btn-white {
            background-color: #FFFFFF; /* Super white */
            color: #000; /* Black text */
            border: 1px solid #000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border-radius: 5px; /* Default border radius */
        }

        .btn-white:hover {
            background: #E0E0E0; /* Light gray on hover for contrast */
            color: black; /* Text color */
            border: 2px solid #000; /* Slightly thicker border */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            transform: translateY(-6px) scale(1.05); /* Increase raise effect */
        }

        .modal-content {
            background-color: #444;
        }
        .modal-header {
            background-color: #555;
            border-bottom: 1px solid #666;
        }
        .modal-footer {
            background-color: #555;
            border-top: 1px solid #666;
        }
    </style>
</head>

<body class="sb-nav-fixed bg-dark">
    <!-- Navigation Bar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-light">
        <a class="navbar-brand ps-3 text-muted" href="../e_portal/employee_dashboard.php">Employee Portal</a>
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
                    <img src="<?php echo !empty($employee['profile_picture']) ? $employee['profile_picture'] : '../img/defaultpfp.png'; ?>" class="rounded-circle border border-dark" width="40" height="40" />
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="../e_portal/e_profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li><hr class="dropdown-divider" /></li>
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
                        
                        <!-- Sidebar Links -->
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

        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <main>
                <!-- Leave Tracker Content -->
                <div class="container">
                    <h1 class="text-center text-light">Leave Tracker</h1>
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th style="color: white;">Employee ID</th>
                                <th style="color: white;">Name</th>
                                <th style="color: white;">Role</th>
                                <th style="color: white;">Department</th>
                                <th style="color: white;">Remaining Leave</th>
                            </tr>
                        </thead>
                        <tbody id="leave-table">
                            <tr>
                                <td><?php echo htmlspecialchars($employee['e_id']); ?></td>
                                <td><?php echo htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($employee['role']); ?></td>
                                <td><?php echo htmlspecialchars($employee['department']); ?></td>
                                <td><?php echo htmlspecialchars($employee['available_leaves']); ?> remaining</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-center mb-5 mt-4">
                    <a href="../e_portal/employee_dashboard.php" class="btn btn-white">Back to Dashboard</a>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/e_dashboard.js"></script>
</body>
</html>
