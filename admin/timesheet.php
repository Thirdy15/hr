<?php
session_start();

if (!isset($_SESSION['a_id'])) {
    header("Location: ../admin/adminlogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Record</title>
    <style>
        /* CSS for the page */

        /* Common button style */
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* New Link Style */
        .new-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745; /* Green color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .new-link:hover {
            background-color: #218838; /* Darker green */
        }

        /* Basic styling for the body and sections */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
        }

        h1 {
            margin: 0;
        }

        main {
            padding: 20px;
        }

        .add-record,
        .attendance-table {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #ccc; /* Gray background */
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 40%; /* Slightly smaller */
            text-align: center;
        }

        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .month h3 {
            background: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: gray; /* Background color black */
        }

        th, td {
            padding: 10px;
            border: 1px solid #e9ecef;
            text-align: center;
            color: white; /* Text color white */
        }

        th {
            background: #007bff;
            color: white;
        }

        td.selected {
            background: #dc3545;
            color: white;
        }

        button {
            margin-top: 20px;
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <header>
        <h1>Attendance Record</h1>
    </header>

    <main>
        <section class="add-record">
            <h2>Add Attendance</h2>
            <form id="attendance-form">
                <div class="form-group">
                    <label for="name">Employee Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="">Select</option>
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                        <option value="Late">Late</option>
                        <option value="Excused">Excused</option>
                    </select>
                </div>
                <button type="submit">Add Record</button>
                <!-- New Link Below the Status -->
                <a href="#" class="new-link" onclick="openModal()">Set No Work</a>
            </form>
        </section>

        <section class="attendance-table">
            <h2>Attendance Records</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="record-table-body">
                    <!-- Records will be dynamically inserted here -->
                </tbody>
            </table>
        </section>
    </main>

    <footer>
    </footer>

    <div id="notWorkingModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Select Not Working Days</h3>
            <div class="navigation">
                <button onclick="prevMonth()">Previous</button>
                <button onclick="nextMonth()">Next</button>
            </div>
            <div id="monthlyCalendar"></div>
            <button onclick="setCompanyWideSchedule()">Confirm</button>
        </div>
    </div>

    <script src="../js/Display.js"></script>
    <script>
        let currentMonth = 0;
        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        function generateCalendar(month) {
            const monthlyCalendar = document.getElementById('monthlyCalendar');
            monthlyCalendar.innerHTML = '';
            
            const monthTable = document.createElement('table');
            const monthHeader = document.createElement('thead');
            monthHeader.innerHTML = `<tr><th colspan="7">${months[month]}</th></tr>`;
            monthTable.appendChild(monthHeader);

            const daysHeader = document.createElement('tr');
            const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
            daysOfWeek.forEach(day => {
                const th = document.createElement('th');
                th.textContent = day;
                daysHeader.appendChild(th);
            });
            monthTable.appendChild(daysHeader);

            const monthBody = document.createElement('tbody');
            let date = 1;
            for (let i = 0; i < 6; i++) {
                const weekRow = document.createElement('tr');
                for (let j = 0; j < 7; j++) {
                    const dayCell = document.createElement('td');
                    if (date <= daysInMonth[month]) {
                        dayCell.textContent = date;
                        dayCell.onclick = () => dayCell.classList.toggle('selected');
                        date++;
                    } else {
                        dayCell.innerHTML = '&nbsp;';
                    }
                    weekRow.appendChild(dayCell);
                }
                monthBody.appendChild(weekRow);
            }
            monthTable.appendChild(monthBody);
            monthlyCalendar.appendChild(monthTable);
        }

        function openModal() {
            document.getElementById('notWorkingModal').style.display = 'block';
            generateCalendar(currentMonth);
        }

        function closeModal() {
            document.getElementById('notWorkingModal').style.display = 'none';
        }

        function nextMonth() {
            currentMonth = (currentMonth + 1) % 12;
            generateCalendar(currentMonth);
        }

        function prevMonth() {
            currentMonth = (currentMonth - 1 + 12) % 12;
            generateCalendar(currentMonth);
        }

        function setCompanyWideSchedule() {
            const days = document.querySelectorAll('td.selected');
            if (days.length === 0) {
                alert('Please select at least one day');
                return;
            }
            const notWorkingDays = [];
            days.forEach(day => {
                const month = months[currentMonth];
                const date = day.textContent;
                notWorkingDays.push(`${month} ${date}`);
            });

            fetch('no_work.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ notWorkingDays })
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                closeModal();
            });
        }
    </script>
</body>
</html>
