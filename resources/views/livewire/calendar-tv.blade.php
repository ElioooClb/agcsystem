<div>
    <div id="tv-calendar-container" wire:ignore>
        <div id="tv-calendar"></div>
    </div>
</div>
{{-- Message pour la TV --}}
<div class="marquee-rtl">
    <div>
        @foreach ($message as $message)
            {{ $message->message }}
        @endforeach
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:load', function() {
            Promise.all(JSON.parse(@this.events).map(event => {
                    const chantier = event.chantier;
                    const idChantier = chantier.id;
                    return {
                        ...event,
                        classNames: ['bg-' + chantier.color + '-500', 'idChantierEvent' + idChantier],
                    };
                }))
                .then(eventsData => {
                    const Calendar = window.Calendar;
                    const calendarEl = document.getElementById('tv-calendar');

                    const calendar = new Calendar(calendarEl, {
                        plugins: [dayGridPlugin, listPlugin, timeGridPlugin, multiMonthPlugin,
                            interactionPlugin
                        ],
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: '',
                        },
                        buttonText: {
                            today: 'Aujourd\'hui',
                            month: 'Mois',
                            week: 'Semaine',
                            day: 'Jour',
                            list: 'Liste  de la samaine',
                        },
                        hiddenDays: [6, 0], // enleve le samedi et dimanche
                        events: eventsData,
                        editable: false,
                        selectable: false,
                        locale: 'fr',
                        firstDay: 1,
                        height: "auto",
                        eventDidMount: handleEventMount,
                    });

                    calendar.render();
                });
        });

        function handleEventMount(info) {
            const event = info.event;
            if (event.extendedProps.acronyms) {
                const contentEl = document.createElement('div');
                contentEl.className = ['acronym-list text-white text-right pr-2'];
                contentEl.textContent = event.extendedProps.acronyms;
                const eventElement = info.el;
                eventElement.appendChild(contentEl);
            }
        }
    </script>
    @vite('resources/js/heures/chantier.js')
    <script>
        setTimeout(function() {
            location.reload();
        }, 300000); // 300000 millisecondes = 5 minutes
    </script>
@endpush
