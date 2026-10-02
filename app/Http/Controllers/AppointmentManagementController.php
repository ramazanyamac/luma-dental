<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\AppointmentManagementService;
use App\Support\AppointmentCalendar;
use App\Support\AppointmentLinks;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AppointmentManagementController extends Controller
{
    public function show(Appointment $appointment): Response
    {
        app()->setLocale($appointment->locale);

        return response()->view('dental.appointment.manage', ['appointment' => $appointment->load('treatment')])
            ->withHeaders(['Cache-Control' => 'private, no-store', 'Referrer-Policy' => 'no-referrer', 'X-Robots-Tag' => 'noindex, nofollow']);
    }

    public function cancel(Request $request, Appointment $appointment, AppointmentManagementService $management): RedirectResponse
    {
        app()->setLocale($appointment->locale);
        $request->validate(['confirm_cancel' => ['accepted']]);
        $management->cancel($appointment, byPatient: true);

        return redirect()->to(AppointmentLinks::for($appointment))->with('success', __('management.cancelled_notice'));
    }

    public function calendar(Appointment $appointment): Response
    {
        app()->setLocale($appointment->locale);

        return response(AppointmentCalendar::render($appointment->load('treatment')))
            ->withHeaders([
                'Content-Type' => 'text/calendar; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="luma-'.$appointment->reference.'.ics"',
                'Cache-Control' => 'private, no-store',
                'Referrer-Policy' => 'no-referrer',
            ]);
    }
}
