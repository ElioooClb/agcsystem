<div>
    <!-- Début [SPECGT18] - Ajout de la recherche par id partie user -->
    <div class="gap-3 p-3 d-flex">
        <label for="searchInput" class="form-label">Recherche par IdAff :</label>
        <input type="text" class="form-control-sm" id="searchInput" placeholder="Saisir IdAff...">
        <button type="button" id="resetButton">&#x2715;</button>
    </div>
    <!-- Fin [SPECGT18] - Ajout de la recherche par id partie user -->

    <!-- Début [SPECGT18] - Recréation de la page -->
    <div class="flex m-6">
        <div class="w-1/6">
            <div id="events" class="hidden calendarListingWorksites">
                <p class="text-2xl font-bold text-left">Résultat :</p>
                <p id="noMatchMessage" class="hidden">Aucun chantier trouvé.</p>

                @foreach ($chantiers as $chantier)
                    @if (!$archivedWorkSites->contains($chantier))
                        <menu class="calendarListingWorksites">
                            <li data-id-chantier="{{ $chantier->id }}" data-event='@json(['title' => $chantier->title])'
                                class="dropEvent bg-{{ $chantier->color }}-500">
                                {{ $chantier->title }}
                            </li>
                        </menu>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="w-5/6" wire:ignore>
            <div id="calendar"></div>

            <div id="events">
                {{-- Modal info chantier --}}
                @csrf
                @foreach ($chantiers as $chantier)
                    <div id="chantierModalInfo_{{ $chantier->id }}" class="modalCh modalInfo">
                        <div class="p-6 bg-white rounded-lg shadow-lg modalCh-content">
                            <span class="absolute top-0 right-0 p-4 cursor-pointer close">&times;</span>
                            <h3 class="mb-4 text-2xl font-bold title_info">Informations sur le chantier
                                "{{ $chantier->title }}"</h3>

                            {{-- IdAff --}}
                            <p class="idaff">IdAff_{{ $chantier->id }}</p>
                            <hr>
                            {{-- Heure Chantier --}}
                            <p class="hourInfo">Heures Prévues : {{ $chantier->hours }} h</p>
                            <hr>
                            {{-- Liste teckos assignés --}}
                            <div class="listeteckos">
                                <p class="mt-4 listeTeck">Liste des techniciens affectés : </p>
                                <ul class="ulListeTeck">
                                    @foreach ($chantier->users as $user)
                                        <li class="d-flex assignedUser">
                                            {{ $user->name }},
                                            {{ $chantier->userHours[$user->id] ?? '0' }}h
                                        </li>
                                    @endforeach
                                    <li class="my-2">
                                        Total : {{ $chantier->userHours ? array_sum($chantier->userHours) : '0' }}h
                                    </li>
                                </ul>

                            </div>

                            <hr>
                            {{-- Montant Chantiers --}}
                            <div class="form-group amounts" data-id="{{ $chantier->id }}">
                                <p class="text_decoration">Montant :</p>
                                <p class="amountMaterial">Montant Matériel : {{ $chantier->materialamount }} €</p>
                                <p class="amountService">Montant Service : {{ $chantier->serviceamount }} €</p>
                            </div>
                            <hr>
                            {{-- Options Chantiers --}}
                            <div class="form-group" id="options" data-id="{{ $chantier->id }}">
                                <p>Tâches Réalisées : </p>

                                <label class="form-control @if ($chantier->visite === 1) green @endif">
                                    <input type="checkbox" name="visite" value="visite"
                                        {{ $chantier->visite == 1 ? 'checked' : '' }} disabled> Visite
                                </label>


                                <label class="form-control @if ($chantier->bpe === 1) green @endif">
                                    <input type="checkbox" name="bpe" value="bpe"
                                        {{ $chantier->bpe == 1 ? 'checked' : '' }} disabled> BPE
                                </label>


                                <label class="form-control @if ($chantier->numero === 1) green @endif">
                                    <input type="checkbox" name="numero" value="numero"
                                        {{ $chantier->numero == 1 ? 'checked' : '' }} disabled> Numéro
                                </label>


                                <label class="form-control @if ($chantier->aiguillage === 1) green @endif">
                                    <input type="checkbox" name="aiguillage" value="aiguillage"
                                        {{ $chantier->aiguillage == 1 ? 'checked' : '' }} disabled> Aiguillage
                                </label>


                                <label class="form-control @if ($chantier->tirage === 1) green @endif">
                                    <input type="checkbox" name="tirage" value="tirage"
                                        {{ $chantier->tirage == 1 ? 'checked' : '' }} disabled> Tirage
                                </label>


                                <label class="form-control @if ($chantier->pto === 1) green @endif">
                                    <input type="checkbox" name="pto" value="pto"
                                        {{ $chantier->pto == 1 ? 'checked' : '' }} disabled> PTO
                                </label>
                                <label class="form-control @if ($chantier->rop === 1) green @endif">
                                    <input type="checkbox" name="rop" value="rop"
                                        {{ $chantier->rop == 1 ? 'checked' : '' }} disabled> ROP
                                </label>


                                <label class="form-control @if ($chantier->reflecto === 1) green @endif">
                                    <input type="checkbox" name="reflecto" value="reflecto"
                                        {{ $chantier->reflecto == 1 ? 'checked' : '' }} disabled> Reflecto
                                </label>
                            </div>
                            <hr>
                            {{-- Observation(s) Chantier --}}
                            <p data-id-="{{ $chantier->id }} " class="mt-4 text-2xl">Observations : </p>
                            @if ($chantier->observation)
                                <p class="obs">{{ $chantier->observation }}</p>
                            @else
                                <p class="obs">Aucune observation</p>
                            @endif
                            <hr>
                            {{-- Boutons Supression Evènement --}}
                            <div class="mt-4 modal-buttons">
                                <button id="deleteEvent_{{ $chantier->id }}"
                                    class="px-4 py-2 font-bold text-white bg-red-500 rounded-full hover:bg-red-700"
                                    data-id="{{ $chantier->id }}"
                                    data-url="{{ route('chantier.destroy', ['idChantier' => $chantier->id]) }}">
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Fin [SPECGT18] - Recréation de la page -->
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:load', function() {
            // Load events asynchronously
            Promise.all(JSON.parse(@this.events).filter(event => chantierIds.includes(event.id_chantier)).map(
                event => {
                    const idChantier = event.id_chantier;
                    return findChantierById(idChantier).then(chantier => {
                        return {
                            ...event,
                            // Dynamically set background color based on chantier color
                            classNames: ['bg-' + chantier.color + '-500', 'idChantierEvent' +
                                idChantier
                            ],
                        };
                    });
                })).then(eventsData => {

                // Get calendar and draggable elements
                const calendarEl = document.getElementById('calendar');
                const draggableEl = document.getElementById('events');

                // Make events draggable
                new Draggable(draggableEl, {
                    itemSelector: '.dropEvent'
                });

                let currentEvent = null;
                // Initialize the calendar
                const calendar = new Calendar(calendarEl, {
                    plugins: [dayGridPlugin, listPlugin, timeGridPlugin, multiMonthPlugin,
                        interactionPlugin
                    ],
                    // Set header options
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listWeek',
                    },
                    buttonText: {
                        today: 'Aujourd\'hui',
                        month: 'Mois',
                        week: 'Semaine',
                        day: 'Jour',
                        list: 'Liste de la semaine',
                    },
                    // Hide weekends
                    hiddenDays: [6, 0],
                    // Provide event data
                    events: eventsData,
                    // Allow event editing
                    editable: true,
                    // Allow event selection
                    selectable: false,
                    // Set locale to French
                    locale: 'fr',
                    // Set time zone to Paris
                    timeZone: 'Europe/paris',

                    // Event resize handler
                    eventResize: info => {
                        // Handle event resize
                        // Update event on the backend
                        if (info.event['extendedProps']['data-id'] != null) {
                            const id = info.event['extendedProps']['data-id'];
                            @this.eventChange(id, info.event);
                        } else {
                            const id = info.event.id;
                            @this.eventChange(id, info.event);
                        }
                    },

                    // Event drop handler
                    eventDrop: info => {
                        // Handle event drop
                        // Update event on the backend
                        if (info.event['extendedProps']['data-id'] != null) {
                            const id = info.event['extendedProps']['data-id'];
                            @this.eventChange(id, info.event);
                        } else {
                            const id = info.event.id;
                            @this.eventChange(id, info.event);
                        }
                    },

                    // Event receive handler
                    eventReceive: info => {
                        // Handle event receive (when a new event is dropped onto the calendar)
                        // Generate unique ID for the event
                        const id = create_UUID();
                        // Retrieve chantier ID
                        const id_chantier = info.draggedEl.getAttribute('data-id-chantier');
                        // Extract color class from dragged element
                        const draggedElClass = info.draggedEl.className;
                        const colorRegex = /bg-(\w+)-500/;
                        const match = draggedElClass.match(colorRegex);
                        const colorClass = match[0];

                        // Set event properties
                        info.event.setProp('classNames', ['idChantierEvent' + id_chantier,
                            colorClass
                        ]);
                        info.event.setExtendedProp('data-id', id);

                        // Add the event on the backend
                        @this.eventAdd(info.event, id, id_chantier);
                    },

                    // Event click handler
                    eventClick: info => {
                        // Handle event click
                        // Retrieve event and chantier IDs
                        var id = info.event.id;
                        var id_chantier = info.event['extendedProps']['id_chantier'];
                        // If chantier ID is not available in event props, extract from class name
                        if (id_chantier == null || id_chantier == undefined || id_chantier ==
                            '') {
                            const eventClass = info.event.classNames.find(className => className
                                .startsWith('idChantierEvent'));
                            const regex = /idChantierEvent(\d+)/;
                            const match = eventClass.match(regex);
                            id_chantier = match[1];
                            id = info.event['extendedProps']['data-id'];
                        }
                        // Show modal for event information
                        var modalInfo = document.getElementById('chantierModalInfo_' +
                            id_chantier);
                        window.onclick = function(event) {
                            // Close modal if clicked outside of it
                            if (event.target == modalInfo) {
                                modalInfo.style.display = "none";
                            }
                        };
                        var span = modalInfo.getElementsByClassName("close")[0];
                        // Close modal on click of close button
                        span.onclick = function() {
                            modalInfo.style.display = "none";
                        };
                        modalInfo.style.display = "block";

                        // Delete event on click of delete button
                        var deleteEvent = document.querySelector("#deleteEvent_" + id_chantier);
                        deleteEvent.onclick = function() {
                            if (confirm("Voulez-vous vraiment supprimer cet événement ?")) {
                                info.event.remove();
                                // Remove event from backend
                                @this.eventRemove(id);
                                modalInfo.style.display = "none";
                            }
                        };
                    },
                });
                // Render the calendar
                calendar.render();
            });
        });

        // Function to generate UUID
        create_UUID = () => {
            let dt = new Date().getTime();
            const uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
                let r = (dt + Math.random() * 16) % 16 | 0;
                dt = Math.floor(dt / 16);
                return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });
            return uuid;
        }

        // Function to find chantier by ID
        async function findChantierById(idChantier) {
            try {
                const response = await fetch('/trouver-chantier/' + idChantier, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                });
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('There was an error:', error);
            }
        };

        // Début [SPECGT18] - Script pour la recherche par id
        document.getElementById('searchInput').addEventListener('keypress', function(event) {
            // Check if the pressed key is 'Enter'
            if (event.key === 'Enter') {
                // Get the search input value and convert it to uppercase
                let filter = this.value.toUpperCase();
                // Get the list of worksites
                let chantierList = document.getElementById('events');
                chantierList.classList.remove('hidden');
                // Get all the list items in the worksites list
                let chantiers = chantierList.getElementsByTagName('li');
                let matchFound = false;

                // Get the archived work sites
                let archivedWorkSites = @json($archivedWorkSites->pluck('id')->toArray());

                // Loop through each list item
                for (let i = 0; i < chantiers.length; i++) {
                    // Get the worksite id from the data-id-chantier attribute
                    let chantierId = chantiers[i].getAttribute('data-id-chantier');
                    // Check if the worksite id exists
                    if (chantierId) {
                        // Check if the worksite id matches the search input value
                        if (chantierId === filter) {
                            // If it matches, display the list item
                            chantiers[i].style.display = '';
                            matchFound = true;
                        } else {
                            // If it doesn't match, hide the list item
                            chantiers[i].style.display = 'none';
                        }
                    }
                }

                // Check if the worksite is archived
                if (archivedWorkSites.includes(parseInt(filter))) {
                    // Add a pop-up message to inform the user that the worksite is archived
                    alert('Le chantier ' + filter + ' est archivé.');
                    // Change the no match message
                    document.getElementById('noMatchMessage').textContent = 'Le chantier ' + filter +
                        ' est archivé.';
                } else {
                    // Reset the no match message
                    document.getElementById('noMatchMessage').textContent = 'Aucun chantier trouvé.';
                }

                // If no match is found, display the error message
                if (!matchFound) {
                    document.getElementById('noMatchMessage').classList.remove('hidden');
                } else {
                    document.getElementById('noMatchMessage').classList.add('hidden');
                }
            }
        });

        document.getElementById('resetButton').addEventListener('click', function() {
            // Clear the search input field
            document.getElementById('searchInput').value = '';
            // Get all the list items in the worksites list
            let chantiers = document.getElementById('events').getElementsByTagName('li');
            // Loop through each list item
            for (let i = 0; i < chantiers.length; i++) {
                // Display the list item
                chantiers[i].style.display = '';
                document.getElementById('events').classList.add('hidden');
            }
            // Hide the no match message
            document.getElementById('noMatchMessage').classList.add('hidden');
        });
        // Fin [SPECGT18] - Script pour la recherche par id
    </script>

    @vite('resources/js/heures/chantier.js')
@endpush
