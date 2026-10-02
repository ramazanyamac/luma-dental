<div class="mx-auto max-w-2xl py-6 text-center sm:py-10">
    <span class="mx-auto flex size-16 items-center justify-center rounded-full border-2 border-teal-800 text-teal-900"><x-heroicon-o-check class="size-8" aria-hidden="true" /></span>
    <h2 data-success-title tabindex="-1" class="mt-6 font-serif text-3xl text-teal-950 outline-none sm:text-5xl">{{ __('appointment.success.title') }}</h2>
    <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-slate-500">{{ __('appointment.success.description') }}</p>
    <section class="mt-8 rounded-xl border border-stone-200 bg-stone-50/70 p-6 text-left sm:p-8">
        <h3 class="font-serif text-xl text-teal-950">{{ __('appointment.review.appointment') }}</h3>
        <dl class="mt-5 space-y-5">
            @foreach (['treatment', 'datetime', 'duration', 'location'] as $field)
                <div class="grid gap-1 sm:grid-cols-2 sm:gap-4">
                    <dt class="text-sm text-slate-500">{{ __('appointment.review.' . $field) }}</dt>
                    <dd data-success-value="{{ $field }}" class="break-words text-sm text-slate-800"></dd>
                </div>
            @endforeach
            <div class="border-t border-stone-200 pt-4">
                <dt class="text-xs text-slate-500">{{ __('appointment.success.reference') }}</dt>
                <dd data-success-value="reference" class="mt-1 break-all font-mono text-xs text-slate-700"></dd>
            </div>
        </dl>
    </section>
    <div class="mt-6 flex flex-wrap justify-center gap-3">
        <a data-calendar-download class="inline-flex items-center gap-2 rounded-full bg-teal-900 px-6 py-3 text-sm font-semibold text-white"><x-heroicon-o-calendar-days class="size-4" />{{ __('appointment.success.calendar') }}</a>
        <a href="{{ route('dental.' . app()->getLocale() . '.home') }}" class="rounded-full border border-teal-900 px-6 py-3 text-sm font-semibold text-teal-900">{{ __('appointment.success.home') }}</a>
    </div>
    <a data-manage-appointment class="mt-5 inline-block text-sm font-semibold text-teal-800 underline underline-offset-4">{{ __('management.manage') }}</a>
    <section class="mt-8 border-t border-stone-200 pt-8" aria-live="polite">
        <x-heroicon-o-envelope class="mx-auto size-8 text-teal-800" aria-hidden="true" />
        <h3 data-email-title class="mt-3 font-serif text-2xl text-teal-950"></h3>
        <p data-email-status class="mt-3 text-sm leading-6 text-slate-600"></p>
        <p data-success-value="email" class="mt-2 break-all text-sm font-semibold text-teal-900"></p>
    </section>
</div>
