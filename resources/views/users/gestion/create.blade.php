<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight title_page">
            Création d'un nouvel utilisateur
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="container mt-5">
                        <!-- Success message -->
                        {{-- @if(Session::has('success'))
                        <div class="alert alert-success">
                            {{Session::get('success')}}
                        </div>
                        @endif --}}
                        <form method="post" action="{{route('user.store')}}">
                            <!-- CROSS Site Request Forgery Protection -->
                            @csrf
                            <div class="mb-6">
                                <label class="block">
                                    <span class="text-gray-700">Selection du rôle</span>
                                    <select name="role_id" class="block w-full mt-1 rounded-md">
                                        @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">
                                            {{ $role->role }}
                                        </option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Nom</label>
                                <input type="text" class="form-control input_user" name="name" id="name">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control input_user" name="email" id="email">
                            </div>
                            <div class="form-group">
                                <label>Fonction</label>
                                <input type="text" class="form-control input_user" name="fonction" id="fonction">
                            </div>
                            <div class="form-group">
                                <label>Acronyme</label>
                                <input type="text" class="form-control input_user" name="acronyme" id="acronyme">
                            </div>
                            <div class="form-group">
                                <label>Mot de passe</label>
                                <input type="text" class="form-control input_user" name="password" id="password">
                            </div>
                            <input type="submit" name="send" value="Enregistrer" class="btn btn-dark btn-block btn_sub">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
