<div>
    <!-- Partie recherche et navigation - Regroupée en une seule section cohérente -->
    <div class="grid grid-cols-2 ml-[20px]">
        <!-- Zone de recherche gauche -->
        <aside class="float-left max-w-[60%]">
            <!-- Recherche par IdAff -->
            <div class="items-center gap-3 order d-flex sm:flex-column lg:flex-row">
                <label for="searchInput" class="m-0">Recherche par IdAff :</label>
                <div class="flex items-center h-12 overflow-hidden border border-black rounded-lg">
                    <input id="searchInput" type="text" placeholder="Saisir idaff..."
                        class="flex-1 h-full px-2 text-black rounded-l-lg outline-none">
                    <button id="resetButton"
                        class="h-full px-3 font-bold text-white bg-red-500 rounded-r-lg">&#x2715;</button>
                </div>
            </div>
            <!-- Bouton création chantier -->
            <menu class="grid grid-cols-1 gap-4 mt-4 justify-items-start">
                <button>
                    <a class="w-auto btn btn-primary btn_chantier" href="{{ route('chantier.create') }}">Creer un
                        nouveau chantier</a>
                </button>
            </menu>
        </aside>

        <!-- Zone de recherche droite -->
        <article class="justify-self-end me-4">
            <div class="items-center gap-3 order d-flex">
                <label for="searchHours" class="m-0">Recherche horaire par IdAff :</label>
                <div class="flex items-center h-12 overflow-hidden border border-black rounded-lg">
                    <input name="searchHours" id="searchHoursInput" type="text" placeholder="Saisir idaff..."
                        class="flex-1 h-full px-3 text-black rounded-l-lg outline-none">
                    <button id="searchHoursReset"
                        class="h-full px-3 font-bold text-white bg-red-500 rounded-none">&#x2715;</button>
                    <button id="searchHoursValid"
                        class="h-full px-3 font-bold text-white bg-green-500 rounded-r-lg">&#x2714;</button>
                </div>
            </div>
        </article>
    </div>

    <!-- Modal de recherche d'heures - Extraite en section distincte -->
    <div id="searchHoursModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow">
            <div class="flex items-center justify-center flex-column">
                <h3 id="searchHoursTitle" class="text-white rounded-md size-[16px] py-[5px] px-[16px] bg-orange-400">
                    titre
                </h3>
                <span id="searchHoursIdaff"></span>
            </div>
            <div class="relative my-4 overflow-x-auto shadow-md sm:rounded-lg">
                <table class="table bg-gray-500 table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">Utilisateur</th>
                            <th class="text-center">Heures</th>
                        </tr>
                    </thead>
                    <tbody id="searchHoursBody"></tbody>
                </table>
            </div>
            <div class="flex justify-center">
                <button id="searchHoursCloseBtn" class="px-4 py-2 text-white bg-gray-500 rounded">Fermer</button>
            </div>
        </div>
    </div>

    <!-- Container principal du calendrier -->
    <div id="calendar-container" wire:ignore>
        <p style="text-align: left; margin-left: 10px; font-size: 1.2em; font-weight: bold;">Chantiers en cours:</p>

        <div id="events" class="calendarListingWorksites">
            <p id="noMatchMessage" style="display: none;">Aucun chantier trouvé.</p>

            <menu class="calendarListingWorksites">
                @php
                    // Définition des variables communes
                    $colors = [
                        'STAGE_1A' => 'bg-danger',
                        'STAGE_1B' => 'bg-warning',
                        'STAGE_1C' => 'bg-success',
                    ];

                    $prevStage = [
                        'STAGE_1A' => 'STAGE_1A',
                        'STAGE_1B' => 'STAGE_1A',
                        'STAGE_1C' => 'STAGE_1B',
                    ];

                    $nextStage = [
                        'STAGE_1A' => 'STAGE_1B',
                        'STAGE_1B' => 'STAGE_1C',
                        'STAGE_1C' => 'STAGE_1C',
                    ];
                @endphp

                @foreach ($chantiers as $chantier)
                    @if (!collect($archivedWorkSites)->contains($chantier))
                        <!-- Élément du chantier dans la liste -->
                        <li data-id-chantier="{{ $chantier->id }}" data-event='@json(['title' => $chantier->title])'
                            data-stage="{{ $chantier->stage_state }}" class="menu-item dropEvent mb-3 p-0">
                            <div
                                class="bg-{{ $chantier->color }}-500 relative rounded-lg w-full p-2 mb-0 cursor-pointer">
                                {{ $chantier->title }}
                                <span data-id={{ $chantier->id }}* data-stage="{{ $chantier->stage_state }}"
                                    class="stage-indicator absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full border border-dark {{ $colors[$chantier->stage_state] ?? 'bg-gray-300' }}">
                                </span>
                            </div>
                        </li>

                        @csrf
                        <!-- Modal d'édition du chantier -->
                        <div id="chantierModal_{{ $chantier->id }}" data-id="{{ $chantier->id }}" class="modalCh">
                            <div class="p-6 bg-white rounded-lg shadow-lg modalCh-content modal_admin">
                                <span class="absolute top-0 right-0 p-4 cursor-pointer close">&times;</span>
                                <h3 class="mb-4 text-2xl font-bold title_info title-modal">
                                    Modification du chantier "{{ $chantier->title }}"
                                </h3>

                                <!-- Information d'identifiant -->
                                <p>IdAff_{{ $chantier->id }}</p>
                                <hr>

                                <!-- Section titre -->
                                <div>
                                    <p>Titre Chantier :</p>
                                    <input class="title input_admin" type="text" name="title"
                                        value="{{ $chantier->title }}">
                                    <button
                                        class="px-4 py-2 mx-auto mt-2 font-bold text-center text-white bg-green-500 rounded-full btn-title hover:bg-green-700"
                                        data-id="{{ $chantier->id }}">Modifier Nom
                                    </button>
                                </div>
                                <hr>

                                <!-- Section état facturation -->
                                <p>État de la facturation :</p>
                                <div>
                                    @php
                                        $invoice = $chantier->invoices;
                                        $existRequestedAt = $invoice && $invoice->requested_at;
                                        $existFilledAt = $invoice && $invoice->filled_at;
                                        $isDisabled = false;
                                        $isReturnable = $chantier->states->status !== 'initial';
                                        $colorClass = '';

                                        if ($chantier->invoices !== null) {
                                            if (
                                                $chantier->states &&
                                                ($chantier->states->status === 'billable' ||
                                                    $chantier->states->status === 'partiallyBilled')
                                            ) {
                                                if (
                                                    !property_exists($chantier->invoices, 'number') ||
                                                    $chantier->invoices->number === null
                                                ) {
                                                    $isDisabled = true;
                                                }
                                            }
                                        }

                                        switch ($chantier->states->status) {
                                            case 'pendingArchiving':
                                                $colorClass = 'bg-orange-500 hover:bg-orange-700';
                                                break;
                                            case 'billable':
                                            case 'partiallyBilled':
                                                $colorClass = $isDisabled
                                                    ? 'bg-gray-500 enabled:hover:bg-gray-700'
                                                    : 'bg-gray-500 hover:bg-gray-700';
                                                break;
                                            default:
                                                $colorClass = 'bg-green-500 hover:bg-green-700';
                                        }
                                    @endphp

                                    <button
                                        class="px-4 py-2 mx-auto mt-2 font-bold text-center text-white rounded-full invoiceStateBtn {{ $colorClass }}"
                                        data-id="{{ $chantier->id }}" data-status="{{ $chantier->states->status }}"
                                        @if ($isDisabled) disabled @endif>
                                        {{ $chantier->states->label }}
                                    </button>

                                    <div class="my-2 stateContainer" data-id="{{ $chantier->id }}"
                                        {{ $existRequestedAt ? '' : 'hidden' }}>
                                        <p class="flex pt-2">
                                            <span class='requestedAtTitle' data-id="{{ $chantier->id }}">
                                                Facture demandée le :
                                            </span>
                                            <strong>
                                                <span class='requestedAtDate formattedDate'
                                                    data-id="{{ $chantier->id }}">{{ $existRequestedAt ? $chantier->invoices->requested_at : '' }}
                                                </span>
                                            </strong>
                                            <button class="cursor-pointer invoiceReturnStateBtn ms-3"
                                                data-id="{{ $chantier->id }}"
                                                data-status="{{ $chantier->states->status }}"
                                                @if (!$isReturnable) disabled @endif>
                                                <img src={{ $isReturnable ? '/front/images/return-icon.svg' : '/front/images/return-icon-disabled.svg' }}
                                                    alt='icône retour {{ $isReturnable ? '' : 'désactivée' }}'>
                                            </button>
                                        </p>

                                        <p class="flex pb-2 invoiceContainer" data-id="{{ $chantier->id }}">
                                            <label for="invoiceNumber" class="font-thin me-2">Facture n° :</label>
                                            <input class='invoiceInputNumber text-[14px]' data-id="{{ $chantier->id }}"
                                                data-status="{{ $chantier->states->status }}" type='text'
                                                name="invoiceNumber"
                                                value="{{ $existFilledAt ? $chantier->invoices->number : '' }}"
                                                @if (!$isDisabled) disabled @endif>
                                            <button class="mx-4 invoiceValidateBtn" data-id="{{ $chantier->id }}"
                                                data-status="{{ $chantier->states->status }}"
                                                @if (!$isDisabled) disabled @endif>
                                                <img src='/front/images/{{ $isDisabled ? 'validate-icon.svg' : 'validate-icon-disabled.svg' }}'
                                                    alt='icône validation {{ $isDisabled ? '' : 'désactivée' }}'>
                                            </button>
                                            <button class="invoiceDeleteBtn" data-id="{{ $chantier->id }}"
                                                data-status="{{ $chantier->states->status }}"
                                                @if (!$isDisabled) disabled @endif>
                                                <img src='/front/images/{{ $isDisabled ? 'trashbin-icon.svg' : 'trashbin-icon-disabled.svg' }}'
                                                    alt='icône poubelle {{ $isDisabled ? '' : 'désactivée' }}'>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                                <hr>

                                <!-- Section heures -->
                                <div>
                                    <p>Heure Prévue :</p>
                                    <input class="hour input_admin" type="text" name="hours"
                                        value="{{ $chantier->hours }}">
                                    <button
                                        class="px-4 py-2 mx-auto mt-2 font-bold text-center text-white bg-green-500 rounded-full btn-hour hover:bg-green-700"
                                        data-id="{{ $chantier->id }}">Modifier Temps
                                    </button>
                                </div>
                                <hr>

                                <!-- Section techniciens disponibles -->
                                <p class="mt-4">Techniciens Disponibles : </p>
                                @csrf
                                <select multiple="multiple" name="user_id[]"
                                    class="block w-full rounded-md selected-users font-2xl">
                                    @foreach ($users as $user)
                                        @if (!$chantier->users->contains($user) && $user->email != null)
                                            <option value="{{ $user->id }}" class="text-2xl">
                                                {{ $user->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <button
                                    class="px-4 py-2 mt-2 font-bold text-white bg-green-500 rounded-full btn-assign hover:bg-green-700"
                                    form="chantierForm_{{ $chantier->id }}">Ajouter Technicien
                                </button>
                                <hr>

                                <!-- Section techniciens affectés -->
                                <p class="mt-4 listeTeck">Liste des techniciens affectés : </p>
                                <ul class="ulListeTeck">
                                    @foreach ($chantier->users as $user)
                                        <li class="d-flex assignedUser">
                                            <p>{{ $user->name }}</p>
                                            <button class="deleteAssignedUser" data-id-user="{{ $user->id }}"
                                                data-name-user="{{ $user->name }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                    <path
                                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                                    <path
                                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                                                </svg>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                                <hr>

                                <!-- Section montants -->
                                <div class="form-group amounts" data-id="{{ $chantier->id }}">
                                    <label>Montant matériel :</label>
                                    <div class="montant">
                                        <input type="float" class="form-control amount" name="materialamount"
                                            value="{{ $chantier->materialamount }}">
                                        <p>€</p>
                                    </div>

                                    <label>Montant Service :</label>
                                    <div class="montant">
                                        <input type="float" class="form-control amount" name="serviceamount"
                                            value="{{ $chantier->serviceamount }}">
                                        <p>€</p>
                                    </div>
                                    <button
                                        class="px-4 py-2 mx-auto mt-2 font-bold text-center text-white bg-green-500 rounded-full hover:bg-green-700 btn-amount"
                                        data-id="{{ $chantier->id }}">Modifier Montant
                                    </button>
                                </div>
                                <hr>

                                <!-- Section tâches -->
                                <div class="form-group" id="options" data-id="{{ $chantier->id }}">
                                    <p>Tâches Réalisées : </p>
                                    @if ($chantier->parameters->count())
                                        @foreach ($chantier->parameters as $parameter)
                                            <label class="form-control @if ($parameter->pivot->completed === 1) green @endif">
                                                <input type="checkbox" name="{{ $parameter->label }}"
                                                    value="{{ $parameter->label }}" data-id="{{ $parameter->id }}"
                                                    {{ $parameter->pivot->completed == 1 ? 'checked' : '' }}>
                                                {{ $parameter->label }}
                                            </label>
                                        @endforeach
                                    @else
                                        <p>Il n'y a pas de paramètres associés à ce chantier.</p>
                                    @endif
                                </div>
                                <hr>

                                <!-- Section type/couleur -->
                                <div class="relative inline-block w-48 form-group">
                                    <label>Type : </label>
                                    @csrf
                                    <select name="color" data-id="{{ $chantier->id }}"
                                        class="block px-4 py-2 pr-8 border border-gray-300 rounded-md shadow-sm colorSelect focus:outline-none focus:ring-0">
                                        @php
                                            $typeOptions = [
                                                'green' => 'ROP',
                                                'yellow' => 'SYSTEME ELECTRONIQUE',
                                                'red' => 'MAINTENANCE',
                                                'purple' => 'LAN',
                                                'blue' => 'RACCO',
                                                'gray' => 'FON',
                                                'orange' => 'Vie',
                                            ];
                                        @endphp

                                        @foreach ($typeOptions as $color => $label)
                                            <option value="{{ $color }}"
                                                {{ $chantier->color == $color ? 'selected' : '' }}
                                                class="{{ $chantier->color == $color ? 'text-white' : '' }} bg-{{ $color }}-500 text-white">
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <hr>

                                <!-- Section étape -->
                                @php
                                    $stage = $chantier->stages;
                                    $color = $colors[$stage->code];
                                @endphp

                                <div class="flex items-center gap-2 my-4">
                                    <span data-id="{{ $chantier->id }}"
                                        class="modal-stage-indicator w-3 h-3 rounded-full border border-dark {{ $color }}"></span>
                                    <p data-id={{ $chantier->id }} class="text-2xl m-0 stage-label">
                                        {{ $stage->label }}</p>
                                </div>
                                <menu id='staging-menu' class="flex items-center gap-2">
                                    <button
                                        class="staging-backward-btn px-4 py-2 font-bold text-white bg-orange-500 rounded-3 hover:bg-orange-700"
                                        data-id="{{ $chantier->id }}" data-direction="backward"
                                        data-next-stage="{{ $prevStage[$stage->code] }}"
                                        data-current-stage="{{ $stage->code }}">
                                        Revenir à l'étape précédente
                                    </button>
                                    <button
                                        class="staging-forward-btn px-4 py-2 font-bold text-white bg-green-500 rounded-3 hover:bg-green-700"
                                        data-id="{{ $chantier->id }}" data-direction="forward"
                                        data-next-stage="{{ $nextStage[$stage->code] }}"
                                        data-current-stage="{{ $stage->code }}">
                                        Passer à l'étape suivante
                                    </button>
                                </menu>
                                <hr>

                                <!-- Section observations -->
                                <p data-id-="{{ $chantier->id }} " class="mt-4 text-2xl">Observations : </p>
                                <textarea name="observations" cols="15" rows="5" class="block w-full mt-1 rounded-md observations">{{ $chantier->observation }}</textarea>
                                <button
                                    class="px-4 py-2 mx-auto mt-2 font-bold text-center text-white bg-green-500 rounded-full btn-observation hover:bg-green-700"
                                    data-id="{{ $chantier->id }}">Ajouter Observation
                                </button>
                                <hr>

                                <!-- Bouton supprimer -->
                                <div class="mt-4 modal-buttons">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="px-4 py-2 font-bold text-white bg-red-500 rounded-full delete-btn hover:bg-red-700"
                                        data-id="{{ $chantier->id }}"
                                        data-url="{{ route('chantier.destroy', ['idChantier' => $chantier->id, 'idUser' => auth()->user()->id]) }}">
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal d'informations du chantier -->
                        <div id="chantierModalInfo_{{ $chantier->id }}" class="modalCh modalInfo">
                            <div class="p-6 bg-white rounded-lg shadow-lg modalCh-content">
                                <span class="absolute top-0 right-0 p-4 cursor-pointer close">&times;</span>
                                <h3 class="mb-4 text-2xl font-bold title_info">Informations sur le chantier
                                    "{{ $chantier->title }}"</h3>

                                <p class="idaff">IdAff_{{ $chantier->id }}</p>
                                <hr>
                                <p class="hourInfo">Heure Prévue : {{ $chantier->hours }} h</p>
                                <hr>

                                <!-- Liste des techniciens avec heures -->
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

                                <!-- Affichage des montants -->
                                <div class="form-group amounts" data-id="{{ $chantier->id }}">
                                    <p class="text_decoration">Montant :</p>
                                    <p class="amountMaterial">Montant Matériel : {{ $chantier->materialamount }} €</p>
                                    <p class="amountService">Montant Service : {{ $chantier->serviceamount }} €</p>
                                </div>
                                <hr>

                                <!-- Affichage des tâches -->
                                <div class="form-group" id="options" data-id="{{ $chantier->id }}">
                                    <p>Tâches Réalisées : </p>
                                    @if ($chantier->parameters->count())
                                        @foreach ($chantier->parameters as $parameter)
                                            <label
                                                class="form-control @if ($parameter->pivot->completed === 1) green @endif">
                                                <input type="checkbox" name="{{ $parameter->label }}"
                                                    value="{{ $parameter->label }}"
                                                    {{ $parameter->pivot->completed == 1 ? 'checked' : '' }} disabled>
                                                {{ $parameter->label }}
                                            </label>
                                        @endforeach
                                    @else
                                        <p>Il n'y a pas de paramètres associés à ce chantier.</p>
                                    @endif
                                </div>
                                <hr>

                                <!-- Affichage des observations -->
                                <p data-id-="{{ $chantier->id }} " class="mt-4 text-2xl">Observations : </p>
                                <p class="obs">{{ $chantier->observation ?: 'Aucune observation' }}</p>
                                <hr>

                                <!-- Bouton supprimer -->
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
                    @endif
                @endforeach
            </menu>
        </div>

        <div id="calendar"></div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:load', () => {
            // Modules organisés par fonctionnalité
            const WorksiteCalendar = {
                calendar: null,
                currentEvent: null,

                async init() {
                    this.initDraggable();
                    await this.loadEvents();
                    this.renderAstreintesButton();
                },

                initDraggable() {
                    const Draggable = window.Draggable;
                    new Draggable(document.getElementById('events'), {
                        itemSelector: '.dropEvent'
                    });
                },

                async loadEvents() {
                    const eventsData = await Promise.all(JSON.parse(@this.events).map(this.processEvent));
                    this.renderCalendar(eventsData);
                },

                refetchEvents() {
                    WorksiteCalendar.calendar.refetchEvents();
                },

                processEvent(event) {
                    // Cas 1 : Autres events
                    if (event.chantier_id !== undefined) {
                        // Astreinte
                        if (event.oncall_duty === 1) {
                            return {
                                ...event,
                                start: event.date,
                                title: Utils.generateAstreinteTitle(event.user.acronyme),
                                classNames: [
                                    'bg-red-500',
                                    'border border-dark',
                                    'oncall-duty',
                                ],
                                isWorksite: false,
                                isAstreinte: true,
                                allDay: true,
                                display: 'block',
                                userAcronyme: event.user.acronyme,
                            };
                        }

                        // Jour férié
                        if (event.state === 5) {
                            return {
                                ...event,
                                start: event.date,
                                id_chantier: event.chantier_id,
                                chantier: {
                                    id: event.chantier_id,
                                    title: '🎉 Jour férié 🎉',
                                    color: 'gray',
                                },
                                title: '🎉 Jour férié 🎉',
                                classNames: [
                                    'public-holiday',
                                    'idChantierEvent' + event.chantier_id,
                                ],
                                isWorksite: false,
                                display: 'background',
                                color: 'rgba(34, 197, 94, 0.3)',
                                backgroundColor: 'rgba(34, 197, 94, 0.3)',
                                borderColor: 'rgba(34, 197, 94, 0.3)',
                                textColor: '#000000',
                            };
                        }
                    }

                    // Cas 2 : Custom Events
                    if (event.isCustomEvent) {
                        return {
                            ...event,
                            classNames: [
                                'border border-dark',
                                'custom-event',
                            ],
                            isWorksite: false,
                            isCustomEvent: true,
                            editable: true,
                            display: 'block'
                        };
                    }

                    // Cas 3 : Event chantier (a déjà id_chantier)
                    if (event.id_chantier) {
                        const chantier = event.chantier;
                        const idChantier = chantier.id;
                        const stage = chantier.stage_state;
                        return {
                            ...event,
                            classNames: [
                                'bg-' + chantier.color + '-500',
                                'idChantierEvent' + idChantier
                            ],
                            stage_color: Utils.stageColor(stage),
                            isWorksite: true,
                        };
                    }
                },

                renderCalendar(eventsData) {
                    const Calendar = window.Calendar;
                    const calendarEl = document.getElementById('calendar');

                    this.calendar = new Calendar(calendarEl, {
                        plugins: [
                            dayGridPlugin,
                            listPlugin,
                            timeGridPlugin,
                            multiMonthPlugin,
                            interactionPlugin
                        ],
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,listWeek',
                        },
                        customButtons: {
                            showAstreintes: {
                                text: 'Astreintes',
                                click: function() {
                                    const events = this.calendar.getEvents();
                                    const astreintes = events.filter(event => event
                                        .extendedProps
                                        .isAstreinte);
                                    astreintes.forEach(event => {
                                        event.setProp('display', event.getProp(
                                                'display') === 'none' ? 'block' :
                                            'none');
                                    });
                                }
                            },
                            showCustomEvents: {
                                text: 'Évènements',
                                click: function() {
                                    const events = this.calendar.getEvents();
                                    const customEvents = events.filter(event => event
                                        .extendedProps
                                        .isCustomEvent);
                                    customEvents.forEach(event => {
                                        event.setProp('display', event.getProp(
                                                'display') === 'none' ? 'block' :
                                            'none');
                                    });
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
                        hiddenDays: [6, 0], // enlève le samedi et dimanche
                        events: eventsData,
                        eventContent: this.handleEventContent,
                        editable: true,
                        selectable: false,
                        locale: 'fr',
                        timeZone: 'Europe/paris',
                        firstDay: 1,
                        height: 'full',
                        eventResize: this.handleEventResize,
                        eventDrop: this.handleEventDrop,
                        eventReceive: this.handleEventReceive,
                        eventClick: this.handleEventClick,
                        dateClick: this.handleDateClick,
                        eventDidMount: this.handleEventDidMount,
                    });

                    this.calendar.render();
                },

                renderAstreintesButton() {
                    // Ajouter les checkboxes après le rendu du calendrier
                    const headerLeft = document.querySelector(
                        '.fc-header-toolbar .fc-toolbar-chunk:first-child');
                    const checkboxContainer = document.createElement('div');
                    checkboxContainer.className = 'fc-button-group';
                    checkboxContainer.innerHTML = `
                        <label class="fc-button fc-button-primary me-2" style="background-color: #2c3e50; color: #e2e8f0; display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" id="showAstreintes" hidden checked>
                            <span class="loader hidden"></span>
                            <span>Astreintes</span>
                        </label>
                    `;
                    headerLeft.appendChild(checkboxContainer);

                    // Ajouter le style pour le loader
                    const loaderStyle = document.createElement('style');
                    loaderStyle.textContent = `
                        .loader {
                            display: inline-block;
                            width: 16px;
                            height: 16px;
                            border: 2px solid #ffffff;
                            border-radius: 50%;
                            border-top-color: transparent;
                            animation: spin 1s linear infinite;
                        }
                        @keyframes spin {
                            to {
                                transform: rotate(360deg);
                            }
                        }
                        .hidden {
                            display: none !important;
                        }
                    `;
                    document.head.appendChild(loaderStyle);

                    // Gestionnaires d'événements pour les checkboxes
                    document.getElementById('showAstreintes').addEventListener('change', function(e) {
                        const button = e.target.parentElement;
                        const loader = button.querySelector('.loader');
                        const text = button.querySelector('span:not(.loader)');
                        loader.classList.remove('hidden');

                        const events = WorksiteCalendar.calendar.getEvents();
                        const astreintes = events.filter(event => event.extendedProps.isAstreinte);

                        // setTimeout(() => {
                        FilterHandler.showAstreintes(button, astreintes);
                        loader.classList.add('hidden');
                        // }, 1);
                    });
                },

                handleEventDidMount(info) {
                    if (info.event.classNames.includes('public-holiday')) {
                        info.el.style.opacity = '1';
                    };
                },

                handleEventContent(info) {
                    if (info.event.extendedProps.isWorksite) {
                        const stageColor = info.event.extendedProps.stage_color || 'bg-gray-300';
                        const id = info.event.extendedProps.chantier?.id || info.event.extendedProps
                            .id_chantier || '';
                        return ContentHandler.generateSmiley({
                            title: info.event.title,
                            indicator: 'worksite',
                            indicatorColorClass: stageColor,
                            indicatorDataId: id,
                        });
                    } else if (info.event.extendedProps.isAstreinte) {
                        return ContentHandler.generateSmiley({
                            title: info.event.title,
                            indicator: 'astreinte',
                        });
                    } else if (info.event.extendedProps.isCustomEvent) {
                        return ContentHandler.generateSmiley({
                            title: info.event.title,
                            indicator: 'custom-event',
                        });
                    } else {
                        // Retourne le contenu par défaut pour les autres types d'événements
                        return {
                            html: `<div class="px-2 py-1">${info.event.title}</div>`
                        };
                    }
                },

                handleEventResize(info) {
                    if (!info.event.extendedProps.isWorksite) return;
                    const id = info.event['extendedProps']['data-id'] || info.event.id;
                    @this.eventChange(id, info.event);
                },

                handleEventDrop(info) {
                    const id = info.event['extendedProps']['data-id'] || info.event.id;
                    const stageColor = info.event.extendedProps.stage_color || 'bg-gray-300';

                    // Mise à jour de l'indicateur de stage
                    const eventEl = info.el;
                    if (eventEl) {
                        const indicator = eventEl.querySelector('.stage-indicator-events');
                        if (indicator) {
                            indicator.className =
                                `stage-indicator stage-indicator-events w-3 h-3 rounded-full border border-dark ${stageColor}`;
                        }
                    }

                    @this.eventChange(id, info.event);
                },

                handleEventReceive(info) {
                    try {
                        // Vérification des données essentielles
                        if (!info.draggedEl || !info.event) {
                            console.error('Éléments manquants pour le drag & drop');
                            return;
                        }

                        const id = Utils.createUUID();
                        const id_chantier = info.draggedEl.getAttribute('data-id-chantier');

                        if (!id_chantier) {
                            console.error('ID du chantier manquant');
                            return;
                        }

                        // Récupération de la couleur de l'élément dragué
                        const draggedElDiv = info.draggedEl.querySelector('div');
                        if (!draggedElDiv) {
                            console.error('Div de couleur non trouvé');
                            return;
                        }

                        // Récupération de la couleur du stage
                        const stageIndicator = draggedElDiv.querySelector('.stage-indicator');
                        const stageColor = Utils.stageColor(info.draggedEl.getAttribute('data-stage'));

                        const draggedElClass = draggedElDiv.className;
                        const colorRegex = /bg-(\w+)-500/;
                        const match = draggedElClass.match(colorRegex);
                        const colorClass = match ? match[0] : 'bg-gray-500';

                        // Configuration de l'événement
                        info.event.setProp('classNames', [
                            'idChantierEvent' + id_chantier,
                            colorClass,
                            'cursor-pointer'
                        ]);

                        // Ajout des propriétés étendues
                        info.event.setExtendedProp('data-id', id);
                        info.event.setExtendedProp('isWorksite', true);
                        info.event.setExtendedProp('id_chantier', id_chantier);
                        info.event.setExtendedProp('stage_color', stageColor);

                        // Appel au backend
                        @this.eventAdd(info.event, id, id_chantier);
                    } catch (error) {
                        console.error('Erreur lors de la réception de l\'évènement:', error);
                    }
                },

                handleEventClick(info) {
                    if (info.event.extendedProps.isAstreinte) {
                        ModalHandler.showAstreinteInfo(null, info.event);
                    } else if (info.event.extendedProps.isCustomEvent) {
                        ModalHandler.showCustomEventInfo(info.event);
                    } else if (info.event.extendedProps.isWorksite) {
                        let id = info.event.id;
                        let id_chantier = info.event['extendedProps']['id_chantier'];

                        // Récupération de l'id_chantier et id pour les événements créés par drag and drop
                        if (!id_chantier) {
                            // Récupération de la classe de l'événement
                            const eventClass = info.event.classNames.find(className =>
                                className.startsWith('idChantierEvent')
                            );

                            // Expression régulière pour rechercher le nombre dans la classe
                            const regex = /idChantierEvent(\d+)/;
                            const match = eventClass.match(regex);

                            // Récupération de l'id chantier
                            id_chantier = match[1];
                            id = info.event['extendedProps']['data-id'];
                        }
                        ModalHandler.showChantierInfo(id, id_chantier, info);
                    }
                },

                handleDateClick(info) {
                    ModalHandler.showChoiceInfo(info);
                },

                updateStageIndicators(chantierId, stageColor) {
                    // Récupère tous les événements du calendrier
                    const events = this.calendar.getEvents();

                    // Filtre les événements du chantier spécifique
                    const chantierEvents = events.filter(event => {
                        const eventChantierId = event.extendedProps.chantier?.id || event
                            .extendedProps
                            .id_chantier;
                        return eventChantierId == chantierId;
                    });

                    // Met à jour la couleur du stage pour chaque événement
                    WorksiteCalendar.calendar.batchRendering(() => {
                        chantierEvents.forEach(event => {
                            event.setExtendedProp('stage_color', stageColor);
                        });
                    });
                },
            };

            const SearchModule = {
                init() {
                    this.initIdSearch();
                    this.initHoursSearch();
                },

                initIdSearch() {
                    document.getElementById('searchInput').addEventListener('keypress', this
                        .handleIdSearch);
                    document.getElementById('resetButton').addEventListener('click', this
                        .resetIdSearch);
                },

                handleIdSearch(event) {
                    // Check if the pressed key is 'Enter'
                    if (event.key === 'Enter') {
                        // Get the search input value and convert it to uppercase
                        let filter = this.value.toUpperCase();
                        // Get the list of worksites
                        let chantierList = document.getElementById('events');
                        // Get all the list items in the worksites list
                        let chantiers = chantierList.getElementsByTagName('li');
                        let matchFound = false;

                        // Get the archived work sites
                        let archivedWorkSites = @json(collect($archivedWorkSites)->pluck('id')->toArray());

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
                            // Redirect to the work site page
                            window.location.href = '/voir-chantier/' + filter;
                        }

                        // If no match is found, display the error message
                        document.getElementById('noMatchMessage').style.display = matchFound ? 'none' :
                            'block';
                    }
                },

                resetIdSearch() {
                    // Clear the search input field
                    document.getElementById('searchInput').value = '';
                    // Get all the list items in the worksites list
                    let chantiers = document.getElementById('events').getElementsByTagName('li');
                    // Loop through each list item
                    for (let i = 0; i < chantiers.length; i++) {
                        // Display the list item
                        chantiers[i].style.display = '';
                    }
                    // Hide the no match message
                    document.getElementById('noMatchMessage').style.display = 'none';
                },

                initHoursSearch() {
                    document.getElementById('searchHoursInput').addEventListener('keypress', this
                        .handleHoursSearch);
                    document.getElementById('searchHoursValid').addEventListener('click', this
                        .validateHoursSearch);
                    document.getElementById('searchHoursCloseBtn').addEventListener('click', this
                        .closeHoursModal);
                },

                handleHoursSearch(e) {
                    if (e.key === 'Enter') {
                        SearchModule.searchChantierHours(e.target.value);
                    }
                },

                validateHoursSearch() {
                    const value = document.getElementById('searchHoursInput').value;
                    if (value && value !== '') {
                        SearchModule.searchChantierHours(value);
                    }
                },

                closeHoursModal() {
                    document.getElementById('searchHoursModal').classList.add('hidden');
                },

                searchChantierHours(value) {
                    fetch('/trouver-chantier/heures/' + value, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.id) {
                                SearchModule.generateSearchHoursModal(data);
                            }
                        })
                        .catch((e) => {
                            Utils.flashMe('warning', 'Aucun chantier trouvé.');
                        });
                },

                generateSearchHoursModal(data) {
                    const title = document.getElementById('searchHoursTitle');
                    const tbody = document.getElementById('searchHoursBody');
                    const span = document.getElementById('searchHoursIdaff');

                    title.innerText = '';
                    tbody.innerHTML = '';
                    span.innerText = '';

                    title.innerText = data.title;
                    title.classList.add('bg-' + data.color + '-500');

                    const text = 'IdAff : ' + data.id;
                    let totalWorksite = 0;
                    span.innerText = text;

                    data.users.forEach(user => {
                        let totalHours = 0;
                        data.times.forEach(time => {
                            if (time.user_id === user.id) {
                                totalHours += time.hours_day + time.hours_night + time
                                    .hours_travel;
                            }
                        });
                        totalWorksite += totalHours;
                        const tr = document.createElement('tr');
                        tr.classList.add('text-center');
                        const tdUser = document.createElement('td');
                        const tdHours = document.createElement('td');

                        tdUser.innerText = user.name;
                        tdHours.innerText = totalHours;

                        tr.appendChild(tdUser);
                        tr.appendChild(tdHours);
                        tbody.appendChild(tr);
                    });

                    const totalTR = document.createElement('tr');
                    totalTR.classList.add('text-center');
                    const userTD = document.createElement('td');
                    const hoursTD = document.createElement('td');

                    userTD.innerHTML = 'Total : ';
                    hoursTD.innerHTML = totalWorksite;

                    totalTR.appendChild(userTD);
                    totalTR.appendChild(hoursTD);
                    tbody.appendChild(totalTR);

                    document.getElementById('searchHoursModal').classList.remove('hidden');
                }
            };

            const FilterHandler = {
                showAstreintes(element, astreintes) {
                    const isChecked = element.querySelector('input').checked;

                    // Utiliser batchRendering pour optimiser les performances
                    WorksiteCalendar.calendar.batchRendering(() => {
                        astreintes.forEach(event => {
                            event.setProp('display', isChecked ? 'block' : 'none');
                        });
                    });

                    element.style.backgroundColor = isChecked ? '#2c3e50' : '#e2e8f0';
                    element.style.color = isChecked ? 'white' : '#2c3e50';
                },
            };

            const ModalHandler = {
                showChantierInfo(id, id_chantier, info) {
                    // Récupération de l'élément modal info
                    const modalInfo = document.getElementById('chantierModalInfo_' + id_chantier);

                    // Fermeture de la modal si on clique en dehors
                    window.onclick = function(event) {
                        if (event.target == modalInfo) {
                            modalInfo.style.display = "none";
                        }
                    };

                    // Get the <span> element that closes the modal
                    const span = modalInfo.getElementsByClassName("close")[0];
                    // Fermeture de la modal quand clique de la croix
                    span.onclick = function() {
                        modalInfo.style.display = "none";
                    };

                    modalInfo.style.display = "block";

                    // Suppression de l'événement
                    const deleteEvent = document.querySelector("#deleteEvent_" + id_chantier);
                    deleteEvent.onclick = function() {
                        if (confirm("Voulez-vous vraiment supprimer cet évènement ?")) {
                            info.event.remove();
                            @this.eventRemove(id);
                            modalInfo.style.display = "none";
                        }
                    };
                },

                showAstreinteInfo(date = null, event = null) {
                    // Créer une modal pour ajouter une astreinte
                    const modal = document.createElement('dialog');
                    modal.id = "showAstreinte";
                    modal.className = 'p-6 bg-white rounded-lg shadow';
                    modal.innerHTML = `
                        <div class="w-full w-auto">
                            <h3 class="mb-4 text-xl font-bold">${event ? 'Gérer l\'astreinte' : 'Ajouter une astreinte'}</h3>
                            <p class="mb-4 text-sm text-gray-600">L'astreinte sera créée pour toute la semaine (du lundi au vendredi) à partir de la date sélectionnée.</p>
                            <div class="mb-4">
                                <label class="block mb-2">Date de début (sera ajustée au lundi de la semaine)</label>
                                <input type="date" id="astreinteDate" class="w-full p-2 border rounded" ${event ? 'disabled' : ''}>
                            </div>
                            <div class="mb-4">
                                <label class="block mb-2">Technicien</label>
                                <select id="astreinteUser" class="w-full p-2 border rounded" ${event ? '' : ''}>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex justify-end gap-2">
                                ${event ? `
                                                                                                                                                                                                    <button id="deleteAstreinte" class="btn btn-danger btn-sm d-flex align-items-center" title="Supprimer complètement l'astreinte">
                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x me-1" viewBox="0 0 16 16">
                                                                                                                                                                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                                                                                                                                                            </svg>
                                                                                                                                                                                                        <span>Supprimer</span>
                                                                                                                                                                                        </button>
                                                                                                                                                                                                    <button id="updateAstreinte" class="btn btn-primary btn-sm d-flex align-items-center" title="Mettre à jour">
                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil me-1" viewBox="0 0 16 16">
                                                                                                                                                                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                                                                                                                                                                            </svg>
                                                                                                                                                                                                        <span>Mettre à jour</span>
                                                                                                                                                                                        </button>
                                                                                                                                                                                    ` : ''}
                                <button id="cancelAstreinte" class="btn btn-secondary btn-sm d-flex align-items-center" title="Annuler">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x me-1" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                    <span>Annuler</span>
                                </button>
                                ${!event ? `
                                                                                                                                                                                                    <button id="saveAstreinte" class="btn btn-success btn-sm d-flex align-items-center" title="Enregistrer">
                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check me-1" viewBox="0 0 16 16">
                                                                                                                                                                                                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                                                                                                                                                                            </svg>
                                                                                                                                                                                                        <span>Enregistrer</span>
                                                                                                                                                                                        </button>
                                                                                                                                                                                    ` : ''}
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    // Ajouter le style pour l'overlay
                    const style = document.createElement('style');
                    style.textContent = `
                        dialog::backdrop {
                            background-color: rgba(0, 0, 0, 0.5);
                        }
                    `;
                    document.head.appendChild(style);

                    // Si une date est fournie, on la formate et on la définit dans l'input
                    if (date) {
                        const formattedDate = date.toISOString().split('T')[0];
                        document.getElementById('astreinteDate').value = formattedDate;
                    }

                    // Si c'est une astreinte existante, on remplit les champs
                    if (event) {
                        const eventDate = event.start.toISOString().split('T')[0];
                        document.getElementById('astreinteDate').value = eventDate;
                        document.getElementById('astreinteUser').value = event.extendedProps.user_id;
                    }

                    // Afficher la modale
                    modal.showModal();

                    // Gestionnaire pour fermer la modal (bouton Annuler)
                    document.getElementById('cancelAstreinte').onclick = () => modal.close();

                    // Gestionnaire pour supprimer l'astreinte
                    if (event) {
                        document.getElementById('deleteAstreinte').onclick = function() {
                            if (confirm("Voulez-vous vraiment supprimer cette astreinte ?")) {
                                @this.deleteAstreinte(event.id);
                                ContentHandler.deleteAstreinteWeekly(event.start);
                                ModalHandler.closeAndRemoveModal(modal);
                            }
                        };

                        // Gestionnaire pour mettre à jour l'astreinte
                        document.getElementById('updateAstreinte').onclick = function() {
                            const userId = document.getElementById('astreinteUser').value;
                            @this.updateAstreinte(event.id, userId);
                            ModalHandler.closeAndRemoveModal(modal);
                        };
                    }

                    // Gestionnaire pour le clic en dehors de la modale
                    modal.addEventListener('click', (e) => {
                        const rect = modal.getBoundingClientRect();
                        const isInDialog = (rect.top <= e.clientY && e.clientY <= rect.bottom &&
                            rect.left <= e.clientX && e.clientX <= rect.right);
                        if (!isInDialog) {
                            ModalHandler.closeAndRemoveModal(modal);
                        }
                    });

                    // Nettoyer la modale après sa fermeture
                    modal.addEventListener('close', () => {
                        ModalHandler.closeAndRemoveModal(modal);
                    });
                },

                showChoiceInfo(info) {
                    // Créer une modal pour choisir le type d'événement
                    const modal = document.createElement('dialog');
                    modal.id = "showChoiceModal";
                    modal.className = 'p-6 bg-white rounded-lg shadow';
                    modal.innerHTML = `
                        <div class="w-full w-auto">
                            <h3 class="mb-4 text-xl font-bold">Ajouter un évènement</h3>
                            <div class="mb-4">
                                <label class="block mb-2">Type d'évènement</label>
                                <select id="eventType" class="w-full p-2 border rounded">
                                    <option value="astreinte">Astreinte</option>
                                    <option value="custom">Évènement personnalisé</option>
                                </select>
                            </div>
                            <div id="astreinteFields" class="hidden">
                                <div class="mb-4">
                                    <label class="block mb-2">Date de début (sera ajustée au lundi de la semaine)</label>
                                    <input type="date" id="astreinteDate" class="w-full p-2 border rounded">
                                </div>
                                <div class="mb-4">
                                    <label class="block mb-2">Technicien</label>
                                    <select id="astreinteUser" class="w-full p-2 border rounded">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" data-acronyme="{{ $user->acronyme }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="customEventFields" class="hidden">
                                <div class="mb-4">
                                    <label class="block mb-2">Titre</label>
                                    <input type="text" id="customEventTitle" class="w-full p-2 border rounded">
                                </div>
                                <div class="mb-4">
                                    <label class="block mb-2">Description</label>
                                    <textarea id="customEventDescription" class="w-full p-2 border rounded"></textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="block mb-2">Couleur</label>
                                    <input type="color" id="customEventColor" class="w-full p-2 border rounded" value="#3788d8">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button id="cancelEvent" class="btn btn-secondary btn-sm d-flex align-items-center" title="Annuler">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x me-1" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                    <span>Annuler</span>
                                </button>
                                <button id="saveEvent" class="btn btn-success btn-sm d-flex align-items-center" title="Enregistrer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check me-1" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                    </svg>
                                    <span>Enregistrer</span>
                                </button>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    // Ajouter le style pour l'overlay
                    const style = document.createElement('style');
                    style.textContent = `
                        dialog::backdrop {
                            background-color: rgba(0, 0, 0, 0.5);
                        }
                    `;
                    document.head.appendChild(style);

                    // Afficher la modale
                    modal.showModal();

                    // Si une date est fournie, on la formate et on la définit dans l'input
                    if (info.date) {
                        const formattedDate = info.date.toISOString().split('T')[0];
                        document.getElementById('astreinteDate').value = formattedDate;
                    }

                    // Gestionnaire pour le type d'événement
                    const eventTypeSelect = document.getElementById('eventType');
                    const customEventFields = document.getElementById('customEventFields');
                    const astreinteFields = document.getElementById('astreinteFields');

                    eventTypeSelect.addEventListener('change', (e) => {
                        const isAstreinte = e.target.value === 'astreinte';
                        customEventFields.classList.toggle('hidden', isAstreinte);
                        astreinteFields.classList.toggle('hidden', !isAstreinte);
                    });

                    // Afficher le formulaire correspondant au type sélectionné
                    eventTypeSelect.dispatchEvent(new Event('change'));

                    // Gestionnaire pour fermer la modal (bouton Annuler)
                    document.getElementById('cancelEvent').onclick = () => ModalHandler.closeAndRemoveModal(
                        modal);

                    // Gestionnaire pour sauvegarder l'événement
                    document.getElementById('saveEvent').onclick = function() {
                        const eventType = eventTypeSelect.value;
                        const date = info.date.toISOString().split('T')[0];

                        if (eventType === 'astreinte') {
                            const astreinteDate = document.getElementById('astreinteDate').value;
                            const select = document.getElementById('astreinteUser');
                            const userId = select.value;
                            const selectedOption = select.options[select.selectedIndex];
                            const userAcronyme = selectedOption.dataset.acronyme;

                            // Ajouter l'astreinte au calendrier (backend)
                            @this.addAstreinte(astreinteDate, userId);

                            // Créer un événement pour chaque jour de la semaine
                            const startDate = moment(astreinteDate).startOf('isoWeek');
                            const endDate = moment(astreinteDate).endOf('isoWeek');

                            // Boucle sur chaque jour de la semaine
                            for (let date = startDate; date.isSameOrBefore(endDate); date.add(1, 'days')) {
                                const event = {
                                    id: Utils.createUUID(),
                                    start: date.format('YYYY-MM-DD'),
                                    end: date.format('YYYY-MM-DD'),
                                    title: Utils.generateAstreinteTitle(userAcronyme),
                                    color: '#f97316',
                                    extendedProps: {
                                        isAstreinte: true,
                                        user_id: userId,
                                        userAcronyme: userAcronyme
                                    }
                                };
                                WorksiteCalendar.calendar.addEvent(event);
                            }
                            ModalHandler.closeAndRemoveModal(modal);
                        } else {
                            const title = document.getElementById('customEventTitle').value;
                            const description = document.getElementById('customEventDescription').value;
                            const backgroundColor = document.getElementById('customEventColor').value;

                            if (!title) {
                                Utils.flashMe('warning', 'Veuillez saisir un titre');
                                return;
                            }

                            // Calcul de la couleur de texte en fonction de la luminosité de la couleur de fond
                            const rgb = Utils.hexToRgb(backgroundColor);
                            const brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
                            const textColor = brightness > 128 ? '#000000' : '#ffffff';

                            // Ajouter l'événement personnalisé au calendrier (backend)
                            @this.addCustomEvent(date, title, backgroundColor, textColor, description);

                            // Ajouter l'événement personnalisé au calendrier
                            const customEvent = {
                                id: Utils.createUUID(),
                                title: title,
                                start: date,
                                end: date,
                                backgroundColor: backgroundColor,
                                textColor: textColor,
                                extendedProps: {
                                    isCustomEvent: true,
                                    description: description
                                }
                            };
                            WorksiteCalendar.calendar.addEvent(customEvent);
                            ModalHandler.closeAndRemoveModal(modal);
                        }
                    };

                    // Gestionnaire pour le clic en dehors de la modale
                    modal.addEventListener('click', (e) => {
                        const rect = modal.getBoundingClientRect();
                        const isInDialog = (rect.top <= e.clientY && e.clientY <= rect.bottom &&
                            rect.left <= e.clientX && e.clientX <= rect.right);
                        if (!isInDialog) {
                            ModalHandler.closeAndRemoveModal(modal);
                        }
                    });

                    // Nettoyer la modale après sa fermeture
                    modal.addEventListener('close', () => {
                        ModalHandler.closeAndRemoveModal(modal);
                    });
                },

                showCustomEventInfo(event) {
                    // Créer une modal pour éditer l'événement personnalisé
                    const modal = document.createElement('dialog');
                    modal.id = "showCustomEventModal";
                    modal.className = 'p-6 bg-white rounded-lg shadow';
                    modal.innerHTML = `
                        <div class="w-full w-auto">
                            <h3 class="mb-4 text-xl font-bold">Modifier l'évènement</h3>
                            <div class="mb-4">
                                <label class="block mb-2">Titre</label>
                                <input type="text" id="customEventTitle" class="w-full p-2 border rounded" value="${event.title}">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-2">Description</label>
                                <textarea id="customEventDescription" class="w-full p-2 border rounded">${event.extendedProps.description || ''}</textarea>
                            </div>
                            <div class="mb-4">
                                <label class="block mb-2">Couleur</label>
                                <input type="color" id="customEventColor" class="w-full p-2 border rounded" value="${event.backgroundColor}">
                            </div>
                            <div class="flex justify-end gap-2">
                                <button id="deleteCustomEvent" class="btn btn-danger btn-sm d-flex align-items-center" title="Supprimer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash me-1" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3h11V2h-11v1Z"/>
                                    </svg>
                                    <span>Supprimer</span>
                                </button>
                                <button id="cancelCustomEvent" class="btn btn-secondary btn-sm d-flex align-items-center" title="Annuler">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x me-1" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                    <span>Annuler</span>
                                </button>
                                <button id="updateCustomEvent" class="btn btn-success btn-sm d-flex align-items-center" title="Mettre à jour">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check me-1" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                    </svg>
                                    <span>Mettre à jour</span>
                                </button>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    // Ajouter le style pour l'overlay
                    const style = document.createElement('style');
                    style.textContent = `
                        dialog::backdrop {
                            background-color: rgba(0, 0, 0, 0.5);
                        }
                    `;
                    document.head.appendChild(style);

                    // Afficher la modale
                    modal.showModal();

                    // Gestionnaire pour fermer la modal (bouton Annuler)
                    document.getElementById('cancelCustomEvent').onclick = () => ModalHandler
                        .closeAndRemoveModal(modal);

                    // Gestionnaire pour supprimer l'événement
                    document.getElementById('deleteCustomEvent').onclick = function() {
                        if (confirm("Voulez-vous vraiment supprimer cet évènement ?")) {
                            const eventId = event.id.replace('custom_', '');
                            @this.deleteCustomEvent(eventId);
                            event.remove();
                            ModalHandler.closeAndRemoveModal(modal);
                        }
                    };

                    // Gestionnaire pour mettre à jour l'événement
                    document.getElementById('updateCustomEvent').onclick = function() {
                        const title = document.getElementById('customEventTitle').value;
                        const description = document.getElementById('customEventDescription').value;
                        const backgroundColor = document.getElementById('customEventColor').value;

                        if (!title) {
                            Utils.flashMe('warning', 'Veuillez saisir un titre');
                            return;
                        }

                        const eventId = event.id.replace('custom_', '');
                        @this.updateCustomEvent(eventId, title, backgroundColor, description);

                        // Mettre à jour l'événement dans le calendrier
                        event.setProp('title', title);
                        event.setProp('backgroundColor', backgroundColor);
                        event.setProp('borderColor', backgroundColor);
                        event.setExtendedProp('description', description);
                        ModalHandler.closeAndRemoveModal(modal);
                    };

                    // Gestionnaire pour le clic en dehors de la modale
                    modal.addEventListener('click', (e) => {
                        const rect = modal.getBoundingClientRect();
                        const isInDialog = (rect.top <= e.clientY && e.clientY <= rect.bottom &&
                            rect.left <= e.clientX && e.clientX <= rect.right);
                        if (!isInDialog) {
                            ModalHandler.closeAndRemoveModal(modal);
                        }
                    });

                    // Nettoyer la modale après sa fermeture
                    modal.addEventListener('close', () => {
                        ModalHandler.closeAndRemoveModal(modal);
                    });
                },

                closeAndRemoveModal(modal) {
                    modal.close();
                    modal.remove();
                }
            };

            const ContentHandler = {
                generateSmiley({
                    title,
                    indicator,
                    indicatorColorClass = undefined,
                    indicatorDataId = undefined,
                }) {
                    const container = document.createElement('div');
                    container.style.display = 'flex';
                    container.style.justifyContent = 'center';
                    container.style.alignItems = 'center';
                    container.style.gap = '8px';
                    container.style.width = '100%';
                    container.classList.add('px-2', 'py-1');

                    const text = document.createElement('div');
                    text.textContent = title;
                    text.style.flex = '1';
                    text.style.overflow = 'hidden';
                    text.style.textOverflow = 'ellipsis';
                    text.style.whiteSpace = 'nowrap';
                    container.appendChild(text);

                    const indicatorContainer = document.createElement('div');
                    indicatorContainer.style.width = '16px';
                    indicatorContainer.style.display = 'flex';
                    indicatorContainer.style.justifyContent = 'center';
                    indicatorContainer.style.alignItems = 'center';
                    indicatorContainer.style.flexShrink = '0';

                    switch (indicator) {
                        case 'astreinte':
                            const emojiSpan = document.createElement('span');
                            emojiSpan.textContent = '🚨';
                            emojiSpan.style.flexShrink = '0';
                            emojiSpan.style.width = '16px';
                            indicatorContainer.appendChild(emojiSpan);
                            break;
                        case 'worksite':
                            const circle = document.createElement('span');
                            circle.className =
                                `stage-indicator stage-indicator-events w-3 h-3 rounded-full border border-dark
                                ${indicatorColorClass || 'bg-gray-300'}`;
                            if (indicatorDataId) circle.setAttribute('data-id', indicatorDataId);
                            indicatorContainer.appendChild(circle);
                            break;
                        case 'custom-event':
                            const customEventSpan = document.createElement('span');
                            customEventSpan.textContent = '📅';
                            customEventSpan.style.flexShrink = '0';
                            customEventSpan.style.width = '16px';
                            indicatorContainer.appendChild(customEventSpan);
                            break;
                        default:
                            break;
                    }
                    container.appendChild(indicatorContainer);
                    return {
                        domNodes: [container]
                    };
                },

                deleteAstreinteWeekly(event) {
                    const calendar = WorksiteCalendar.calendar;
                    const startOfWeek = Utils.getMonday(event.start);
                    const endOfWeek = new Date(startOfWeek);
                    endOfWeek.setDate(endOfWeek.getDate() + 6);

                    const eventsToRemove = calendar.getEvents().filter(e =>
                        e.title === event.title &&
                        e.start >= startOfWeek.toDate() &&
                        e.start <= endOfWeek.toDate()
                    );

                    calendar.batchRendering(() => {
                        eventsToRemove.forEach(e => e.remove());
                    });
                }

            };

            const Utils = {
                createUUID() {
                    let dt = new Date().getTime();
                    const uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
                        let r = (dt + Math.random() * 16) % 16 | 0;
                        dt = Math.floor(dt / 16);
                        return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                    });
                    return uuid;
                },

                flashMe(icon, msg) {
                    window.flashAlert(icon, msg);
                },

                stageColor(stage) {
                    switch (stage) {
                        case 'STAGE_1A':
                            return 'bg-danger';
                        case 'STAGE_1B':
                            return 'bg-warning';
                        case 'STAGE_1C':
                            return 'bg-success';
                        default:
                            return 'bg-gray-300';
                    }
                },

                hexToRgb(hex) {
                    // Supprimer le # si présent
                    hex = hex.replace('#', '');

                    // Convertir en RGB
                    const r = parseInt(hex.substring(0, 2), 16);
                    const g = parseInt(hex.substring(2, 4), 16);
                    const b = parseInt(hex.substring(4, 6), 16);

                    return {
                        r,
                        g,
                        b
                    };
                },

                generateAstreinteTitle(userAcronyme) {
                    return 'Astreinte ' + userAcronyme;
                },

                getMonday(date) {
                    const d = new Date(date);
                    const day = d.getDay();
                    const diff = (day === 0 ? -6 : 1 - day);
                    d.setDate(d.getDate() + diff);
                    return d;
                },
            };

            // Expose la méthode au niveau global pour qu'elle soit accessible depuis worksiteStaging.js
            window.WorksiteCalendar = WorksiteCalendar;

            // Initialisation des modules
            WorksiteCalendar.init();
            SearchModule.init();

            // Écouteur pour le rafraîchissement après ajout d'astreinte
            ['Added', 'Updated', 'Deleted'].forEach(action => {
                Livewire.on(`astreinte${action}`, () => {
                    WorksiteCalendar.loadEvents();
                    Utils.flashMe('success', `Astreinte ${action.toLowerCase()}e avec succès`);
                });
            });
            Livewire.on('astreinteNotFound', () => {
                WorksiteCalendar.loadEvents();
                Utils.flashMe('danger', 'Astreinte non trouvée');
            });
        });
    </script>

    @vite('resources/js/heures/chantier.js')
    @vite('resources/js/heures/worksiteState.js')
    @vite('resources/js/heures/deleteWorksite.js')
    @vite('resources/js/heures/worksiteStaging.js')
@endpush
