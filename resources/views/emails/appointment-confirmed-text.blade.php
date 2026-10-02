{!! __(($cancelled ?? false) ? (($forClinic ?? false) ? 'appointment.cancellation.clinic_subject' : 'appointment.cancellation.subject') : (($rescheduled ?? false) ? 'appointment.rescheduled.subject' : 'appointment.mail.subject')) !!}

{!! ($forClinic ?? false) ? __('management.patient') . ': ' . $appointment->name : __('appointment.mail.greeting', ['name' => $appointment->name]) !!}
{!! __(($cancelled ?? false) ? (($forClinic ?? false) ? 'appointment.cancellation.clinic_description' : 'appointment.cancellation.description') : (($rescheduled ?? false) ? 'appointment.rescheduled.description' : 'appointment.success.description')) !!}

{!! __('appointment.review.treatment') !!}: {!! __('appointment.treatments.' . $appointment->treatment->code) !!}
@if ($rescheduled ?? false)
{!! __('appointment.rescheduled.old_time') !!}: {!! \Carbon\CarbonImmutable::parse($oldStartsAt)->setTimezone(config('appointments.timezone'))->translatedFormat('d F Y H:i') !!}
@endif
{!! __('appointment.review.datetime') !!}: {!! $appointment->starts_at->setTimezone(config('appointments.timezone'))->translatedFormat('d F Y H:i') !!} ({!! config('appointments.timezone') !!})
{!! __('appointment.review.duration') !!}: {!! __('appointment.duration', ['minutes' => $appointment->starts_at->diffInMinutes($appointment->ends_at)]) !!}
{!! __('appointment.review.location') !!}: {!! __('appointment.review.address') !!}
{!! __('appointment.success.reference') !!}: {!! $appointment->reference !!}

Luma Dental

{!! __('management.manage') !!}: {!! \App\Support\AppointmentLinks::for($appointment) !!}
{!! __('management.private') !!}
