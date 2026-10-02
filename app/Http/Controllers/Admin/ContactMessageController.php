<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\MailDelivery;
use App\Services\MailDeliveryService;
use App\Support\AdminUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(): View
    {
        return view('admin.contacts.index', ['messages' => ContactMessage::with('latestDelivery')->latest('id')->paginate(20)]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        return view('admin.contacts.show', [
            'message' => $contactMessage,
            'deliveries' => MailDelivery::where('contact_message_id', $contactMessage->id)->latest('id')->paginate(10),
        ]);
    }

    public function resend(ContactMessage $contactMessage, MailDeliveryService $deliveries): RedirectResponse
    {
        $deliveries->contact($contactMessage, resend: true);

        return redirect()->to(AdminUrl::route('admin.contacts.show', ['contactMessage' => $contactMessage]))->with('success', __('operations.queued'));
    }
}
