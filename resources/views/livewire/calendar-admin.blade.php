<div>
    <div class="grid grid-cols-2 ml-[20px]">
        <aside class="float-left max-w-[60%]">
            <!-- Début [SPECGT11] - Ajout de la recherche par id -->
            <div class="items-center gap-3 order d-flex sm:flex-column lg:flex-row">
                <label for="searchInput" class="m-0">Recherche par IdAff :</label>
                <div class="flex items-center h-12 overflow-hidden border border-black rounded-lg">
                    <input id="searchInput" type="text" placeholder="Saisir idaff..."
                        class="flex-1 h-full px-2 text-black rounded-l-lg outline-none">
                    <button id="resetButton"
                        class="h-full px-3 font-bold text-white bg-red-500 rounded-r-lg">&#x2715;</button>
                </div>
            </div>
            <!-- Fin [SPECGT11] - Ajout de la recherche par id -->
            <!-- Bouton pour afficher la fenêtre modale -->
            <menu class="grid grid-cols-1 gap-4 mt-4 justify-items-start">
                <button>
                    <a class="w-auto btn btn-primary btn_chantier" href="{{ route('chantier.create') }}">Creer un
                        nouveau chantier</a>
                </button>
            </menu>
        </aside>
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

    <div id="searchHoursModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow">
            <!-- Modal header -->
            <div class="flex items-center justify-center flex-column">
                <h3 id="searchHoursTitle" class="text-white rounded-md size-[16px] py-[5px] px-[16px] bg-orange-400">
                    titre
                </h3>
                <span id="searchHoursIdaff"></span>
            </div>
            <!-- Modal body -->
            <div class="relative my-4 overflow-x-auto shadow-md sm:rounded-lg">
                <table class="table bg-gray-500 table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">Utilisateur</th>
                            <th class="text-center">Heures</th>
                        </tr>
                    </thead>
                    <tbody id="searchHoursBody">
                    </tbody>
                </table>
            </div>
            <!-- Modal footer -->
            <div class="flex justify-center">
                <button id="searchHoursCloseBtn" class="px-4 py-2 text-white bg-gray-500 rounded">Fermer</button>
            </div>
        </div>
    </div>

    <!--[SPECGT10] - prévisionnel de charge -> calcul des sommes -->
    <div id="calendar-container" wire:ignore>
        <p style="text-align: left; margin-left: 10px; font-size: 1.2em; font-weight: bold;"> Chantiers en
            cours:</p>
        {{-- DEBUT [SPECGT17] - Ajout d'un scroll css et d'une gestion de l'affichage mobile --}}
        <div id="events" class="calendarListingWorksites">
            <!-- Début [SPECGT11] - Affichage d'un message si aucun chantier n'est trouvé -->
            <p id="noMatchMessage" style="display: none;">Aucun chantier trouvé.</p>
            <!-- Fin [SPECGT11] - Affichage d'un message si aucun chantier n'est trouvé -->

            <menu class="calendarListingWorksites">
                @php
                    $colors = [
                        'STAGE_1A' => 'bg-danger',
                        'STAGE_1B' => 'bg-warning',
                        'STAGE_1C' => 'bg-success',
                    ];
                @endphp
                @foreach ($chantiers as $chantier)
                    @if (!collect($archivedWorkSites)->contains($chantier))
                        <li data-id-chantier="{{ $chantier->id }}" data-event='@json(['title' => $chantier->title])'
                            class="menu-item dropEvent bg-{{ $chantier->color }}-500 relative p-2 text-md">

                            <span class="block pr-6">
                                {{ $chantier->title }}
                            </span>

                            <span
                                data-id={{ $chantier->id }}
                                class="stage-indicator absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full border border-dark {{ $colors[$chantier->stage_state] ?? 'bg-gray-300' }}">
                            </span>

                        </li>

                        @csrf
                        <div id="chantierModal_{{ $chantier->id }}" data-id="{{ $chantier->id }}" class="modalCh">
                            <div class="p-6 bg-white rounded-lg shadow-lg modalCh-content modal_admin">
                                <span class="absolute top-0 right-0 p-4 cursor-pointer close">&times;</span>
                                <h3 class="mb-4 text-2xl font-bold title_info title-modal">
                                    Modification du chantier
                                    "{{ $chantier->title }}"</h3>
                                <!-- [SPECGT1] -->
                                {{-- IdAff --}}
                                <p>IdAff_{{ $chantier->id }}</p>
                                <hr>
                                {{-- Titre Chantier --}}
                                <div>
                                    <p>Titre Chantier :</p>
                                    <!-- Début [SPECGT26] - Optimisation et corrections du code -->
                                    <input class="title input_admin" type="text" name="title"
                                        value="{{ $chantier->title }}">
                                    <!-- Fin [SPECGT26] - Optimisation et corrections du code -->
                                    <button
                                        class="px-4 py-2 mx-auto mt-2 font-bold text-center text-white bg-green-500 rounded-full btn-title hover:bg-green-700"
                                        data-id="{{ $chantier->id }}">Modifier Nom
                                    </button>
                                </div>
                                <hr>
                                <p>État de la facturation :</p>
                                <div>
                                    {{-- DEBUT [SPECGT21] - gestion de la facturation --}}
                                    <?php
                                    $invoice = $chantier->invoices;
                                    $existRequestedAt = $invoice && $invoice->requested_at;
                                    $existFilledAt = $invoice && $invoice->filled_at;
                                    $isDisabled = false;
                                    $isReturnable = $chantier->states->status !== 'initial';
                                    $colorClass = '';
                                    if ($chantier->invoices !== null) {
                                        if ($chantier->states && ($chantier->states->status === 'billable' || $chantier->states->status === 'partiallyBilled')) {
                                            if (!property_exists($chantier->invoices, 'number') || $chantier->invoices->number === null) {
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
                                            $colorClass = $isDisabled ? 'bg-gray-500 enabled:hover:bg-gray-700' : 'bg-gray-500 hover:bg-gray-700';
                                            break;
                                        default:
                                            $colorClass = 'bg-green-500 hover:bg-green-700';
                                    }
                                    ?>
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
                                {{-- Heure Chantiers --}}
                                <div>
                                    <p>Heure Prévue :</p>
                                    <!-- Début [SPECGT26] - Optimisation et corrections du code -->
                                    <input class="hour input_admin" type="text" name="hours"
                                        value="{{ $chantier->hours }}">
                                    <!-- Fin [SPECGT26] - Optimisation et corrections du code -->
                                    <button
                                        class="px-4 py-2 mx-auto mt-2 font-bold text-center text-white bg-green-500 rounded-full btn-hour hover:bg-green-700"
                                        data-id="{{ $chantier->id }}">Modifier Temps
                                    </button>
                                </div>
                                <hr>

                                {{-- Liste Teckos Dispo --}}
                                <p class="mt-4">Techniciens Disponibles : </p>
                                @csrf
                                <select multiple="multiple" name="user_id[]"
                                    class="block w-full rounded-md selected-users font-2xl">
                                    @foreach ($users as $user)
                                        @if (!$chantier->users->contains($user))
                                            @if ($user->email != null)
                                                <option value="{{ $user->id }}" class="text-2xl">
                                                    {{ $user->name }}
                                                </option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                                <button
                                    class="px-4 py-2 mt-2 font-bold text-white bg-green-500 rounded-full btn-assign hover:bg-green-700"
                                    form="chantierForm_{{ $chantier->id }}">Ajouter Technicien
                                </button>
                                <hr>


                                {{-- Liste Teckos Chantiers --}}
                                <p class="mt-4 listeTeck">Liste des techniciens affectés : </p>
                                <ul class="ulListeTeck">
                                    @foreach ($chantier->users as $user)
                                        <li class="d-flex assignedUser">
                                            <p>{{ $user->name }}</p>

                                            {{-- Suppression Teckos --}}
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
                                {{-- Montant Chantiers --}}
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
                                {{-- Options Chantiers --}}
                                <div class="form-group" id="options" data-id="{{ $chantier->id }}">
                                    <p>Tâches Réalisées : </p>

                                    {{-- Début [SPECGT6] - Modification de la gestion des tâches --}}
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
                                    {{-- Fin [SPECGT6] - Modification de la gestion des tâches --}}
                                </div>
                                <hr>
                                {{-- Couleurs de l'événement --}}
                                <div class="relative inline-block w-48 form-group">
                                    <label>Type : </label>
                                    @csrf
                                    <select name="color" data-id="{{ $chantier->id }}"
                                        class="block px-4 py-2 pr-8 border border-gray-300 rounded-md shadow-sm colorSelect focus:outline-none focus:ring-0">
                                        <option value="green" {{ $chantier->color == 'green' ? 'selected' : '' }}
                                            class="{{ $chantier->color == 'green' ? 'text-white' : '' }} bg-green-500 text-white">
                                            ROP
                                        </option>
                                        <option value="yellow" {{ $chantier->color == 'yellow' ? 'selected' : '' }}
                                            class="{{ $chantier->color == 'yellow' ? 'text-white' : '' }} bg-yellow-500 text-white">
                                            SYSTEME ELECTRONIQUE
                                        </option>
                                        <option value="red" {{ $chantier->color == 'red' ? 'selected' : '' }}
                                            class="{{ $chantier->color == 'red' ? 'text-white' : '' }} bg-red-500 text-white">
                                            MAINTENANCE
                                        </option>
                                        <option value="purple" {{ $chantier->color == 'purple' ? 'selected' : '' }}
                                            class="{{ $chantier->color == 'purple' ? 'text-white' : '' }} bg-purple-500 text-white">
                                            LAN
                                        </option>
                                        <option value="blue" {{ $chantier->color == 'blue' ? 'selected' : '' }}
                                            class="{{ $chantier->color == 'blue' ? 'text-white' : '' }} bg-blue-500 text-white">
                                            RACCO
                                        </option>
                                        <option value="gray" {{ $chantier->color == 'gray' ? 'selected' : '' }}
                                            class="{{ $chantier->color == 'gray' ? 'text-white' : '' }} bg-gray-500 text-white">
                                            FON
                                        </option>
                                        <option value="orange" {{ $chantier->color == 'orange' ? 'selected' : '' }}
                                            class="{{ $chantier->color == 'orange' ? 'text-white' : '' }} bg-orange-500 text-white">
                                            Vie
                                        </option>
                                    </select>
                                </div>
                                <hr>
                                {{-- Stage Chantier --}}
                                @php
                                    $stage = $chantier->stages;
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
                                    $color = $colors[$stage->code];
                                @endphp

                                <div class="flex items-center gap-2 my-4">
                                    <span data-id="{{ $chantier->id }}"
                                        class="modal-stage-indicator w-3 h-3 rounded-full border border-dark {{ $color }}"></span>
                                    <p data-id={{ $chantier->id }} class="text-2xl m-0 stage-label">{{ $stage->label }}</p>
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
                                {{-- Observation(s) Chantier --}}
                                <p data-id-="{{ $chantier->id }} " class="mt-4 text-2xl">Observations : </p>
                                <textarea name="observations" cols="15" rows="5" class="block w-full mt-1 rounded-md observations">{{ $chantier->observation }}</textarea>
                                <button
                                    class="px-4 py-2 mx-auto mt-2 font-bold text-center text-white bg-green-500 rounded-full btn-observation hover:bg-green-700"
                                    data-id="{{ $chantier->id }}">Ajouter Observation
                                </button>
                                <hr>
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

                        {{-- Modal Info Chantiers --}}
                        <div id="chantierModalInfo_{{ $chantier->id }}" class="modalCh modalInfo">
                            <div class="p-6 bg-white rounded-lg shadow-lg modalCh-content">
                                <span class="absolute top-0 right-0 p-4 cursor-pointer close">&times;</span>
                                <h3 class="mb-4 text-2xl font-bold title_info">Informations sur le chantier
                                    "{{ $chantier->title }}"</h3>

                                <!-- [SPECGT1] -->
                                {{-- IdAff --}}
                                <p class="idaff">IdAff_{{ $chantier->id }}</p>
                                <hr>
                                {{-- Heure Chantier --}}
                                <p class="hourInfo">Heure Prévue : {{ $chantier->hours }} h</p>
                                <hr>
                                {{-- Liste teckos assignés --}}
                                <div class="listeteckos">
                                    <p class="mt-4 listeTeck">Liste des techniciens affectés : </p>
                                    <ul class="ulListeTeck">
                                        @foreach ($chantier->users as $user)
                                            <li class="d-flex assignedUser">
                                                {{ $user->name }},
                                                <!-- Début [SPECGT19] - Affichage des heures à côté des noms -->
                                                {{ $chantier->userHours[$user->id] ?? '0' }}h
                                                <!-- Fin [SPECGT19] - Affichage des heures à côté des noms -->
                                            </li>
                                        @endforeach
                                        <!-- Début [SPECGT19] - Affichage du total des heures -->
                                        <li class="my-2">
                                            Total : {{ $chantier->userHours ? array_sum($chantier->userHours) : '0' }}h
                                        </li>
                                        <!-- Fin [SPECGT19] - Affichage du total des heures -->
                                    </ul>

                                </div>

                                <hr>
                                {{-- Montant Chantiers --}}
                                <div class="form-group amounts" data-id="{{ $chantier->id }}">
                                    <p class="text_decoration">Montant :</p>
                                    <p class="amountMaterial">Montant Matériel : {{ $chantier->materialamount }} €
                                    </p>
                                    <p class="amountService">Montant Service : {{ $chantier->serviceamount }} €</p>
                                </div>
                                <hr>
                                {{-- Options Chantiers --}}
                                <div class="form-group" id="options" data-id="{{ $chantier->id }}">
                                    <p>Tâches Réalisées : </p>

                                    {{-- Début [SPECGT6] - Modification de la gestion des tâches --}}
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
                                    {{-- Fin [SPECGT6] - Modification de la gestion des tâches --}}
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
                    @endif
                @endforeach
            </menu>
        </div>

        <div id="calendar"></div>
    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('livewire:load', function() {
            Promise.all(JSON.parse(@this.events).map(event => {
                // DEBUT - [SPECGT26] - Optimisation et corrections du code, correction de la dépendance chantier et suppression des requêtes en boucles
                const chantier = event.chantier;
                const idChantier = chantier.id;
                return {
                    ...event,
                    classNames: ['bg-' + chantier.color + '-500', 'idChantierEvent' + idChantier],
                };
                // FIN - [SPECGT26] - Optimisation et corrections du code, correction de la dépendance chantier et suppression des requêtes en boucles
            })).then(eventsData => {
                const Calendar = window.Calendar;
                const calendarEl = document.getElementById('calendar');
                const Draggable = window.Draggable;
                new Draggable(document.getElementById('events'), {
                    itemSelector: '.dropEvent'
                });

                // Initialisation de la variable currentEvent
                let currentEvent = null;
                const calendar = new Calendar(calendarEl, {
                    plugins: [dayGridPlugin, listPlugin, timeGridPlugin, multiMonthPlugin,
                        interactionPlugin
                    ],

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
                        list: 'Liste  de la samaine',
                    },
                    hiddenDays: [6, 0], // enleve le samedi et dimanche
                    events: eventsData,
                    editable: true,
                    selectable: false,
                    locale: 'fr',
                    timeZone: 'Europe/paris',
                    firstDay: 1,
                    // Lors du redimensionnement d'un événement, et mise à jour de la variable de l'id pour pouvoir le manipuler avant recharge de la page
                    eventResize: info => {
                        const id = info.event['extendedProps']['data-id'] || info.event.id;
                        @this.eventChange(id, info.event);
                    },

                    // Lors du drag d'un événement, mise à jour de la variable currentEvent
                    eventDrop: info => {
                        const id = info.event['extendedProps']['data-id'] || info.event.id;
                        @this.eventChange(id, info.event);
                    },
                    // Lors du drag d'un événement, mise à jour de la variable currentEvent
                    eventReceive: info => {
                        const id = create_UUID();
                        const id_chantier = info.draggedEl.getAttribute('data-id-chantier');

                        // Récupération de la classe complète de l'élément dragué
                        const draggedElClass = info.draggedEl.className;
                        // Utilisation d'une expression régulière pour extraire la couleur
                        const colorRegex = /bg-(\w+)-500/;
                        const match = draggedElClass.match(colorRegex);

                        // Récupération de la couleur
                        const colorClass = match[0]; // Classe tailwinds pour background color

                        // Appliquer le style à l'événement
                        info.event.setProp('classNames', ['idChantierEvent' + id_chantier,
                            colorClass
                        ]);

                        // Ajout d'un attribut id-chantier à l'événement pour pouvoir le manipuler avant recharge de la page
                        info.event.setExtendedProp('data-id', id);

                        @this.eventAdd(info.event, id, id_chantier);
                    },
                    // Lors du clique sur un événement, affichage des informations sur le chantier
                    eventClick: info => {

                        var id = info.event.id;
                        var id_chantier = info.event['extendedProps']['id_chantier'];
                        // Récupération de l'id_chantier et id pour les événements créés par drag and drop avant rechargement de la page car null
                        if (id_chantier == null || id_chantier == undefined || id_chantier ==
                            '') {
                            // Récupération de la classe de l'événement
                            const eventClass = info.event.classNames.find(className => className
                                .startsWith('idChantierEvent'));

                            // Expression régulière pour rechercher le nombre dans la classe
                            const regex = /idChantierEvent(\d+)/;
                            const match = eventClass.match(regex);
                            // Récupération de l'id chantier
                            id_chantier = match[1];
                            id = info.event['extendedProps']['data-id'];
                        }
                        // Récupération de l'élément modal info
                        var modalInfo = document.getElementById('chantierModalInfo_' +
                            id_chantier);

                        // Fermeture de la modal si on clique en dehors
                        window.onclick = function(event) {
                            if (event.target == modalInfo) {
                                modalInfo.style.display = "none";
                            }
                        };
                        // Get the <span> element that closes the modal
                        var span = modalInfo.getElementsByClassName("close")[0];
                        // Fermeture de la modal quand clique de la croix
                        span.onclick = function() {
                            modalInfo.style.display = "none";
                        };

                        modalInfo.style.display = "block";

                        // Suppression de l'événement
                        var deleteEvent = document.querySelector("#deleteEvent_" + id_chantier);
                        deleteEvent.onclick = function() {
                            if (confirm("Voulez-vous vraiment supprimer cet événement ?")) {
                                info.event.remove();
                                @this.eventRemove(id);
                                modalInfo.style.display = "none";
                            }
                        };
                    },
                });
                calendar.render();
            });
        });

        // Fonction pour un id la création d'un évent
        function create_UUID() {
            let dt = new Date().getTime();
            const uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
                let r = (dt + Math.random() * 16) % 16 | 0;
                dt = Math.floor(dt / 16);
                return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });
            return uuid;
        }

        //[SPECGT10] pop up somme
        // Fonction pour afficher la boîte modale + ajoutez un événement au bouton pour ouvrir le pop-up
        var openPopupButton = document.getElementById("openPopupButton");
        var closePopupButton = document.getElementById("closePopupButton");
        var popupContainer = document.getElementById("popupContainer");

        if (openPopupButton && closePopupButton && popupContainer) {
            openPopupButton.addEventListener("click", function() {
                popupContainer.classList.add("show");
            });

            closePopupButton.addEventListener("click", function() {
                popupContainer.classList.remove("show");
            });
        }
        //FIN [SPECGT10] pop up somme

        // Début [SPECGT11] - Script pour la recherche par id
        document.getElementById('searchInput').addEventListener('keypress', function(event) {
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
                if (!matchFound) {
                    document.getElementById('noMatchMessage').style.display = 'block';
                } else {
                    document.getElementById('noMatchMessage').style.display = 'none';
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
            }
            // Hide the no match message
            document.getElementById('noMatchMessage').style.display = 'none';
        });
        // Fin [SPECGT11] - Script pour la recherche par id

        document.getElementById('searchHoursInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                fetch('/trouver-chantier/heures/' + e.target.value, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.id) {
                            generateSearchHoursModal(data);
                        }
                    })
                    .catch((e) => {
                        flashMe('warning', 'Aucun chantier trouvé.');
                    });
            }
        });

        document.getElementById('searchHoursValid').addEventListener('click', (e) => {
            const value = document.getElementById('searchHoursInput').value;
            if (value && value !== '') {
                fetch('/trouver-chantier/heures/' + value, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.id) {
                            generateSearchHoursModal(data);
                        }
                    })
                    .catch((e) => {
                        flashMe('warning', 'Aucun chantier trouvé.');
                    });
            }
        });

        document.getElementById('searchHoursCloseBtn').addEventListener('click', () => {
            searchHoursModal = document.getElementById('searchHoursModal');
            searchHoursModal.classList.add('hidden');
        });

        function generateSearchHoursModal(data) {
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
                        totalHours += time.hours_day + time.hours_night + time.hours_travel;
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

            searchHoursModal = document.getElementById('searchHoursModal');
            searchHoursModal.classList.remove('hidden');
        }

        function flashMe(icon, msg) {
            window.flashAlert(icon, msg);
        }
    </script>

    @vite('resources/js/heures/chantier.js')
    @vite('resources/js/heures/worksiteState.js')
    @vite('resources/js/heures/deleteWorksite.js')
    @vite('resources/js/heures/worksiteStaging.js')
@endpush
