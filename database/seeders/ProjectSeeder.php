<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            [
                'budget_code' => 'FB-SB-2024',
                'name'        => 'Senior Boys Team',
                'description' => 'Football — Senior Boys',
            ],
            [
                'budget_code' => 'FB-SG-2024',
                'name'        => 'Senior Girls Team',
                'description' => 'Football — Senior Girls',
            ],
            [
                'budget_code' => 'FB-U14B-2024',
                'name'        => 'Under 14 Boys Team',
                'description' => 'Football — Under 14 Boys',
            ],
            [
                'budget_code' => 'FB-U17G-2024',
                'name'        => 'Under 17 Girls Team',
                'description' => 'Football — Under 17 Girls',
            ],
            [
                'budget_code' => 'FB-U20B-2024',
                'name'        => 'Under 20 Boys Team',
                'description' => 'Football — Under 20 Boys',
            ],
        ];

        foreach ($teams as $team) {
            Project::updateOrCreate(
                ['budget_code' => $team['budget_code']],
                [
                    'name'                             => $team['name'],
                    'daily_meal_limit_per_beneficiary' => 1,
                    'is_active'                        => true,
                    'description'                      => $team['description'],
                ]
            );
        }
    }
}