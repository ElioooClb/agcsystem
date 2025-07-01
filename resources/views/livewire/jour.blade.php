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
    @auth
    @if(auth()->user()->role === 'Admin') {{-- ou ->is_admin selon ta DB --}}
    <form method="GET" action="{{ route('chantier.create') }}" class="mb-4 d-flex gap-2 align-items-center">
        <input type="hidden" name="realisation_date" value="{{ $date }}">
        <button type="submit" class="btn btn-primary">
            ➕ Créer un chantier à cette date
        </button>
    </form>
    @endif
    @endauth


    <!-- Nouvelle grille de cartes Tailwind -->
    <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        @forelse($chantiers as $chantier)
        <div class="rounded-2xl shadow-lg border-l-8 p-5 bg-white"
            style="border-color: {{ $chantier->color ?? '#888' }}">
            <h3 class="text-xl font-bold mb-2">{{ $chantier->title }}</h3>

            <span class="inline-block text-white text-sm font-semibold px-3 py-1 rounded mb-3"
                style="background-color: {{ $chantier->color ?? '#888' }}">
                {{ $chantier->type() }}
            </span>

            <p class="text-sm mb-1">
                <strong>Commencé le :</strong> {{ \Carbon\Carbon::parse($chantier->realisation_date)->format('d/m/Y') }}
            </p>

            <p class="text-sm mb-2">
                <strong>Identifiant :</strong> {{ $chantier->getIdAff() }}
            </p>

            <div class="mb-2">
                <strong class="block">Tâches :</strong>
                @if($chantier->taches && $chantier->taches->isNotEmpty())
                <ul class="list-disc list-inside text-sm text-gray-700">
                    @foreach($chantier->taches as $tache)
                    <li>{{ $tache->libelle }}</li>
                    @endforeach
                </ul>
                @else
                <p class="text-sm text-gray-500 italic">Aucune tâche enregistrée.</p>
                @endif
            </div>

            <div>
                <strong class="block">Observations :</strong>
                <p class="text-sm text-gray-700">{{ $chantier->observation ?? 'Aucune.' }}</p>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-500">Aucun chantier prévu pour ce jour.</p>
        @endforelse
    </div>
</div>
@endsection