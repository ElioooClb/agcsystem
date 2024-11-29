<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 title_page">
            Mes heures
        </h2>
    </x-slot>
    <livewire:heures :user-id="$user->id" :initial-times="$times" :chantiers="$chantiers" :initial-productive-hours="$productiveHours" :initial-non-productive-hours="$nonProductiveHours" />
    @livewireScripts
</x-app-layout>
