@props([

    'id',
    'wireModel',
    'value' => null,
    'defaultHour' => '09',
    'defaultMinute' => '00',
    'defaultPeriod' => 'AM',
])

@php
    $initial = $value ? \Carbon\Carbon::parse($value) : null;
    $hour = $initial ? $initial->format('h') : $defaultHour;
    $minute = $initial ? $initial->format('i') : $defaultMinute;
    $period = $initial ? $initial->format('A') : $defaultPeriod;
@endphp

<div
    x-data="{
        hour: '{{ $hour }}',
        minute: '{{ $minute }}',
        period: '{{ $period }}',
        get time24() {
            let h = parseInt(this.hour, 10) || 0;
            let m = (this.minute || '00').padStart(2, '0');
            if (this.period === 'PM' && h !== 12) h += 12;
            if (this.period === 'AM' && h === 12) h = 0;
            return String(h).padStart(2, '0') + ':' + m;
        },
    }"
    x-init="$nextTick(() => $wire.set('{{ $wireModel }}', time24))"
    x-effect="$wire.set('{{ $wireModel }}', time24)"
    class="inline-block"
>
    <div class="flex items-center gap-1.5 rounded-lg border border-gray-300 bg-gray-50 shadow-sm dark:border-gray-600 dark:bg-gray-700 pl-3 pr-2 py-2 focus-within:ring-2 focus-within:ring-primary-500/30 focus-within:border-primary-500 dark:focus-within:border-primary-500">
        <select
            x-model="hour"
            class="min-w-0 w-11 rounded-md border-0 bg-transparent py-0.5 text-sm font-medium text-gray-900 focus:ring-0 dark:text-white dark:bg-transparent [&>option]:bg-white [&>option]:text-gray-900 dark:[&>option]:bg-gray-700 dark:[&>option]:text-white"
            aria-label="{{ __('common.hour') }}"
        >
            @for ($h = 1; $h <= 12; $h++)
                @php
                    $val = str_pad((string) $h, 2, '0', STR_PAD_LEFT);
                @endphp
                <option value="{{ $val }}">{{ $val }}</option>
            @endfor
        </select>
        <select
            x-model="minute"
            class="min-w-0 w-11 rounded-md border-0 bg-transparent py-0.5 text-sm font-medium text-gray-900 focus:ring-0 dark:text-white dark:bg-transparent [&>option]:bg-white [&>option]:text-gray-900 dark:[&>option]:bg-gray-700 dark:[&>option]:text-white"
            aria-label="{{ __('common.minute') }}"
        >
            @for ($m = 0; $m < 60; $m += 5)
                @php
                    $val = str_pad((string) $m, 2, '0', STR_PAD_LEFT);
                @endphp
                <option value="{{ $val }}">{{ $val }}</option>
            @endfor
        </select>
        <span class="shrink-0 w-px h-5 bg-gray-300 dark:bg-gray-500 mx-0.5" aria-hidden="true"></span>
        <select
            x-model="period"
            class="min-w-0 w-14 rounded-md border-0 bg-transparent py-0.5 text-sm font-medium text-gray-900 text-center focus:ring-0 dark:text-white dark:bg-transparent [&>option]:bg-white [&>option]:text-gray-900 dark:[&>option]:bg-gray-700 dark:[&>option]:text-white pl-2 pr-6"
            aria-label="{{ __('common.period') }}"
        >
            <option value="AM">AM</option>
            <option value="PM">PM</option>
        </select>
    </div>
    <input type="hidden" id="{{ $id }}" x-model="time24" wire:model.defer="{{ $wireModel }}">
</div>
