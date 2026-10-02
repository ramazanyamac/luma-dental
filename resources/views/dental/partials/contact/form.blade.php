<div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
    <h2 class="font-serif text-3xl text-slate-950">
        {{ __('contact.form.title') }}
    </h2>

    <p class="mt-2 text-sm leading-6 text-slate-600">
        {{ __('contact.form.description') }}
    </p>

    {{-- Gönderim adresini servis adımında bağlayacağız. --}}
    <form
        id="contact-form"
        action="{{ route('dental.' . app()->getLocale() . '.contact.store') }}"
        method="POST"
        novalidate
        data-sending="{{ __('contact.form.sending') }}"
        data-invalid="{{ __('contact.form.invalid') }}"
        data-failed="{{ __('contact.form.failed') }}"
        class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="contact-name" class="sr-only">
                {{ __('contact.form.name') }}
            </label>

            <input
                id="contact-name"
                name="name"
                type="text"
                autocomplete="name"
                required
                maxlength="120"
                placeholder="{{ __('contact.form.name') }} *"
                class="w-full rounded-lg border border-stone-200 bg-[#fcfbf9] px-4 py-3 text-sm outline-none transition focus:border-teal-800 focus:ring-2 focus:ring-teal-800/15">
        </div>

        <div>
            <label for="contact-email" class="sr-only">
                {{ __('contact.form.email') }}
            </label>

            <input
                id="contact-email"
                name="email"
                type="email"
                autocomplete="email"
                required
                maxlength="254"
                placeholder="{{ __('contact.form.email') }} *"
                class="w-full rounded-lg border border-stone-200 bg-[#fcfbf9] px-4 py-3 text-sm outline-none transition focus:border-teal-800 focus:ring-2 focus:ring-teal-800/15">
        </div>

        <x-forms.phone-input
            id="contact-phone"
            name="phone"
            :label="__('contact.form.phone')"
            :required="true" />

        <div>
            <label for="contact-subject" class="sr-only">
                {{ __('contact.form.subject') }}
            </label>

            <div class="relative">
                <select
                    id="contact-subject"
                    name="subject"
                    required
                    class="w-full appearance-none rounded-lg border border-stone-200 bg-[#fcfbf9] py-3 pl-4 pr-10 text-sm outline-none transition invalid:text-slate-500 focus:border-teal-800 focus:ring-2 focus:ring-teal-800/15">
                    <option value="" disabled selected>
                        {{ __('contact.form.subject') }} *
                    </option>

                    @foreach (['general', 'appointment', 'treatment', 'other'] as $subject)
                    <option value="{{ $subject }}">
                        {{ __('contact.subject.' . $subject) }}
                    </option>
                    @endforeach
                </select>

                <x-heroicon-o-chevron-down
                    class="pointer-events-none absolute right-4 top-1/2 size-4 -translate-y-1/2 text-slate-600"
                    aria-hidden="true" />
            </div>
        </div>

        <div>
            <label for="contact-message" class="sr-only">
                {{ __('contact.form.message') }}
            </label>

            <textarea
                id="contact-message"
                name="message"
                rows="5"
                required
                maxlength="5000"
                placeholder="{{ __('contact.form.message') }} *"
                class="block min-h-36 w-full resize-y rounded-lg border border-stone-200 bg-[#fcfbf9] px-4 py-3 text-sm outline-none transition focus:border-teal-800 focus:ring-2 focus:ring-teal-800/15"></textarea>
        </div>

        <button
            type="submit"
            class="group inline-flex w-full items-center justify-center gap-2 rounded-full bg-teal-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-teal-800">
            <span data-submit-label>
                {{ __('contact.form.send') }}
            </span>

            <x-heroicon-o-arrow-right
                class="size-4 transition-transform duration-200 group-hover:translate-x-1"
                aria-hidden="true" />
        </button>

        <p
            data-form-status
            role="status"
            aria-live="polite"
            aria-atomic="true"
            class="text-sm"></p>
    </form>
</div>