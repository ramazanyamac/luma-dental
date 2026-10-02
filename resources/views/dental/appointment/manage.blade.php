<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow"><meta name="referrer" content="no-referrer">
    <title>{{ __('management.details') }} | Luma Dental</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-stone-50 text-slate-800">
    <header class="border-b border-stone-200 bg-white px-6 py-5"><a href="{{ route('dental.' . app()->getLocale() . '.home') }}"><img src="{{ asset('images/dental/logo.png') }}" alt="Luma Dental" class="mx-auto h-12 w-auto"></a></header>
    <main class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
        <h1 class="font-serif text-3xl text-teal-950">{{ __('management.details') }}</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">{{ __('management.manage_intro') }}</p>
        @if (session('success'))<p role="status" class="mt-5 rounded-lg bg-teal-50 p-4 text-sm text-teal-900">{{ session('success') }}</p>@endif
        @if ($errors->any())<ul role="alert" class="mt-5 rounded-lg bg-red-50 p-4 text-sm text-red-800">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
        <section class="mt-6 rounded-xl border border-stone-200 bg-white p-6 sm:p-8">
            <h2 class="font-serif text-xl text-teal-950">{{ $appointment->name }}</h2>
            @include('dental.appointment.record-details')
            <p class="mt-6 break-all border-t border-stone-200 pt-4 text-xs text-slate-500">{{ __('appointment.success.reference') }}: {{ $appointment->reference }}</p>
        </section>
        <div class="mt-5 flex flex-wrap gap-3">
            <a href="{{ \App\Support\AppointmentLinks::for($appointment, 'calendar') }}" class="rounded-full bg-teal-900 px-6 py-3 text-sm font-semibold text-white">{{ __('appointment.success.calendar') }}</a>
            <a href="{{ route('dental.' . app()->getLocale() . '.contact') }}" class="rounded-full border border-teal-900 px-6 py-3 text-sm font-semibold text-teal-900">{{ __('appointment.mail.contact') }}</a>
        </div>
        <p class="mt-4 text-sm text-slate-500">{{ __('management.change_request') }}</p>
        @if ($appointment->status === 'confirmed' && $appointment->starts_at->isFuture())
            <section class="mt-8 rounded-xl border border-red-200 bg-white p-6">
                <h2 class="font-serif text-xl text-red-900">{{ __('management.cancel') }}</h2>
                <p class="mt-3 text-sm leading-6 text-slate-500">{{ __('management.cancel_hint') }}</p>
                <form method="POST" action="{{ \App\Support\AppointmentLinks::for($appointment, 'cancel') }}" class="mt-5">@csrf
                    <label class="flex items-start gap-3 text-sm"><input type="checkbox" name="confirm_cancel" value="1" required class="mt-1">{{ __('management.confirm_cancel') }}</label>
                    <button class="mt-5 rounded-full border border-red-300 px-6 py-3 text-sm font-semibold text-red-800">{{ __('management.cancel') }}</button>
                </form>
            </section>
        @endif
        <p class="mt-8 text-center text-xs text-slate-500">{{ __('management.private') }}</p>
    </main>
</body>
</html>
