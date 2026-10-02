@php
$steps = [
1 => 'treatment',
2 => 'datetime',
3 => 'details',
4 => 'confirmation',
];
@endphp

<nav aria-label="{{ __('appointment.steps.label') }}">
    <ol class="grid grid-cols-4 gap-3 sm:gap-6">
        @foreach ($steps as $number => $key)
        <li
            data-step-item="{{ $number }}"
            data-status="{{ $number === 1 ? 'current' : 'upcoming' }}"
            class="group/step relative min-w-0">
            @if (!$loop->last)
            <span
                aria-hidden="true"
                class="absolute left-10 right-0 top-4 h-px bg-stone-200 group-data-[status=complete]/step:bg-teal-700/40"></span>
            @endif

            <button
                type="button"
                data-go-to-step="{{ $number }}"
                @disabled($number !==1)
                @if ($number===1) aria-current="step" @endif
                class="relative block w-full rounded-lg text-left outline-none focus-visible:ring-2 focus-visible:ring-teal-800 focus-visible:ring-offset-4 disabled:cursor-default">
                <span
                    class="flex size-8 items-center justify-center rounded-full bg-stone-200 text-xs font-semibold text-slate-600 group-data-[status=current]/step:bg-teal-900 group-data-[status=current]/step:text-white group-data-[status=complete]/step:bg-teal-900 group-data-[status=complete]/step:text-white">
                    <span class="group-data-[status=complete]/step:hidden">
                        {{ $number }}
                    </span>

                    <x-heroicon-o-check
                        class="hidden size-4 group-data-[status=complete]/step:block"
                        aria-hidden="true" />
                </span>

                <span class="mt-3 block text-xs font-semibold leading-5 text-slate-800 sm:text-sm">
                    {{ __('appointment.steps.' . $key . '.title') }}
                </span>

                <span
                    data-step-description="{{ $number }}"
                    data-default-text="{{ __('appointment.steps.' . $key . '.description') }}"
                    class="mt-1 hidden text-xs leading-5 text-slate-500 sm:block">
                    {{ __('appointment.steps.' . $key . '.description') }}
                </span>
            </button>
        </li>
        @endforeach
    </ol>
</nav>