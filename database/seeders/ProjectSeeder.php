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
                'name'        => 'Senior Boys Team',
                'description' => 'Football — Senior Boys',
            ],
            [
                'name'        => 'Senior Girls Team',
                'description' => 'Football — Senior Girls',
            ],
            [
                'name'        => 'Under 14 Boys Team',
                'description' => 'Football — Under 14 Boys',
            ],
            [
                'name'        => 'Under 17 Girls Team',
                'description' => 'Football — Under 17 Girls',
            ],
            [
                'name'        => 'Under 20 Boys Team',
                'description' => 'Football — Under 20 Boys',
            ],
        ];

        foreach ($teams as $team) {
            Project::updateOrCreate(
                ['name' => $team['name']],
                [
                    'daily_meal_limit_per_beneficiary' => 1,
                    'is_active'                        => true,
                    'description'                      => $team['description'],
                ]
            );
        }
    }
}