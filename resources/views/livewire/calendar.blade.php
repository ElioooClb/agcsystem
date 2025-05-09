{{-- Planning Chantier côté Administrateur  --}}
@if (Auth::user()->role_id === 1)
    @include('livewire.calendar-admin-v2')
{{-- Planning Côté Utilisateur --}}
@elseif (Auth::user()->role_id === 2)
    @include('livewire.calendar-user')
{{-- Planning Pour Télé --}}
@elseif (Auth::user()->role_id === 3)
    @include('livewire.calendar-tv')
@elseif (Auth::user()->role_id === 4)
    @include('livewire.calendar-admin')
@endif
