<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Team;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pre-populate the coached_team_id field for existing coach records
        if ($this->record->role === User::ROLE_COACH) {
            $data['coached_team_id'] = Team::where('coach_id', $this->record->id)->value('id');
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncCoachTeam($this->record, $this->data['coached_team_id'] ?? null);
    }

    private function syncCoachTeam(User $user, ?int $teamId): void
    {
        // If role changed away from coach, release any team they were coaching
        if ($user->role !== User::ROLE_COACH) {
            Team::where('coach_id', $user->id)->update(['coach_id' => null]);
            return;
        }

        // Clear previous assignment then apply the new one
        Team::where('coach_id', $user->id)->update(['coach_id' => null]);

        if ($teamId) {
            $team = Team::find($teamId);
            if ($team) {
                $team->update(['coach_id' => $user->id]);
                $user->update(['assigned_project_id' => $team->project_id]);
            }
        }
    }
}