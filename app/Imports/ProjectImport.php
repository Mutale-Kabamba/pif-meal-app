<?php

namespace App\Imports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class ProjectImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public int $importedCount = 0;

    public function model(array $row): ?Project
    {
        // updateOrCreate handles upsert; return null so the framework
        // does not attempt a second save() on the already-persisted model.
        Project::updateOrCreate(
            ['budget_code' => trim($row['budget_code'])],
            [
                'name'                              => trim($row['name']),
                'daily_meal_limit_per_beneficiary'  => (int) ($row['daily_meal_limit'] ?? $row['daily_meal_limit_per_beneficiary'] ?? 1),
                'is_active'                         => filter_var($row['is_active'] ?? 1, FILTER_VALIDATE_BOOLEAN),
                'description'                       => isset($row['description']) ? trim($row['description']) : null,
            ]
        );

        $this->importedCount++;

        return null;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'budget_code' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required'        => 'The project name is required.',
            'budget_code.required' => 'The budget code is required.',
        ];
    }
}
