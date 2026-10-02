<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentDetailsRequest;
use Illuminate\Http\JsonResponse;
use Propaganistas\LaravelPhone\PhoneNumber;

class AppointmentDetailsController extends Controller
{
    public function validateDetails(
        AppointmentDetailsRequest $request
    ): JsonResponse {
        return response()->json([
            'phone' => (new PhoneNumber($request->validated('phone'), $request->validated('phone_country')))->formatE164(),
            'message' => __('appointment.details.validated'),
        ]);
    }
}
