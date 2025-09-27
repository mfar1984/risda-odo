@props(['disabled' => false, 'value' => '', 'placeholder' => 'dd/mm/yyyy hh:mm'])

@php
    $inputId = $attributes->get('id', 'datetime_' . uniqid());
    $displayValue = $value ? \Carbon\Carbon::parse($value)->format('d/m/Y H:i') : '';
@endphp

<div style="position: relative;">
    <input
        type="text"
        id="{{ $inputId }}"
        @disabled($disabled)
        value="{{ $displayValue }}"
        {{ $attributes->merge(['class' => 'form-input form-datetime-input-daterangepicker']) }}
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
    >
</div>

@push('scripts')
<script>
$(function() {
    $('#{{ $inputId }}').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        timePickerIncrement: 15,
        minYear: 1901,
        maxYear: parseInt(moment().format('YYYY'), 10) + 10,
        locale: {
            format: 'DD/MM/YYYY HH:mm',
            firstDay: 1
        },
        opens: 'left',
        drops: 'auto'
    }, function(start, end, label) {
        // Update hidden input with Y-m-d H:i:s format for Laravel
        const hiddenInput = $('input[name="{{ $attributes->get('name') }}"][type="hidden"]');
        if (hiddenInput.length > 0) {
            hiddenInput.val(start.format('YYYY-MM-DD HH:mm:ss'));
        }
    });
});
</script>
@endpush

<!-- Hidden input for form submission with Y-m-d H:i:s format -->
@if($attributes->get('name'))
<input type="hidden" name="{{ $attributes->get('name') }}" value="{{ $value }}" />
@endif
