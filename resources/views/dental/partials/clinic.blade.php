<section id="about" class="bg-[#fafaf8] py-16 lg:py-20">
    <div class="grid lg:grid-cols-2">
        {{-- Ekranın sol kenarına yaslanan görsel --}}
        <div class="relative h-80 sm:h-[440px] lg:h-auto lg:min-h-[580px]">
            <img
                src="{{ asset('images/dental/06_clinic_interior.png') }}"
                alt="{{ __('clinic.image_alt') }}"
                class="absolute inset-0 h-full w-full rounded-r-2xl object-cover"
                loading="lazy"
            >
        </div>

        {{-- Metin ve özellikler --}}
        <div class="flex items-center px-6 pt-10 lg:py-6 lg:pl-12 lg:pr-8 xl:pl-14">
            <div class="w-full max-w-[600px]">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#7c8d94]">
                    {{ __('clinic.eyebrow') }}
                </p>

                <h2 class="mt-4 font-serif text-4xl leading-[1.12] tracking-tight text-[#17313e] xl:text-5xl">
                    {{ __('clinic.title') }}
                </h2>

                <p class="mt-5 text-base leading-7 text-[#778187]">
                    {{ __('clinic.description') }}
                </p>

                <div class="mt-10 grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-4">
                    @foreach ([
                        'environment' => 'bi-gem',
                        'team' => 'pepicon-people',
                        'standards' => 'heroicon-o-shield-check',
                        'experience' => 'akar-leaf'
                    ] as $feature => $icon)
                        <div class="flex flex-col items-center text-center">
                            <x-dynamic-component
                                :component="$icon"
                                class="size-10 text-[#437582]"
                                aria-hidden="true"
                            />

                            <p class="mt-4 text-xs font-medium leading-6 text-[#778187]">
                                {{ __('clinic.' . $feature) }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <a
                    href="#team"
                    class="group mt-9 inline-flex items-center justify-center gap-3 rounded-full border-2 border-[#b5c6cb] px-8 py-3.5 text-sm font-semibold text-[#355866] transition-colors hover:border-[#355866] hover:bg-white"
                >
                    {{ __('clinic.story') }}

                    <x-heroicon-o-arrow-right
                        class="size-4 transition-transform duration-200 group-hover:translate-x-1"
                        aria-hidden="true"
                    />
                </a>
            </div>
        </div>
    </div>
</section>