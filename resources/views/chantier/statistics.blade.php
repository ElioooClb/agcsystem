<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Indicateurs de production
        </h2>
    </x-slot>
    @livewire('stats')
    @livewireScripts
</x-app-layout>
