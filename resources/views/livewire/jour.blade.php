@extends('layouts.app')

@section('content')
<div class="container">
    @php
    \Carbon\Carbon::setLocale('fr');
    @endphp

    <h2 class="mb-4">
        Planning du {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
    </h2>
    <div class="d-flex justify-content-between align-items-center mb-3">
        @php
        $prevDate = \Carbon\Carbon::parse($date)->subDay()->toDateString();
        $nextDate = \Carbon\Carbon::parse($date)->addDay()->toDateString();
        @endphp

        <a href="{{ route('planning.jour', ['realisation_date' => $prevDate]) }}" class="btn btn-outline-secondary">
            ← Jour précédent
        </a>

        <a href="{{ route('planning.jour', ['realisation_date' => $nextDate]) }}" class="btn btn-outline-secondary">
            Jour suivant →
        </a>
    </div>

    <form method="GET" action="{{ route('planning.jour') }}" class="mb-4">
        <input type="date" name="realisation_date" value="{{ $date }}" class="form-control d-inline w-auto" onchange="this.form.submit()">
    </form>

    <div class="row">
        <p class="text-muted">
            Chantiers trouvés : {{ $chantiers->count() }}
        </p>
        @forelse($chantiers as $chantier)
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card border-3 h-100 shadow-sm" style="border-left: 8px solid {{ $chantier->color ?? '#888' }}">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title">
                            <strong>{{ $chantier->title }}</strong>
                        </h5>

                        <span class="badge mb-2" style="background-color: {{ $chantier->color }}; color: white;">
                            {{ $chantier->type() }}
                        </span>

                        <p class="card-text mb-1">
                            <strong>Commencé le :</strong> {{ \Carbon\Carbon::parse($chantier->realisation_date)->format('d/m/Y') }}
                        </p>
                        <p class="card-text mb-2">
                            <strong>Identifiant :</strong> {{ $chantier->getIdAff() }}
                        </p>
                    </div>

                    <button class="btn btn-outline-primary mt-auto" data-bs-toggle="modal" data-bs-target="#chantierModal{{ $chantier->id }}">
                        Détails du chantier
                    </button>
                </div>
            </div>
        </div>



        <!-- Modal -->
        <div class="modal fade" id="chantierModal{{ $chantier->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $chantier->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <strong>Tâches réalisées :</strong>
                        @if($chantier->taches && $chantier->taches->isNotEmpty())
                        <ul>
                            @foreach($chantier->taches as $tache)
                            <li>{{ $tache->libelle }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p>Aucune tâche enregistrée.</p>
                        @endif

                        <strong>Observations :</strong>
                        <p>{{ $chantier->observation ?? 'Aucune.' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <p>Aucun chantier prévu pour ce jour.</p>
        @endforelse
    </div>

    @endsection