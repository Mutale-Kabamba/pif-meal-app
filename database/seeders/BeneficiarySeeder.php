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
        $projects = Project::all();
        $firstNames = ['Amina', 'Fatima', 'Hassan', 'Ibrahim', 'Mariam', 'Omar', 'Zainab', 'Yusuf', 'Khadija', 'Abdi', 'Sahra', 'Mohamed', 'Halima', 'Ali', 'Nimo'];
        $lastNames = ['Hussein', 'Omar', 'Diallo', 'Traore', 'Mensah', 'Kamara', 'Conteh', 'Sesay', 'Jalloh', 'Turay', 'Koroma', 'Bangura', 'Dumbuya', 'Sankoh', 'Fofana'];

        $projectIndex = 0;
        foreach ($projects as $project) {
            $projectIndex++;
            for ($i = 0; $i < 15; $i++) {
                $firstName = $firstNames[($projectIndex * 5 + $i) % count($firstNames)];
                $lastName = $lastNames[($i * 3) % count($lastNames)];
                $name = "{$firstName} {$lastName}";
                Beneficiary::firstOrCreate(
                    ['name' => $name, 'project_id' => $project->id],
                    [
                        'shortcode' => $this->generateUniqueShortcode(),
                        'qr_token'  => (string) Str::uuid(),
                        'is_active' => true,
                    ]
                );
            }
        }
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
