@extends('layouts.app')

@section('content')
<div class="container">
    @php
    \Carbon\Carbon::setLocale('fr');
    @endphp

    <h2 class="mb-4">
        Planning du {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
    </h2>
    <form method="GET" action="{{ route('planning.jour') }}" class="mb-4">
        <input type="date" name="date" value="{{ $date }}" class="form-control d-inline w-auto" onchange="this.form.submit()">
    </form>

    <div class="row">
        @forelse($chantiers as $chantier)
        <div class="col-md-4 mb-3">
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#chantierModal{{ $chantier->id }}">
                {{ $chantier->nom }} ({{ $chantier->idAff }})
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="chantierModal{{ $chantier->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $chantier->nom }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <strong>Tâches réalisées :</strong>
                        <ul>
                            @foreach($chantier->taches as $tache)
                            <li>{{ $tache->libelle }}</li>
                            @endforeach
                        </ul>
                        <strong>Observations :</strong>
                        <p>{{ $chantier->observations }}</p>
                        <!-- Ajoute ici d'autres infos si besoin -->
                    </div>
                </div>
            </div>
        </div>
        @empty
        <p>Aucun chantier prévu pour ce jour.</p>
        @endforelse
    </div>
</div>
@endsection