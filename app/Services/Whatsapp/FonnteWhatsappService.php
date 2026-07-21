<?php

namespace App\Services\Whatsapp;

use App\Services\Contracts\WhatsappServiceInterface;
use Illuminate\Support\Facades\Http;

class FonnteWhatsappService implements WhatsappServiceInterface
{
    /**
     * Send a WhatsApp message using Fonnte API.
     */
    public function sendMessage(string $phone, string $message): array
    {
        $token = config('services.whatsapp.fonnte_token');
        
        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62', // Optional: force ID code if not formatted
            ]);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => 'sent',
                    'response' => $response->json(),
                    'target' => $phone
                ];
            }
            
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $response->body(),
                'target' => $phone
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $e->getMessage(),
                'target' => $phone
            ];
        }
    }
}
