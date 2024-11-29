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
    {{-- small table --}}
    <section class="flex justify-center mb-3">
        <table class='my-3 border border-dark'>
            <thead class="border-b border-black">
                <tr>
                    <th colspan="1" class="bg-gray-400 !border !border-dark"></th>
                    <th class="text-center align-middle border y-2 textpx-4 border-dark">Taux horaire</th>
                    <th class="px-4 py-2 text-center align-middle border border-dark">Chiffre d'affaire</th>
                    <th class="px-4 py-2 text-center align-middle border border-dark">Heures de production</th>
                </tr>
            </thead>
            <tbody>
                <tr class="">
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
                    <td class='text-center align-middle !text-red-500 border border-dark'>{{ round($this->totalRevenue / $this->periodConsumedHours) }}
                    </td>
                    <td class='text-center align-middle !text-red-500 border border-dark'>{{ $this->totalRevenue }}</td>
                    <td class='text-center align-middle !text-red-500 border border-dark'>
                        {{ $this->periodConsumedHours }}</td>
                </tr>
                <tr>
                    <td class="!text-red-500 border border-dark px-4 py-2 text-center align-middle">Saisies</td>
                    <td colspan="2" class="bg-gray-400"></td>
                    <td colspan="1" class="!text-red-500 border border-dark px-4 py-2 text-center align-middle">
                        {{ $this->periodHours }}
                    </td>
                </tr>
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
                        <td class="px-4 py-2 text-red-500 border border-dark">{{ round(intval($worksite->moe) / $worksite->periodProductiveHours) }}</td>
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
