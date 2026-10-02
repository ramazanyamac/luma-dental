@extends('admin.layouts.app')
@section('title', __('management.details'))
@section('content')
<a href="{{ \App\Support\AdminUrl::route('admin.appointments.index') }}" class="text-sm text-teal-800 underline">← {{ __('management.back') }}</a>
<h1 class="mt-5 font-serif text-3xl text-teal-950">{{ __('management.details') }}</h1>
<p class="mt-2 break-all font-mono text-xs text-slate-500">{{ $appointment->reference }}</p>
<div class="mt-7 grid items-start gap-6 lg:grid-cols-2">
    <section class="rounded-xl border border-stone-200 bg-white p-6">
        <h2 class="font-serif text-xl text-teal-950">{{ __('appointment.review.appointment') }}</h2>
        @include('dental.appointment.record-details')
    </section>
    <section class="rounded-xl border border-stone-200 bg-white p-6">
        <h2 class="font-serif text-xl text-teal-950">{{ __('management.patient') }}</h2>
        <dl class="mt-5 space-y-4 text-sm">
            @foreach ([
                'name' => $appointment->name, 'email' => $appointment->email, 'phone' => $appointment->phone,
                'birth_date' => $appointment->birth_date?->format('d/m/Y'),
                'gender' => $appointment->gender ? __('appointment.gender.' . $appointment->gender) : null,
                'reason' => $appointment->reason ? __('appointment.reasons.' . $appointment->reason) : null,
                'new_patient' => __($appointment->is_new_patient ? 'appointment.review.yes' : 'appointment.review.no'),
                'notes' => $appointment->notes,
            ] as $field => $value)
                <div><dt class="text-slate-500">{{ __('appointment.fields.' . $field) }}</dt><dd class="mt-1 whitespace-pre-wrap break-words">{{ $value ?: '—' }}</dd></div>
            @endforeach
        </dl>
    </section>
    <section class="rounded-xl border border-stone-200 bg-white p-6">
        <h2 class="font-serif text-xl text-teal-950">{{ __('management.email') }}</h2>
        <p class="mt-4 text-sm">{{ __('management.mail.' . $appointment->mail_status) }}</p>
        <dl class="mt-4 space-y-3 text-sm text-slate-500">
            <div><dt>{{ __('management.last_attempt') }}</dt><dd>{{ $appointment->mail_attempted_at?->setTimezone(config('appointments.timezone'))->format('d/m/Y H:i') ?? '—' }}</dd></div>
            <div><dt>{{ __('management.last_sent') }}</dt><dd>{{ $appointment->confirmation_sent_at?->setTimezone(config('appointments.timezone'))->format('d/m/Y H:i') ?? '—' }}</dd></div>
        </dl>
        @if ($appointment->status === 'confirmed' && $appointment->starts_at->isFuture())
            <form method="POST" action="{{ \App\Support\AdminUrl::route('admin.appointments.resend', ['appointment' => $appointment]) }}" class="mt-5">@csrf<button class="rounded-lg bg-teal-900 px-5 py-3 text-sm font-semibold text-white">{{ __('management.resend') }}</button></form>
        @endif
    </section>
    @if ($appointment->status === 'cancelled')
        <section class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="font-serif text-xl text-teal-950">{{ __('management.cancellation_email') }}</h2>
            <p class="mt-4 text-sm">{{ __('management.mail.' . $appointment->cancellation_mail_status) }}</p>
            <p class="mt-3 text-sm text-slate-500">{{ __('management.last_attempt') }}: {{ $appointment->cancellation_mail_attempted_at?->setTimezone(config('appointments.timezone'))->format('d/m/Y H:i') ?? '—' }}</p>
            <form method="POST" action="{{ \App\Support\AdminUrl::route('admin.appointments.resend', ['appointment' => $appointment]) }}" class="mt-5">@csrf<button class="rounded-lg bg-teal-900 px-5 py-3 text-sm font-semibold text-white">{{ __('management.resend') }}</button></form>
        </section>
    @endif
    @if ($appointment->status === 'confirmed' && $appointment->starts_at->isFuture())
        <section class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="font-serif text-xl text-teal-950">{{ __('management.reschedule') }}</h2>
            <p class="mt-3 text-sm leading-6 text-slate-500">{{ __('management.time_hint') }}</p>
            <form method="POST" action="{{ \App\Support\AdminUrl::route('admin.appointments.update', ['appointment' => $appointment]) }}" class="mt-5">
                @csrf @method('PATCH')
                <label class="block text-sm">{{ __('management.new_time') }}<input type="datetime-local" name="starts_at" required value="{{ old('starts_at', $appointment->starts_at->setTimezone(config('appointments.timezone'))->format('Y-m-d\TH:i')) }}" class="mt-2 block w-full rounded-lg border border-stone-300 px-3 py-2"></label>
                <button class="mt-4 rounded-lg bg-teal-900 px-5 py-3 text-sm font-semibold text-white">{{ __('management.save') }}</button>
            </form>
        </section>
        <section class="rounded-xl border border-red-200 bg-white p-6 lg:col-span-2">
            <h2 class="font-serif text-xl text-red-900">{{ __('management.cancel') }}</h2>
            <p class="mt-3 text-sm text-slate-500">{{ __('management.cancel_hint') }}</p>
            <form method="POST" action="{{ \App\Support\AdminUrl::route('admin.appointments.cancel', ['appointment' => $appointment]) }}" class="mt-4">@csrf
                <label class="flex items-start gap-3 text-sm"><input type="checkbox" name="confirm_cancel" value="1" required class="mt-1">{{ __('management.confirm_cancel') }}</label>
                <button class="mt-4 rounded-lg border border-red-300 px-5 py-3 text-sm font-semibold text-red-800">{{ __('management.cancel') }}</button>
            </form>
        </section>
    @endif
</div>
<section class="mt-8"><h2 class="font-serif text-xl">{{ __('operations.delivery_history') }}</h2>@include('admin.deliveries')</section>
@endsection
