@props([

    'id',
    'wireModel',
    'value' => '',
    'minDate' => '',
    'maxDate' => '',
])

<input type="hidden" id="{{ $id }}" wire:model.blur="{{ $wireModel }}">
<div wire:ignore
    class="inline-datepicker"
    data-inline-datepicker="{{ $id }}"
    data-date="{{ $value }}"
    data-min-date="{{ $minDate }}"
    data-max-date="{{ $maxDate }}"
></div>
