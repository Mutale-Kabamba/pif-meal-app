<?php

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Filament\Resources\BeneficiaryResource;
use App\Services\IdentityGenerationService;
use Filament\Resources\Pages\CreateRecord;

class CreateBeneficiary extends CreateRecord
{
    protected static string $resource = BeneficiaryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $service = app(IdentityGenerationService::class);

        // Automated security token assignment definitions
        $data['shortcode'] = $service->generateShortcode();
        $data['qr_token']  = $service->generateQrToken();

        return $data;
    }
}