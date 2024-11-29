<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight title_page">
            Création d'un nouveau mesasge de diffusion général
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
                        <form method="post" action="{{route('message.store')}}">
                            <!-- CROSS Site Request Forgery Protection -->
                            @csrf

                            <div class="form-group">
                                <label>Message à diffuser</label>
                                <input type="text" class="form-control" name="message" id="message">
                            </div>
                            <input type="submit" name="send" value="Enregistrer" class="btn btn-dark btn-block btn_sub">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
