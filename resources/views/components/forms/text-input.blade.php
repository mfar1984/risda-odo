@props(['disabled' => false])

<input {{ $attributes->merge(['class' => 'form-input']) }} {{ $disabled ? 'readonly' : '' }}>
