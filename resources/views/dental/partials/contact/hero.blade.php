<section
    class="relative isolate overflow-hidden bg-[#f5f2ed]"
    aria-labelledby="contact-heading"
>
    <img
        src="{{ asset('images/dental/contact-hero.png') }}"
        alt=""
        class="absolute inset-0 h-full w-full object-cover object-right"
        fetchpriority="high"
    >

    <div
        aria-hidden="true"
        class="absolute inset-0 bg-linear-to-r from-[#faf8f4]/95 via-[#faf8f4]/80 to-[#faf8f4]/20"
    ></div>

    <div class="relative mx-auto flex min-h-[420px] max-w-7xl items-center px-6 py-14 lg:min-h-[460px]">
        <div class="w-full max-w-3xl">
            <h1
                id="contact-heading"
                class="font-serif text-5xl leading-tight text-slate-950 sm:text-6xl"
            >
                {{ __('contact.hero.title') }}
            </h1>

            <p class="mt-5 max-w-xl text-base leading-7 text-slate-700">
                {{ __('contact.hero.description') }}
            </p>

            <div class="mt-9 grid gap-6 sm:grid-cols-3">

                <a
                    href="tel:+902123456789"
                    class="group flex items-center gap-3"
                >
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-teal-900 text-white transition-colors group-hover:bg-teal-800">
                        <x-heroicon-o-phone
                            class="size-5"
                            aria-hidden="true"
                        />
                    </span>

                    <span>
                        <span class="block text-sm font-medium text-slate-950">
                            {{ __('contact.phone') }}
                        </span>

                        <span class="mt-1 block text-sm text-slate-700">
                            +90 212 345 67 89
                        </span>
                    </span>
                </a>

                <a
                    href="mailto:info@lumadental.com"
                    class="group flex items-center gap-3"
                >
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-teal-900 text-white transition-colors group-hover:bg-teal-800">
                        <x-heroicon-o-envelope
                            class="size-5"
                            aria-hidden="true"
                        />
                    </span>

                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-slate-950">
                            {{ __('contact.email') }}
                        </span>

                        <span class="mt-1 block break-words text-sm text-slate-700">
                            info@lumadental.com
                        </span>
                    </span>
                </a>

                <div class="flex items-center gap-3">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-teal-900 text-white">
                        <x-heroicon-o-map-pin
                            class="size-5"
                            aria-hidden="true"
                        />
                    </span>

                    <div>
                        <span class="block text-sm font-medium text-slate-950">
                            {{ __('contact.visit') }}
                        </span>

                        <span class="mt-1 block text-sm text-slate-700">
                            {{ __('contact.location') }}
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>