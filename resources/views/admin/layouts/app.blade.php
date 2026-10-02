<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', __('admin.common.administration')) | Luma Dental</title>

    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-stone-50 text-slate-800 antialiased">
    @php
    $currentRoute = request()->route();

    $routeParameters = array_merge(
    request()->except('lang'),
    $currentRoute->parameters()
    );
    @endphp

    <header class="border-b border-stone-200 bg-white">
        <div class="mx-auto flex flex-wrap max-w-6xl items-center justify-between gap-4 px-6 py-5">
            <a href="{{ \App\Support\AdminUrl::route('admin.dashboard') }}">
                <img
                    src="{{ asset('images/dental/logo.png') }}"
                    alt="Luma Dental"
                    class="h-12 w-auto">
            </a>

            <div class="flex items-center gap-5">
                <nav
                    aria-label="{{ __('admin.common.language') }}"
                    class="flex items-center gap-2 text-sm font-semibold">
                    <a
                        href="{{ \App\Support\AdminUrl::route(
                            $currentRoute->getName(),
                            array_merge($routeParameters, ['lang' => 'en']),
                            'en'
                        ) }}"
                        lang="en"
                        class="{{ app()->getLocale() === 'en' ? 'text-teal-900' : 'text-slate-400' }}">
                        EN
                    </a>

                    <span class="text-slate-300">|</span>

                    <a
                        href="{{ \App\Support\AdminUrl::route(
                            $currentRoute->getName(),
                            $routeParameters,
                            'tr'
                        ) }}"
                        lang="tr"
                        class="{{ app()->getLocale() === 'tr' ? 'text-teal-900' : 'text-slate-400' }}">
                        TR
                    </a>
                </nav>

                @if (auth()->user()?->is_admin)
                <form
                    method="POST"
                    action="{{ \App\Support\AdminUrl::route('admin.logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium transition hover:bg-stone-100">
                        {{ __('admin.auth.sign_out') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-12">
        @if (auth()->user()?->is_admin)
            <nav class="mb-8 flex flex-wrap gap-5 text-sm font-semibold text-teal-800" aria-label="{{ __('admin.common.administration') }}">
                <a href="{{ \App\Support\AdminUrl::route('admin.dashboard') }}" class="underline underline-offset-4">{{ __('admin.dashboard.title') }}</a>
                <a href="{{ \App\Support\AdminUrl::route('admin.contacts.index') }}" class="underline underline-offset-4">{{ __('operations.contacts') }}</a>
                <a href="{{ \App\Support\AdminUrl::route('admin.appointments.index') }}" class="underline underline-offset-4">{{ __('management.title') }}</a>
                <a href="{{ \App\Support\AdminUrl::route('admin.availability.index') }}" class="underline underline-offset-4">{{ __('admin.dashboard.manage_availability') }}</a>
            </nav>
        @endif
        @if (auth()->user()?->is_admin)
            @foreach (['success', 'mail_status'] as $message)
                @if (session($message))<p role="status" class="mb-5 rounded-lg border border-teal-200 bg-teal-50 p-4 text-sm text-teal-900">{{ session($message) }}</p>@endif
            @endforeach
            @if ($errors->any())<ul role="alert" class="mb-5 rounded-lg bg-red-50 p-4 text-sm text-red-800">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
        @endif
        @yield('content')
    </main>
</body>

</html>