<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AvailabilityWindowController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MailDeliveryController;
use App\Http\Controllers\AppointmentAvailabilityController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentDetailsController;
use App\Http\Controllers\AppointmentManagementController;
use App\Http\Controllers\AppointmentPageController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\SetAdminLocale;
use App\Http\Middleware\SetSiteLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$pages = [
    'home' => '',
    'contact' => '/contact',
];

foreach ($pages as $page => $path) {
    // İngilizce sayfalar
    Route::get($path ?: '/', function (Request $request) use ($page) {
        if ($request->cookie('site_locale') === 'tr') {
            return redirect()->route("dental.tr.{$page}");
        }

        app()->setLocale('en');

        return view("dental.{$page}", ['page' => $page]);
    })->name("dental.en.{$page}");

    // Türkçe sayfalar
    Route::get('/tr'.$path, function () use ($page) {
        app()->setLocale('tr');

        return response()
            ->view("dental.{$page}", ['page' => $page])
            ->withCookie(cookie('site_locale', 'tr', 60 * 24 * 365));
    })->name("dental.tr.{$page}");

    // İngilizce seçimini kaydet ve aynı sayfaya yönlendir
    Route::get('/en'.$path, function () use ($page) {
        return redirect()
            ->route("dental.en.{$page}")
            ->withCookie(cookie('site_locale', 'en', 60 * 24 * 365));
    })->name("dental.switch-en.{$page}");
}

Route::get('/hello', function () {
    return response()->json([
        'message' => 'Merhaba, Ramazan',
        'framework' => 'Laravel',
    ]);
});

Route::post('/contact', [ContactMessageController::class, 'store'])
    ->middleware([SetSiteLocale::class.':en', 'throttle:contact'])
    ->name('dental.en.contact.store');

Route::post('/tr/contact', [ContactMessageController::class, 'store'])
    ->middleware([SetSiteLocale::class.':tr', 'throttle:contact'])
    ->name('dental.tr.contact.store');

foreach (['en' => 'admin', 'tr' => 'tr/admin'] as $locale => $prefix) {
    Route::prefix($prefix)
        ->name($locale === 'tr' ? 'tr.' : '')
        ->middleware(SetAdminLocale::class.':'.$locale)
        ->group(function () {
            Route::get('/login', [AuthController::class, 'create'])
                ->name('login');

            Route::post('/login', [AuthController::class, 'store'])
                ->name('admin.login.store');

            Route::middleware(['auth', EnsureUserIsAdmin::class])
                ->group(function () {
                    Route::get('/', [DashboardController::class, 'index'])
                        ->name('admin.dashboard');

                    Route::get('/contacts', [App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('admin.contacts.index');
                    Route::get('/contacts/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('admin.contacts.show');
                    Route::post('/contacts/{contactMessage}/resend', [App\Http\Controllers\Admin\ContactMessageController::class, 'resend'])->middleware('throttle:5,1')->name('admin.contacts.resend');
                    Route::post('/deliveries/{mailDelivery}/retry', [MailDeliveryController::class, 'retry'])->middleware('throttle:5,1')->name('admin.deliveries.retry');

                    Route::get('/appointments', [App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('admin.appointments.index');
                    Route::get('/appointments/{appointment}', [App\Http\Controllers\Admin\AppointmentController::class, 'show'])->name('admin.appointments.show');
                    Route::patch('/appointments/{appointment}', [App\Http\Controllers\Admin\AppointmentController::class, 'update'])->name('admin.appointments.update');
                    Route::post('/appointments/{appointment}/cancel', [App\Http\Controllers\Admin\AppointmentController::class, 'cancel'])->name('admin.appointments.cancel');
                    Route::post('/appointments/{appointment}/resend', [App\Http\Controllers\Admin\AppointmentController::class, 'resend'])->middleware('throttle:5,1')->name('admin.appointments.resend');

                    Route::post('/logout', [AuthController::class, 'destroy'])
                        ->name('admin.logout');

                    Route::get(
                        '/availability',
                        [AvailabilityWindowController::class, 'index']
                    )->name('admin.availability.index');

                    Route::post(
                        '/availability',
                        [AvailabilityWindowController::class, 'store']
                    )->name('admin.availability.store');

                    Route::get(
                        '/availability/{availabilityWindow}/edit',
                        [AvailabilityWindowController::class, 'edit']
                    )->name('admin.availability.edit');

                    Route::patch(
                        '/availability/{availabilityWindow}',
                        [AvailabilityWindowController::class, 'update']
                    )->name('admin.availability.update');

                    Route::delete(
                        '/availability/{availabilityWindow}',
                        [AvailabilityWindowController::class, 'destroy']
                    )->name('admin.availability.destroy');
                });
        });
}

Route::get(
    '/appointment/slots',
    [AppointmentAvailabilityController::class, 'index']
)
    ->middleware('throttle:60,1')
    ->name('dental.appointment.slots');

Route::get(
    '/appointment/dates',
    [AppointmentAvailabilityController::class, 'dates']
)
    ->middleware('throttle:60,1')
    ->name('dental.appointment.dates');

Route::get(
    '/appointment',
    [AppointmentPageController::class, 'index']
)->name('dental.en.appointment');

Route::get(
    '/tr/appointment',
    [AppointmentPageController::class, 'index']
)->name('dental.tr.appointment');

Route::get('/en/appointment', function () {
    return redirect()
        ->route('dental.en.appointment')
        ->withCookie(
            cookie('site_locale', 'en', 60 * 24 * 365)
        );
})->name('dental.switch-en.appointment');

Route::post(
    '/appointment/details/validate',
    [AppointmentDetailsController::class, 'validateDetails']
)
    ->middleware(SetSiteLocale::class.':en')
    ->name('dental.en.appointment.details.validate');

Route::post(
    '/tr/appointment/details/validate',
    [AppointmentDetailsController::class, 'validateDetails']
)
    ->middleware(SetSiteLocale::class.':tr')
    ->name('dental.tr.appointment.details.validate');

foreach (['en' => '/appointment', 'tr' => '/tr/appointment'] as $locale => $path) {
    Route::post($path, [AppointmentController::class, 'store'])
        ->middleware([SetSiteLocale::class.':'.$locale, 'throttle:10,1'])
        ->block(120, 120)
        ->name('dental.'.$locale.'.appointment.store');
}

Route::middleware(['signed', 'throttle:30,1'])->group(function () {
    Route::get('/appointment/manage/{appointment:reference}', [AppointmentManagementController::class, 'show'])->name('appointment.manage.show');
    Route::post('/appointment/manage/{appointment:reference}/cancel', [AppointmentManagementController::class, 'cancel'])->name('appointment.manage.cancel');
    Route::get('/appointment/manage/{appointment:reference}/calendar', [AppointmentManagementController::class, 'calendar'])->name('appointment.manage.calendar');
});
