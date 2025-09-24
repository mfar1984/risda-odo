@props(['disabled' => false])

<button {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['type' => 'button', 'class' => 'btn-success']) }}>
    {{ $slot }}
</button>
