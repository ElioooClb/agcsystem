<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Editer un utlisateur        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="container mt-5">
                        <!-- Success message -->
                        {{-- @if(Session::has('success'))
                        <div class="alert alert-success">
                            {{Session::get('success')}}
                        </div>
                        @endif --}}
                        <form method="post" action="{{route('user.update', $user->id)}}">
                            <!-- CROSS Site Request Forgery Protection -->
                            @method('PUT')
                            @csrf
                            <div class="mb-6">
                                <label class="block">
                                    <span class="text-gray-700">Selection du rôle</span>
                                    <select name="role_id" class="block w-full mt-1 rounded-md">
                                        <option value="{{ $user->role_id }}">{{ $user->role->role }}</option>
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
                                <input type="text" class="form-control input_user" name="name" id="name" value="{{ $user->name }}">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control input_user" name="email" id="email" value="{{ $user->email }}">
                            </div>
                            <div class="form-group">
                                <label>Fonction</label>
                                <input type="text" class="form-control input_user" name="fonction" id="fonction" value="{{ $user->fonction }}">
                            </div>
                            <div class="form-group">
                                <label>Acronyme</label>
                                <input type="text" class="form-control input_user" name="acronyme" id="acronyme" value="{{ $user->acronyme }}">
                            </div>
                            {{-- <div class="form-group">
                                <label>Mot de passe</label>
                                <input type="text" class="form-control" name="password" id="password" value="{{ $user->password }}">
                            </div> --}}
                            <input type="submit" name="send" value="Valider" class="btn btn-dark btn-block btn_sub">
                        </form>
                            <hr>
                        <form enctype="multipart/form-data" method="POST" action="{{ route('user.updatePassword', $user->id) }}">
                            @method('PUT')
                            @csrf

                            <div class="form-group ">
                                <label for="title">Mot de passe* (minimum 8 caractères) </label>

                                    <input id="password" type="text" class="form-control input_user"  name="password" value="">

                            </div>
                            {{-- <div class="form-group ">
                                <label for="title">vérification du mot de passe* </label>

                                    <input id="password-confirm" type="text" class="form-control input_user"  name="password_confirmation" placeholder="vérification du mot de passe" value="">

                            </div> --}}
                            <div class="form-group ">
                                <input type="submit" name="updatePassord" value="Valider" class="btn btn-dark btn-block btn_sub">
                                    {{-- <button type="submit" class="btn btn-primary">Editer le mot de passe</button> --}}

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
