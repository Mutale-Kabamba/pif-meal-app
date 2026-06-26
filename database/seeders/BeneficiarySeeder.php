<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BeneficiarySeeder extends Seeder
{
    private array $usedShortcodes = [];

    public function run(): void
    {
        // Beneficiaries are imported via Excel — no sample data seeded.
    }

    private function generateUniqueShortcode(): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5));
        } while (in_array($code, $this->usedShortcodes));

        $this->usedShortcodes[] = $code;
        return $code;
    }
}
