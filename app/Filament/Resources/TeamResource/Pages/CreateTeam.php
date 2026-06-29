<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Project officers can only create teams in their own project
        $user = auth()->user();
        if ($user?->isProjectOfficer() && $user->assigned_project_id) {
            $data['project_id'] = $user->assigned_project_id;
        }

        return $data;
    }
}
