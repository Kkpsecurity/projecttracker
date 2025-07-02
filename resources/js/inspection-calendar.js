import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

// Export for global use
window.FullCalendar = {
    Calendar,
    dayGridPlugin,
    interactionPlugin
};

// Import the CSS
import '@fullcalendar/core/vdom.css';
import '@fullcalendar/daygrid/main.css';
