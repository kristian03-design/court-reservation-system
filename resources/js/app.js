import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const calendarElement = document.getElementById('reservation-calendar');

    if (calendarElement && window.FullCalendar) {
        const calendar = new window.FullCalendar.Calendar(calendarElement, {
            initialView: 'dayGridMonth',
            height: 520,
            events: calendarElement.dataset.eventsUrl,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek',
            },
        });

        calendar.render();
    }
});
