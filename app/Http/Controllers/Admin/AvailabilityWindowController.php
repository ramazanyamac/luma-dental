<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvailabilityWindow;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use App\Support\AdminUrl;

class AvailabilityWindowController extends Controller
{
    public function index(): View
    {
        $timezone = config('appointments.timezone');

        $windows = AvailabilityWindow::query()
            ->where('ends_at', '>', CarbonImmutable::now('UTC'))
            ->orderBy('starts_at')
            ->paginate(20);

        return view('admin.availability.index', [
            'windows' => $windows,
            'timezone' => $timezone,
            'today' => CarbonImmutable::now($timezone)->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $timezone = config('appointments.timezone');
        $today = CarbonImmutable::now($timezone)->toDateString();

        $validated = $request->validate(
            [
                'date' => [
                    'bail',
                    'required',
                    'date_format:Y-m-d',
                    'after_or_equal:' . $today,
                ],
                'start_time' => [
                    'bail',
                    'required',
                    'date_format:H:i',
                ],
                'end_time' => [
                    'bail',
                    'required',
                    'date_format:H:i',
                    'after:start_time',
                ],
            ],
            [
                'date.after_or_equal' => __(
                    'admin.availability.validation.date_future'
                ),
                'end_time.after' => __(
                    'admin.availability.validation.end_after_start'
                ),
            ]
        );

        $startsAt = CarbonImmutable::createFromFormat(
            '!Y-m-d H:i',
            $validated['date'] . ' ' . $validated['start_time'],
            $timezone
        )->utc();

        $endsAt = CarbonImmutable::createFromFormat(
            '!Y-m-d H:i',
            $validated['date'] . ' ' . $validated['end_time'],
            $timezone
        )->utc();

        if ($startsAt->lessThanOrEqualTo(CarbonImmutable::now('UTC'))) {
            throw ValidationException::withMessages([
                'start_time' => __('admin.availability.validation.start_future'),
            ]);
        }

        try {
            AvailabilityWindow::create([
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() !== '23P01') {
                throw $exception;
            }

            throw ValidationException::withMessages([
                'start_time' => __('admin.availability.validation.overlap'),
            ]);
        }

        return redirect()
            ->to(AdminUrl::route('admin.availability.index'))
            ->with('success', __('admin.availability.success.created'));
    }

    public function destroy(
        AvailabilityWindow $availabilityWindow
    ): RedirectResponse {
        DB::transaction(function () use ($availabilityWindow) {
            $window = AvailabilityWindow::query()
                ->whereKey($availabilityWindow->id)
                ->lockForUpdate()
                ->firstOrFail();

            $hasAppointments = Appointment::query()
                ->where('status', 'confirmed')
                ->where('starts_at', '<', $window->ends_at)
                ->where('ends_at', '>', $window->starts_at)
                ->exists();

            if ($hasAppointments) {
                throw ValidationException::withMessages([
                    'availability' => __(
                        'admin.availability.validation.has_appointments'
                    ),
                ]);
            }

            $window->delete();
        });

        return redirect()
            ->to(AdminUrl::route('admin.availability.index'))
            ->with('success', __('admin.availability.success.removed'));
    }

    public function edit(AvailabilityWindow $availabilityWindow): View
    {
        $timezone = config('appointments.timezone');

        return view('admin.availability.edit', [
            'window' => $availabilityWindow,
            'timezone' => $timezone,
            'today' => CarbonImmutable::now($timezone)->toDateString(),
        ]);
    }

    public function update(
        Request $request,
        AvailabilityWindow $availabilityWindow
    ): RedirectResponse {
        $timezone = config('appointments.timezone');
        $today = CarbonImmutable::now($timezone)->toDateString();

        $validated = $request->validate([
            'date' => [
                'bail',
                'required',
                'date_format:Y-m-d',
                'after_or_equal:' . $today,
            ],
            'start_time' => [
                'bail',
                'required',
                'date_format:H:i',
            ],
            'end_time' => [
                'bail',
                'required',
                'date_format:H:i',
                'after:start_time',
            ],
        ], [
            'date.after_or_equal' => 'Please select today or a future date.',
            'end_time.after' => 'The end time must be after the start time.',
        ]);

        $startsAt = CarbonImmutable::createFromFormat(
            '!Y-m-d H:i',
            $validated['date'] . ' ' . $validated['start_time'],
            $timezone
        )->utc();

        $endsAt = CarbonImmutable::createFromFormat(
            '!Y-m-d H:i',
            $validated['date'] . ' ' . $validated['end_time'],
            $timezone
        )->utc();

        if ($startsAt->lessThanOrEqualTo(CarbonImmutable::now('UTC'))) {
            throw ValidationException::withMessages([
                'start_time' => 'The start time must be in the future.',
            ]);
        }

        try {
            DB::transaction(function () use (
                $availabilityWindow,
                $startsAt,
                $endsAt
            ) {
                $window = AvailabilityWindow::query()
                    ->whereKey($availabilityWindow->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $wouldExcludeAppointments = Appointment::query()
                    ->where('status', 'confirmed')
                    ->where('starts_at', '<', $window->ends_at)
                    ->where('ends_at', '>', $window->starts_at)
                    ->where(function ($query) use ($startsAt, $endsAt) {
                        $query
                            ->where('starts_at', '<', $startsAt)
                            ->orWhere('ends_at', '>', $endsAt);
                    })
                    ->exists();

                if ($wouldExcludeAppointments) {
                    throw ValidationException::withMessages([
                        'availability' => __(
                            'admin.availability.validation.excludes_appointments'
                        ),
                    ]);
                }

                $window->update([
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                ]);
            });
        } catch (QueryException $exception) {
            if ($exception->getCode() !== '23P01') {
                throw $exception;
            }

            throw ValidationException::withMessages([
                'start_time' => 'This period overlaps another availability window.',
            ]);
        }

        return redirect()
            ->to(AdminUrl::route('admin.availability.index'))
            ->with('success', __('admin.availability.success.updated'));
    }
}
