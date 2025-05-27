<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Gestion des utilisateurs
        </h2>
    </x-slot>


    <!--/Preloader-->
    <div class="wrapper box-layout theme-1-active pimary-color-blue">
        <div class="container-fluid">
            <div class="my-4 col-sm-12">
                <a class="btn btn-primary" href="{{ route('user.create') }}">Ajouter un nouvel utilisateur</a>
            </div>

            <div class="col-sm-12">
                <div class="panel panel-default workSiteListing">
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="table-wrap">
                                <div class="table-responsive">
                                    <table id="example" class="table table-hover display pb-30">
                                        <thead>
                                            <tr>
                                                <th>Avatar</th>
                                                <th>Nom</th>
                                                <th>Mail</th>
                                                <th>Fonction</th>
                                                <th>Role</th>
                                                <th>Date de création</th>
                                                <th>Coef. Prod (%)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Avatar</th>
                                                <th>Nom</th>
                                                <th>Mail</th>
                                                <th>Fonction</th>
                                                <th>Role</th>
                                                <th>Date de création</th>
                                                <th>Coef. Prod (%)</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @foreach ($users as $user)
                                                @if ($user->email != null)
                                                    <tr>
                                                        <td class="flex justify-center">
                                                            <x-user-avatar :user="$user" size="sm" />
                                                        </td>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->fonction }}</td>
                                                        <td>{{ $user->role->role }}</td>
                                                        <td>{{ $user->created_at }}</td>
                                                        <td class='coef' data-id={{$user->id}}>{{ $user->coef_prod }}</td>

                                                        <td><a class="btn btn-primary"
                                                                href="{{ route('user.edite', $user->id) }}">Editer
                                                            </a> |
                                                            {{-- <a class="button is-primary" href="{{route('user.destroy', $user->id)}}">Supprimer</a> --}}
                                                            <a href="#" title="Supprimer"
                                                                class="btn btn-danger btn-icon left-icon"
                                                                data-toggle="modal"
                                                                data-target="#modal1{{ $user->id }}">Supprimer</a>
                                                        </td>


                                                        <div class="modal fade" id="modal1{{ $user->id }}"
                                                            tabindex="-1" role="dialog"
                                                            aria-labelledby="modalLabel{{ $user->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="modalLabel{{ $user->id }}">
                                                                            Suppression
                                                                            de {{ $user->fonction }}
                                                                            {{ $user->name }}</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Etes-vous sûr de supprimer ce
                                                                        {{ $user->fonction }} ?
                                                                        Cette action est irréversible.
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Annuler</button>
                                                                        <a href="{{ route('user.destroy', $user->id) }}"
                                                                            type="button"
                                                                            class="btn btn-danger">Supprimer</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </tr>
                                                @endif
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
        <!-- Modal Background -->
        <div id="modal"
            class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-500 bg-opacity-75">
            <!-- Modal Content -->
            <div class="p-6 bg-white rounded-lg shadow-lg w-96">
                <h2 class="mb-4 text-2xl font-semibold text-center">Saisir un Coefficient</h2>

                <!-- Formulaire avec un input pour le coefficient -->
                <div class="mb-4">
                    <label for="coef" class="block text-sm font-medium text-gray-700">Coefficient</label>
                    <input type="number" id="coef"
                        class="block w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        placeholder="Entrez un coefficient">
                </div>

                <!-- Boutons Fermer et Valider -->
                <div class="flex justify-end space-x-2">
                    <button id="closeModal"
                        class="px-4 py-2 text-gray-700 bg-gray-300 rounded-md hover:bg-gray-400">Fermer</button>
                    <button id="validateCoef"
                        class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Valider</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /#wrapper -->
    @vite('resources/js/users/coefProd.js')
    @push('table')
    @endpush
</x-app-layout>
