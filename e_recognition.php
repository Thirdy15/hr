<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard with Certificate of Recognition</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed bg-dark">
    <!-- Top Navigation Bar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-light">
        <a class="navbar-brand ps-3 text-muted" href="../e_portal/employee_dashboard.php">Employee Portal</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars text-dark"></i></button>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item text-dark d-flex flex-column align-items-start">
                <span class="big text-dark mb-1">
                  
                </span>
                <span class="big text-dark">
                    
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
        <!-- Sidebar Navigation -->
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
                    <div class="small">Logged in as: </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <main>
                <!-- Certificate of Recognition Content -->
                <div class="container-fluid bg-dark text-white py-5">
                    <div class="row justify-content-center">
                        <div class="col-md-12 text-center">
                            <h1 class="text-center mb-4 text-yellow">Certificates of Recognition</h1>
                            <div class="row justify-content-center">
                                <div class="col-md-8 border border-light rounded p-5 bg-dark text-white">
                                    <div class="row justify-content-center">
                                        <!-- Certificate 1 -->
                                        <div class="col-md-4">
                                            <div class="certificate bg-dark text-white">
                                                <div class="certificate-header">
                                                    <h2 class="text-center mb-4 text-yellow">Certificate of Recognition</h2>
                                                </div>
                                                <div class="certificate-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <img src="https://via.placeholder.com/150" alt="Employee Picture" class="img-fluid rounded-circle">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h3 class="text-center mb-4 text-yellow" id="employee-name-1">Lennon Aguilor</h3>
                                                            <p class="text-center mb-4 text-yellow" id="employee-role-1">Software Engineer</p>
                                                            <p class="text-center mb-4 text-yellow" id="employee-department-1">IT Department</p>
                                                            <p class="text-center mb-4 text-yellow">In recognition of outstanding contributions to the company.</p>
                                                            <p class="text-center mb-4 text-yellow">Your dedication, hard work, and commitment to excellence have not gone unnoticed.</p>
                                                            <p class="text-center mb-4 text-yellow">We are grateful for your service and look forward to your continued success.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="certificate-footer">
                                                    <p class="text-center mb-4 text-yellow">Date: <span id="date-1"></span></p>
                                                    <p class="text-center mb-4 text-yellow">Signature: ______________________________</p>
                                                    <button class="btn btn-primary" id="download-certificate-1">Download Certificate</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Certificate 2 -->
                                        <div class="col-md-4">
                                            <div class="certificate bg-dark text-white">
                                                <div class="certificate-header">
                                                    <h2 class="text-center mb-4 text-yellow">Certificate of Recognition</h2>
                                                </div>
                                                <div class="certificate-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <img src="https://via.placeholder.com/150" alt="Employee Picture" class="img-fluid rounded-circle">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h3 class="text-center mb-4 text-yellow" id="employee-name-2">Steffano Dizo</h3>
                                                            <p class="text-center mb-4 text-yellow" id="employee-role-2">Marketing Manager</p>
                                                            <p class="text-center mb-4 text-yellow" id="employee-department-2">Marketing Department</p>
                                                            <p class="text-center mb-4 text-yellow">In recognition of outstanding contributions to the company.</p>
                                                            <p class="text-center mb-4 text-yellow">Your dedication, hard work, and commitment to excellence have not gone unnoticed.</p>
                                                            <p class="text-center mb-4 text-yellow">We are grateful for your service and look forward to your continued success.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="certificate-footer">
                                                    <p class="text-center mb-4 text-yellow">Date: <span id="date-2"></span></p>
                                                    <p class="text-center mb-4 text-yellow">Signature: ______________________________</p>
                                                    <button class="btn btn-primary" id="download-certificate-2">Download Certificate</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Certificate 3 -->
                                        <div class="col-md-4">
                                            <div class="certificate bg-dark text-white">
                                                <div class="certificate-header">
                                                    <h2 class="text-center mb-4 text-yellow">Certificate of Recognition</h2>
                                                </div>
                                                <div class="certificate-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <img src="https://via.placeholder.com/150" alt="Employee Picture" class="img-fluid rounded-circle">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h3 class="text-center mb-4 text-yellow" id="employee-name-3">Wendel Ureta</h3>
                                                            <p class="text-center mb-4 text-yellow" id="employee-role-3">Sales Representative</p>
                                                            <p class="text-center mb-4 text-yellow" id="employee-department-3">Sales Department</p>
                                                            <p class="text-center mb-4 text-yellow">In recognition of outstanding contributions to the company.</p>
                                                            <p class="text-center mb-4 text-yellow">Your dedication, hard work, and commitment to excellence have not gone unnoticed.</p>
                                                            <p class="text-center mb-4 text-yellow">We are grateful for your service and look forward to your continued success.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="certificate-footer">
                                                    <p class="text-center mb-4 text-yellow">Date: <span id="date-3"></span></p>
                                                    <p class="text-center mb-4 text-yellow">Signature: ______________________________</p>
                                                    <button class="btn btn-primary" id="download-certificate-3">Download Certificate</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

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
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/e_dashboard.js"></script>
    <script src="../js/e_recog.js"></script>
</body>
</html>
