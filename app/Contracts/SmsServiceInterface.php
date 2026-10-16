<?php

namespace App\Contracts;

use App\DTOs\SmsResponseDTO;


interface SmsServiceInterface
{
    
    public function send(string $mobile, string $message): SmsResponseDTO;

    
    public function getProviderKey(): string;
}
