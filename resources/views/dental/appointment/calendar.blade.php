<div>
    <h2
        data-calendar-title
        tabindex="-1"
        class="font-serif text-3xl text-teal-950 outline-none sm:text-4xl">
        {{ __('appointment.calendar.title') }}
    </h2>

    <p class="mt-3 text-sm leading-6 text-slate-500">
        {{ __('appointment.calendar.description') }}
    </p>

    <div class="mt-7 grid items-start gap-5 lg:grid-cols-2">
        <div class="rounded-xl border border-stone-200 p-4 sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <button
                    type="button"
                    data-previous-month
                    aria-label="{{ __('appointment.calendar.previous_month') }}"
                    class="flex size-10 items-center justify-center rounded-full border border-stone-200 transition hover:bg-stone-50 disabled:cursor-not-allowed disabled:opacity-30">
                    <x-heroicon-o-chevron-left
                        class="size-4"
                        aria-hidden="true" />
                </button>

                <p
                    id="appointment-calendar-month"
                    data-calendar-month
                    aria-live="polite"
                    class="text-center text-base font-semibold capitalize text-teal-950"></p>

                <button
                    type="button"
                    data-next-month
                    aria-label="{{ __('appointment.calendar.next_month') }}"
                    class="flex size-10 items-center justify-center rounded-full border border-stone-200 transition hover:bg-stone-50">
                    <x-heroicon-o-chevron-right
                        class="size-4"
                        aria-hidden="true" />
                </button>
            </div>

            <div
                data-calendar-weekdays
                aria-hidden="true"
                class="mt-6 grid grid-cols-7 gap-1 text-center text-xs font-medium text-slate-500"></div>

            <div
                data-calendar-days
                role="group"
                aria-labelledby="appointment-calendar-month"
                class="mt-3 grid grid-cols-7 gap-1"></div>

            <p
                data-calendar-status
                role="status"
                class="mt-4 text-sm text-slate-500"></p>

            <button
                type="button"
                data-calendar-retry
                class="mt-3 text-sm font-semibold text-teal-800 underline underline-offset-4"
                hidden>
                {{ __('appointment.calendar.retry') }}
            </button>
        </div>

        <section
            aria-labelledby="appointment-time-title"
            class="rounded-xl border border-stone-200 p-4 sm:p-6">
            <h3
                id="appointment-time-title"
                class="font-semibold text-teal-950">
                {{ __('appointment.time.title') }}
            </h3>

            <p class="mt-2 text-xs leading-5 text-slate-500">
                {{ __('appointment.time.timezone', [
                    'timezone' => config('appointments.timezone'),
                ]) }}
            </p>

            <p
                data-slots-status
                role="status"
                class="mt-5 text-sm leading-6 text-slate-500">
                {{ __('appointment.time.select_date') }}
            </p>

            <div data-slots-container class="space-y-6">
                <fieldset data-slots-group="morning" hidden>
                    <legend class="mb-3 text-sm font-medium text-slate-700">
                        {{ __('appointment.time.morning') }}
                    </legend>

                    <div
                        data-slots-grid="morning"
                        class="grid grid-cols-3 gap-2 sm:grid-cols-4"></div>
                </fieldset>

                <fieldset data-slots-group="afternoon" hidden>
                    <legend class="mb-3 text-sm font-medium text-slate-700">
                        {{ __('appointment.time.afternoon') }}
                    </legend>

                    <div
                        data-slots-grid="afternoon"
                        class="grid grid-cols-3 gap-2 sm:grid-cols-4"></div>
                </fieldset>
            </div>

            <button
                type="button"
                data-slots-retry
                class="mt-4 text-sm font-semibold text-teal-800 underline underline-offset-4"
                hidden>
                {{ __('appointment.calendar.retry') }}
            </button>
        </section>
    </div>

    <div
        data-appointment-summary
        class="mt-5 rounded-xl border border-stone-200 bg-stone-50/70 p-5"
        hidden>
        <div class="flex items-start gap-4">
            <span
                class="flex size-11 shrink-0 items-center justify-center rounded-full bg-stone-200/60 text-teal-900"
                aria-hidden="true">
                <x-heroicon-o-calendar-days class="size-5" />
            </span>

            <div class="min-w-0">
                <h3 class="font-serif text-lg text-teal-950">
                    {{ __('appointment.time.selected_title') }}
                </h3>

                <p
                    data-summary-treatment
                    class="mt-2 text-sm font-medium text-slate-700"></p>

                <p
                    data-selected-date
                    class="mt-1 text-sm text-slate-600"></p>

                <p
                    data-summary-time
                    role="status"
                    class="mt-1 text-sm font-medium text-teal-900"></p>
            </div>
        </div>
    </div>
</div>