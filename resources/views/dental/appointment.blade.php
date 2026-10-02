@extends('dental.layouts.app')

@section('title', __('appointment.hero.title') . ' | Luma Dental')

@section('content')
<section
    id="appointment-booking"
    data-booking-token="{{ $bookingToken }}"
    data-booking-url="{{ route('dental.' . app()->getLocale() . '.appointment.store') }}"
    data-timezone="{{ config('appointments.timezone') }}"
    data-current-step="1"
    data-compact="false"
    data-dates-url="{{ route('dental.appointment.dates') }}"
    data-today="{{ now(config('appointments.timezone'))->toDateString() }}"
    data-copy="{{ json_encode([
            'loading' => __('appointment.calendar.loading'),
            'empty' => __('appointment.calendar.empty'),
            'failed' => __('appointment.calendar.failed'),
            'selectedDate' => __('appointment.calendar.selected_date'),
        ], JSON_UNESCAPED_UNICODE) }}"
    data-slots-url="{{ route('dental.appointment.slots') }}"
    data-slots-copy="{{ json_encode([
    'selectDate' => __('appointment.time.select_date'),
    'loading' => __('appointment.time.loading'),
    'empty' => __('appointment.time.empty'),
    'failed' => __('appointment.time.failed'),
    'timeLabel' => __('appointment.time.label'),
], JSON_UNESCAPED_UNICODE) }}"
    class="group/booking relative isolate min-h-[70vh] bg-[#f8f7f4] pb-12 sm:pb-16">
    @include('dental.appointment.hero')

    <div
        class="relative z-10 mx-auto -mt-16 max-w-6xl px-4 sm:px-6 group-data-[compact=true]/booking:-mt-24">
        <div class="rounded-xl border border-stone-200/80 bg-white/95 p-4 shadow-sm backdrop-blur-sm sm:p-7 lg:p-8">
            <div class="px-1 pb-6 pt-1 sm:px-3 sm:pb-8">
                @include('dental.appointment.steps')
            </div>

            <div class="rounded-xl border border-stone-200/80 bg-white p-4 sm:p-6">
                <div data-booking-step="treatment">
                    @include('dental.appointment.treatments')

                    <p
                        id="appointment-treatment-error"
                        role="alert"
                        class="mt-4 text-sm text-red-600"
                        hidden>
                        {{ __('appointment.validation.treatment_required') }}
                    </p>

                    <div class="mt-7 flex justify-end">
                        <button
                            type="button"
                            data-show-calendar
                            class="group inline-flex items-center gap-2 rounded-full bg-teal-900 px-7 py-3 text-sm font-semibold text-white transition hover:bg-teal-800">
                            {{ __('appointment.navigation.to_datetime') }}

                            <x-heroicon-o-arrow-right
                                class="size-4 transition-transform group-hover:translate-x-1"
                                aria-hidden="true" />
                        </button>
                    </div>
                </div>

                <div data-booking-step="calendar" hidden>
                    @include('dental.appointment.calendar')

                    <p
                        data-slot-error
                        role="alert"
                        class="mt-5 text-sm text-red-600"
                        hidden>
                        {{ __('appointment.validation.slot_required') }}
                    </p>

                    <div class="mt-7 flex flex-wrap items-center justify-between gap-4">
                        <button
                            type="button"
                            data-show-treatments
                            class="rounded-full border border-stone-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-stone-50">
                            {{ __('appointment.navigation.back') }}
                        </button>

                        <button
                            type="button"
                            data-show-details
                            class="rounded-full bg-teal-900 px-7 py-3 text-sm font-semibold text-white transition hover:bg-teal-800">
                            {{ __('appointment.navigation.to_details') }}
                        </button>
                    </div>
                </div>

                <div data-booking-step="details" hidden>
                    @include('dental.appointment.details')
                </div>
                <div data-booking-step="review" hidden>
                    @include('dental.appointment.review')
                </div>
                <div data-booking-step="success" hidden>
                    @include('dental.appointment.success')
                </div>
            </div>
        </div>
    </div>
</section>
@endsection