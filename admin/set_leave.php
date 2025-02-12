<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hr2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notWorkingDays = json_decode(file_get_contents('php://input'), true)['notWorkingDays'];
    $sql = "DELETE FROM not_working_days";
    $conn->query($sql);

    foreach ($notWorkingDays as $day) {
        $sql = "INSERT INTO not_working_days (date) VALUES ('$day')";
        $conn->query($sql);
    }

    echo "Not working days set successfully";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company-wide Not Working Schedule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .month {
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
        }
        th, td {
            padding: 10px;
            border: 1px solid #e9ecef;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        td.selected {
            background: #dc3545;
            color: white;
        }
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
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            text-align: center;
        }
        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
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
        .navigation {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Company-wide Not Working Schedule</h2>
        <button onclick="openModal()">Set Not Working Days</button>
    </div>

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

            fetch('schedule.php', {
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