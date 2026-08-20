<?php

namespace App\Services\Contracts;

interface WhatsappServiceInterface
{
    /**
     * Send a WhatsApp message to a specific phone number.
     */
    public function sendMessage(string $phone, string $message): array;
}
