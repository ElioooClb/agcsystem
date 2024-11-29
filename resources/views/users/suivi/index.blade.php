<x-app-layout>
    @if (Auth::user()->role_id === 1 || Auth::user()->role_id === 2 )
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight title_page">
            Planning
        </h2>
    </x-slot>
    @endif
    <livewire:calendar />
    @livewireScripts

    <script>
        const chantierIds = {!! json_encode($chantiers) !!};
    </script>
    
</x-app-layout>