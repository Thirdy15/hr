<?php
session_start();
include '../db/db_conn.php';
include '../phpqrcode/qrlib.php'; // Include phpqrcode library

if (isset($_SESSION['update_success'])) {
    echo '<script>alert("' . htmlspecialchars($_SESSION['update_success']) . '");</script>';
    unset($_SESSION['update_success']);
}

// Fetch user info
$employeeId = $_SESSION['e_id'];
$sql = "SELECT e_id, firstname, middlename, lastname, birthdate, email, role, position, department, phone_number, address, pfp FROM employee_register WHERE e_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employeeId);
$stmt->execute();
$result = $stmt->get_result();
$employeeInfo = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Generate QR Code content
$qrData = 'Employee ID: ' . $employeeInfo['e_id'] . ' | Email: ' . $employeeInfo['email'];

$qrCodeDir = '../qrcodes/';
if (!is_dir($qrCodeDir)) {
    mkdir($qrCodeDir, 0755, true); // Create the directory if it doesn't exist
}

// Path to store the generated QR Code image
$qrImagePath = '../qrcodes/employee_' . $employeeId . '.png';

// Generate QR Code and save it as a PNG image
QRcode::png($qrData, $qrImagePath, QR_ECLEVEL_L, 4);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="User Profile Dashboard" />
    <meta name="author" content="Your Name" />
    <title>User Profile - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="bg-warning" id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sidenav navbar navbar-dark bg-dark">
                <div class="container-fluid">
                    <div class="big text-light">Hello, <?php echo htmlspecialchars($employeeInfo['firstname'] . ' ' . $employeeInfo['middlename'] . ' ' . $employeeInfo['lastname']); ?></div>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="../main/front.php" onclick="confirmLogout(event)">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4 bg-dark">
                    <h1 class="big mt-4 text-light">My Profile</h1>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4 border border-light">
                                <div class="card-header bg-warning">
                                    <h5 class="card-title text-center">Profile Picture</h5>
                                </div>
                                <div class="card-body text-center bg-dark">
                                    <img src="<?php echo !empty($employeeInfo['pfp']) ? htmlspecialchars($employeeInfo['pfp']) : '../img/defaultpfp.png'; ?>" class="img-fluid rounded-circle border border-light" width="200" height="200" alt="Profile Picture">
                                    <a href="javascript:void(0);" id="editPictureButton">
                                        <i class="text-light me-0 fas fa-edit"></i>
                                    </a>
                                    <input type="file" id="profilePictureInput" name="profile_picture" style="display:none;" accept="image/*">
                                    <table class="table text-light mt-3 text-start">
                                        <tr>
                                            <td>Name:</td>
                                            <td><?php echo htmlspecialchars($employeeInfo['firstname'] . ' ' . $employeeInfo['middlename'] . ' ' . $employeeInfo['lastname']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>ID:</td>
                                            <td>#<?php echo htmlspecialchars($employeeInfo['e_id']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Role:</td>
                                            <td><?php echo htmlspecialchars($employeeInfo['role']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Position:</td>
                                            <td><?php echo htmlspecialchars($employeeInfo['position']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Department:</td>
                                            <td><?php echo htmlspecialchars($employeeInfo['department']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Email:</td>
                                            <td><?php echo htmlspecialchars($employeeInfo['email']); ?></td>
                                        </tr>
                                        
                                    </table>
                                     <button type="button" class="btn btn-warning btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#qrCodeModal">Show QR Code</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card mb-4 border border-light">
                                <div class="card-header bg-warning">
                                    <h5 class="card-title text-center">My Information</h5>
                                </div>
                                <div class="card-body bg-dark">
                                    <form id="infoForm" action="../e_portal/update_eprofile.php" method="post">
                                        <div class="row mb-3">
                                            <label for="inputfName" class="col-sm-2 col-form-label text-light">First Name:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control bg-dark text-light border border-light" id="inputfName" name="firstname" value="<?php echo htmlspecialchars($employeeInfo['firstname']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inputmName" class="col-sm-2 col-form-label text-light">Middle Name:</label>
                                            <div class="col-sm-9"> 
                                                <input type="text" class="form-control bg-dark text-light border border-light" id="inputmName" name="middlename" value="<?php echo htmlspecialchars($employeeInfo['middlename']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inputlName" class="col-sm-2 col-form-label text-light">Last Name:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control bg-dark text-light border border-light" id="inputlName" name="lastname" value="<?php echo htmlspecialchars($employeeInfo['lastname']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inputbirth" class="col-sm-2 col-form-label text-light">Birthdate:</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control bg-dark text-light border border-light" id="inputbirth" name="birthdate" value="<?php echo htmlspecialchars($employeeInfo['birthdate']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inputEmail" class="col-sm-2 col-form-label text-light">Email Address:</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control bg-dark text-light border border-light" id="inputEmail" name="email" value="<?php echo htmlspecialchars($employeeInfo['email']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inputPhone" class="col-sm-2 col-form-label text-light">Phone Number:</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control bg-dark text-light border border-light" id="inputPhone" name="phone_number" value="<?php echo htmlspecialchars($employeeInfo['phone_number']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inputAddress" class="col-sm-2 col-form-label text-light">Address:</label>
                                            <div class="mb-3 col-sm-9">
                                                <textarea class="form-control border bg-dark text-light border-light" id="inputAddress" name="address" rows="1" readonly><?php echo htmlspecialchars($employeeInfo['address']); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <button type="submit" class="btn btn-warning border border-light d-none">Save Changes</button>
                                            <button type="button" id="editButton" class="btn btn-warning border border-light">Update Information</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content bg-dark text-light">
                        <div class="modal-header">
                            <h5 class="modal-title" id="qrCodeModalLabel">Employee QR Code</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <!-- Display the generated QR code -->
                            <img src="<?php echo $qrImagePath; ?>" alt="QR Code" class="img-fluid border border-light" width="200">
                            <p class="mt-3">Employee ID: <?php echo htmlspecialchars($employeeInfo['e_id']); ?></p>
                            <p>Name: <?php echo htmlspecialchars($employeeInfo['firstname'] . ' ' . $employeeInfo['middlename'] . ' ' . $employeeInfo['lastname']); ?></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
    <script src="../js/profile.js"></script>
</body>
</html>