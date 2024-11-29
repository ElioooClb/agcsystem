<div>
    <section class="mx-[50px] mb-[50px]" wire:ignore>
        <div id='weekTotalsTitle' class="flex items-center justify-center gap-4">
        </div>
        <div id='calendar'></div>
    </section>
    <section>
        <div id="notesModal" class="fixed inset-0 z-10 hidden overflow-auto">
            <div class="flex items-center justify-center min-h-screen bg-black bg-opacity-50 notesModalBg">
                <div class="flex flex-col min-w-0 p-8 mb-4 bg-white rounded-lg md:min-w-[400px]">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold">Notes de l'utilisateur</h2>
                        <button class="text-gray-500 closeButton hover:text-gray-700 focus:outline-none"
                            aria-label="Close modal">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <textarea class="flex-grow text-gray-700 min-h-0 md:min-h-[300px] modal-body"> </textarea>
                    <div class="flex justify-end mt-6">
                        <button
                            class="px-4 py-2 text-gray-800 bg-gray-200 rounded closeButton hover:bg-gray-300 focus:outline-none">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="actionsModal" class="fixed inset-0 z-10 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Fond gris semi-transparent -->
                <div id="actionsModalBg" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-50"></div>
                <!-- Boîte modale -->
                <div
                    class="inline-block w-full max-w-lg p-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <!-- Titre -->
                    <h2 data-date="" class="text-3xl font-bold text-center b-6 "></h2>
                    <div class="flex items-center justify-center mb-3 text-white">
                        <span id='current-event' class="p-3 rounded"></span>
                    </div>

                    <!-- Heures de Jour -->
                    <div class="mb-6">
                        <label class="block mb-3 font-bold text-gray-700">Nombre d'heures : <br>(Si journée complète ne
                            rien spécifier) </label>
                        <input id="hours_day" type="time" value="00:00" name="heureJour"
                            class="w-full px-4 py-2 mb-3 text-2xl leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-lg appearance-none h-14 focus:outline-none focus:shadow-outline-gray"
                            placeholder="Sélectionnez une heure" required>
                    </div>
                    <hr>
                    <!-- Boutons -->
                    <menu id="actionsMenu" class="grid grid-cols-2 gap-4">
                        <button type="button" value="1"
                            class="px-6 py-3 text-white bg-green-500 rounded hover:bg-green-600 focus:outline-none focus:shadow-outline-green">
                            Congé Payé
                        </button>
                        <button type="button" value="2"
                            class="px-6 py-3 text-white bg-yellow-500 rounded hover:bg-yellow-600 focus:outline-none focus:shadow-outline-red">
                            Récupération
                        </button>
                        <button type="button" value="3"
                            class="px-6 py-3 text-white bg-blue-500 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline-blue">
                            Arrêt
                        </button>
                        <button type="button" value="4"
                            class="px-6 py-3 text-white bg-purple-500 rounded hover:bg-purple-600 focus:outline-none focus:shadow-outline-purple">
                            Absence
                        </button>
                        <button type="button" value="5"
                            class="px-6 py-3 text-white bg-gray-500 rounded hover:bg-gray-600 focus:outline-none focus:shadow-outline-gray">
                            Férié
                        </button>
                        <button type="button" data-action='deleteAvailability'
                            class="px-6 py-3 text-white bg-red-500 rounded deleteBtn hover:bg-red-600 focus:outline-none focus:shadow-outline-red">
                            Supprimer
                        </button>
                    </menu>
                    <input id="chantier_id" type="hidden" name="id_chantier" value="">

                    <!-- Bouton de Supprimer -->
                    <div class="col-span-2 mt-6 text-center">
                        <!-- Croix en haut à droite -->
                        <div class="absolute top-0 right-0 pt-6 pr-6">
                            <button id="actionBtnClose"
                                class="text-gray-400 closeButton hover:text-gray-500 focus:outline-none">
                                <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M6.707 6.293a1 1 0 011.414 0L12 10.586l3.879-3.88a1 1 0 111.414 1.414L13.414 12l3.88 3.879a1 1 0 01-1.414 1.414L12 13.414l-3.879 3.88a1 1 0 01-1.414-1.414L10.586 12 6.707 8.121a1 1 0 010-1.414z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div wire:ignore x-data="dropdown" x-global="isEndDate">
                        <button @click="toggle"
                            class="px-6 py-3 text-white bg-green-500 rounded hover:bg-green-600 focus:outline-none focus:shadow-outline-green">Sélection
                            de dates</button>
                        <div x-show="isOpen">
                            <hr>
                            <menu class="p-3 date-selection">
                                <label for="date_debut">Date de début</label>
                                <input
                                    class="w-full px-4 py-2 mb-3 text-2xl leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-lg appearance-none h-14 focus:outline-none focus:shadow-outline-gray"
                                    type="date" name="date_debut" id="date_debut">
                                <label for="date_fin">Date de fin</label>
                                <input
                                    class="w-full px-4 py-2 mb-3 text-2xl leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-lg appearance-none h-14 focus:outline-none focus:shadow-outline-gray"
                                    type="date" name="date_fin" id="date_fin">
                            </menu>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scriptsHeures')
    <script>
        // DEBUT [SPECGT20] - Refonte de la page pour empêcher l'actualisation lors de l'enregistrement
        document.addEventListener('livewire:load', () => {
            const CALCUL_TITLE = document.getElementById('weekTotalsTitle');
            // Link to the fullcalendar plugin in the node_modules folder
            const Calendar = window.Calendar;
            const dayGridPlugin = window.dayGridPlugin;
            const timeGridPlugin = window.timeGridPlugin;
            const listPlugin = window.listPlugin;
            const interactionPlugin = window.interactionPlugin;
            const multiMonthPlugin = window.multiMonthPlugin;
            const moment = window.moment;

            Alpine.data('dropdown', () => ({
                isOpen: false,

                toggle() {
                    this.isOpen = !this.isOpen
                },
            }));

            // Calendar
            let weekendsVisible = false;
            const calendarEl = document.getElementById('calendar');
            const calendar = new Calendar(calendarEl, {
                plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin, multiMonthPlugin],
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next toggleWeekendsButton today',
                    center: 'title',
                    right: 'dayGridMonth,listWeek',
                },
                customButtons: {
                    toggleWeekendsButton: {
                        text: 'weekends',
                        click: function() {
                            toggleWeekends();
                        }
                    }
                },
                buttonText: {
                    today: 'Aujourd\'hui',
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour',
                    list: 'Liste de la semaine',
                },
                dayHeaderFormat: {
                    weekday: 'long',
                },
                hiddenDays: [6, 0], // enleve le samedi et dimanche
                locale: 'fr',
                firstDay: 1,
                dateClick: function(arg) {
                    if (!arg.jsEvent.target.classList.contains("fc-daygrid-day-top") && arg.jsEvent
                        .target.tagName.toLowerCase() !== 'img') {
                        handleClick(arg);
                    }
                },
                events: @json($events),
                eventClick: function(arg) {
                    if (!arg.jsEvent.target.classList.contains("fc-daygrid-day-top")) {
                        handleClick(arg, true);
                    }
                },
                // DEBUT [SPECGT26] - Affichage des totaux par semaine et par mois
                datesSet: function(arg) {
                    if (arg.view.type === 'listWeek') {
                        let startDate = new Date(arg.start);
                        let endDate = new Date(arg.end);
                        startDate.setDate(startDate.getDate() + 1);
                        endDate.setDate(endDate.getDate() - 1);
                        Livewire.emit('onCalculWeekListHours', {
                            start: startDate.toISOString().split('T')[0],
                            end: arg.end.toISOString().split('T')[0],
                            dayHours: 0,
                            nightHours: 0,
                            travelHours: 0,
                            unproductiveHours: 0,
                            isOncallDuty: false,
                        });
                    }
                    if (arg.view.type === 'dayGridMonth') {
                        let dateStr = new Date(arg.view.currentStart);
                        dateStr.setDate(dateStr.getDate() + 1);
                        Livewire.emit('onCalculMonthlyHours', {
                            start: dateStr.toISOString().split('T')[0],
                            totalHours: 0,
                        });
                    }
                    isEventWeekend();
                },
                dayCellDidMount: function(arg) {
                    const cellHeaderElement = arg.el.querySelector('.fc-daygrid-day-top');
                    cellHeaderElement.classList.add("bg-slate-200");
                    cellHeaderElement.classList.add('fc-tooltips');
                    const noteElement = generateNote(calendar, arg);
                    cellHeaderElement.appendChild(noteElement);
                },
                eventDidMount: function(arg) {
                    if (arg.view.type === 'listWeek') {
                        if (arg.event.extendedProps.dayHours > 7) {
                            arg.el.style.backgroundColor = '#ffaeae';
                        }
                        if (arg.event.extendedProps.nightHours != 0) {
                            arg.el.style.backgroundColor = '#ffaeae';
                        }
                        if (arg.event.extendedProps.travelHours != 0) {
                            arg.el.style.backgroundColor = '#ffaeae';
                        }
                        if (arg.event.extendedProps.isOncallDuty) {
                            arg.el.style.backgroundColor = '#ffaeae';
                        }
                    }
                    isEventWeekend();

                    const viewType = arg.view.type;

                    // Hide events in listWeek view if they are not work events
                    if (viewType === 'listWeek') {
                        if (!arg.event.extendedProps.workEvent) {
                            arg.el.style.display = 'none'; // Hides the event
                        } else {
                            arg.el.style.display = ''; // Shows the event
                        }
                    }

                    // Ensure events are displayed in dayGridMonth
                    if (viewType === 'dayGridMonth') {
                        arg.el.style.display = ''; // Always show events in this view
                    }
                },
                eventRemove: function(arg) {
                    isEventWeekend();
                },
                // FIN [SPECGT26] - Affichage des totaux par semaine et par mois
                viewDidMount: function(arg) {
                    if (arg.view.type === 'listWeek') {
                        calendar.getEvents().forEach(event => {
                            if (!event.extendedProps.isAvaibility && !event.extendedProps
                                .oncallDuty && !event.extendedProps.nightEvent && !event
                                .extendedProps.onBusinessTrip) {
                                event.setProp('title', event.extendedProps.weekViewTitle);
                            } else if (!event.extendedProps.isAvaibility && event.extendedProps
                                .oncallDuty) {
                                const newTitle = ' Astreinte';
                                event.setProp('title', newTitle);
                            }
                        });
                    }
                    if (arg.view.type === 'dayGridMonth') {
                        calendar.getEvents().forEach(event => {
                            event.setProp('title', event.extendedProps.dayViewTitle);
                        });
                    }
                },
            });
            calendar.render();

            const menu = document.getElementById("actionsMenu");
            const actionsButtons = menu.querySelectorAll("button:not(.deleteBtn)");
            const actionsModalEl = document.getElementById('actionsModal');
            const deleteBtn = actionsModalEl.querySelector('.deleteBtn');

            actionsButtons.forEach((button) => {
                button.addEventListener("click", (e) => {
                    let dateDebut;
                    let dateFin;
                    if (!isEndDate.isOpen) {
                        dateDebut = actionsModalEl.querySelector('h2').getAttribute('data-date');
                    } else {
                        dateDebut = document.getElementById('date_debut').value;
                        dateFin = document.getElementById('date_fin').value;
                        if (!dateDebut || !dateFin) {
                            window.flashAlert('error',
                                'Veuillez sélectionner une date de début et une date de fin.');
                            return;
                        }
                    }
                    if (dateDebut === null) {
                        window.flashAlert('error',
                            'Veuillez sélectionner une date de début et une date de fin');
                        return;
                    }
                    const method = button.getAttribute('data-action');
                    const stateId = button.value;
                    const hoursElement = actionsModalEl.querySelector('#hours_day');
                    let hours = "07:00";
                    if (hoursElement.value.length === 0) {
                        hours = actionsModalEl.querySelector('#hours_day').value;
                    }
                    Livewire.emit('onAvailability', {
                        method: method,
                        date: dateDebut,
                        dayHours: hours,
                        stateId: stateId,
                        title: null,
                        start: dateDebut,
                        end: dateFin,
                        id: null,
                        allDay: true,
                        textColor: 'white',
                        backgroundColor: null,
                        note: null,
                        className: 'text-center',
                        isAvaibility: true,
                    });
                });
            });

            deleteBtn.addEventListener("click", (e) => {
                e.preventDefault();
                window.confirmationAlert('Êtes-vous sûr de vouloir supprimer cet évènement ?').then((
                    result) => {
                    if (!result) return;
                    const eventId = deleteBtn.getAttribute('data-id');
                    Livewire.emit('onDeleteEvent', {
                        eventId: eventId
                    });
                });
            });

            Livewire.on('availabilityProcessed', (data) => {
                data.forEach((event) => {
                    const localEvent = calendar.getEventById(event.id);
                    if (localEvent) {
                        localEvent.remove();
                    }
                    calendar.addEvent(event);
                    window.flashAlert('success', 'Evènement(s) ajouté(s) avec succès');
                });
            });
            Livewire.on('eventDeleted', (data) => {
                const localEvent = calendar.getEventById(data.eventId);
                if (localEvent) {
                    localEvent.remove();
                    flashAlert('success', 'Evènement supprimé avec succès');
                }
            });
            Livewire.on('onWeekListHoursCalculated', (data) => {
                generateWeekTitles(data);
            });
            Livewire.on('onMonthlyHoursCalculated', (data) => {
                generateMonthTitle(data);
                if (data.travelEventsDays.length > 0) {
                    data.travelEventsDays.forEach((value, key) => {
                        if (!calendar.getEventById(value.id)) {
                            calendar.addEvent({
                                id: value.id,
                                title: 'Trajet : ' + value.hours_travel + 'h',
                                start: value.date,
                                backgroundColor: '#886bfa',
                                textColor: 'white',
                                className: 'text-center',
                                travelsHours: value.hours_travel,
                            });
                        }
                    });
                }
            });

            /**
             * Handle the click event on the calendar
             * [SPECGT20]
             * @param {Object} arg
             * @param {Boolean} isEvent
             */
            function handleClick(arg, isEvent = false) {
                const spanEl = document.getElementById('current-event');
                spanEl.innerHTML = '';
                spanEl.style.backgroundColor = '';
                const date = arg.date ?? arg.event.start;
                const modalEl = document.getElementById('actionsModal');
                const h2El = modalEl.querySelector('h2');
                const deleteBtn = modalEl.querySelector('.deleteBtn');
                h2El.setAttribute('data-date', moment(date).format('YYYY-MM-DD'));
                h2El.textContent = moment(date).format('DD/MM/YYYY');
                const hours = modalEl.querySelector('#hours_day');
                hours.value = '00:00';
                if (!isEvent) {
                    deleteBtn.classList.add('hidden');
                } else {
                    spanEl.textContent = arg.event.title;
                    spanEl.style.backgroundColor = arg.event.backgroundColor;
                    deleteBtn.setAttribute('data-id', arg.event.id);
                    deleteBtn.classList.remove('hidden');
                    hours.value = floatToTime(arg.event.extendedProps.dayHours);
                    if (arg.event.extendedProps.dayHours > 0) {}
                }
                modalEl.classList.remove("hidden");
            }

            /**
             * Generate the note tooltips element
             * [SPECGT20]
             * @param {Object} calendar
             * @param {Object} arg
             * @returns {Object}
             */
            function generateNote(calendar, arg) {
                const divarg = document.createElement('span');
                const events = calendar.getEvents();
                const currentEvents = events.filter(e => {
                    if (e.start.toDateString() !== arg.date.toDateString()) return false;
                    if (e.extendedProps.isAvaibility) return false;
                    if (e.extendedProps.nightEvent) return false;
                    return true;
                })
                if (currentEvents.length > 0) {
                    const e = currentEvents[0];
                    if (e.extendedProps) {
                        if (e.extendedProps.note !== '') {
                            divarg.id = 'time' + e.id;
                            divarg.setAttribute('data-id', e.id);
                            divarg.classList.add("tooltips");

                            const svgEl = document.createElement('img');
                            svgEl.src = '/front/images/tooltip.svg';
                            svgEl.width = '20';
                            svgEl.height = '20';
                            divarg.appendChild(svgEl);

                            divarg.addEventListener('click', (event) => {
                                event.stopPropagation();
                                const notesModal = document.getElementById('notesModal');
                                const modalBody = notesModal.querySelector('.modal-body');
                                modalBody.value = e.extendedProps.note;
                                notesModal.classList.remove('hidden');
                            });
                        }
                    }
                }
                return divarg;
            }

            /**
             * Generate the week titles
             * [SPECGT26]
             * @param {Object} data
             * @return {void}
             */
            function generateWeekTitles(data) {
                CALCUL_TITLE.innerHTML = '';
                const total = data.dayHours + data.nightHours + data.travelsHours + data.unproductiveHours;

                const pTotal = generateTitle('Total : ', total, ['bg-gray-500']);
                CALCUL_TITLE.appendChild(pTotal);

                const pDay = generateTitle('Heures de jour : ', data.dayHours);
                CALCUL_TITLE.appendChild(pDay);

                const pNight = generateTitle('Heures de nuit : ', data.nightHours);
                CALCUL_TITLE.appendChild(pNight);

                const pTravel = generateTitle('Heures de trajet : ', data.travelsHours);
                CALCUL_TITLE.appendChild(pTravel);

                const pUnproductive = generateTitle('Heures non productives : ', data.unproductiveHours);
                CALCUL_TITLE.appendChild(pUnproductive);
            }

            /**
             * Generate the month title
             * [SPECGT26]
             * @param {Object} data
             * @return {void}
             */
            function generateMonthTitle(data) {
                CALCUL_TITLE.innerHTML = '';
                const total = data.dayHours + data.nightHours + data.travelsHours;
                const pTotal = generateTitle('Total : ', total, ['bg-gray-500']);
                CALCUL_TITLE.appendChild(pTotal);

                const pDay = generateTitle('Heures de jour : ', data.dayHours);
                CALCUL_TITLE.appendChild(pDay);

                const pNight = generateTitle('Heures de nuit : ', data.nightHours);
                CALCUL_TITLE.appendChild(pNight);

                const pTravel = generateTitle('Heures de trajet : ', data.travelsHours);
                CALCUL_TITLE.appendChild(pTravel);
            }

            /**
             * Toggle the weekends
             * [SPECGT26]
             * @return {void}
             */
            function toggleWeekends() {
                weekendsVisible = !weekendsVisible;
                calendar.setOption('hiddenDays', weekendsVisible ? [] : [6, 0]);
            }

            /**
             * Check if there is an event on the weekend and adapt the button weekends style
             * [SPECGT26]
             * @return {void}
             */
            function isEventWeekend() {
                const view = calendar.view;
                const start = view.activeStart;
                const end = view.activeEnd;
                const events = calendar.getEvents();

                const weekendEventExists = events.some(event => {
                    const eventStart = event.start;
                    const eventEnd = event.end || eventStart;

                    if (eventStart < start || eventEnd > end) {
                        return false;
                    }

                    const dayOfWeek = eventStart.getDay();
                    return dayOfWeek === 6 || dayOfWeek === 0; // 6 is Saturday, 0 is Sunday
                });

                if (weekendEventExists) {
                    document.querySelector('.fc-toggleWeekendsButton-button').style
                        .backgroundColor = 'rgb(255, 174, 174)';
                } else {
                    document.querySelector('.fc-toggleWeekendsButton-button').style
                        .backgroundColor = '#151e27';
                }
            }

            /**
             * Convert a float to a time format
             * [SPECGT26]
             * @param {Number} double
             * @return {String}
             */
            function floatToTime(double) {
                var hours = Math.floor(double);
                var minutes = Math.round((double - hours) * 60);
                return (hours < 10 ? '0' : '') + hours + ":" + (minutes < 10 ? '0' : '') + minutes;
            }

            /**
             * Generate a title element
             * [SPECGT26]
             * @param {String} title
             * @param {Number} hours
             * @param {Object} styles
             * @return {Object}
             */
            function generateTitle(title, hours, extraClass = []) {
                const p = document.createElement('p');
                p.classList.add('p-2', 'border', 'rounded', 'shadow', ...extraClass);
                p.textContent = title + hours + 'h';
                return p
            }
        });
    </script>
    @vite('resources/js/heures/scheduleModalHandler.js')
@endpush
