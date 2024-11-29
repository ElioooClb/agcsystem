<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (Auth::user()->role_id === 1)
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Utilisateur</h5>
                                        <p class="card-text">Visualiser, créer, supprimer des Utilisateurs.</p>
                                        <a href="{{ route('users.index') }}" class="btn btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Liste des chantiers</h5>
                                        <p class="card-text">Visualiser, créer, supprimer des chantiers.</p>
                                        <a href="{{ route('chantier.index') }}" class="btn btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Liste des heures des techniciens</h5>
                                        <p class="card-text">Voir toutes les heures de tous les techniciens.</p>
                                        <a href="{{ route('tekosTime.index') }}" class="btn btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">messages</h5>
                                        <p class="card-text">Messages à diffuser sur le mode démo (tv couloir)</p>
                                        <a href="{{ route('message.index') }}" class="btn btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Historique heures des techniciens supprimés</h5>
                                        <p class="card-text">Voir toutes les heures de tous les techniciens.</p>
                                        <a href="{{ route('tekosTime.dist') }}" class="btn btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Gestion des paramètres des chantiers</h5>
                                        <p class="card-text">Voir les paramètres pour les chantiers et les modèles.</p>
                                        <a href="{{ route('parameters.manage') }}" class="btn btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class='row'>
                            @if (Auth::user()->fonction === 'Président')
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Pilotage</h5>
                                            <p class="card-text">Piloter avec l'affichage de données.</p>
                                            <a href="{{ route('chantier.statistiques') }}"
                                                class="btn btn-primary">Accéder</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Gestion des emails de notifications</h5>
                                            <p class="card-text">Voir et modifier les emails de notification</p>
                                            <a href="{{ route('email-settings') }}" class="btn btn-primary">Accéder</a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Mes heures</h5>
                                        <p class="card-text">With supporting text below as a natural lead-in to
                                            additional content.</p>
                                        <a href="#" class="btn btn-primary">Go somewhere</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Special title treatment</h5>
                                        <p class="card-text">With supporting text below as a natural lead-in to
                                            additional content.</p>
                                        <a href="#" class="btn btn-primary">Go somewhere</a>
                                    </div>
                                </div>
                            </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
