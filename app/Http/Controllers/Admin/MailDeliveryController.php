<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailDelivery;
use App\Services\MailDeliveryService;
use App\Support\AdminUrl;
use Illuminate\Http\RedirectResponse;

class MailDeliveryController extends Controller
{
    public function retry(MailDelivery $mailDelivery, MailDeliveryService $deliveries): RedirectResponse
    {
        $deliveries->retry($mailDelivery);

        return redirect()->to(AdminUrl::route('admin.dashboard'))->with('success', __('operations.queued'));
    }
}
