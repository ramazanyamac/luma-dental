<section class="mt-6 flex flex-col gap-5 rounded-2xl border border-stone-200 bg-white px-6 py-6 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-start gap-4 sm:items-center">
        <x-bi-headset
            class="size-9 shrink-0 text-teal-950"
            aria-hidden="true"
        />

        <div>
            <h2 class="font-serif text-xl text-slate-950">
                {{ __('contact.support.title') }}
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-600">
                {{ __('contact.support.description') }}
            </p>
        </div>
    </div>

    <a
        href="tel:+902123456789"
        class="group inline-flex shrink-0 items-center justify-center gap-3 self-start rounded-full border border-teal-950 px-6 py-2.5 text-sm font-medium text-teal-950 transition-colors hover:bg-teal-950 hover:text-white sm:self-auto"
    >
        {{ __('contact.support.button') }}

        <x-heroicon-o-arrow-right
            class="size-4 transition-transform duration-200 group-hover:translate-x-1"
            aria-hidden="true"
        />
    </a>
</section>