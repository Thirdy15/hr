<?php
session_start();

// Include database connection
include '../db/db_conn.php'; 

// Ensure the user is logged in and is an employee
if (!isset($_SESSION['e_id'])) {
    echo "Please log in to view your evaluation.";
    exit;
}

$employeeId = $_SESSION['e_id'];

// Fetch the employee details
$sql_employee = "SELECT firstname, middlename, lastname, role FROM employee_register WHERE e_id = ?";
$stmt_employee = $conn->prepare($sql_employee);
$stmt_employee->bind_param("i", $employeeId);
$stmt_employee->execute();
$employee_result = $stmt_employee->get_result();

// Check if employee data exists
if ($employee_result->num_rows > 0) {
    $employeeInfo = $employee_result->fetch_assoc();
} else {
    echo "Employee data not found.";
    exit;
}

// Fetch the average of the employee's evaluations
$sql = "SELECT 
            AVG(quality) AS avg_quality, 
            AVG(communication_skills) AS avg_communication_skills, 
            AVG(teamwork) AS avg_teamwork, 
            AVG(punctuality) AS avg_punctuality, 
            AVG(initiative) AS avg_initiative,
            COUNT(*) AS total_evaluations 
        FROM admin_evaluations 
        WHERE e_id = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employeeId);
$stmt->execute();
$result = $stmt->get_result();

// Check if evaluations exist
if ($result->num_rows > 0) {
    $evaluation = $result->fetch_assoc();
} else {
    echo "No evaluations found.";
    exit;
}

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
        /* Global style for text color */
        body {
            color: white; /* Apply white text color globally */
        }
        
        /* Navbar text color */
        .navbar-nav .nav-item a {
            color: white !important; /* Force white color for navbar items */
        }

        /* Table styling */
        table th, table td {
            color: white; /* Apply white text to table cells */
        }

        /* Navbar background */
        .sb-topnav.navbar-dark {
            background-color: #333; /* Dark background for navbar */
        }

        /* Sidebar menu text color */
        .sb-sidenav-dark .nav-link {
            color: white; /* Sidebar links in white */
        }

        .sb-sidenav-dark .sb-sidenav-menu-heading {
            color: white; /* Sidebar menu heading in white */
        }
    </style>
  </head>

  <body class="sb-nav-fixed bg-dark">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-light">
        <a class="navbar-brand ps-3 text-muted" href="../e_portal/employee_dashboard.php">Human Resources</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars text-dark"></i></button>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item text-dark d-flex flex-column align-items-start">
                <span class="big text-dark mb-1">
                    <?php echo htmlspecialchars($employeeInfo['firstname'] . ' ' . $employeeInfo['middlename'] . ' ' . $employeeInfo['lastname']); ?>
                </span>
                <span class="big text-dark">
                    <?php echo htmlspecialchars($employeeInfo['role']); ?>
                </span>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../img/defaultpfp.png" class="rounded-circle border border-dark" width="40" height="40" />
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
                                 <a class="nav-link" href="../main/timeout.php">Out</a>
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
                    <div class="small">Logged in as: <?php echo htmlspecialchars($employeeInfo['firstname'] . ' ' . $employeeInfo['lastname']); ?></div>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">
            <main>
                <div class="container mt-5">
                    <h2>Your Evaluation Results</h2>
                    <p>Total number of evaluations: <?php echo htmlspecialchars($evaluation['total_evaluations']); ?></p>

                    <div class="chart-container">
                        <canvas id="evaluationChart" width="400" height="200"></canvas>
                    </div>

                    <table class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Average Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Quality of Work</td>
                                <td><?php echo htmlspecialchars(number_format($evaluation['avg_quality'], 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Communication Skills</td>
                                <td><?php echo htmlspecialchars(number_format($evaluation['avg_communication_skills'], 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Teamwork</td>
                                <td><?php echo htmlspecialchars(number_format($evaluation['avg_teamwork'], 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Punctuality</td>
                                <td><?php echo htmlspecialchars(number_format($evaluation['avg_punctuality'], 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Initiative</td>
                                <td><?php echo htmlspecialchars(number_format($evaluation['avg_initiative'], 2)); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <footer class="py-4 bg-light mt-auto bg-dark">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between small">
                <div class="text-muted">Copyright &copy; Your Website 2023</div>
                <div>
                    <a href="#">Privacy Policy</a> &middot; <a href="#">Terms &amp; Conditions</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('evaluationChart').getContext('2d');
        const chartData = {
            labels: [
                'Quality of Work', 
                'Communication Skills', 
                'Teamwork', 
                'Punctuality', 
                'Initiative'
            ],
            datasets: [{
                label: 'Average Ratings',
                data: [
                    <?php echo htmlspecialchars(number_format($evaluation['avg_quality'], 2)); ?>,
                    <?php echo htmlspecialchars(number_format($evaluation['avg_communication_skills'], 2)); ?>,
                    <?php echo htmlspecialchars(number_format($evaluation['avg_teamwork'], 2)); ?>,
                    <?php echo htmlspecialchars(number_format($evaluation['avg_punctuality'], 2)); ?>,
                    <?php echo htmlspecialchars(number_format($evaluation['avg_initiative'], 2)); ?>
                ],
                backgroundColor: [
                    'rgba(26, 188, 156, 0.2)', 
                    'rgba(41, 128, 185, 0.2)', 
                    'rgba(241, 196, 15, 0.2)', 
                    'rgba(231, 76, 60, 0.2)', 
                    'rgba(155, 89, 182, 0.2)'
                ],
                borderColor: [
                    'rgba(26, 188, 156, 1)', 
                    'rgba(41, 128, 185, 1)', 
                    'rgba(241, 196, 15, 1)', 
                    'rgba(231, 76, 60, 1)', 
                    'rgba(155, 89, 182, 1)'
                ],
                borderWidth: 1
            }]
        };

        const myChart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5 // Assuming ratings are out of 5
                    }
                }
            }
        });
    </script>
  </body>
</html>
