<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-secondary']) }}>
    {{ $slot }}
</button>
