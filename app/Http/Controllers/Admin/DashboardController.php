<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MailDelivery;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $start = CarbonImmutable::now(config('appointments.timezone'))->startOfDay()->utc();
        $today = Appointment::where('status', 'confirmed')->where('starts_at', '>=', $start)->where('starts_at', '<', $start->addDay());

        return view('admin.dashboard', [
            'todayCount' => (clone $today)->count(),
            'appointments' => $today->with('treatment')->orderBy('starts_at')->paginate(10, ['*'], 'appointments_page'),
            'nextAppointment' => Appointment::with('treatment')->where('status', 'confirmed')->where('starts_at', '>=', now())->orderBy('starts_at')->first(),
            'failedCount' => MailDelivery::where('status', 'failed')->count(),
            'pendingCount' => MailDelivery::whereIn('status', ['pending', 'sending'])->count(),
            'failedDeliveries' => MailDelivery::where('status', 'failed')->latest('id')->paginate(10, ['*'], 'mail_page'),
        ]);
    }
}
