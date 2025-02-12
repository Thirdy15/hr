<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notWorkingDays = json_decode(file_get_contents('php://input'), true)['notWorkingDays'];
    file_put_contents('not_working_days.json', json_encode($notWorkingDays));
    echo "Not working days updated successfully!";
    exit();
}

$notWorkingDays = json_decode(file_get_contents('not_working_days.json'), true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Work Days</title>
    <style>
        /* CSS for the page */
        /* ...existing styles... */
        .not-working {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>No Work Days</h1>
    </header>

    <main>
        <div id="calendar"></div>
    </main>

    <script>
        const notWorkingDays = <?php echo json_encode($notWorkingDays); ?>;
        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        function generateCalendar() {
            const calendar = document.getElementById('calendar');
            calendar.innerHTML = '';

            months.forEach((month, monthIndex) => {
                const monthDiv = document.createElement('div');
                monthDiv.innerHTML = `<h3>${month}</h3>`;
                const monthTable = document.createElement('table');
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
                        if (date <= daysInMonth[monthIndex]) {
                            dayCell.textContent = date;
                            if (notWorkingDays.includes(`${month} ${date}`)) {
                                dayCell.classList.add('not-working');
                            }
                            date++;
                        } else {
                            dayCell.innerHTML = '&nbsp;';
                        }
                        weekRow.appendChild(dayCell);
                    }
                    monthBody.appendChild(weekRow);
                }
                monthTable.appendChild(monthBody);
                monthDiv.appendChild(monthTable);
                calendar.appendChild(monthDiv);
            });
        }

        generateCalendar();
    </script>
</body>
</html>
