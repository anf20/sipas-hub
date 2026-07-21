<?php

namespace App\Services\Whatsapp;

use App\Services\Contracts\WhatsappServiceInterface;
use Illuminate\Support\Facades\Log;

class LogWhatsappService implements WhatsappServiceInterface
{
    /**
     * Simulate sending a WhatsApp message by logging it.
     */
    public function sendMessage(string $phone, string $message): array
    {
        // For local testing, we intercept the phone if WA_TEST_NUMBER is set
        $testNumber = config('services.whatsapp.test_number');
        $targetPhone = $testNumber ?: $phone;

        Log::info("=== WHATSAPP MESSAGE (LOG DRIVER) ===");
        Log::info("Target Phone: " . $targetPhone . ($testNumber ? " (Intercepted from $phone)" : ""));
        Log::info("Message:\n" . $message);
        Log::info("=====================================");

        // Return a dummy successful response
        return [
            'success' => true,
            'status' => 'sent',
            'response' => 'Logged successfully for local testing.',
            'target' => $targetPhone
        ];
    }
}
