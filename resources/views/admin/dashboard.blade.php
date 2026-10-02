@extends('admin.layouts.app')
@section('title', __('admin.dashboard.title'))
@section('content')
<h1 class="font-serif text-3xl text-teal-950">{{ __('admin.dashboard.welcome', ['name' => auth()->user()->name]) }}</h1>
<div class="mt-7 grid gap-4 sm:grid-cols-3">
    @foreach (['today' => $todayCount, 'pending' => $pendingCount, 'failed' => $failedCount] as $label => $count)
        <section class="rounded-xl border border-stone-200 bg-white p-6"><h2 class="text-sm text-slate-500">{{ __('operations.' . $label) }}</h2><p class="mt-2 text-3xl font-semibold text-teal-950">{{ $count }}</p></section>
    @endforeach
</div>
<section class="mt-6 rounded-xl border border-stone-200 bg-white p-6">
    <h2 class="font-serif text-xl">{{ __('operations.next') }}</h2>
    @if ($nextAppointment)
        <a class="mt-4 block text-teal-800 underline" href="{{ \App\Support\AdminUrl::route('admin.appointments.show', ['appointment' => $nextAppointment]) }}">{{ $nextAppointment->name }} · {{ $nextAppointment->starts_at->setTimezone(config('appointments.timezone'))->format('d/m/Y H:i') }}</a>
    @else <p class="mt-4 text-sm text-slate-500">{{ __('operations.no_appointments') }}</p> @endif
</section>
<section class="mt-8">
    <h2 class="font-serif text-xl">{{ __('operations.today') }}</h2>
    <div class="mt-4 space-y-3">
    @forelse ($appointments as $appointment)
        <a class="flex flex-wrap justify-between gap-2 rounded-lg border border-stone-200 bg-white p-4 text-sm" href="{{ \App\Support\AdminUrl::route('admin.appointments.show', ['appointment' => $appointment]) }}"><span>{{ $appointment->starts_at->setTimezone(config('appointments.timezone'))->format('H:i') }} · {{ $appointment->name }}</span><span class="text-slate-500">{{ __('appointment.treatments.' . $appointment->treatment->code) }}</span></a>
    @empty <p class="text-sm text-slate-500">{{ __('operations.no_appointments') }}</p> @endforelse
    </div>
    {{ $appointments->withQueryString()->links() }}
</section>
<section class="mt-8"><h2 class="font-serif text-xl">{{ __('operations.failed') }}</h2>@include('admin.deliveries', ['deliveries' => $failedDeliveries])</section>
@endsection
