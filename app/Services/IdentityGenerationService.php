<?php

namespace App\Services;

use App\Models\Beneficiary;
use Illuminate\Support\Str;

class IdentityGenerationService
{
    public function generateShortcode(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $code = '';
            for ($i = 0; $i < 5; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (Beneficiary::where('shortcode', $code)->exists());

        return $code;
    }

    public function generateQrToken(): string
    {
        return (string) Str::uuid();
    }

    public function assignUniqueIdentity(Beneficiary $beneficiary): void
    {
        if (empty($beneficiary->shortcode)) {
            $beneficiary->shortcode = $this->generateShortcode();
        }
        if (empty($beneficiary->qr_token)) {
            $beneficiary->qr_token = $this->generateQrToken();
        }
    }
}
