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
        $data['qr_token'] = $service->generateQrToken();

        // Project officers can only create beneficiaries in their own project
        $user = auth()->user();
        if ($user?->isProjectOfficer() && $user->assigned_project_id) {
            $data['project_id'] = $user->assigned_project_id;
        }
        
        return $data;
    }
}