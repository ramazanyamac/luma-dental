<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Services\AppointmentAvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentAvailabilityController extends Controller
{
    public function index(
        Request $request,
        AppointmentAvailabilityService $availability
    ): JsonResponse {
        $timezone = config('appointments.timezone');
        $today = CarbonImmutable::now($timezone)->toDateString();

        $validated = $request->validate([
            'date' => [
                'bail',
                'required',
                'date_format:Y-m-d',
                'after_or_equal:' . $today,
            ],
            'treatment_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('treatments', 'id')
                    ->where('is_active', true),
            ],
        ]);

        $treatment = Treatment::findOrFail(
            $validated['treatment_id']
        );

        return response()->json([
            'date' => $validated['date'],
            'timezone' => $timezone,
            'treatment_id' => $treatment->id,
            'duration_minutes' => $treatment->duration_minutes,
            'slots' => $availability->slotsForDate(
                $treatment,
                $validated['date']
            ),
        ])->header('Cache-Control', 'no-store');
    }

    public function dates(
        Request $request,
        AppointmentAvailabilityService $availability
    ): JsonResponse {
        $validated = $request->validate([
            'month' => [
                'bail',
                'required',
                'date_format:Y-m',
            ],
            'treatment_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('treatments', 'id')
                    ->where('is_active', true),
            ],
        ]);

        $treatment = Treatment::findOrFail(
            $validated['treatment_id']
        );

        return response()->json([
            'month' => $validated['month'],
            'timezone' => config('appointments.timezone'),
            'treatment_id' => $treatment->id,
            'dates' => $availability->datesForMonth(
                $treatment,
                $validated['month']
            ),
        ])->header('Cache-Control', 'no-store');
    }
}
