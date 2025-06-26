{{-- Page de la création d'un nouveau chantier --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Création d'un nouveau chantier
        </h2>
    </x-slot>

    <div class="py-12 py_create">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="container mt-5">
                        {{-- Formulaire pour la création d'un chantier pointant vers la route en lien  --}}
                        <form method="post" action="{{ route('chantier.store') }}" enctype="multipart/form-data">
                            @csrf
                            {{-- Nom --}}
                            <div class="form-group">
                                <label>Nom chantier *</label>
                                <input type="text" class="form-control form_create" name="title" id="title"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="realisation_date" class="form-label">Date de réalisation</label>
                                <input type="date" class="form-control" id="realisation_date" name="realisation_date"
                                    value="{{ old('realisation_date', $realisation_date ?? '') }}" required>
                            </div>
                            {{-- Temps devis --}}
                            <div class="form-group">
                                <label>Temps de travail prévu * :</label>
                                <input type="float" class="form-control form_create" name="hours" id="hours">
                            </div>
                            <hr>
                            {{-- Liste Teckos Dispo --}}

                            <p class="mt-4">Techniciens Disponibles : </p>
                            <select multiple="multiple" name="user_id[]"
                                class="block w-full rounded-md selected-users font-2xl">
                                @foreach ($users as $user)
                                @if ($user->email != null)
                                <option value="{{ $user->id }}" class="text-2xl">
                                    {{ $user->name }}
                                </option>
                                @endif
                                @endforeach
                            </select>
                            <button
                                class="px-4 py-2 mt-2 font-bold text-white bg-blue-500 rounded-full btn-assign hover:bg-blue-700"
                                type="button">Ajouter Technicien
                            </button>
                            <hr>

                            {{-- Liste Teckos Chantiers --}}
                            <p class="mt-4 listeTeck">Liste des techniciens affectés : </p>
                            <ul class="ulListeTeck">

                            </ul>
                            <hr>
                            <input type="hidden" name="assigned_users" id="assignedUsersInput">

                            {{-- Cout Chantier --}}
                            <!-- debut [SPECGT7] obligatoire de remplir ces deux champs en plus du titre avec un message l'indiquant
                            + pas de rechargement de la page avec suppression des champs d�j� remplis-->
                            <div class="form-group" id="amount">
                                <label>Montant matériel * :</label>
                                <input type="float" class="form-control form_create" name="materialamount"
                                    class="amount" required>
                                <label>Montant Service * :</label>
                                <input type="float" class="form-control form_create" name="serviceamount"
                                    class="amount" required>
                            </div>
                            <!-- fin [SPECGT7] -->
                            <hr>
                            {{-- Options Chantiers --}}
                            {{-- Début [SPECGT6] Ajout de la sélection d'un modèle de paramètres --}}
                            <div class="form-group">
                                <label>Modèle *</label>
                                <select name="loadout_id" class="form-control form_create" required>
                                    <option class="text-md" value="" selected disabled>Veuillez sélectionner un modèle</option>
                                    @foreach ($loadouts as $loadout)
                                    @php
                                    $parameters = $loadout->parameters->pluck('label')->join(', ');
                                    @endphp
                                    <option class="text-md" value="{{ $loadout->id }}" title="{{ $parameters }}">
                                        {{ $loadout->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- FIn [SPECGT6] Ajout de la sélection d'un modèle de paramètres --}}
                            <hr>

                            {{-- Couleur pour planning --}}
                            <div class="relative inline-block form-group">
                                <label>Type : </label>
                                <select id="colorSelect" name="color"
                                    class="block w-full px-4 py-2 pr-8 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-0">
                                    <option value="green" class="text-white bg-green-500">ROP</option>
                                    <option value="yellow" class="text-white bg-yellow-500">SYSTEME ELECTRONIQUE
                                    </option>
                                    <option value="red" class="text-white bg-red-500">MAINTENANCE</option>
                                    <option value="purple" class="text-white bg-purple-500">LAN</option>
                                    <option value="blue" class="text-white bg-blue-500">RACCO</option>
                                    <option value="gray" class="text-white bg-gray-500">FON</option>
                                    <option value="orange" class="text-white bg-orange-500">Vie des réseaux</option>

                                </select>
                            </div>

                            <input type="submit" name="send" value="Submit"
                                class="text-white bg-blue-500 btn hover:bg-blue-700 btn-block">
                        </form>
                        <p class="mt-3 border border-rounded border-dark">
                            * : <span class="text-red-500">Obligatoire</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @vite('resources/js/chantier/create.js')

</x-app-layout>