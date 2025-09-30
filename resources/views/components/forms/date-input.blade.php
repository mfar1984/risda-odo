@props(['disabled' => false, 'value' => '', 'placeholder' => 'dd/mm/yyyy'])

@php
    $inputId = $attributes->get('id', 'date_' . uniqid());
    $displayValue = $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '';
@endphp

<div style="position: relative;">
    <input
        type="text"
        id="{{ $inputId }}"
        @disabled($disabled)
        value="{{ $displayValue }}"
        {{ $attributes->merge(['class' => 'form-input form-date-input-daterangepicker h-9 pl-3', 'style' => 'height:36px !important; min-height:36px !important; padding-left:12px !important;']) }}
        placeholder="{{ $placeholder }}"
        autocomplete="off"
    >
</div>

@push('scripts')
<script>
$(function() {
    $('#{{ $inputId }}').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minYear: 1901,
        maxYear: parseInt(moment().format('YYYY'), 10) + 10,
        locale: {
            format: 'DD/MM/YYYY',
            firstDay: 1
        },
        opens: 'left',
        drops: 'auto'
    }, function(start, end, label) {
        // Update hidden input with Y-m-d format for Laravel
        const hiddenInput = $('input[name="{{ $attributes->get('name') }}"][type="hidden"]');
        if (hiddenInput.length > 0) {
            hiddenInput.val(start.format('YYYY-MM-DD'));
        }
    });
});
</script>
@endpush

<!-- Hidden input for form submission with Y-m-d format -->
@if($attributes->get('name'))
<input type="hidden" name="{{ $attributes->get('name') }}" value="{{ $value }}" />
@endif
