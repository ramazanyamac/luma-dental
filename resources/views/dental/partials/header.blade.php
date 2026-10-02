@php
    $locale = app()->getLocale();
    $page = $page ?? 'home';

    $homeUrl = route("dental.{$locale}.home");
    $contactUrl = route("dental.{$locale}.contact");
    $appointmentUrl = route("dental.{$locale}.appointment");
@endphp

<header data-site-header class="border-b border-slate-100 bg-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-6 py-5 lg:gap-6">

        <a
            href="{{ $homeUrl }}"
            class="text-xl font-semibold tracking-widest text-teal-900"
        >
            <img
                src="{{ asset('images/dental/logo.png') }}"
                alt="LUMA DENTAL"
                class="h-12 w-auto"
            >
        </a>

        <button
            type="button"
            data-menu-toggle
            aria-expanded="false"
            aria-controls="site-navigation"
            aria-label="{{ $locale === 'tr' ? 'Menüyü aç' : 'Open menu' }}"
            data-open-label="{{ $locale === 'tr' ? 'Menüyü aç' : 'Open menu' }}"
            data-close-label="{{ $locale === 'tr' ? 'Menüyü kapat' : 'Close menu' }}"
            class="inline-flex size-11 items-center justify-center rounded-full border border-stone-200 text-teal-900 transition-colors hover:bg-stone-50 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-800 lg:hidden"
        >
            <x-heroicon-o-bars-3 data-menu-open-icon class="size-6" aria-hidden="true" />
            <x-heroicon-o-x-mark data-menu-close-icon class="hidden size-6" aria-hidden="true" />
        </button>

        <div id="site-navigation" class="hidden w-full border-t border-stone-100 pt-4 lg:flex lg:w-auto lg:flex-1 lg:items-center lg:justify-end lg:gap-8 lg:border-0 lg:pt-0">
        <nav
            class="flex flex-col gap-1 text-sm font-medium lg:mr-auto lg:ml-auto lg:flex-row lg:items-center lg:gap-8"
            aria-label="{{ $locale === 'tr' ? 'Ana menü' : 'Main navigation' }}"
        >
            <a
                href="{{ $homeUrl }}#about"
                class="rounded-lg px-3 py-3 underline-offset-8 hover:bg-stone-50 focus-visible:outline-teal-800 lg:p-0 lg:hover:bg-transparent hover:text-teal-700 hover:underline"
            >
                {{ __('nav.about') }}
            </a>

            <a
                href="{{ $homeUrl }}#treatments"
                class="rounded-lg px-3 py-3 underline-offset-8 hover:bg-stone-50 focus-visible:outline-teal-800 lg:p-0 lg:hover:bg-transparent hover:text-teal-700 hover:underline"
            >
                {{ __('nav.treatments') }}
            </a>

            <a
                href="{{ $homeUrl }}#team"
                class="rounded-lg px-3 py-3 underline-offset-8 hover:bg-stone-50 focus-visible:outline-teal-800 lg:p-0 lg:hover:bg-transparent hover:text-teal-700 hover:underline"
            >
                {{ __('nav.team') }}
            </a>

            <a
                href="{{ $contactUrl }}"
                @if ($page === 'contact') aria-current="page" @endif
                class="rounded-lg px-3 py-3 underline-offset-8 hover:bg-stone-50 focus-visible:outline-teal-800 lg:p-0 lg:hover:bg-transparent hover:text-teal-700 hover:underline {{ $page === 'contact' ? 'text-teal-900 underline' : '' }}"
            >
                {{ __('nav.contact') }}
            </a>
        </nav>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-4 border-t border-stone-100 pt-5 pb-2 lg:mt-0 lg:flex-nowrap lg:border-0 lg:p-0">
            <div
                class="shrink-0 text-sm font-semibold"
                role="group"
                aria-label="{{ $locale === 'tr' ? 'Dil seçimi' : 'Language selection' }}"
            >
                <a
                    href="{{ route('dental.switch-en.' . $page) }}"
                    lang="en"
                    hreflang="en"
                    aria-label="English"
                    class="{{ $locale === 'en' ? 'text-teal-900' : 'text-slate-400' }}"
                >
                    EN
                </a>

                <span class="mx-1 text-slate-300" aria-hidden="true">|</span>

                <a
                    href="{{ route('dental.tr.' . $page) }}"
                    lang="tr"
                    hreflang="tr"
                    aria-label="Türkçe"
                    class="{{ $locale === 'tr' ? 'text-teal-900' : 'text-slate-400' }}"
                >
                    TR
                </a>
            </div>

            <a
                href="{{ $appointmentUrl }}"
                class="group inline-flex items-center gap-2 rounded-full bg-teal-900 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-teal-800 sm:px-7"
            >
                {{ __('cta.book') }}

                <x-heroicon-o-arrow-right
                    class="size-4 shrink-0 transition-transform duration-200 group-hover:translate-x-1"
                    aria-hidden="true"
                />
            </a>
        </div>
        </div>
    </div>
</header>