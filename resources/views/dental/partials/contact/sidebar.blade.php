<aside class="flex flex-col gap-5">
    <div class="relative flex min-h-[360px] flex-1 items-end overflow-hidden rounded-2xl p-5">
        <img
            src="{{ asset('images/dental/06_clinic_interior.png') }}"
            alt="{{ __('contact.clinic.image_alt') }}"
            class="absolute inset-0 h-full w-full object-cover"
            loading="lazy"
        >

        <div class="relative flex w-full items-start gap-3 rounded-xl bg-white/95 p-5 shadow-sm">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-stone-100 text-teal-950">
                <x-heroicon-o-map-pin
                    class="size-5"
                    aria-hidden="true"
                />
            </span>

            <div>
                <h2 class="font-serif text-xl text-slate-950">
                    {{ __('contact.clinic.title') }}
                </h2>

                <address class="mt-2 text-sm not-italic leading-6 text-slate-600">
                    {{ __('contact.location') }}
                </address>
                <a
                    href="https://www.google.com/maps/dir/?api=1&destination=Maslak%2C%20Istanbul"
                    target="_blank"
                    rel="noopener noreferrer"
                        class="group mt-3 inline-flex items-center gap-2 text-sm font-medium text-teal-950 hover:underline"
                    >
                        {{ __('contact.directions') }}

                        <x-heroicon-o-arrow-right
                            class="size-4 transition-transform duration-200 group-hover:translate-x-1"
                            aria-hidden="true"
                        />
                </a>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white p-6">
        <div class="flex items-center gap-3">
            <x-heroicon-o-clock
                class="size-5 shrink-0 text-teal-950"
                aria-hidden="true"
            />

            <h2 class="font-serif text-xl text-slate-950">
                {{ __('contact.hours.title') }}
            </h2>
        </div>

        <dl class="mt-5 divide-y divide-stone-100 text-sm">
            <div class="flex items-center justify-between gap-4 py-3">
                <dt class="text-slate-600">
                    {{ __('contact.hours.weekdays') }}
                </dt>
                <dd class="shrink-0 font-medium text-slate-900">
                    09:00 – 19:00
                </dd>
            </div>

            <div class="flex items-center justify-between gap-4 py-3">
                <dt class="text-slate-600">
                    {{ __('contact.hours.saturday') }}
                </dt>
                <dd class="shrink-0 font-medium text-slate-900">
                    09:00 – 15:00
                </dd>
            </div>

            <div class="flex items-center justify-between gap-4 py-3">
                <dt class="text-slate-600">
                    {{ __('contact.hours.sunday') }}
                </dt>
                <dd class="font-medium text-slate-900">
                    {{ __('contact.hours.closed') }}
                </dd>
            </div>
        </dl>
    </div>
</aside>