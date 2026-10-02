<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Services\AppointmentBookingService;
use App\Support\AppointmentLinks;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request, AppointmentBookingService $booking): JsonResponse
    {
        $appointment = $booking->book($request->validated());
        $appointment->load('treatment');
        $mailStatus = $appointment->fresh()->mail_status;

        return response()->json([
            'reference' => $appointment->reference,
            'manage_url' => AppointmentLinks::for($appointment),
            'treatment' => __('appointment.treatments.'.$appointment->treatment->code),
            'starts_at' => $appointment->starts_at->toIso8601String(),
            'ends_at' => $appointment->ends_at->toIso8601String(),
            'duration_minutes' => $appointment->starts_at->diffInMinutes($appointment->ends_at),
            'email' => $appointment->email,
            'mail_sent' => $mailStatus === 'sent',
            'mail_status' => $mailStatus,
        ], $appointment->wasRecentlyCreated ? 201 : 200)->header('Cache-Control', 'no-store');
    }
}
