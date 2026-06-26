<?php

namespace App\Imports;

use App\Models\Beneficiary;
use App\Models\Project;
use App\Services\IdentityGenerationService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class BeneficiaryImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public int $importedCount = 0;
    public int $skippedCount  = 0;

    /** @var array<string, int|null> project budget_code → id cache */
    private array $projectCache = [];

    private IdentityGenerationService $identityService;

    public function __construct()
    {
        $this->identityService = app(IdentityGenerationService::class);
    }

    public function model(array $row): ?Beneficiary
    {
        $budgetCode = trim($row['project_budget_code'] ?? '');

        // Resolve project id (cached to avoid N+1)
        if (!array_key_exists($budgetCode, $this->projectCache)) {
            $this->projectCache[$budgetCode] = Project::where('budget_code', $budgetCode)->value('id');
        }

        $projectId = $this->projectCache[$budgetCode];

        if (!$projectId) {
            $this->skippedCount++;
            return null; // unknown project — skip silently
        }

        // Skip exact duplicates (same name in same project)
        $alreadyExists = Beneficiary::where('project_id', $projectId)
            ->where('name', trim($row['name']))
            ->exists();

        if ($alreadyExists) {
            $this->skippedCount++;
            return null;
        }

        $beneficiary = new Beneficiary([
            'project_id'        => $projectId,
            'name'              => trim($row['name']),
            'is_active'         => filter_var($row['is_active'] ?? 1, FILTER_VALIDATE_BOOLEAN),
            'literacy_enrolled' => filter_var($row['literacy_enrolled'] ?? 0, FILTER_VALIDATE_BOOLEAN),
        ]);

        // Auto-generate shortcode and QR token
        $this->identityService->assignUniqueIdentity($beneficiary);

        $this->importedCount++;

        return $beneficiary;
    }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'project_budget_code' => 'required|string',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required'                => 'Beneficiary name is required.',
            'project_budget_code.required' => 'project_budget_code is required (e.g. PROJ-001).',
        ];
    }
}
