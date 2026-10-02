<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MailDelivery;
use App\Services\AppointmentManagementService;
use App\Services\AppointmentNotificationService;
use App\Support\AdminUrl;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['confirmed', 'cancelled'])],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);
        $query = Appointment::with('treatment');
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['date'])) {
            $start = CarbonImmutable::parse($filters['date'], config('appointments.timezone'))->startOfDay();
            $query->where('starts_at', '>=', $start->utc())->where('starts_at', '<', $start->addDay()->utc());
        }
        if (! empty($filters['search'])) {
            $term = '%'.$filters['search'].'%';
            $query->where(function ($query) use ($term) {
                $query->where('name', 'ilike', $term)->orWhere('email', 'ilike', $term)->orWhere('reference', 'ilike', $term);
            });
        }

        return view('admin.appointments.index', [
            'appointments' => $query->orderByDesc('starts_at')->orderByDesc('id')->paginate(20)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function show(Appointment $appointment): View
    {
        return view('admin.appointments.show', ['appointment' => $appointment->load('treatment'), 'deliveries' => MailDelivery::where('appointment_id', $appointment->id)->latest('id')->paginate(10)]);
    }

    public function update(Request $request, Appointment $appointment, AppointmentManagementService $management): RedirectResponse
    {
        $data = $request->validate(['starts_at' => ['required', 'date_format:Y-m-d\TH:i']]);
        $start = CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $data['starts_at'], config('appointments.timezone'))->utc();
        $management->reschedule($appointment, $start);
        $sent = $appointment->fresh()->mail_status === 'sent';

        return redirect()->to(AdminUrl::route('admin.appointments.show', ['appointment' => $appointment]))
            ->with('success', __('management.rescheduled'))
            ->with('mail_status', __($sent ? 'management.mail_sent' : 'operations.queued'));
    }

    public function cancel(Request $request, Appointment $appointment, AppointmentManagementService $management): RedirectResponse
    {
        $request->validate(['confirm_cancel' => ['accepted']]);
        $cancelled = $management->cancel($appointment);
        $sent = $cancelled ? $appointment->fresh()->cancellation_mail_status === 'sent' : null;

        return redirect()->to(AdminUrl::route('admin.appointments.show', ['appointment' => $appointment]))
            ->with('success', __('management.cancelled_notice'))
            ->with('mail_status', $sent === null ? null : __($sent ? 'management.mail_sent' : 'operations.queued'));
    }

    public function resend(Appointment $appointment, AppointmentNotificationService $notifications): RedirectResponse
    {
        $sent = $appointment->status === 'cancelled'
            ? $notifications->sendCancellation($appointment, resend: true)
            : $notifications->send($appointment);

        return redirect()->to(AdminUrl::route('admin.appointments.show', ['appointment' => $appointment]))
            ->with('mail_status', __($sent ? 'management.mail_sent' : 'operations.queued'));
    }
}
