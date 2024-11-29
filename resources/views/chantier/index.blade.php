<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Gestion des chantiers
        </h2>
    </x-slot>

    <!--/Preloader-->
    <div class="wrapper box-layout theme-1-active pimary-color-blue">
        <!-- Main Content -->
        <div class="container-fluid">
            <div class="col-sm-12">
                <div class="p-3 panel panel-default">
                    {{-- DEBUT - [SPECGT9] - Ajout des inputs facturer et en cours --}}
                    <div class="gap-3 archive">
                        <input type="checkbox" id="checkArchived" name="btnArchived">
                        <label for="btnArchived">Archivé</label>
                        <input type="checkbox" id="checkToBill" name="btnToBill">
                        <label for="btnToBill">A facturer</label>
                        <input type="checkbox" id="checkInProgress" name="btnInProgress">
                        <label for="btnInProgress">En cours</label>
                    </div>
                    {{-- FIN - [SPECGT9] - Ajout des inputs facturer et en cours --}}
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="table-wrap">
                                <div class="table-responsive">
                                    <table id="example" class="table table-hover display pb-30">
                                        <thead>
                                            <tr>
                                                <!-- [SPECGT1] -->
                                                <th>IdAff</th>
                                                <th>Chantier</th>
                                                <th>Numéro de facture</th>
                                                <th>Temps Devis</th>
                                                <th>Temps Passé</th>
                                                <th>Montant Matériel</th>
                                                <th>Montant Service</th>
                                                <th>Date de Réalisation</th>
                                                <th class="no-export">Action</th>

                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <!-- [SPECGT1] -->
                                                <th>IdAff</th>
                                                <th>Chantier</th>
                                                <th>Numéro de facture</th>
                                                <th>Temps Devis</th>
                                                <th>Temps Passé</th>
                                                <th>Montant Matériel</th>
                                                <th>Montant Service</th>
                                                <th>Date de Réalisation</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            {{-- DEBUT - [SPECGT9] - Action pour gérer l'état du chantier --}}
                                            @foreach ($chantiers as $item)
                                                @php
                                                    $isLucrativeCSS = '';
                                                    if ($item->states->status === 'archived') {
                                                        if ($item->totalHours > $item->hours) {
                                                            $isLucrativeCSS = 'bg-danger text-white';
                                                        } elseif ($item->totalHours == $item->hours) {
                                                            $isLucrativeCSS = 'bg-warning text-black';
                                                        } else {
                                                            $isLucrativeCSS = 'bg-success text-white';
                                                        }
                                                    }
                                                @endphp
                                                <tr class="{{ $isLucrativeCSS }}" id={{ $item->id }}
                                                    data-totalhours="{{ $item->totalHours }}"
                                                    data-hours="{{ $item->hours }}"
                                                    data-status="{{ $item->states->status_group }}"
                                                    data-visible="{{ $item->visible }}">
                                                    <!-- [SPECGT1] -->
                                                    <td>idAff_{{ $item->id }}</td>
                                                    <td>{{ $item->title }}</td>
                                                    <td>{{ $item->invoices && $item->invoices->number ? $item->invoices->number : '------' }}
                                                    </td>
                                                    <td>{{ $item->hours ? $item->hours : '------' }}</td>
                                                    <td>{{ $item->formattedEstimatedTotalHours }}</td>
                                                    <td>{{ $item->formattedEstimatedMaterialAmount }}</td>
                                                    <td>{{ $item->formattedEstimatedServicesAmount }}</td>
                                                    <td>{{ $item->realisation_date ? $item->realisation_date : '------' }}
                                                    </td>
                                                    <td>
                                                        <menu class="actions">
                                                            <a class="btn btn-primary"
                                                                href="{{ route('chantier.show', $item) }}">Voir</a>
                                                            {{-- @if ($item->states->status === 'archived')
                                                                <a class="btn btn-primary onState" href="#"
                                                                    data-id="{{ $item->id }}"
                                                                    data-target-state="pendingArchiving"
                                                                    data-action="downgrade">Désarchiver</a>
                                                            @elseif ($item->states->status === 'pendingArchiving')
                                                                <a class="btn btn-primary onState" href="#"
                                                                    data-id="{{ $item->id }}"
                                                                    data-target-state="archived"
                                                                    data-action="update">Archiver</a>
                                                            @endif --}}
                                                        </menu>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            {{-- FIN - [SPECGT9] - Action pour gérer l'état du chantier --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Main Content -->
    </div>
    <!-- /#wrapper -->
    @push('table')
        @vite('resources/js/chantier/workSite.js')
    @endpush
</x-app-layout>
