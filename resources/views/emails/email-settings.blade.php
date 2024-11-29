<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Gestion des paramètres d'envoi d'email
        </h2>
    </x-slot>
    <div class="wrapper box-layout email-settings">
        <div class="flex" x-data="{
            currentId: '{{ $emails->first()->id }}',
            currentType: '{{ $emails->first()->type }}',
            currentTitle: '{{ $emails->first()->label ?? 'Detail for Button 1' }}',
            currentLabel: '{{ $emails->first()->label }}',
            currentEmail: '{{ $emails->first()->email }}'
        }">
            <aside>
                @foreach ($emails as $email)
                    <button class="general-button"
                        @click="currentEmail = '{{ $email->email }}'; currentLabel = '{{ $email->label }}'; currentTitle = '{{ $email->label ?? 'Detail for Button 1' }}'; currentType = '{{ $email->type }}'; currentId = '{{ $email->id }}'">
                        {{ $email->label ?? 'Paramètre à venir...' }}
                    </button>
                @endforeach
            </aside>
            <section>
                <article class="detail-container">
                    <h2 x-text="currentTitle">Detail for Button 1</h2>
                    <hr>
                    <form action="{{ route('email-settings.update') }}" method="POST"  x-data="formHandler()" @submit="submitForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" x-model="currentId">
                        <input type="hidden" name="type" x-model="currentType">
                        <div class="w-3/4 mb-4">
                            <label for="label" class="block text-gray-700 dark:text-gray-200">Label</label>
                            <input type="text" name="label" id="label" x-model="currentLabel"
                                class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-700 focus:outline-none focus:ring focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 dark:text-gray-200">Email</label>
                            <input type="email" name="email" id="email" x-model="currentEmail" required
                                class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-700 focus:outline-none focus:ring focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <button type="submit" class="general-button">Enregistrer</button>
                    </form>
                </article>
            </section>
        </div>
    </div>
    @vite('resources/js/emails/email-settings.js')
</x-app-layout>
