<section class="relative isolate overflow-hidden bg-[#f7f8f6]">
    {{-- Mobilde üstte, masaüstünde tüm bölümün arka planında --}}
    <img
        src="{{ asset('images/dental/hero.png') }}"
        alt=""
        fetchpriority="high"
        class="h-72 w-full object-cover object-right sm:h-96 lg:absolute lg:inset-0 lg:h-full"
    >

    {{-- Soldaki metinlerin okunmasını sağlayan renk geçişi --}}
    <div
        aria-hidden="true"
        class="pointer-events-none absolute inset-0 hidden bg-linear-to-r from-[#f7f8f6] from-0% via-[#f7f8f6]/85 via-30% to-transparent to-65% lg:block"
    ></div>

    <div class="relative mx-auto flex max-w-7xl items-center px-6 py-12 lg:min-h-[700px] lg:py-20">
        <div class="w-full lg:max-w-[560px]">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#657780] sm:text-sm">
                {{ __('hero.eyebrow') }}
            </p>

            <h1 class="mt-5 font-serif text-4xl leading-[1.08] tracking-tight text-[#17313e] sm:text-5xl lg:text-[56px]">
                {{ __('hero.title') }}
            </h1>

            <p class="mt-7 max-w-[490px] text-base leading-7 text-[#637078] lg:text-lg">
                {{ __('hero.description') }}
            </p>

            <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                <a
                    href="{{ route('dental.' . app()->getLocale() . '.appointment') }}"
                    class="group inline-flex items-center justify-center gap-3 rounded-full bg-[#185564] px-7 py-4 text-sm font-semibold text-white transition-colors hover:bg-[#103e49]"
                >
                    {{ __('cta.book') }}

                    <x-heroicon-o-arrow-right
                        class="size-4 shrink-0 transition-transform duration-200 group-hover:translate-x-1"
                        aria-hidden="true"
                    />
                </a>

                <a
                    href="#treatments"
                    class="inline-flex items-center justify-center rounded-full border-2 border-[#b5c6cb] bg-white/30 px-7 py-4 text-sm font-semibold text-[#355866] transition-colors hover:border-[#355866] hover:bg-white/70"
                >
                    {{ __('cta.explore') }}
                </a>
            </div>
        </div>
    </div>
        </section>