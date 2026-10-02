<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class AppointmentPageController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $locale = $request->routeIs('dental.tr.appointment')
            ? 'tr'
            : 'en';

        if (
            $locale === 'en'
            && $request->cookie('site_locale') === 'tr'
        ) {
            return redirect()->route('dental.tr.appointment');
        }

        app()->setLocale($locale);

        $treatments = Treatment::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get([
                'id',
                'code',
                'duration_minutes',
                'image_path',
            ]);

        $bookingToken = (string) Str::uuid();
        $tokens = $request->session()->get('appointment.tokens', []);
        $tokens[$bookingToken] = true;
        $request->session()->put('appointment.tokens', array_slice($tokens, -10, null, true));

        return response()
            ->view('dental.appointment', [
                'page' => 'appointment',
                'treatments' => $treatments,
                'bookingToken' => $bookingToken,
            ])
            ->withCookie(
                cookie('site_locale', $locale, 60 * 24 * 365)
            );
    }
}
