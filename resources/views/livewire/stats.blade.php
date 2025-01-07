<div class="max-h-[120vh] overflow-hidden">
    {{-- inputs --}}
    <section class="flex flex-row items-center justify-center gap-3">
        <label for="start" class="mb-0 start-label">Début</label>
        {{-- HTML for website --}}
        <input id="start" type="date" wire:model="start"
            class="p-2 border rounded-lg border-dark focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 start-date-input">
        {{-- HTML for old TV --}}
        <input id="start-fallback" type="text" wire:model="start" placeholder="jj/mm/aaaa"
            class="hidden p-2 border rounded-lg border-dark start-fallback-input">

        <label id="end" for="end" class="mb-0 end-label">Fin</label>
        {{-- HTML for website --}}
        <input id="end" type="date" wire:model="end"
            class="p-2 border rounded-lg border-dark focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 end-date-input">
        {{-- HTML for old TV --}}
        <input id="end-fallback" type="text" wire:model="end" placeholder="jj/mm/aaaa"
            class="hidden p-2 border rounded-lg border-dark end-fallback-input">

        <button
            class="p-2 text-white bg-blue-500 rounded-lg btn btn-primary hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400"
            wire:click="handleGenerateStats">
            Générer les stats
        </button>
    </section>
    <hr class="mt-10 mb-10">
    {{-- small tables --}}
    <section class="flex justify-center mb-3">
        {{-- Potentiels and Real --}}
        <table class='my-3 border border-dark'>
            <thead class="border-b border-black bg-slate-600">
                <tr>
                    <th class="px-4 py-2 text-center text-white align-middle border border-dark"><strong>Production
                            estimée</strong></th>
                    <th class="px-4 py-2 text-center text-white align-middle border border-dark">Taux horaire</th>
                    <th class="px-4 py-2 text-center text-white align-middle border border-dark">Valeur</th>
                    <th class="px-4 py-2 text-center text-white align-middle border border-dark">H
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-blue-100">
                    <td class="!text-blue-500 border border-dark px-4 py-2 text-center align-middle">Potentiels
                    </td>
                    <td id="tdAvgHourlyRate"
                        class="text-center align-middle !text-blue-500 border border-dark cursor-pointer hover:bg-blue-200">
                        {{ $this->averageHourlyRate }} €/heure
                    </td>
                    <td class='text-center align-middle !text-blue-500 border border-dark'>{{ $this->potentialCA }} €
                    </td>
                    <td class='text-center align-middle !text-blue-500 border border-dark'>{{ $this->potentialHours }}
                        heures
                    </td>
                </tr>
                <tr>
                    <td class="!text-red-500 border border-dark px-4 py-2 text-center align-middle">Réalisée
                    </td>
                    <td class='text-center align-middle !text-red-500 border border-dark'>
                        {{ round($this->totalRevenue / $this->periodConsumedHours) }} €/heure
                    </td>
                    <td class='text-center align-middle !text-red-500 border border-dark'>{{ $this->totalRevenue }} €
                    </td>
                    <td class='text-center align-middle !text-red-500 border border-dark'>
                        {{ $this->periodConsumedHours }} heures</td>
                </tr>
                <tr class="bg-blue-100">
                    <td class="!text-red-500 border border-dark px-4 py-2 text-center align-middle">Total des heures
                        travaillées</td>
                    <td colspan="2" class="bg-gray-400"></td>
                    <td colspan="1" class="!text-red-500 border border-dark px-4 py-2 text-center align-middle">
                        {{ $this->periodHours }} heures
                    </td>
                </tr>
            </tbody>
        </table>
        {{-- Static global stats --}}
        <table class='m-3 border border-dark'>
            <thead class="text-white border-b border-black bg-slate-600">
                <th colspan="2" class="px-4 py-2 text-center align-middle">Affaires en cours</th>
            </thead>
            <tbody>
                <tr class="bg-blue-100">
                    <td class="px-4 text-center align-middle border border-dark">Fournitures</td>
                    <td class="px-4 text-center align-middle border border-dark">
                        <strong>{{ $totalMaterialAmount }}</strong>
                        €</td>
                </tr>
                <tr>
                    <td class="px-4 text-center align-middle border border-dark">Mains d'oeuvres</td>
                    <td class="px-4 py-2 text-center align-middle border border-dark">
                        <strong>{{ $totalServiceAmount }}</strong> €
                    </td>
                </tr>
                <tr class="bg-blue-100">
                    <td class="px-4 text-center align-middle border border-dark">Stock d'heures</td>
                    <td class="px-4 text-center align-middle border border-dark">
                        <strong>{{ $totalHoursDone * $this->averageHourlyRate }}</strong>
                        €</td>
                </tr>
                <tr>
                    <td class="px-4 text-center align-middle border border-dark">Heures affectées</td>
                    <td class="px-4 text-center align-middle border border-dark"><strong>{{ $totalHoursDone }}</strong>
                        heures</td>
                </tr>
            </tbody>
        </table>
        {{-- Static worksites stats --}}
        <table class='m-3 border border-dark'>
            <thead class="text-white border-b border-black bg-slate-600">
                <th class="px-4 py-2 text-center align-middle">Activité</th>
                <th class="px-4 py-2 text-center align-middle">Fournitures</th>
                <th class="px-4 py-2 text-center align-middle">MOE devis</th>
                <th class="px-4 py-2 text-center align-middle">H prévues</th>
                <th class="px-4 py-2 text-center align-middle">Quantité</th>
                <th class="px-4 py-2 text-center align-middle">Reste (h)</th>
            </thead>
            <tbody>
                @php
                    // Définition des types de chantier et des données associées
                    $workSitesData = [
                        'Démarrés' => [
                            'totals' => $startedWorkSitesTotals,
                            'count' => count($startedWorkSites),
                            'rest' => $restInProgress,
                        ],
                        'Prévisionnels' => [
                            'totals' => $upcomingWorkSitesTotals,
                            'count' => count($upcomingWorkSites),
                            'rest' => $restUpcoming,
                        ],
                        'À facturer' => [
                            'totals' => $toBillWorkSitesTotals,
                            'count' => count($toBillWorkSites),
                            'rest' => $restToBill,
                        ],
                    ];
                @endphp
                @foreach ($workSitesData as $status => $data)
                    <tr class="{{ $loop->index % 2 == 0 ? 'bg-blue-100' : '' }}">
                        <td class="px-2 text-center align-middle border border-dark">
                            <strong>{{ $status }}</strong>
                        </td>
                        <td class="text-center align-middle border border-dark">
                            {{ !empty($data['totals']['totalMaterialAmount']) ? $data['totals']['totalMaterialAmount'] . ' €' : '-' }}
                        </td>
                        <td class="text-center align-middle border border-dark">
                            {{ !empty($data['totals']['totalServiceAmount']) ? $data['totals']['totalServiceAmount'] . ' €' : '-' }}
                        </td>
                        <td class="text-center align-middle border border-dark">
                            {!! !empty($data['totals']['totalHoursScheduled'])
                                ? ($status == 'À facturer'
                                    ? '<strong>' . $data['totals']['totalHoursScheduled'] . '</strong>' . ' heures'
                                    : $data['totals']['totalHoursScheduled'] . ' heures')
                                : '-' !!}
                        </td>
                        <td class="text-center align-middle border border-dark">{{ $data['count'] }}</td>
                        <td class="text-center align-middle border border-dark">
                            {!! !empty($data['rest'])
                                ? ($status == 'À facturer'
                                    ? '<strong>' . $data['rest'] . '</strong>' . ' heures'
                                    : $data['rest'] . ' heures')
                                : '-' !!}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    {{-- Datas --}}
    <section class="overflow-y-auto border border-dark max-h-[500px] m-6">
        <table id='worksitesTable' class="w-full m-0 border border-collapse table-auto border-dark">
            <thead class="sticky top-0 z-10 bg-white">
                <tr>
                    <th>
                        <div class="flex items-center m-1 space-x-2">
                            <input type="checkbox" id="toggleArchived"
                                class="w-5 h-5 text-blue-500 cursor-pointer form-checkbox hover:ring-2 hover:ring-blue-300" />
                            <label for="toggleArchived"
                                class="m-0 text-sm font-medium leading-none text-gray-700 cursor-pointer ps-2 hover:text-blue-500">
                                Cacher les chantiers archivés
                            </label>
                        </div>
                    </th>
                    <th colspan="10" class="text-center align-middle">
                        <strong>
                            Début {{ $this->startObject->format('d/m/Y') }} - Fin
                            {{ $this->endObject->format('d/m/Y') }}
                        </strong>
                    </th>
                </tr>
                <tr class="text-2xl text-center text-white bg-slate-600">
                    <th id="businessID"
                        class="px-2 py-2 text-center border cursor-pointer border-dark hover:bg-slate-500">
                        <i id="statusIcon" class="me-2"></i> <!-- Icône dynamique -->
                        Affaire
                    </th>
                    <th class="py-2 text-center border border-dark">IdAff</th>
                    <th></th>
                    <th class="py-2 text-center border border-dark">H imputées</th>
                    <th class="py-2 text-center border border-dark">Production (euros)</th>
                    <th class="py-2 text-center border border-dark">Taux horaire</th>
                    <th></th>
                    <th class="py-2 text-center border border-dark">% réalisé</th>
                    <th class="py-2 text-center border border-dark">H totales passées</th>
                    <th class="py-2 text-center border border-dark">H révisées</th>
                    <th class="py-2 text-center border border-dark">MOE devis</th>
                </tr>
            </thead>
            <tfoot class="sticky bottom-0 p-0 m-0 text-2xl text-white bg-slate-600">
                <tr>
                    <td colspan="11" class="bg-gray-400"></td>
                </tr>
                <tr class="text-center">
                    <td colspan="2" class="px-4 py-2 border border-dark">Heures de production non imputées sur
                        affaire</td>
                    <td></td>
                    <td class="px-4 py-2 border border-dark">{{ $this->periodUnproductiveHours }}</td>
                    <td colspan="1" class="px-4 py-2 bg-gray-400 border border-dark"></td>
                    <td colspan="2" class="px-4 py-2 border border-dark">Heures d'interventions non facturés</td>
                    <td class="px-4 py-2 border border-dark">{{ $this->UnbillableHours }}</td>
                    <td colspan="5" class="px-4 py-2 bg-gray-400 border border-dark"></td>
                </tr>
                <tr>
                    <td colspan="11" class="bg-gray-400"></td>
                </tr>
                <tr class="text-center">
                    <td colspan="2" class="px-4 py-2 border border-dark">Total des heures travaillées hors
                        production</td>
                    <td></td>
                    <td class="px-4 py-2 border border-dark">{{ $this->globalUnproductiveHours }}</td>
                    <td colspan="8" class="px-4 py-2 bg-gray-400 border border-dark"></td>
                </tr>
                <tr class="text-center">
                    <td colspan="2" class="px-4 py-2 border border-dark">Total des fournitures facturées sur la
                        période</td>
                    <td></td>
                    <td colspan="1" class="px-4 py-2 bg-gray-400 border border-dark"></td>
                    <td class="px-4 py-2 border border-dark">{{ $this->periodMaterialAmount }}</td>
                    <td colspan="6" class="px-4 py-2 bg-gray-400 border border-dark"></td>
                </tr>
                <tr>
                    <td colspan="2" class="px-4 py-2 border border-dark text-end">Total :</td>
                    <td></td>
                    <td class="px-4 py-2 font-bold text-center border border-dark">
                        {{ $this->periodConsumedHours }}</td>
                    <td class="px-4 py-2 font-bold text-center border border-dark">
                        {{ $this->totalRevenue }}</td>
                    <td colspan="7" class="px-4 py-2 border bg-slate-600 border-dark"></td>
                </tr>
            </tfoot>
            <tbody class='overflow-auto'>
                @foreach ($this->worksites as $worksite)
                    <tr wire:key='worksite-{{ $worksite->id }}'
                        class="text-center {{ $loop->index % 2 == 0 ? 'bg-blue-100' : '' }}"
                        data-status="{{ $worksite->states->status }}"
                        data-status-group="{{ $worksite->states->status_group }}">
                        <td class="py-2 border border-dark">
                            <div class="flex items-center justify-between gap-2 mx-2">
                                @if ($worksite->states->status_group === 'archived')
                                    <i class="text-slate-500 fas fa-archive"></i>
                                @elseif ($worksite->states->status_group === 'inProgress')
                                    <i class="text-green-500 fas fa-sync-alt"></i>
                                @elseif ($worksite->states->status_group === 'toBill')
                                    <i class="text-red-500 fas fa-file-invoice-dollar"></i>
                                @else
                                    <i class="text-blue-500 fas fa-calendar-day"></i>
                                @endif
                                <span>{{ $worksite->title }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2 border border-dark">{{ $worksite->id }}</td>
                        <td></td>
                        <td class="px-4 py-2 border border-dark">
                            <span class="text-red-500">{{ $worksite->periodProductiveHours }}</span> heures
                        </td>
                        <td class="px-4 py-2 border border-dark">
                            <span class="text-red-500">{{ intval($worksite->moe) }}</span> €
                        </td>
                        <td class="px-4 py-2 border border-dark">
                            <span
                                class="text-red-500">{{ round(intval($worksite->moe) / $worksite->periodProductiveHours) }}</span>
                            €/heures
                        </td>
                        <td></td>
                        <td class="px-4 py-2 border border-dark">{{ $worksite->progress }} %</td>
                        <td class="px-4 py-2 border border-dark">{{ $worksite->totalConsumedHours }} heures</td>
                        <td id="{{ $worksite->id }}"
                            class="px-4 py-2 border cursor-pointer hoursCell border-dark hover:bg-blue-200">
                            {{ $worksite->revised_hours }} heures
                        </td>
                        <td id="{{ $worksite->id }}"
                            class="px-4 py-2 border cursor-pointer serviceAmountCell border-dark hover:bg-blue-200">
                            {{ intval($worksite->serviceamount) }} €
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </section>
</div>

@push('stats')
    @vite('resources/js/chantier/statistics.js')
@endpush
