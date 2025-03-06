import './bootstrap';
// Import the FullCalendar bundle
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import listPlugin from '@fullcalendar/list';
import timeGridPlugin from '@fullcalendar/timegrid';

import interactionPlugin, { Draggable } from '@fullcalendar/interaction';
import multiMonthPlugin from '@fullcalendar/multimonth';
import frLocale from '@fullcalendar/core/locales/fr';

// Import the dataTables bundle
import DataTable from 'datatables.net-dt';

// Import the moment plugin
import moment from "moment";
import 'moment/locale/fr';

// Import the moment plugin
import Alpine from 'alpinejs';

// Import the sweetalert2 and animate.css plugin
import Swal from 'sweetalert2';
import { flashAlert, confirmationAlert } from '/resources/js/utils/flashAlert.js';

// Import the tippy plugin and css
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

// Add flashAlert to the window
window.flashAlert = flashAlert;
window.confirmationAlert = confirmationAlert;
window.Swal = Swal;

// Add FullCalendar to the window
window.Calendar = Calendar;
window.dayGridPlugin = dayGridPlugin;
window.timeGridPlugin = timeGridPlugin;
window.listPlugin = listPlugin;
window.interactionPlugin = interactionPlugin;
window.Draggable = Draggable;
window.multiMonthPlugin = multiMonthPlugin;
window.frLocale = frLocale;

// Add DataTable to the window
window.DataTable = DataTable;

// Add moment to the window
window.moment = moment;
window.moment.locale('fr');

document.addEventListener('alpine:init', () => {
    Alpine.directive('global', function (el, { expression }) {
        let f = new Function('_', '$data', '_.' + expression + ' = $data;return;');
        f(window, el._x_dataStack[0]);
    });
});

window.Alpine = Alpine;
document.addEventListener('DOMContentLoaded', () => { Alpine.start() });

// Add tippy to the window
window.tippy = tippy;
