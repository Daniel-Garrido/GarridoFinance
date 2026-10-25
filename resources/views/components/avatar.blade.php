@props(['user', 'size' => 'md'])

{{-- Avatar del usuario: su foto de perfil o, si no tiene, sus iniciales. size: sm | md | lg --}}
<span {{ $attributes->merge(['class' => 'gf-avatar gf-avatar-' . $size]) }}>
    @if ($user?->profile_photo_url)
        <img src="{{ $user->profile_photo_url }}" alt="Foto de {{ $user->full_name }}">
    @else
        {{ $user?->initials ?? 'GF' }}
    @endif
</span>
