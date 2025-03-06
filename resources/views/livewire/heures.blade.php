<div class="m-6">
    <div wire:ignore>
        <div class="mb-6" id="calendar"></div>

        <div id="events">
            {{-- Modal info chantier --}}
            <div id="actionsModal" class="fixed inset-0 z-10 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Fond gris semi-transparent -->
                    <div id="actionsModalBg" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-50"></div>
                    <!-- Boîte modale -->
                    <div
                        class="inline-block w-full max-w-lg p-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                        <!-- Titre -->
                        <div class="flex flex-col mb-3">
                            <span class="absolute top-0 right-0 p-4 cursor-pointer close" id="closeModal">&times;</span>
                            <h2 class="m-4 text-3xl font-bold text-center b-6" id="title"></h2>
                            <p class="block my-auto font-bold text-gray-700 border shadow border-dark" id="idAff">
                            </p>
                        </div>
                        <form method="post" action="{{ route('time.store') }}" enctype="multipart/form-data"
                            id="timeForm" class="w-full">
                            @csrf
                            <!-- Date -->
                            <div class="mb-4">
                                <label for="date" class="block mb-3 font-bold text-gray-700">
                                    Date :
                                </label>
                                <input id="date" type="date" name="date"
                                    class="w-full px-4 py-2 mb-3 text-2xl leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-lg appearance-none h-14 focus:outline-none focus:shadow-outline-gray">
                            </div>

                            <!-- Heures de Jour -->
                            <div class="mb-6">
                                <label for="dHours" class="block mb-3 font-bold text-gray-700">
                                    Heures de jour :
                                </label>
                                <input id="dHours" type="time" name="dHours" value="00:00" min="00:00"
                                    class="w-full px-4 py-2 mb-3 text-2xl leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-lg appearance-none h-14 focus:outline-none focus:shadow-outline-gray">
                            </div>

                            <!-- Heures hors production -->
                            <section id='hoursSelectSection' class="flex flex-col mb-6">
                                <label for="hoursSelect" class="block mb-3 font-bold text-gray-700">Choisissez une
                                    option :</label>
                                <select id="hoursSelect" name="type"
                                    class="w-full px-4 py-2 text-2xl leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-lg appearance-none h-14 focus:outline-none focus:shadow-outline-gray">
                                    <option value='0'>Heure non productive</option>
                                    <option value='1'>Astreinte</option>
                                    <option value='2'>Grand trajet</option>
                                    @if ($user->fonction == 'Bureau étude' || $user->fonction == 'Président')
                                        <option value='3'>Intervention non facturée</option>
                                    @endif
                                </select>
                            </section>

                            <div id="prodHoursDiv">
                                <!-- Heures de Nuit -->
                                <div class="mb-6">
                                    <label for="nHours" class="block mb-3 font-bold text-gray-700">
                                        Heures de nuit (de 20 à 6h) :
                                    </label>
                                    <input id="nHours" type="time" name="nHours" value="00:00"
                                        class="w-full px-4 py-2 mb-3 text-2xl leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-lg appearance-none h-14 focus:outline-none focus:shadow-outline-gray">
                                </div>

                                <!-- Heures Passager -->
                                <div class="mb-6">
                                    <label for="pHours" class="block mb-3 font-bold text-gray-700">
                                        Heures de trajet passager :
                                    </label>
                                    <input id="pHours" type="time" name="pHours" value="00:00"
                                        class="w-full px-4 py-2 mb-3 text-2xl leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-lg appearance-none h-14 focus:outline-none focus:shadow-outline-gray">
                                </div>
                            </div>

                            <!-- Note -->
                            <div class="mb-6" id="noteDiv">
                                <div class="flex items-center mb-3">
                                    <label for="note" class="font-bold text-gray-700">
                                        Note pour le service social :
                                    </label>
                                    <img src="/front/images/tooltip.svg" width="20" height="20" class="ml-2"
                                        id="infoImage">
                                </div>
                                <textarea id="note" name="note"
                                    class="w-full px-4 py-2 mb-3 text-2xl leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-lg appearance-none focus:outline-none focus:shadow-outline-gray"
                                    rows="5">
                                </textarea>
                            </div>

                            <input id="userId" type="hidden" name="userId" value="">
                            <input id="worksiteId" type="hidden" name="worksiteId" value="">

                            <!-- Boutons -->
                            <div class="flex items-center justify-center mb-3">
                                <div class="flex flex-row gap-2">
                                    <button type="button" id="delete"
                                        class="w-auto px-4 py-2 font-bold text-center text-white bg-red-500 rounded hover:bg-red-700">
                                        Supprimer
                                    </button>
                                    <button type="submit" id="validate"
                                        class="w-auto px-4 py-2 font-bold text-center text-white bg-blue-500 rounded hover:bg-blue-700">
                                        Valider
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:load', function() {
            let weekendsVisible = false;
            const userId = {{ $userId }};
            const chantierIds = @json($chantierIds);
            const times = @json($times);
            const productiveHours = @json($productiveHours);
            const nonProductiveHours = @json($nonProductiveHours);
            const parsedEvents = @json($events);
            Promise.all(parsedEvents.filter(event => chantierIds.includes(event.id_chantier)).map(
                event => {
                    const idChantier = event.id_chantier;
                    return findChantierByIdLocal(idChantier).then(chantier => {
                        // Vérification si l'utilisateur a entré des heures pour ce chantier
                        const timeForEvent = times.find(time => time.chantier_id === idChantier &&
                            time.date === event.start);
                        const hasUserEnteredHour = Boolean(timeForEvent);

                        // Ajout de la classe si nécessaire
                        const classNames = hasUserEnteredHour ? ['idChantierEvent' + idChantier,
                            'text-center'
                        ] : [
                            'idChantierEvent' + idChantier, 'bg-gray-500', 'text-center'
                        ];

                        return {
                            ...event,
                            classNames: classNames,
                            hours_day: hasUserEnteredHour ? timeForEvent.hours_day : 0,
                            hours_night: hasUserEnteredHour ? timeForEvent.hours_night : 0,
                            hours_travel: hasUserEnteredHour ? timeForEvent.hours_travel : 0,
                            isWorksiteEvent: true,
                            originalTitle: event.title,
                            editable: true,
                        };
                    });
                })).then(eventsData => {
                const calendarEl = document.getElementById('calendar');
                const calendar = new Calendar(calendarEl, {
                    plugins: [dayGridPlugin, listPlugin, timeGridPlugin, multiMonthPlugin,
                        interactionPlugin
                    ],
                    headerToolbar: {
                        left: 'prev,next toggleWeekendsButton today',
                        center: 'title',
                        right: 'dayGridMonth,listWeek',
                    },
                    customButtons: {
                        toggleWeekendsButton: {
                            text: 'Weekends',
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
                    hiddenDays: [6, 0],
                    firstDay: 1,
                    events: eventsData,
                    editable: false,
                    selectable: false,
                    locale: 'fr',
                    timeZone: 'Europe/paris',
                    dayCellDidMount: function(arg) {
                        const cellDate = arg.date;
                        const times = @json($times);

                        const createHoursInfo = (timeForCellDate, title, backgroundColor, type,
                            editable = false) => {
                            if (timeForCellDate) {
                                const existingEvent = calendar.getEvents().find(event =>
                                    event.start.toDateString() === cellDate
                                    .toDateString() && event.title === title);

                                if (!existingEvent) {
                                    const event = {
                                        title: title,
                                        start: cellDate,
                                        allDay: true,
                                        backgroundColor: backgroundColor,
                                        textColor: '#FFF',
                                        editable: editable,
                                        isWorksiteEvent: false,
                                        classNames: ['text-center'],
                                        originalTitle: title,
                                        hours: convertToTimeFormat(timeForCellDate
                                            .hours_day),
                                        type: type,
                                        timeID: timeForCellDate.id,
                                        note: timeForCellDate.note,
                                    };
                                    calendar.addEvent(event);
                                }
                            }
                        }

                        if (times) {
                            times.forEach(time => {
                                if (time.user_id === userId && time.date === cellDate
                                    .toISOString().split('T')[0] &&
                                    time.chantier_id === null) {
                                    if (time.oncall_duty) {
                                        createHoursInfo(time, 'Astreinte', '#ed8936', 1,
                                            true);
                                    } else if (time.on_business_trip) {
                                        createHoursInfo(time, 'Grand Trajet', '#46755b',
                                            2,
                                            true);
                                    } else if (time.unbillable) {
                                        createHoursInfo(time, convertToTimeFormat(time
                                                .hours_day) +
                                            ' Intervention non facturée', '#9c23a1',
                                            3,
                                            true);
                                    } else if (time.state) {
                                        switch (time.state) {
                                            case 1:
                                                createHoursInfo(time, 'Congé Payé',
                                                    '#22C55E', 10);
                                                break;
                                            case 2:
                                                createHoursInfo(time, 'Récup.',
                                                    '#EAB308', 10);
                                                break;
                                            case 3:
                                                createHoursInfo(time, 'Arrêt',
                                                    '#3B82F6', 10);
                                                break;
                                            case 4:
                                                createHoursInfo(time, 'Absence',
                                                    '#A855F7', 10);
                                                break;
                                            case 5:
                                                createHoursInfo(time, 'Férié',
                                                    '#a0aec0', 10);
                                                break;
                                        }
                                    } else {
                                        createHoursInfo(time, convertToTimeFormat(time
                                                .hours_day) + ' heures',
                                            '#FF99FF', 0, true);
                                    }
                                }
                            });
                        }

                        // Calculate the total productive and non-productive hours for the current date
                        const productiveHoursForDate = productiveHours[cellDate.toISOString()
                            .split('T')[0]] || 0;
                        const nonProductiveHoursForDate = nonProductiveHours[cellDate
                            .toISOString().split('T')[0]] || 0;
                        const totalHoursForDate = productiveHoursForDate +
                            nonProductiveHoursForDate;

                        // Create a new element to display the total hours for the current date
                        const totalHoursElement = document.createElement('div');
                        totalHoursElement.textContent = 'Total : ' + convertToTimeFormat(
                            totalHoursForDate) + 'h';
                        totalHoursElement.style.color = '#555';

                        // Add the total hours element to the cell header
                        const cellHeaderElement = arg.el.querySelector('.fc-daygrid-day-top');
                        cellHeaderElement.appendChild(totalHoursElement);
                        cellHeaderElement.classList.add("bg-slate-200");
                        cellHeaderElement.classList.add('fc-tooltips');
                    },
                    // Fin [SPECGT25] - Modification de l'affichage des heures pour intégrer les astreintes
                    viewDidMount: function(arg) {
                        if (arg.view.type === 'listWeek') {
                            calendar.getEvents().forEach(event => {
                                if (!event.extendedProps.isAvaibility) {
                                    event.setProp('title', event.extendedProps
                                        .originalTitle);
                                }
                            });
                        }
                    },
                    viewWillUnmount: function(arg) {
                        if (arg.view.type === 'listWeek') {
                            calendar.getEvents().forEach(event => {
                                if (!event.extendedProps.isAvaibility) {
                                    event.setProp('title', event.extendedProps
                                        .originalTitle);
                                }
                            });
                        }
                    },
                    dateClick: function(info) {
                        const date = info.date;
                        const events = calendar.getEvents().filter(event => {
                            return event.start.toDateString() === date.toDateString();
                        });
                        const currentEvents = events.filter(event => !event.extendedProps
                            .isWorksiteEvent);
                        handleModal({
                            info,
                            currentEvents
                        });
                    },
                    eventClick: function(info) {
                        if (info.event.extendedProps.isWorksiteEvent) {
                            var id = info.event.id;
                            var worksite_id = info.event['extendedProps']['id_chantier'];
                            var worksite_title = info.event.title;

                            var modalInfo = document.getElementById('actionsModal');
                            showModal(modalInfo, worksite_title, info.event.start.toISOString()
                                .split('T')[0], 'IdAff_' + worksite_id);

                            document.querySelector('label[for="dHours"]').textContent =
                                'Heures de jour :';

                            const deleteButton = document.querySelector('#delete');
                            deleteButton.classList.add('hidden');

                            const dHours = document.querySelector('#dHours');
                            const nHours = document.querySelector('#nHours');
                            const pHours = document.querySelector('#pHours');
                            const prodHoursDiv = document.querySelector('#prodHoursDiv');
                            prodHoursDiv.classList.remove('hidden');
                            dHours.value = '00:00';
                            nHours.value = '00:00';
                            pHours.value = '00:00';

                            // FIXME
                            const selectEl = document.querySelector('#hoursSelectSection');
                            selectEl.classList.add('hidden');

                            const noteDiv = document.querySelector('#noteDiv');
                            noteDiv.classList.add('hidden');

                            times.forEach(time => {
                                if (info.event.start.toISOString().split('T')[0] ===
                                    time
                                    .date && worksite_id === time.chantier_id) {
                                    dHours.value = convertToTimeFormat(time.hours_day);
                                    nHours.value = convertToTimeFormat(time
                                        .hours_night);
                                    pHours.value = convertToTimeFormat(time
                                        .hours_travel);
                                    deleteButton.classList.remove('hidden');
                                }
                            });
                        } else if (info.event) {
                            handleModal({
                                info
                            });
                        } else {
                            // Others
                            info.jsEvent.preventDefault();
                        }
                    },
                    eventDidMount: function(info) {
                        if (info.event.extendedProps.isWorksiteEvent) {
                            let dayHours = info.event.extendedProps.hours_day;
                            let nightHours = info.event.extendedProps.hours_night;
                            let passengerHours = info.event.extendedProps.hours_travel;

                            function validateHours(hours) {
                                return hours === undefined || isNaN(hours) ? 0 : hours;
                            }

                            dayHours = validateHours(dayHours);
                            nightHours = validateHours(nightHours);
                            passengerHours = validateHours(passengerHours);

                            tippy(info.el, {
                                content: "Heures de jour : " + convertToTimeFormat(
                                        dayHours) +
                                    "<br>Heures de nuit : " + convertToTimeFormat(
                                        nightHours) +
                                    "<br>Heures passager : " + convertToTimeFormat(
                                        passengerHours),
                                allowHTML: true
                            });
                        }
                    },
                });
                calendar.render();

                function toggleWeekends() {
                    weekendsVisible = !weekendsVisible;
                    calendar.setOption('hiddenDays', weekendsVisible ? [] : [6, 0]);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const deleteButton = document.querySelector("#delete");
            const selectEl = document.querySelector('#hoursSelect');
            const hoursEl = document.querySelector('#dHours');
            const form = document.querySelector('#timeForm');
            const submitButton = document.querySelector('#validate');

            selectEl.addEventListener('change', function() {
                const selectedValue = selectEl.value;
                const numericValue = parseInt(selectedValue, 10);
                switch (numericValue) {
                    case 1:
                        hoursEl.value = '07:00';
                        hoursEl.readOnly = true;
                        hoursEl.classList.add('bg-gray-300');
                        break;
                    case 2:
                        hoursEl.value = '00:00';
                        hoursEl.readOnly = true;
                        hoursEl.classList.add('bg-gray-300');
                        break;
                    case 3:
                        hoursEl.value = '00:00';
                        hoursEl.readOnly = false;
                        hoursEl.classList.remove('bg-gray-300');
                        break;
                    default:
                        hoursEl.value = '00:00';
                        hoursEl.readOnly = false;
                        hoursEl.classList.remove('bg-gray-300');
                        break;
                }
            })

            deleteButton.addEventListener("click", function(event) {
                event.preventDefault();

                // Add a confirmation alert before deleting the time entry
                window.confirmationAlert('Êtes-vous sûr de vouloir supprimer cette heure ?')
                    .then((result) => {
                        if (result) {
                            const userId = document.querySelector("#userId").value;
                            const dateInput = document.querySelector("#date");
                            const selectedDate = new Date(dateInput.value);
                            const worksiteId = document.querySelector("#worksiteId").value;
                            const selectedValue = selectEl.value;
                            const isOncallDutyEvent = selectedValue === '1';
                            const onBusinessTrip = selectedValue === '2';

                            if (isOncallDutyEvent || onBusinessTrip) {
                                // Create an array to store all promises
                                let promises = [];
                                if (isOncallDutyEvent) {
                                    const startOfWeek = getWeekFromDay(selectedDate).getDate();
                                    const endOfWeek = startOfWeek + 6;

                                    for (let i = startOfWeek + 1; i <= endOfWeek + 1; i++) {
                                        const date = new Date(selectedDate.getFullYear(), selectedDate
                                            .getMonth(), i);
                                        const dateString = date.toISOString().split('T')[0];
                                        let url = '/supprimer-heure-astreinte/' + userId + '/' +
                                            dateString;

                                        // Add each promise to the array
                                        promises.push(
                                            fetch(url, {
                                                method: 'DELETE',
                                                headers: {
                                                    "X-CSRF-TOKEN": document
                                                        .querySelector(
                                                            'meta[name="csrf-token"]')
                                                        .getAttribute("content"),
                                                },
                                            })
                                        );
                                    }
                                }

                                if (onBusinessTrip) {
                                    const dateString = selectedDate.toISOString().split('T')[0];
                                    let url = '/supprimer-heure-business-trip/' + userId + '/' +
                                        dateString;
                                    promises.push(
                                        fetch(url, {
                                            method: 'DELETE',
                                            headers: {
                                                "X-CSRF-TOKEN": document
                                                    .querySelector('meta[name="csrf-token"]')
                                                    .getAttribute("content"),
                                            }
                                        })
                                    );
                                }

                                // Wait for all promises to be resolved before reloading the page
                                Promise.all(promises).then(() => location.reload());
                            } else {
                                const dateString = selectedDate.toISOString().split('T')[0];
                                let url = '/supprimer-heure-chantier/' + userId + '/' + dateString +
                                    '/' + worksiteId;
                                if (worksiteId === "" || worksiteId == null) {
                                    url = '/supprimer-heure/' + userId + '/' + dateString;
                                }

                                // Delete the time entry
                                fetch(url, {
                                    method: 'DELETE',
                                    headers: {
                                        "X-CSRF-TOKEN": document
                                            .querySelector('meta[name="csrf-token"]')
                                            .getAttribute("content"),
                                    },
                                }).then(() => location.reload());
                            }
                        }
                    });
            });

            form.addEventListener('submit', (event) => {
                // Prevent the form from submitting
                event.preventDefault();

                const dhours = document.getElementById('dHours');
                const label = document.querySelector('label[for="dHours"]');
                const select = document.getElementById('hoursSelect');
                const note = document.getElementById('note');
                const checkedNote = note.value.trim();

                if ((select.value === '0' || select.value === '3') && dhours.value === '00:00' &&
                    checkedNote === '') {
                    const btn = document.getElementById('validate');
                    window.flashAlert('error', 'Veuillez renseigner les heures ou une note pour valider.');
                    return;
                }

                // Get the selected value
                selectEl.disabled = false;

                // Submit the form
                form.submit();
            });

        });

        /**
         * Function to handle the modal for the dateClick and non-productive events
         * 0: Heure non productive, 1: Astreinte, 2: Grand trajet, 3: Intervention non facturée
         * @param info
         * @param dateStr
         * @param times
         * @param isOncallDutyEvent
         */
        async function handleModal({
            info,
            currentEvents
        }) {
            const dateStr = info.dateStr ?? info.event.start.toISOString().split('T')[0];
            const events = {
                'unproductive': null,
                'unbillable': null,
                'create': {
                    'hours': '00:00',
                    'type': 0,
                    'note': '',
                }
            }

            if (currentEvents && currentEvents.length > 0) {
                currentEvents.forEach(event => {
                    if (event.extendedProps.type === 0) events.unproductive = event;
                    if (event.extendedProps.type === 3) events.unbillable = event;
                });
            }

            const hasUnproductive = events.unproductive;
            const hasUnbillable = events.unbillable;
            const hasEvent = hasUnproductive || hasUnbillable;

            const doms = {
                modal: document.querySelector('#actionsModal'),
                deleteBtn: document.querySelector('#delete'),
                prodHoursDiv: document.querySelector('#prodHoursDiv'),
                dHours: document.querySelector('#dHours'),
                selectSection: document.querySelector('#hoursSelectSection'),
                selectEl: document.querySelector('#hoursSelect'),
                noteDiv: document.querySelector('#noteDiv'),
                note: document.querySelector('#note'),
                dbHoursLabel: document.querySelector('label[for="dHours"]'),
                infoImage: document.querySelector('#infoImage'),
            }

            // Define the modal static elements
            doms.prodHoursDiv.classList.add('hidden');
            doms.noteDiv.classList.remove('hidden');
            doms.selectSection.classList.remove('hidden');
            doms.selectEl.classList.remove('hidden');
            hasEvent ? doms.deleteBtn.classList.remove('hidden') : doms.deleteBtn.classList.add('hidden');
            tippy(doms.infoImage, {
                content: "Utiliser cette boite pour signaler une note de frais, des vêtements de travail, etc...",
            });

            if (hasEvent) {
                let choice = null;
                let inputOptions = {
                    Heures: {}
                };

                inputOptions.Heures.create = 'Créer une nouvelle déclaration';
                if (hasUnproductive) inputOptions.Heures.unproductive = 'Heures hors production';
                if (hasUnbillable) inputOptions.Heures.unbillable = 'Heures non facturables';

                if (hasUnproductive || hasUnbillable) {
                    const {
                        value
                    } = await window.Swal.fire({
                        title: "Choisissez une option",
                        text: "Vous avez des heures hors production et des heures non facturables pour cette date. Veuillez choisir laquelle vous souhaitez modifier.",
                        icon: "question",
                        input: "select",
                        inputOptions: inputOptions,
                        inputPlaceholder: 'Choisissez une déclaration',
                        showCancelButton: true,
                        reverseButtons: true,
                        inputValidator: (value) => {
                            return value ? null : 'Veuillez choisir une option.';
                        },
                    });

                    if (value === undefined) return;
                    choice = value;
                }

                const eventType = choice || (hasUnproductive ? 'unproductive' : 'unbillable');

                if (events[eventType]) {
                    populateModal({
                        doms,
                        event: events[eventType],
                        isCreation: choice === 'create',
                    });
                    showModal(doms.modal, `Déclaration d'heures ${eventType} affectée`, dateStr);
                    return;
                }
            }


            if (info.event) {
                populateModal({
                    doms,
                    event: info.event,
                });
                showModal(doms.modal, "Déclaration d'heures hors production affectée", dateStr);
                return;
            }

            // Value the label
            doms.dbHoursLabel.textContent = 'Heures :';

            // select options values
            doms.selectEl.value = '0';
            doms.selectEl.disabled = false;
            doms.selectEl.classList.remove('bg-gray-300');

            // Show the event hours
            dHours.value = '00:00';

            if (event?.extendedProps?.type && event?.extendedProps?.type !== 3) {
                dHours.readOnly = true;
                dHours.classList.add('bg-gray-300');
            } else {
                dHours.readOnly = false;
                dHours.classList.remove('bg-gray-300');
            }

            // Populate the note section with the note of the event
            doms.note.value = '';

            // Show the modal
            showModal(doms.modal, "Déclaration d'heures hors production affectée", dateStr);
        }

        /**
         * Function to populate the modal with the event data
         * @param event
         * @param modalInfo
         * @return void
         */
        function populateModal({
            doms,
            event,
            isCreation = false,
        }) {
            // Ajouter les heures
            doms.dHours.value = event.extendedProps?.hours || '00:00';

            // Choix du select
            doms.selectSection.classList.remove('hidden');
            doms.selectEl.classList.remove('hidden');
            doms.selectEl.value = event.extendedProps?.type ?? 0;
            const isDisabled = !!event.extendedProps?.type || !isCreation;
            doms.selectEl.disabled = isDisabled;
            if (isDisabled) doms.selectEl.classList.add('bg-gray-300');

            // Ajouter la note
            doms.note.value = event.extendedProps?.note ?? '';
        }

        /**
         * Function to show the modal for worksite events
         * @param modalInfo
         * @param titleText
         * @param dateValue
         * @param idAffText
         */
        function showModal(modalInfo, titleText, dateValue, idAffText = '') {
            // Close the modal when clicking outside of it
            window.onclick = function(event) {
                if (event.target === modalInfo) {
                    modalInfo.classList.add("hidden");
                }
            };

            // Close the modal when clicking on the close button
            const span = document.getElementById("closeModal");
            span.onclick = function() {
                modalInfo.classList.add("hidden");
            };

            // Show the modal
            modalInfo.classList.remove("hidden");

            // Get the elements to update in the modal
            const title = document.querySelector('#title');
            const date = document.querySelector('#date');
            const userId = document.querySelector('#userId');
            const worksiteId = document.querySelector('#worksiteId');
            const idAff = document.querySelector('#idAff');

            // Update the elements with the provided values
            title.innerHTML = titleText;
            date.value = dateValue;
            idAff.textContent = idAffText;
            userId.value = {{ $userId }};

            if (idAffText.includes('_')) {
                worksiteId.value = idAffText.split('_')[1];
            } else {
                worksiteId.value = null;
            }
        }

        async function findChantierByIdLocal(idChantier) {
            let chantiers = @json($chantiers);
            return chantiers.find(chantier => chantier.id === idChantier);
        }

        /**
         * Convert a number to a time format (HH:mm)
         * @returns {string}
         * @param number
         */
        function convertToTimeFormat(number) {
            // Calculate hours and minutes
            const hours = Math.floor(number);
            const minutes = Math.round((number - hours) * 60);

            // Format the hours and minutes as strings
            const hoursString = hours < 10 ? `0${hours}` : `${hours}`;
            const minutesString = minutes < 10 ? `0${minutes}` : `${minutes}`;

            // Return the formatted time
            return `${hoursString}:${minutesString}`;
        }

        function getWeekFromDay(date) {
            const day = date.getDay();
            const diff = date.getDate() - day + (day == 0 ? -6 : 1);
            return new Date(date.setDate(diff));
        }
    </script>
    @vite('resources/js/heures/scheduleModalHandler.js')
@endpush
