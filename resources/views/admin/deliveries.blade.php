<div class="mt-5 space-y-3">
    @forelse ($deliveries as $delivery)
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-stone-200 bg-white p-4 text-sm">
            <div class="min-w-0">
                <p class="font-semibold">{{ __('operations.kind.' . $delivery->kind) }} · {{ __('operations.status.' . $delivery->status) }}</p>
                <p class="mt-1 break-all text-slate-500">{{ $delivery->recipient }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('operations.attempts') }}: {{ $delivery->attempts }} · {{ $delivery->attempted_at?->setTimezone(config('appointments.timezone'))->format('d/m/Y H:i') ?? '—' }}</p>
                @if ($delivery->appointment_id)<a class="mt-2 inline-block text-teal-800 underline" href="{{ \App\Support\AdminUrl::route('admin.appointments.show', ['appointment' => $delivery->appointment_id]) }}">{{ __('management.details') }}</a>@endif
                @if ($delivery->contact_message_id)<a class="mt-2 inline-block text-teal-800 underline" href="{{ \App\Support\AdminUrl::route('admin.contacts.show', ['contactMessage' => $delivery->contact_message_id]) }}">{{ __('operations.message') }}</a>@endif
            </div>
            @if ($delivery->status === 'failed')
                <form method="POST" action="{{ \App\Support\AdminUrl::route('admin.deliveries.retry', ['mailDelivery' => $delivery]) }}">@csrf<button class="rounded-lg bg-teal-900 px-4 py-2 text-white">{{ __('management.resend') }}</button></form>
            @endif
        </div>
    @empty
        <p class="text-sm text-slate-500">{{ __('operations.no_deliveries') }}</p>
    @endforelse
</div>
{{ $deliveries->withQueryString()->links() }}
