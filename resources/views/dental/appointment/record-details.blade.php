<dl class="mt-5 space-y-4 text-sm">
    @foreach ([
        __('management.status') => __('management.' . $appointment->status),
        __('appointment.review.treatment') => __('appointment.treatments.' . $appointment->treatment->code),
        __('appointment.review.datetime') => $appointment->starts_at->setTimezone(config('appointments.timezone'))->translatedFormat('d F Y H:i') . ' (Europe/Istanbul)',
        __('appointment.review.duration') => __('appointment.duration', ['minutes' => $appointment->starts_at->diffInMinutes($appointment->ends_at)]),
        __('appointment.review.location') => __('appointment.review.address'),
    ] as $label => $value)
        <div><dt class="text-slate-500">{{ $label }}</dt><dd class="mt-1 break-words font-medium text-slate-800">{{ $value }}</dd></div>
    @endforeach
</dl>
