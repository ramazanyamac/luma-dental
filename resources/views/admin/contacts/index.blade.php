@extends('admin.layouts.app')
@section('title', __('operations.contacts'))
@section('content')
<h1 class="font-serif text-3xl text-teal-950">{{ __('operations.contacts') }}</h1>
<div class="mt-6 space-y-3">
@forelse ($messages as $message)
    <a class="block rounded-xl border border-stone-200 bg-white p-5 hover:border-teal-600" href="{{ \App\Support\AdminUrl::route('admin.contacts.show', ['contactMessage' => $message]) }}">
        <p class="font-semibold">{{ $message->name }}</p>
        <p class="mt-1 break-all text-sm text-slate-500">{{ $message->email }} · {{ $message->created_at->setTimezone(config('appointments.timezone'))->format('d/m/Y H:i') }}</p>
        <p class="mt-2 text-sm">{{ __('operations.status.' . ($message->latestDelivery?->status ?? 'unknown')) }}</p>
    </a>
@empty
    <p class="text-slate-500">{{ __('operations.no_contacts') }}</p>
@endforelse
</div>
<div class="mt-6">{{ $messages->links() }}</div>
@endsection
