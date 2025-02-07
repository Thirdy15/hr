<?php
// Start the session
session_start();

// Include database connection
include '../../db/db_conn.php';

// Ensure session variable is set
if (!isset($_SESSION['e_id'])) {
    die("Error: Employee ID is not set in the session.");
}

// Fetch leave types from leave_requests table
$leaveTypes = [];
$query = "SELECT DISTINCT leave_type FROM leave_requests WHERE e_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['e_id']);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $leaveTypes[$row['leave_type']] = 0; // Initialize with 0 total days
}
$stmt->close();

// Fetch used leave days for each type
$usedLeaveDays = [];
foreach ($leaveTypes as $type => $totalDays) {
    $query = "SELECT SUM(DATEDIFF(end_date, start_date) + 1) AS used_leaves FROM leave_requests WHERE e_id = ? AND status = 'approved' AND leave_type = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $_SESSION['e_id'], $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $usedLeaveDays[$type] = $row['used_leaves'] ?? 0;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leave Tracker</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #000000; /* Changed background color to black */
      margin: 0;
      padding: 0;
      overflow: hidden; /* Prevents scroll bar */
    }

    .container {
      display: block;
      position: absolute; /* Changed from fixed to absolute */
      top: 10%; /* Adjusted top position to move the container to the top */
      left: 50%;
      transform: translate(-50%, 0); /* Removed vertical centering */
      width: 90%; /* Adjusted width to 90% of the viewport */
      max-width: 1200px; /* Set a maximum width for larger screens */
      padding: 20px;
      background-color: #333333; /* Changed background color to dark */
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      z-index: 1000;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
      color: #ffffff; /* Changed text color to white */
    }

    table {
      width: 100%; /* Table takes up 100% of the container width */
      border-collapse: collapse;
      background-color: #444444; /* Changed table background color to dark */
      color: #ffffff; /* Changed text color to white */
    }

    th, td {
      padding: 20px; /* Increased padding to make cells bigger */
      font-size: 15px; /* Adjusted font size to 14px */
      text-align: left;
      border: 1px solid #555555; /* Changed border color to dark gray */
    }

    input {
      width: 100%;
      padding: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
      outline: none; /* Added to remove border on click */
      background-color: #555555; /* Changed input background color to dark */
      color: #ffffff; /* Changed input text color to white */
    }

    input.no-border {
      border: none;
      background-color: transparent;
      font-size: 12px; /* Match font size */
      color: #ffffff; /* Changed input text color to white */
    }

    button {
      width: 20%; /* Increased width */
      padding: 10px; /* Increased padding */
      background-color: black; /* Changed background color to black */
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 20px;
    }

    button:hover {
      background-color: #555555; /* Changed hover background color to a lighter dark */
    }

    .button-container {
      display: flex;
      justify-content: space-between; /* Place buttons on each side */
    }

    #leaveStatus p {
      font-size: 16px;
      margin: 5px 0;
      color: #ffffff; /* Changed text color to white */
    }

    .no-border {
      border: none;
    }

    .no-border-cell {
      border: none;
      text-align: center; /* Optionally align text inside cells */
      font-size: 13px; /* Match font size with large-font */
      color: #ffffff; /* Changed text color to white */
    }

    table {
      width: 100%;
      border-collapse: collapse; /* Ensures borders don't double up */
    }

    table td, table th {
      border: 1px solid #555555; /* Set border for table cells to dark gray */
      padding: 8px;
      text-align: left; /* Adjust text alignment */
    }

    table input {
      border: none; /* Remove border from input elements */
      width: 100%; /* Make input element fill the cell */
      box-sizing: border-box; /* Ensure padding is considered inside the input */
      outline: none; /* Added to remove border on click */
      background-color: #555555; /* Changed input background color to dark */
      color: #ffffff; /* Changed input text color to white */
    }

    .large-font {
      font-size: 13px; /* Increased font size */
      font-weight: normal; /* Changed to normal to make text not bold */
      color: #ffffff; /* Changed text color to white */
    }

    select.no-border {
      border: none;
      background-color: #555555; /* Changed background color to dark */
      font-size: 12px; /* Match font size */
      outline: none; /* Added to remove border on click */
      color: #ffffff; /* Changed select text color to white */
    }
  </style>
</head>
<body>
  <!-- Removed the "Open Leave Tracker" button -->
  <!-- Removed the modal overlay and its functionality -->

  <div class="container" id="leaveTrackerModal">
    <h1>Leave Tracker</h1>
    <div class="button-container">
      <button onclick="window.history.back();">Back</button>
      <!-- Removed the "Open Converter" button -->
    </div> <!-- Added missing closing div tag for button-container -->

    <!-- Removed the modal HTML structure -->

    <br> <!-- Added space instead of separator -->
    <div class="table-container">
      <table>
        <tr>
          <th>Employee ID</th>
          <th>Employee Name</th>
          <th>Role</th> <!-- Added Role column -->
          <th>Leave Type</th>
          <th>Total Days</th>
          <th>Used</th>
        </tr>
        <?php
          // Fetch user info
          $employeeId = $_SESSION['e_id'];
          $sql = "SELECT e_id, firstname, middlename, lastname, available_leaves, role, position, department, phone_number 
                  FROM employee_register WHERE e_id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $employeeId);
          $stmt->execute();
          $result = $stmt->get_result();
          $employeeInfo = $result->fetch_assoc();

          if ($employeeInfo) {
            echo "<tr>
                    <td><input type='text' class='no-border large-font' value='{$employeeInfo['e_id']}' readonly></td>
                    <td><input type='text' class='no-border large-font' value='{$employeeInfo['firstname']} {$employeeInfo['middlename']} {$employeeInfo['lastname']}' readonly></td>
                    <td><input type='text' class='no-border large-font' value='{$employeeInfo['role']}' readonly></td>
                    <td>
                      <select class='no-border large-font' id='leaveTypeSelect'>";
            foreach ($leaveTypes as $type => $totalDays) {
                echo "<option value='$type'>" . ucfirst($type) . " Leave</option>";
            }
            echo "      </select>
                    </td>
                    <td><input type='number' class='no-border' id='totalDays' readonly></td>
                    <td><input type='number' class='no-border' id='usedDays' readonly></td>
                  </tr>";
          } else {
            echo "<tr><td colspan='6'>Error fetching employee information.</td></tr>";
          }

          $stmt->close();
          $conn->close();
        ?>
      </table>
    </div>
  </div>
  
  <script>
    // JavaScript to handle leave type selection and update the table
    const leaveTypes = <?php echo json_encode($leaveTypes); ?>;
    const usedLeaveDays = <?php echo json_encode($usedLeaveDays); ?>;

    document.getElementById('leaveTypeSelect').addEventListener('change', function() {
      const leaveType = this.value;
      document.getElementById('totalDays').value = leaveTypes[leaveType];
      document.getElementById('usedDays').value = usedLeaveDays[leaveType];
    });

    // Trigger change event to set initial values
    document.getElementById('leaveTypeSelect').dispatchEvent(new Event('change'));
  </script>
</body>
</html>
