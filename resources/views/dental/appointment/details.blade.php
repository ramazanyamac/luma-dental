@php
$inputClass = 'mt-2 h-12 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm outline-none focus:border-teal-800 focus:ring-2 focus:ring-teal-800/15';
@endphp

<h2
    data-details-title
    tabindex="-1"
    class="font-serif text-3xl text-teal-950 outline-none sm:text-4xl">
    {{ __('appointment.details.title') }}
</h2>

<p class="mt-3 text-sm leading-6 text-slate-500">
    {{ __('appointment.details.description') }}
</p>

<form
    id="appointment-details-form"
    method="POST"
    action="{{ route('dental.' . app()->getLocale() . '.appointment.details.validate') }}"
    data-validating="{{ __('appointment.details.validating') }}"
    data-invalid="{{ __('appointment.details.invalid') }}"
    data-failed="{{ __('appointment.details.failed') }}"
    class="mt-7"
    novalidate>
    @csrf

    <div class="grid items-start gap-5 lg:grid-cols-2">
        <section class="space-y-5 rounded-xl border border-stone-200 p-5 sm:p-6">
            <h3 class="font-serif text-xl text-teal-950">
                {{ __('appointment.details.personal') }}
            </h3>

            <div data-field="name">
                <label for="appointment-name" class="block text-sm font-medium">
                    {{ __('appointment.fields.name') }} *
                </label>

                <input
                    id="appointment-name"
                    name="name"
                    type="text"
                    autocomplete="name"
                    maxlength="120"
                    required
                    class="{{ $inputClass }}">
            </div>

            <div data-field="email">
                <label for="appointment-email" class="block text-sm font-medium">
                    {{ __('appointment.fields.email') }} *
                </label>

                <input
                    id="appointment-email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    maxlength="254"
                    required
                    class="{{ $inputClass }}">
            </div>

            <div data-field="phone">
                <x-forms.phone-input
                    id="appointment-phone"
                    name="phone"
                    :label="__('appointment.fields.phone')"
                    :required="true" />
            </div>

            <div data-field="birth_date">
                <label for="appointment-birth-date" class="block text-sm font-medium">
                    {{ __('appointment.fields.birth_date') }}
                    <span class="font-normal text-slate-400">
                        ({{ __('appointment.fields.optional') }})
                    </span>
                </label>

                <input
                    id="appointment-birth-date"
                    name="birth_date"
                    type="date"
                    autocomplete="bday"
                    max="{{ now(config('appointments.timezone'))->toDateString() }}"
                    class="{{ $inputClass }}">
            </div>

            <fieldset data-field="gender">
                <legend class="text-sm font-medium">
                    {{ __('appointment.fields.gender') }}
                    <span class="font-normal text-slate-400">
                        ({{ __('appointment.fields.optional') }})
                    </span>
                </legend>

                <div class="mt-3 flex flex-wrap gap-5">
                    @foreach (['male', 'female', 'other'] as $gender)
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm">
                        <input
                            type="radio"
                            name="gender"
                            value="{{ $gender }}"
                            class="size-4 accent-teal-900">

                        {{ __('appointment.gender.' . $gender) }}
                    </label>
                    @endforeach
                </div>
            </fieldset>
        </section>

        <section class="space-y-5 rounded-xl border border-stone-200 p-5 sm:p-6">
            <h3 class="font-serif text-xl text-teal-950">
                {{ __('appointment.details.additional') }}
            </h3>

            <div data-field="reason">
                <label for="appointment-reason" class="block text-sm font-medium">
                    {{ __('appointment.fields.reason') }}
                    <span class="font-normal text-slate-400">
                        ({{ __('appointment.fields.optional') }})
                    </span>
                </label>

                <select
                    id="appointment-reason"
                    name="reason"
                    class="{{ $inputClass }}">
                    <option value="">
                        {{ __('appointment.fields.select') }}
                    </option>

                    @foreach (['general', 'pain', 'cosmetic', 'other'] as $reason)
                    <option value="{{ $reason }}">
                        {{ __('appointment.reasons.' . $reason) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div data-field="notes">
                <label for="appointment-notes" class="block text-sm font-medium">
                    {{ __('appointment.fields.notes') }}
                    <span class="font-normal text-slate-400">
                        ({{ __('appointment.fields.optional') }})
                    </span>
                </label>

                <textarea
                    id="appointment-notes"
                    name="notes"
                    rows="6"
                    maxlength="5000"
                    class="mt-2 w-full resize-y rounded-lg border border-stone-200 bg-white px-4 py-3 text-sm outline-none focus:border-teal-800 focus:ring-2 focus:ring-teal-800/15"></textarea>
            </div>

            <div data-field="is_new_patient">
                <label class="flex cursor-pointer items-start gap-3 text-sm">
                    <input
                        type="checkbox"
                        name="is_new_patient"
                        value="1"
                        class="mt-0.5 size-4 shrink-0 accent-teal-900">

                    <span>{{ __('appointment.fields.new_patient') }}</span>
                </label>
            </div>
        </section>
    </div>

    <p
        data-details-status
        role="status"
        class="mt-5 text-sm"></p>

    <div class="mt-7 flex flex-wrap items-center justify-between gap-4">
        <button
            type="button"
            data-back-calendar
            class="rounded-full border border-stone-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-stone-50">
            {{ __('appointment.navigation.back') }}
        </button>

        <button
            type="submit"
            class="rounded-full bg-teal-900 px-7 py-3 text-sm font-semibold text-white transition hover:bg-teal-800">
            <span data-details-submit-label>
                {{ __('appointment.details.validate') }}
            </span>
        </button>
    </div>
</form>