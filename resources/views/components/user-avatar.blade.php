@props(['user', 'size' => '', 'id' => ''])

@php
    $dimensions = [
        'sm' => 'width: 50px !important; height: 50px !important;',
        'md' => 'width: 100px !important; height: 100px !important;',
        'lg' => 'width: 200px !important; height: 200px !important;',
    ];
    $style = $dimensions[$size] ?? 'width: 50px !important; height: 50px !important;';
@endphp

<div class="d-flex align-items-center">
    <img
        id="{{ $id }}"
        src="{{ asset($user->avatar) }}"
        alt="{{ $user->name }}"
        class="rounded-circle {{ $size }}"
        style="object-fit: cover; {{ $style }}"
    >
</div>
