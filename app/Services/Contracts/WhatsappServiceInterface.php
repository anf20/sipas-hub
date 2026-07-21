<?php

namespace App\Services\Contracts;

interface WhatsappServiceInterface
{
    /**
     * Send a WhatsApp message to a specific phone number.
     *
     * @param string $phone
     * @param string $message
     * @return array
     */
    public function sendMessage(string $phone, string $message): array;
}
