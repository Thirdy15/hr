document.addEventListener('DOMContentLoaded', function() {
    const calendarBody = document.getElementById('calendar-body');
    const prevMonth = document.getElementById('prev-month');
    const nextMonth = document.getElementById('next-month');
    const saveButton = document.getElementById('save-button'); // Assuming there's a button with id 'save-button'
    let currentDate = new Date();
    let selectedDates = new Set();

    function renderCalendar(date) {
        calendarBody.innerHTML = '';
        const month = date.getMonth();
        const year = date.getFullYear();

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        let row = document.createElement('tr');
        for (let i = 0; i < firstDay; i++) {
            row.appendChild(document.createElement('td'));
        }

        for (let day = 1; day <= daysInMonth; day++) {
            if (row.children.length === 7) {
                calendarBody.appendChild(row);
                row = document.createElement('tr');
            }
            const cell = document.createElement('td');
            cell.textContent = day;
            const cellDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            cell.dataset.date = cellDate;
            if (selectedDates.has(cellDate)) {
                cell.classList.add('selected');
            }
            cell.addEventListener('click', function() {
                if (selectedDates.has(cellDate)) {
                    selectedDates.delete(cellDate);
                    cell.classList.remove('selected');
                } else {
                    selectedDates.add(cellDate);
                    cell.classList.add('selected');
                }
            });
            row.appendChild(cell);
        }

        calendarBody.appendChild(row);
    }

    prevMonth.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    nextMonth.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    saveButton.textContent = 'Set No Work'; // Change button text to 'Set No Work'
    saveButton.addEventListener('click', function() {
        // Handle the logic for setting no work days
        console.log('No work days set for:', Array.from(selectedDates));
    });

    renderCalendar(currentDate);
});
