<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Heure de {{ $user->name }}
        </h2>
    </x-slot>

    @livewire("user-schedule-manager", ["id" => $user->id])

    @livewireScripts
    <script>
        const userId = {{ $user->id }};
        const chantierIds = {!! json_encode($chantiers) !!};
        const times = {!! json_encode($times) !!};
    </script>

</x-app-layout>
