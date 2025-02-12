<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notWorkingDays = json_decode(file_get_contents('php://input'), true)['notWorkingDays'];
    file_put_contents('../../admin/not_working_days.json', json_encode($notWorkingDays));
    echo "Not working days updated successfully!";
    exit();
}

$notWorkingDays = json_decode(file_get_contents('../../admin/not_working_days.json'), true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Work Days</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .calendar-container {
            max-width: 800px;
            margin: 50px auto; /* Added margin to position it below */
            padding: 20px;
            background-color: white;
            border: 2px solid #ddd; /* Added border */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: 500px; /* Increased height */
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .calendar-header h2 {
            margin: 0;
            font-size: 25px;
            color: #333;
        }
        .calendar-header button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .calendar-header button:hover {
            background-color: #0056b3;
        }
        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .calendar-table th, .calendar-table td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .calendar-table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }
        .calendar-table td {
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .calendar-table td:hover {
            background-color: #f1f1f1;
        }
        .not-working {
            background-color: #dc3545;
            color: white;
            cursor: pointer;
        }
        .not-working:hover {
            background-color: #c82333;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #333;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white text-center py-4">
        <h1>No Work Days</h1>
    </header>

    <main class="calendar-container">
        <div class="calendar-header">
            <button id="prevMonth">&lt; Previous</button>
            <h2 id="currentMonthYear"></h2>
            <button id="nextMonth">Next &gt;</button>
        </div>
        <table class="calendar-table" id="calendar">
            <thead>
                <tr>
                    <th>Sun</th>
                    <th>Mon</th>
                    <th>Tue</th>
                    <th>Wed</th>
                    <th>Thu</th>
                    <th>Fri</th>
                    <th>Sat</th>
                </tr>
            </thead>
            <tbody id="calendarBody"></tbody>
        </table>
    </main>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>This day has no work.</p>
        </div>
    </div>

    <script>
        const notWorkingDays = <?php echo json_encode($notWorkingDays); ?>;
        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();

        function generateCalendar(month, year) {
            const calendarBody = document.getElementById('calendarBody');
            const currentMonthYear = document.getElementById('currentMonthYear');
            calendarBody.innerHTML = '';

            currentMonthYear.textContent = `${months[month]} ${year}`;

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            let date = 1;
            for (let i = 0; i < 6; i++) {
                const row = document.createElement('tr');
                for (let j = 0; j < 7; j++) {
                    const cell = document.createElement('td');
                    if (i === 0 && j < firstDay) {
                        cell.textContent = '';
                    } else if (date > daysInMonth) {
                        cell.textContent = '';
                    } else {
                        cell.textContent = date;
                        if (notWorkingDays.includes(`${months[month]} ${date}`)) {
                            cell.classList.add('not-working');
                            cell.addEventListener('click', () => {
                                document.getElementById('myModal').style.display = "block";
                            });
                        }
                        date++;
                    }
                    row.appendChild(cell);
                }
                calendarBody.appendChild(row);
                if (date > daysInMonth) break;
            }
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar(currentMonth, currentYear);
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            generateCalendar(currentMonth, currentYear);
        });

        generateCalendar(currentMonth, currentYear);

        const modal = document.getElementById('myModal');
        const span = document.getElementsByClassName('close')[0];

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>