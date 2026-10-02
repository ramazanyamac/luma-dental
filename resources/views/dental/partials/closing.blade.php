<section class="bg-[#fafaf8] py-8">
    <div class="mx-auto max-w-7xl px-6">
        <div class="relative isolate overflow-hidden rounded-2xl bg-[#20515b]">

            <img
                src="{{ asset('images/dental/12_cta_background.png') }}"
                alt=""
                class="absolute inset-0 h-full w-full object-cover object-center"
                loading="lazy"
            >

            <div
                aria-hidden="true"
                class="absolute inset-0 bg-gradient-to-r from-[#17434b]/95 via-[#17434b]/75 to-[#17434b]/15"
            ></div>

            <div class="relative grid min-h-[360px] items-center gap-8 px-6 py-10 sm:px-10 lg:min-h-[370px] lg:grid-cols-2 lg:px-12">

                {{-- Soldaki yazı ve butonlar --}}
                <article class="max-w-xl">
                    <h2 class="font-serif text-4xl leading-tight text-white sm:text-5xl">
                        {{ __('closing.title') }}
                    </h2>

                    <p class="mt-5 max-w-lg text-base leading-7 text-white/85 sm:text-lg">
                        {{ __('closing.description') }}
                    </p>

                    <div class="mt-7 flex flex-wrap items-center gap-4">
                        <a
                            href="{{ route('dental.' . app()->getLocale() . '.appointment') }}"
                            class="group inline-flex items-center gap-3 rounded-full bg-white px-6 py-3.5 font-semibold text-[#20515b] transition hover:bg-[#eef5f3]"
                        >
                            {{ __('cta.book') }}

                            <x-heroicon-o-arrow-right
                                class="size-5 transition-transform duration-300 group-hover:translate-x-1"
                            />
                        </a>

                        <a
                            href="{{ route('dental.' . app()->getLocale() . '.contact') }}"
                            class="inline-flex items-center rounded-full border border-white/40 px-6 py-3.5 font-semibold text-white transition hover:bg-white/10"
                        >
                            {{ __('cta.contact') }}
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>