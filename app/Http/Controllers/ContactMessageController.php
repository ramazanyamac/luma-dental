<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Services\MailDeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Propaganistas\LaravelPhone\PhoneNumber;

class ContactMessageController extends Controller
{
    public function store(
        StoreContactMessageRequest $request,
        MailDeliveryService $deliveries
    ): JsonResponse {
        $data = $request->validated();

        $data['phone'] = (new PhoneNumber(
            $data['phone'],
            $data['phone_country']
        ))->formatE164();

        unset($data['phone_country']);

        DB::transaction(function () use ($data, $deliveries): void {
            $contactMessage = ContactMessage::create([...$data, 'locale' => app()->getLocale()]);
            $deliveries->contact($contactMessage);
        });

        return response()->json([
            'message' => __('contact.form.success'),
        ], 201);
    }
}
