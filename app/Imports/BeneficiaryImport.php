<?php

namespace App\Imports;

use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Team;
use App\Services\IdentityGenerationService;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Row;

class BeneficiaryImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public int $importedCount = 0;
    public int $skippedCount  = 0;

    /** @var array<string, Team|null> team name → Team model cache */
    private array $teamCache = [];

    /** @var array<string, int|null> education project name/code → project id cache */
    private array $educationCache = [];

    private IdentityGenerationService $identityService;

    public function __construct()
    {
        $this->identityService = app(IdentityGenerationService::class);
    }

    public function onRow(Row $row): void
    {
        $rowData = $row->toArray();

        $teamName        = trim($rowData['team'] ?? '');
        $educationProject = trim($rowData['education_project'] ?? '');

        $projectIds = [];
        $teamId     = null;

        // ── Football team lookup ──────────────────────────────────────────
        if (!empty($teamName)) {
            if (!array_key_exists($teamName, $this->teamCache)) {
                $this->teamCache[$teamName] = Team::where('name', $teamName)->first();
            }
            $team = $this->teamCache[$teamName];
            if ($team) {
                $teamId       = $team->id;
                $projectIds[] = $team->project_id;
            }
        }

        // ── Education project lookup (by name) ─────────────────────────────
        if (!empty($educationProject)) {
            if (!array_key_exists($educationProject, $this->educationCache)) {
                $this->educationCache[$educationProject] = Project::where('programme_type', 'education')
                    ->where('name', $educationProject)
                    ->value('id');
            }
            if ($this->educationCache[$educationProject]) {
                $projectIds[] = $this->educationCache[$educationProject];
            }
        }

        if (empty($projectIds)) {
            $this->skippedCount++;
            return; // No valid team or education project found — skip
        }

        // Skip exact duplicates (same name already in the same primary project)
        $exists = Beneficiary::where('name', trim($rowData['name']))
            ->whereHas('projects', fn ($q) => $q->where('projects.id', $projectIds[0]))
            ->exists();

        if ($exists) {
            $this->skippedCount++;
            return;
        }

        $beneficiary = new Beneficiary([
            'name'      => trim($rowData['name']),
            'is_active' => filter_var($rowData['is_active'] ?? 1, FILTER_VALIDATE_BOOLEAN),
            'team_id'   => $teamId,
        ]);

        $this->identityService->assignUniqueIdentity($beneficiary);
        $beneficiary->save();

        // Attach to all resolved projects via pivot (deduped)
        $beneficiary->projects()->sync(array_unique($projectIds));

        $this->importedCount++;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'Beneficiary name is required.',
        ];
    }
}


