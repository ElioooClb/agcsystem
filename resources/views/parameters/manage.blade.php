<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Gestion des paramètres et des modèle
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                                <div class="p-6 text-gray-900 dark:text-gray-100">
                                    <button type="button"
                                        class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-blue-500 border border-transparent rounded-md add-parameter-button hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25"
                                        data-toggle="modal" data-target="#createParamModal">
                                        Ajouter un paramètre
                                    </button>

                                    <ul class="grid grid-cols-1 gap-1 mt-4">
                                        @foreach ($parameters as $parameter)
                                            <li class="col-span-1 bg-white divide-y divide-gray-200 rounded-lg shadow">
                                                <div class="flex items-center justify-between w-full">
                                                    <div class="flex-1 truncate">
                                                        <div class="flex items-center space-x-3">
                                                            <h5 class="text-sm font-medium text-gray-900 truncate">
                                                                {{ $parameter->label }}</h5>
                                                        </div>
                                                    </div>
                                                    @if ($parameter->loadouts()->exists())
                                                        <button type="button"
                                                            class="px-2 py-1 font-bold text-white bg-gray-600 rounded"
                                                            title="Ce paramètre est présent dans un modèle et ne peut pas être supprimé."
                                                            disabled>
                                                            Supprimer
                                                        </button>
                                                    @else
                                                        <form method="POST"
                                                            action="{{ route('parameters.destroy', $parameter) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" id="delete-param"
                                                                class="px-2 py-1 font-bold text-white bg-red-500 rounded delete-button hover:bg-red-700">
                                                                Supprimer
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                                <div class="p-6 text-gray-900 dark:text-gray-100">
                                    <button type="button"
                                        class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-blue-500 border border-transparent rounded-md add-loadout-button hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25"
                                        data-toggle="modal" data-target="#createLoadModal">
                                        Ajouter un modèle
                                    </button>

                                    <ul class="grid grid-cols-1 gap-1">
                                        @foreach ($loadouts as $loadout)
                                            <li
                                                class="col-span-1 mt-4 bg-white divide-y divide-gray-200 rounded-lg shadow">
                                                <div class="flex items-center justify-between w-full space-x-3">
                                                    <div class="flex-1 truncate">
                                                        <div class="flex items-center space-x-3">
                                                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                                                {{ $loadout->title }}</h4>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center">
                                                        @if ($loadout->chantiers()->exists())
                                                            <button type="button"
                                                                class="px-2 py-1 mx-2 font-bold text-white bg-gray-600 rounded"
                                                                title="Ce modèle est lié à un chantier et ne peut pas être modifié."
                                                                disabled>
                                                                Modifier
                                                            </button>
                                                        @else
                                                            <button type="button"
                                                                class="px-2 py-1 mx-2 font-bold text-white bg-green-500 rounded edit-button hover:bg-green-700"
                                                                data-toggle="modal" data-target="#editModal"
                                                                data-loadout-id="{{ $loadout->id }}">
                                                                Modifier
                                                            </button>
                                                        @endif
                                                        <form method="POST"
                                                            action="{{ route('loadouts.destroy', $loadout) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            @if ($loadout->chantiers()->exists())
                                                                <button type="submit"
                                                                    class="px-2 py-1 mx-2 font-bold text-white bg-gray-600 rounded"
                                                                    title="Ce modèle est lié à un chantier et ne peut pas être supprimé."
                                                                    disabled>
                                                                    Supprimer
                                                                </button>
                                                            @else
                                                                <button type="submit" id="delete-load"
                                                                    class="px-2 py-1 mx-2 font-bold text-white bg-red-500 rounded hover:bg-red-700">
                                                                    Supprimer
                                                                </button>
                                                            @endif
                                                        </form>
                                                    </div>
                                                </div>
                                                <details class="border border-black divide-y divide-gray-200">
                                                    <summary>Voir les paramètres</summary>
                                                    <ul>
                                                        @foreach ($loadout->parameters as $parameter)
                                                            <div class="flex items-center flex-1 w-0">
                                                                <h5 class="flex-1 w-0 ml-2 truncate">
                                                                    {{ $parameter->label }}</h5>
                                                            </div>
                                                        @endforeach
                                                    </ul>
                                                </details>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mainModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="mainModalBody">
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/parameters/manage.js')
</x-app-layout>
