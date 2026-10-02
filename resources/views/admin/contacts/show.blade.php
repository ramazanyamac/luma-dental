@extends('admin.layouts.app')
@section('title', __('operations.message'))
@section('content')
<a class="text-sm text-teal-800 underline" href="{{ \App\Support\AdminUrl::route('admin.contacts.index') }}">← {{ __('operations.contacts') }}</a>
<section class="mt-5 rounded-xl border border-stone-200 bg-white p-6">
    <h1 class="font-serif text-2xl text-teal-950">{{ $message->name }}</h1>
    <p class="mt-3 break-all text-sm">{{ $message->email }} · {{ $message->phone }}</p>
    <p class="mt-4 text-sm font-semibold">{{ __('contact.subject.' . $message->subject) }}</p>
    <p class="mt-3 whitespace-pre-wrap break-words text-sm leading-7">{{ $message->message }}</p>
    <form class="mt-5" method="POST" action="{{ \App\Support\AdminUrl::route('admin.contacts.resend', ['contactMessage' => $message]) }}">@csrf<button class="rounded-lg bg-teal-900 px-5 py-3 text-sm text-white">{{ __('management.resend') }}</button></form>
</section>
<h2 class="mt-8 font-serif text-xl">{{ __('operations.delivery_history') }}</h2>
@include('admin.deliveries')
@endsection
