<?php

namespace App\Imports;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Row;

class TeamImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public int $importedCount = 0;
    public int $skippedCount  = 0;

    private array $projectCache = [];
    private array $coachCache   = [];

    public function onRow(Row $row): void
    {
        $rowData     = $row->toArray();
        $teamName    = trim($rowData['name'] ?? '');
        $projectName = trim($rowData['project'] ?? '');
        $coachName   = trim($rowData['coach'] ?? '');
        $isActive    = filter_var($rowData['is_active'] ?? 1, FILTER_VALIDATE_BOOLEAN);

        if (empty($teamName) || empty($projectName)) {
            $this->skippedCount++;
            return;
        }

        // ── Football project lookup (by name) ─────────────────────────────
        if (!array_key_exists($projectName, $this->projectCache)) {
            $this->projectCache[$projectName] = Project::where('programme_type', Project::PROGRAMME_FOOTBALL)
                ->where('name', $projectName)
                ->value('id');
        }

        $projectId = $this->projectCache[$projectName];

        if (!$projectId) {
            $this->skippedCount++;
            return; // Unknown football project — skip
        }

        // ── Coach lookup (by name, optional) ──────────────────────────────
        $coachId = null;
        if (!empty($coachName)) {
            if (!array_key_exists($coachName, $this->coachCache)) {
                $this->coachCache[$coachName] = User::where('name', $coachName)
                    ->whereIn('role', [User::ROLE_COACH, User::ROLE_PROJECT_OFFICER])
                    ->value('id');
            }
            $coachId = $this->coachCache[$coachName] ?? null;
        }

        Team::updateOrCreate(
            ['name' => $teamName, 'project_id' => $projectId],
            ['coach_id' => $coachId, 'is_active' => $isActive]
        );

        $this->importedCount++;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:255',
            'project' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required'    => 'Team name is required.',
            'project.required' => 'Project name is required.',
        ];
    }
}
