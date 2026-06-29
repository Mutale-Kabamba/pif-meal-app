<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Team;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $this->syncCoachTeam($this->record, $this->data['coached_team_id'] ?? null);
    }

    private function syncCoachTeam(User $user, ?int $teamId): void
    {
        if ($user->role !== User::ROLE_COACH) {
            return;
        }

        // Clear any previous assignment for this coach
        Team::where('coach_id', $user->id)->update(['coach_id' => null]);

        if ($teamId) {
            $team = Team::find($teamId);
            if ($team) {
                $team->update(['coach_id' => $user->id]);
                // Derive project from the team so scoping works correctly
                $user->update(['assigned_project_id' => $team->project_id]);
            }
        }
    }
}