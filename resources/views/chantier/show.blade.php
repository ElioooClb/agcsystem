<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight title_page">
            {{$chantier->title}}
        </h2>
    </x-slot>


    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="name_teckos_chantier">
                    <p>nom technicien(s) affecté(s)</p>
                    @foreach ($chantiersUsers as $item)
                    <p>- {{$item->name}}</p>
                    @endforeach
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table id="example" class="table table-hover display  pb-30">
                                    <thead>
                                        <tr>
                                            <th>Nom technicien</th>
                                            <th>Date </th>
                                            <th>Heure de jour </th>
                                            <th>Heures de nuit </th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Nom technicien</th>
                                            <th>Date </th>
                                            <th>Heure de jour</th>
                                            <th>Heures de nuit</th>

                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        
                                        @foreach ($chantiersUsers as $item)
                                            @php
                                                $prevName = null;
                                            @endphp
                                            @foreach($time as $t)
                                                <tr>
                                                    @if($t->user_id === $item->id)
                                                        @if($prevName === $item->name)
                                                            <td scope="row"></td>
                                                        @else
                                                            <td scope="row">{{$item->name}}</td>
                                                            @php
                                                                $prevName = $item->name;
                                                            @endphp
                                                        @endif
							<!-- [TR6GT4] Heures imputees voir chantier tableau des dates  --> 
                                                        <td scope="row">{{ $t->date }}</td>
							<!-- [TR6GT4] Heures imputees voir-chantier tableau des dates  --> 
                                                        @if($t->hours_day > 0)
                                                            <td> {{ $t->hours_day }} H</td>
                                                        @else
                                                            <td>0 H</td>
                                                        @endif
                                                        @if($t->hours_night > 0)
                                                            <td>{{ $t->hours_night }} H</td>
                                                        @else
                                                            <td>0 H</td>
                                                        @endif
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- <p>Nom du chantier : {{$chantier->title}}</p> --}}
                    <!-- [SPECGT1] -->
                    <p>IdAff_{{$chantier->id}}</p>
                    <p>Temps prévu au devis: {{$chantier->hours}} H</p>
                    @if($chantier->materialamount != null)
                        <p>Montant matériel : {{$chantier->materialamount}} €</p>
                    @endif
                    @if($chantier->serviceamount != null)
                        <p>Montant service : {{$chantier->serviceamount}} €</p>
                    @endif
                    <p>Date de réalisation : {{$chantier->created_at}}</p>

                        
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <p>Temps réellement passé sur le chantier : {{ $chantier->totalTime }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</x-app-layout>
