<footer id="contact" class="bg-[#fafaf8] text-[#637078]">
    <div class="mx-auto max-w-7xl px-6 pt-12 pb-6">

        <div class="grid gap-10 pb-10 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1.15fr_0.75fr]">
            {{-- Logo ve açıklama --}}
            <div>
                <a
                    href="{{ app()->getLocale() === 'tr' ? url('/tr') : url('/') }}"
                    class="inline-block"
                >
                    <img
                        src="{{ asset('images/dental/logo.png') }}"
                        alt="Luma Dental"
                        class="h-auto w-48"
                        loading="lazy"
                    >
                </a>

                <p class="mt-5 max-w-sm text-sm leading-7">
                    {{ __('footer.tagline') }}
                    <br>
                    {{ __('footer.description') }}
                </p>
            </div>

            {{-- Hızlı bağlantılar --}}
            <nav aria-label="{{ __('footer.quick_links') }}">
                <h2 class="text-sm font-semibold text-[#355461]">
                    {{ __('footer.quick_links') }}
                </h2>

                <ul class="mt-4 space-y-3 text-sm">
                    <li>
                        <a
                            href="{{ app()->getLocale() === 'tr' ? url('/tr') : url('/') }}"
                            class="underline-offset-4 hover:text-teal-900 hover:underline"
                        >
                            {{ __('nav.home') }}
                        </a>
                    </li>

                    @foreach ([
                        'about' => 'nav.about',
                        'treatments' => 'nav.treatments',
                        'team' => 'nav.team',
                        'contact' => 'nav.contact'
                    ] as $target => $label)
                        <li>
                            <a
                                href="{{ $target === 'contact' ? route('dental.' . app()->getLocale() . '.contact') : route('dental.' . app()->getLocale() . '.home') . '#' . $target }}"
                                class="underline-offset-4 hover:text-teal-900 hover:underline"
                            >
                                {{ __($label) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            {{-- Tedaviler --}}
            <nav aria-label="{{ __('treatments.eyebrow') }}">
                <h2 class="text-sm font-semibold text-[#355461]">
                    {{ __('treatments.eyebrow') }}
                </h2>

                <ul class="mt-4 space-y-3 text-sm">
                    @foreach (['general', 'cosmetic', 'implants', 'orthodontics'] as $treatment)
                        <li>
                            <a
                                href="{{ route('dental.' . app()->getLocale() . '.home') }}#treatment-{{ $treatment }}"
                                class="underline-offset-4 hover:text-teal-900 hover:underline"
                            >
                                {{ __('treatments.' . $treatment . '.title') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            {{-- Sosyal medya --}}
            <div>
                <h2 class="text-sm font-semibold text-[#355461]">
                    {{ __('footer.follow') }}
                </h2>

                <div class="mt-4 flex items-center gap-5 text-[#285968]">
                    @foreach ([
                        'Instagram' => 'bi-instagram',
                        'Facebook' => 'bi-facebook',
                        'YouTube' => 'bi-youtube',
                        'LinkedIn' => 'bi-linkedin'
                    ] as $platform => $icon)
                        <span role="img" aria-label="{{ $platform }}">
                            <x-dynamic-component
                                :component="$icon"
                                class="size-5"
                                aria-hidden="true"
                            />
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Alt satır --}}
        <div class="flex flex-col gap-4 border-t border-[#e3e8e6] pt-5 text-xs sm:flex-row sm:items-center sm:justify-between">
            <p>
                © {{ date('Y') }} Luma Dental.
                {{ __('footer.rights') }}
            </p>

            <div class="flex flex-wrap items-center gap-3">
                <span>{{ __('footer.privacy') }}</span>
                <span aria-hidden="true" class="text-slate-300">|</span>
                <span>{{ __('footer.terms') }}</span>
            </div>
        </div>
    </div>
</footer>