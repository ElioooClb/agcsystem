{{-- Début [SPECGT28] - Refonte de la page déclaration d'heures --}}
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
                        <div class="flex">
                            <span class="absolute top-0 right-0 p-4 cursor-pointer close" id="closeModal">&times;</span>
                            <h2 class="m-4 text-3xl font-bold text-center b-6" id="title"></h2>
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
                                <input id="dHours" type="time" name="dHours" value="00:00"
                                    class="w-full px-4 py-2 mb-3 text-2xl leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-lg appearance-none h-14 focus:outline-none focus:shadow-outline-gray">
                            </div>

                            {{-- Début [SPECGT25] - Ajout de l'input pour les heures d'astreinte --}}
                            <!-- Heures d'Astreinte -->
                            <section class="flex flex-col mb-6 space-y-4">
                                <!-- Astreinte -->
                                <div id="oncallDutyHoursDiv" class="flex items-center">
                                    <input id="odHours" type="checkbox" name="hours" class="mr-2">
                                    <label for="odHours" class="font-bold text-gray-700">Astreinte</label>
                                </div>

                                <!-- Grand trajet -->
                                <div id="businessTripDiv" class="flex items-center">
                                    <input id="obtHours" type="checkbox" name="hours" class="mr-2">
                                    <label for="obtHours" class="font-bold text-gray-700">Grand trajet</label>
                                </div>

                                <!-- Intervention non facturée (conditionnelle) -->
                                {{-- @if ($user->fonction == 'bureau étude') --}}
                                @if ($user->fonction == 'Président')
                                    <div id="unbilledInterventionDiv" class="flex items-center">
                                        <input id="unbilledHours" type="checkbox" name="hours" class="mr-2">
                                        <label for="unbilledHours" class="font-bold text-gray-700">Intervention non
                                            facturée</label>
                                    </div>
                                @endif
                            </section>

                            {{-- Début [SPECGT25] - Ajout de l'input pour les heures d'astreinte --}}
                            {{-- Début [SPECGT24] - AJout de l'input pHours --}}
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
                            {{-- Fin [SPECGT24] - AJout de l'input pHours --}}

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
                            <div class="flex flex-wrap items-center justify-center">
                                <p class="font-bold text-gray-700" id="idAff"></p>
                                <div
                                    class="flex flex-col justify-center w-full gap-2 mt-2 sm:flex-row sm:mt-0 sm:justify-start sm:w-auto">
                                    <button type="button" id="delete"
                                        class="w-full px-4 py-2 font-bold text-white bg-red-500 rounded sm:w-auto hover:bg-red-700">
                                        Supprimer
                                    </button>
                                    <button type="submit" id="validate"
                                        class="w-full px-4 py-2 font-bold text-white bg-blue-500 rounded sm:w-auto hover:bg-blue-700">
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
                        const classNames = hasUserEnteredHour ? ['idChantierEvent' + idChantier] : [
                            'idChantierEvent' + idChantier, 'bg-gray-500'
                        ];

                        return {
                            ...event,
                            classNames: classNames,
                            hours_day: hasUserEnteredHour ? timeForEvent.hours_day : 0,
                            hours_night: hasUserEnteredHour ? timeForEvent.hours_night : 0,
                            hours_travel: hasUserEnteredHour ? timeForEvent.hours_travel : 0,
                            isWorksiteEvent: true,
                            originalTitle: event.title,
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

                    // Début [SPECGT25] - Modification de l'affichage des heures pour intégrer les astreintes
                    dayCellDidMount: function(arg) {
                        const cellDate = arg.date;
                        const times = @json($times);

                        const createHoursInfo = (timeForCellDate, title, backgroundColor) => {
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
                                        editable: false,
                                        isWorksiteEvent: false,
                                        classNames: ['center-text'],
                                        originalTitle: title,
                                    };
                                    calendar.addEvent(event);
                                }
                            }
                        }
                        
                        const timeForCellDate = times.find(time => new Date(time.date)
                            .toDateString() === cellDate.toDateString() && time.user_id ===
                            userId && time.chantier_id === null && !time.state && !time
                            .oncall_duty && !time.on_business_trip);

                        if (timeForCellDate) {
                            createHoursInfo(timeForCellDate, convertToTimeFormat(timeForCellDate
                                .hours_day) + ' heures', '#FF99FF');
                        }

                        // Find the total productive and non-productive hours for the current date
                        const stateTimeForCellDate = times.find(time => new Date(time.date)
                            .toDateString() === cellDate.toDateString() && time.user_id ===
                            userId && time.chantier_id === null && time.state && !time
                            .on_business_trip);

                        // Create a separate hour info element for the non-productive hours
                        switch (stateTimeForCellDate?.state) {
                            case 1:
                                createHoursInfo(stateTimeForCellDate, 'Congé Payé', '#22C55E');
                                break;
                            case 2:
                                createHoursInfo(stateTimeForCellDate, 'Récup.', '#EAB308');
                                break;
                            case 3:
                                createHoursInfo(stateTimeForCellDate, 'Arrêt', '#3B82F6');
                                break;
                            case 4:
                                createHoursInfo(stateTimeForCellDate, 'Absence', '#A855F7');
                                break;
                            case 5:
                                createHoursInfo(stateTimeForCellDate, 'Férié', '#a0aec0');
                                break;
                        }

                        // Create a separate hour info element for the oncall duty hours
                        const oncallDutyTimeForCellDate = times.find(time => new Date(time.date)
                            .toDateString() === cellDate.toDateString() && time.user_id ===
                            userId && time.chantier_id === null && time.oncall_duty && !time
                            .on_business_trip);
                        if (oncallDutyTimeForCellDate) {
                            // Check if an oncall duty event already exists
                            const existingOncallDutyEvent = calendar.getEvents().find(event =>
                                event.start.toDateString() === cellDate.toDateString() &&
                                event.title === 'Astreinte');
                            // If an oncall duty event does not exist, create a new one
                            if (!existingOncallDutyEvent) {
                                createHoursInfo(oncallDutyTimeForCellDate, 'Astreinte',
                                    '#ed8936');
                            }
                        }

                        // Create a separate hour info element for the business trip hours
                        const onBusinessTripTimeForCellDate = times.find(time => new Date(time
                                .date).toDateString() === cellDate.toDateString() && time
                            .user_id === userId && time.chantier_id === null && !time
                            .oncall_duty && time.on_business_trip);
                        if (onBusinessTripTimeForCellDate) {
                            // Check if an oncall duty event already exists
                            const existingOnBusinessTripEvent = calendar.getEvents().find(
                                event =>
                                event.start.toDateString() === cellDate.toDateString() &&
                                event.title === 'Grand Trajet');
                            // If an oncall duty event does not exist, create a new one
                            if (!existingOnBusinessTripEvent) {
                                createHoursInfo(onBusinessTripTimeForCellDate, 'Grand Trajet',
                                    '#46755b');
                            }
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
                        handleModal(info, info.dateStr, times);
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

                            const oncallDutyHoursDiv = document.querySelector(
                                '#oncallDutyHoursDiv');
                            oncallDutyHoursDiv.classList.add('hidden');

                            const onBusinessTripCheckbox = document.getElementById(
                                'businessTripDiv');
                            onBusinessTripCheckbox.classList.add('hidden');

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
                        } else if (info.event.backgroundColor === '#FF99FF') {
                            handleModal(info, info.event.start.toISOString().split('T')[0],
                                times);
                        } else if (info.event.backgroundColor === '#ed8936') {
                            handleModal(info, info.event.start.toISOString().split('T')[0],
                                times, true);
                        } else if (info.event.backgroundColor === '#46755b') {
                            handleModal(info, info.event.start.toISOString().split('T')[0],
                                times, false, true);
                        } else {
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

        // Début [SPECGT25] - Modification de l'ajout des heures d'astreinte
        document.addEventListener('DOMContentLoaded', function() {
            const timeForm = document.querySelector('#timeForm');
            const oncallDutyHoursCheckbox = document.querySelector('#odHours');
            const onBusinessTripCheckbox = document.querySelector('#obtHours');
            const unbilledInterventionCheckbox = document.querySelector('#unbilledHours');
            const hoursInput = document.querySelector('#dHours');
            const submitButton = document.querySelector('#validate');

            oncallDutyHoursCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    hoursInput.value = '07:00';
                }
            });

            timeForm.addEventListener('submit', function(event) {
                // Create an array to store all promises
                let promises = [];
                const isDuty = oncallDutyHoursCheckbox.checked;
                const isTrip = onBusinessTripCheckbox.checked;
                const isUnbilledIntervention = unbilledInterventionCheckbox.checked;
                if (isDuty || isTrip || isUnbilledIntervention) {
                    if (isDuty) {
                        submitButton.disabled = true;
                        event.preventDefault();
                        const dateInput = document.querySelector('#date');
                        const selectedDate = new Date(dateInput.value);
                        const startOfWeek = getWeekFromDay(selectedDate).getDate();
                        const endOfWeek = startOfWeek + 6;

                        for (let i = startOfWeek + 1; i <= endOfWeek + 1; i++) {
                            const date = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), i);
                            const dateString = date.toISOString().split('T')[0];

                            // Add each promise to the array
                            promises.push(
                                fetch('/ajouter-heure-astreinte', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        userId: document.querySelector('#userId').value,
                                        date: dateString
                                    })
                                }).then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        flashAlert('success',
                                            'Les astreintes ont bien été ajoutées.');
                                    } else {
                                        // Handle error
                                        console.error('Error:', data.error);
                                    }
                                })
                                .catch((error) => {
                                    console.error('Error:', error);
                                })
                            );
                        }
                    }
                    if (isTrip) {
                        submitButton.disabled = true;
                        event.preventDefault();
                        const dateInput = document.querySelector('#date');
                        const selectedDate = new Date(dateInput.value);
                        const dateString = selectedDate.toISOString().split('T')[0];
                        const hours = document.querySelector('#dHours').value;
                        promises.push(fetch('/ajouter-heure-business-trip', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    userId: document.querySelector('#userId').value,
                                    date: dateString
                                })
                            })
                            .then(data => {
                                if (data.ok) {
                                    flashAlert('success', 'Le grand trajet a été ajouté');
                                } else {
                                    // Handle error
                                    console.error('Error:', data.error);
                                }
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                            })
                        );
                    }
                    if (isUnbilledIntervention) {
                        submitButton.disabled = true;
                        event.preventDefault();

                        if (hoursInput.value === '00:00') {
                            submitButton.disabled = false;
                            flashAlert('error', 'Veuillez saisir un nombre d\'heures valide');
                            return;
                        }

                        const dateInput = document.querySelector('#date');
                        const selectedDate = new Date(dateInput.value);
                        const dateString = selectedDate.toISOString().split('T')[0];
                        const hours = document.querySelector('#dHours').value;
                        promises.push(fetch('/ajouter-heure-non-facturee', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    date: dateString,
                                    hours: hours
                                })
                            })
                            .then(data => {
                                if (data.ok) {
                                    console.log(data);
                                    flashAlert('success', 'L\'intervention non facturée a été ajoutée');
                                } else {
                                    // Handle error
                                    console.error('Error:', data.error);
                                }
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                            })
                        );
                    }
                    // Wait for all promises to be resolved before reloading the page
                    Promise.all(promises).then(() => {
                        location.reload();
                    });
                } else {
                    submitButton.disabled = true; // Désactivez le bouton de soumission
                    timeForm.submit();
                }
            });
        });
        // Fin [SPECGT25] - Modification de l'ajout des heures d'astreinte

        document.addEventListener('DOMContentLoaded', function() {
            const deleteButton = document.querySelector("#delete");

            deleteButton.addEventListener("click", function(event) {
                event.preventDefault();

                // Add a confirmation alert before deleting the time entry
                window.confirmationAlert('Êtes-vous sûr de vouloir supprimer cette heure ?')
                    .then((result) => {
                        if (result) {
                            const userId = document.querySelector("#userId").value;
                            const dateInput = document.querySelector("#date");
                            const selectedDate = new Date(dateInput.value);
                            const isOncallDutyEvent = document.querySelector("#odHours").checked;
                            const onBusinessTrip = document.querySelector('#obtHours').checked;
                            const worksiteId = document.querySelector("#worksiteId").value;

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
        });

        /**
         * Function to show the modal
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
            var span = document.getElementById("closeModal");
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

        /**
         * Function to handle the modal for the dateClick and non-productive events
         * @param info
         * @param dateStr
         * @param times
         * @param isOncallDutyEvent
         */
        function handleModal(info, dateStr, times, isOncallDutyEvent = false, onBusinessTrip = false) {
            var modalInfo = document.getElementById('actionsModal');
            showModal(modalInfo, "Déclaration d'heures hors production affectée", dateStr);

            document.querySelector('label[for="dHours"]').textContent = 'Heures :';

            const deleteButton = document.querySelector('#delete');
            deleteButton.classList.add('hidden');

            const dHours = document.querySelector('#dHours');
            const prodHoursDiv = document.querySelector('#prodHoursDiv');
            prodHoursDiv.classList.add('hidden');
            dHours.value = '00:00';
            dHours.readOnly = false;

            const oncallDutyHoursDiv = document.querySelector('#oncallDutyHoursDiv');
            oncallDutyHoursDiv.classList.remove('hidden');
            const onBusinessTripDiv = document.querySelector('#businessTripDiv');
            onBusinessTripDiv.classList.remove('hidden');
            const oncallDutyHours = document.querySelector('#odHours');
            oncallDutyHours.checked = false;
            const onBusinessTripCheckbox = document.querySelector('#obtHours');
            onBusinessTripCheckbox.checked = false;
            const unbilledInterventionCheckbox = document.querySelector('#unbilledHours');
            unbilledInterventionCheckbox.checked = false;

            const noteDiv = document.querySelector('#noteDiv');
            noteDiv.classList.remove('hidden');
            const note = document.querySelector('#note');
            note.value = '';

            // Add the image next to the label of the input 'note'
            const infoImage = document.querySelector('#infoImage');

            // Add Tippy tooltip to the info image
            tippy(infoImage, {
                content: "Utiliser cette boite pour signaler une note de frais, des vêtements de travail, etc...",
            });

            times.forEach(time => {
                // Check if the time is non-productive before displaying it
                const eventDate = info.event ? info.event.start.toISOString().split('T')[0] : dateStr;
                if (eventDate === time.date && time.chantier_id === null) {
                    if (isOncallDutyEvent && time.oncall_duty) {
                        // If the event is an oncall duty event, set the checkbox and input to their default values
                        oncallDutyHours.checked = true;
                        dHours.value = '07:00';
                        dHours.readOnly = true;
                        deleteButton.classList.remove('hidden'); // Show the delete button for oncall duty hours
                    } else if (onBusinessTrip && time.on_business_trip) {
                        onBusinessTripCheckbox.checked = true;
                        deleteButton.classList.remove('hidden');
                    } else if (!isOncallDutyEvent && !time.oncall_duty && !time
                        .state) { // Check if the time has a state
                        dHours.value = convertToTimeFormat(time.hours_day);
                        dHours.readOnly = false;
                        deleteButton.classList.remove('hidden');
                    }
                    note.value = time.note;
                }
            });

            // Vérifiez si des heures d'astreinte ont été enregistrées pour le jour sélectionné
            const oncallDutyTimeForDate = times.find(time => new Date(time.date).toDateString() === dateStr && time
                .user_id === userId && time.oncall_duty);
            // Si des heures d'astreinte ont été enregistrées, cachez le bouton de suppression
            if (oncallDutyTimeForDate) {
                deleteButton.classList.add('hidden');
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
{{-- Fin [SPECGT28] - Refonte de la page déclaration d'heures --}}
