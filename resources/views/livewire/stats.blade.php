<div class="max-h-[120vh] overflow-hidden">
    {{-- inputs --}}
    <section class="flex flex-row items-center justify-center gap-3">
        <label for="start" class="mb-0">Début</label>
        <input id="start" type="date" wire:model="start"
            class="p-2 border rounded-lg border-dark focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <label id="end" for="end" class="mb-0">Fin</label>
        <input id="end" type="date" wire:model="end"
            class="p-2 border rounded-lg border-dark focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                    <th colspan="1" class="!border !border-dark"></th>
                    <th class="px-4 py-2 text-center text-white align-middle border border-dark">Taux horaire</th>
                    <th class="px-4 py-2 text-center text-white align-middle border border-dark">Chiffre d'affaire</th>
                    <th class="px-4 py-2 text-center text-white align-middle border border-dark">Heures de production
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-blue-100">
                    <td class="!text-blue-500 border border-dark px-4 py-2 text-center align-middle">Chiffres Potentiels
                    </td>
                    <td id="tdAvgHourlyRate" class='text-center align-middle !text-blue-500 border border-dark'>
                        {{ $this->averageHourlyRate }}
                    </td>
                    <td class='text-center align-middle !text-blue-500 border border-dark'>{{ $this->potentialCA }}</td>
                    <td class='text-center align-middle !text-blue-500 border border-dark'>{{ $this->potentialHours }}
                    </td>
                </tr>
                <tr>
                    <td class="!text-red-500 border border-dark px-4 py-2 text-center align-middle">Chiffres au réel
                    </td>
                    <td class='text-center align-middle !text-red-500 border border-dark'>
                        {{ round($this->totalRevenue / $this->periodConsumedHours) }}
                    </td>
                    <td class='text-center align-middle !text-red-500 border border-dark'>{{ $this->totalRevenue }}</td>
                    <td class='text-center align-middle !text-red-500 border border-dark'>
                        {{ $this->periodConsumedHours }}</td>
                </tr>
                <tr class="bg-blue-100">
                    <td class="!text-red-500 border border-dark px-4 py-2 text-center align-middle">Total des heures
                        travaillées</td>
                    <td colspan="2" class="bg-gray-400"></td>
                    <td colspan="1" class="!text-red-500 border border-dark px-4 py-2 text-center align-middle">
                        {{ $this->periodHours }}
                    </td>
                </tr>
            </tbody>
        </table>
        {{-- Static global stats --}}
        <table class='m-3 border border-dark'>
            <thead class="text-white border-b border-black bg-slate-600">
                <th colspan="2" class="px-4 py-2 text-center align-middle">Montants totaux (sans les archives)</th>
            </thead>
            <tbody>
                <tr class="bg-blue-100">
                    <td class="text-center align-middle border border-dark">Fournitures</td>
                    <td class="text-center align-middle border border-dark"><strong>{{ $totalMaterialAmount }}</strong>
                        €</td>
                </tr>
                <tr>
                    <td class="text-center align-middle border border-dark">Mains d'oeuvres</td>
                    <td class="text-center align-middle border border-dark"><strong>{{ $totalServiceAmount }}</strong> €
                    </td>
                </tr>
                <tr class="bg-blue-100">
                    <td class="text-center align-middle border border-dark">Heures prévues</td>
                    <td class="text-center align-middle border border-dark"><strong>{{ $totalHoursScheduled }}</strong>
                        heures</td>
                </tr>
                <tr>
                    <td class="text-center align-middle border border-dark">Heures affectées</td>
                    <td class="text-center align-middle border border-dark"><strong>{{ $totalHoursDone }}</strong>
                        heures</td>
                </tr>
            </tbody>
        </table>
        {{-- Static worksites stats --}}
        <table class='m-3 border border-dark'>
            <thead class="text-white border-b border-black bg-slate-600">
                <th class="px-4 py-2 text-center align-middle">Chantiers</th>
                <th class="px-4 py-2 text-center align-middle">Total fournitures</th>
                <th class="px-4 py-2 text-center align-middle">Total main d'oeuvres</th>
                <th class="px-4 py-2 text-center align-middle">Total heures prévues</th>
                <th class="px-4 py-2 text-center align-middle">Total chantiers</th>
            </thead>
            <tbody>
                @php
                    // Définition des types de chantier et des données associées
                    $workSitesData = [
                        'Démarrés' => [
                            'totals' => $startedWorkSitesTotals,
                            'count' => count($startedWorkSites),
                        ],
                        'Prévisionnels' => [
                            'totals' => $upcomingWorkSitesTotals,
                            'count' => count($upcomingWorkSites),
                        ],
                        'À facturer' => [
                            'totals' => $toBillWorkSitesTotals,
                            'count' => count($toBillWorkSites),
                        ],
                    ];
                @endphp
                @foreach ($workSitesData as $status => $data)
                    <tr class="{{ $loop->index % 2 == 0 ? 'bg-blue-100' : '' }}">
                        <td class="text-center align-middle border border-dark"><strong>{{ $status }}</strong>
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
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    {{-- Datas --}}
    <section class="overflow-y-auto border border-dark max-h-[500px] m-6">
        <table class="w-full m-0 border border-collapse table-auto border-dark">
            <thead class="sticky top-0 z-10 bg-white">
                <tr>
                    <th colspan="11" class="text-center align-middle">
                        <strong>
                            Début {{ $this->startObject->format('d/m/Y') }} - Fin
                            {{ $this->endObject->format('d/m/Y') }}
                        </strong>
                    </th>
                </tr>
                <tr class="text-2xl text-center text-white bg-slate-600">
                    <th class="px-2 py-2 text-center border border-dark">Affaire</th>
                    <th class="py-2 text-center border border-dark">IdAff</th>
                    <th></th>
                    <th class="py-2 text-center border border-dark">Heures imputées</th>
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
                        class="text-center {{ $loop->index % 2 == 0 ? 'bg-blue-100' : '' }}">
                        <td class="py-2 border border-dark">{{ $worksite->title }}</td>
                        <td class="px-4 py-2 border border-dark">{{ $worksite->id }}</td>
                        <td></td>
                        <td class="px-4 py-2 text-red-500 border border-dark">{{ $worksite->periodProductiveHours }}
                        </td>
                        <td class="px-4 py-2 text-red-500 border border-dark">{{ intval($worksite->moe) }}</td>
                        <td class="px-4 py-2 text-red-500 border border-dark">
                            {{ round(intval($worksite->moe) / $worksite->periodProductiveHours) }}</td>
                        <td></td>
                        <td class="px-4 py-2 border border-dark">{{ $worksite->progress }}</td>
                        <td class="px-4 py-2 border border-dark">{{ $worksite->totalConsumedHours }}</td>
                        <td id={{ $worksite->id }} class="px-4 py-2 border hoursCell border-dark">
                            {{ $worksite->revised_hours }}</td>
                        <td id={{ $worksite->id }} class="px-4 py-2 border serviceAmountCell border-dark">
                            {{ intval($worksite->serviceamount) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="11" class="bg-gray-400"></td>
                </tr>
                <tr class="text-center">
                    <td colspan="2" class="px-4 py-2 border border-dark">Hors production</td>
                    <td></td>
                    <td class="px-4 py-2 border border-dark">{{ $this->periodUnproductiveHours }}</td>
                    <td colspan="8" class="px-4 py-2 bg-gray-400 border border-dark"></td>
                </tr>
                <tr>
                    <td colspan="11" class="bg-gray-400"></td>
                </tr>
                <tr class="text-center">
                    <td colspan="2" class="px-4 py-2 border border-dark">Total hors production</td>
                    <td></td>
                    <td class="px-4 py-2 border border-dark">{{ $this->globalUnproductiveHours }}</td>
                    <td colspan="8" class="px-4 py-2 bg-gray-400 border border-dark"></td>
                </tr>
            </tbody>
        </table>
    </section>
</div>

@push('stats')
    @vite('resources/js/chantier/statistics.js')
@endpush
