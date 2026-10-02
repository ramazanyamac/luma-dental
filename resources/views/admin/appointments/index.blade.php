@extends('admin.layouts.app')
@section('title', __('management.title'))
@section('content')
<div class="mb-8">
    <h1 class="font-serif text-3xl text-teal-950">{{ __('management.title') }}</h1>
    <p class="mt-2 text-sm text-slate-500">{{ __('management.description') }}</p>
</div>
<form method="GET" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-5 sm:grid-cols-2 lg:grid-cols-4">
    <label class="text-sm">{{ __('management.search') }}<input name="search" value="{{ $filters['search'] ?? '' }}" maxlength="120" class="mt-2 block w-full rounded-lg border border-stone-300 px-3 py-2"></label>
    <label class="text-sm">{{ __('management.date') }}<input type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="mt-2 block w-full rounded-lg border border-stone-300 px-3 py-2"></label>
    <label class="text-sm">{{ __('management.status') }}<select name="status" class="mt-2 block w-full rounded-lg border border-stone-300 px-3 py-2">
        <option value="">{{ __('management.all') }}</option>
        @foreach (['confirmed', 'cancelled'] as $status)
            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ __('management.' . $status) }}</option>
        @endforeach
    </select></label>
    <div class="flex items-end gap-4"><button class="rounded-lg bg-teal-900 px-5 py-2 text-sm font-semibold text-white">{{ __('management.filter') }}</button><a href="{{ \App\Support\AdminUrl::route('admin.appointments.index') }}" class="text-sm text-teal-800 underline">{{ __('management.clear') }}</a></div>
</form>
<div class="space-y-3">
    @forelse ($appointments as $appointment)
        <article class="grid gap-4 rounded-xl border border-stone-200 bg-white p-5 sm:grid-cols-2 lg:grid-cols-[1.2fr_1fr_1fr_auto] lg:items-center">
            <div><h2 class="font-semibold text-teal-950">{{ $appointment->name }}</h2><p class="mt-1 break-all text-sm text-slate-500">{{ $appointment->email }}</p></div>
            <div class="text-sm"><p>{{ $appointment->starts_at->setTimezone(config('appointments.timezone'))->translatedFormat('d F Y H:i') }}</p><p class="mt-1 text-slate-500">{{ __('appointment.treatments.' . $appointment->treatment->code) }}</p></div>
            <div class="text-sm"><span class="inline-flex rounded-full px-3 py-1 {{ $appointment->status === 'confirmed' ? 'bg-teal-50 text-teal-800' : 'bg-stone-100 text-slate-600' }}">{{ __('management.' . $appointment->status) }}</span><p class="mt-2 text-xs text-slate-500">{{ __('management.email') }}: {{ __('management.mail.' . $appointment->mail_status) }}</p></div>
            <a href="{{ \App\Support\AdminUrl::route('admin.appointments.show', ['appointment' => $appointment]) }}" class="text-sm font-semibold text-teal-800 underline underline-offset-4">{{ __('management.view') }}<span class="sr-only"> — {{ $appointment->name }}</span></a>
        </article>
    @empty
        <p class="rounded-xl border border-stone-200 bg-white p-8 text-slate-500">{{ __('management.empty') }}</p>
    @endforelse
</div>
<div class="mt-6">{{ $appointments->links() }}</div>
@endsection
