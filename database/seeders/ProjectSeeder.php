<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Update or establish baseline projects with real world schedule structures
        Project::updateOrCreate(
            ['budget_code' => 'MHI-2024-001'],
            [
                'name' => 'Maternal Health Initiative',
                'daily_meal_limit_per_beneficiary' => 1,
                'is_active' => true,
                'description' => 'Monday: Nshima + Beef/Chicken/Sausage & Veg. Tuesday: Rice/Nshima + Beans & Veg. Wednesday: Pounded Maize + Groundnuts. Thursday: Nshima + Chicken/Sausage/Beef & Veg. Friday: Buns, Milk & Bananas.'
            ]
        );

        Project::updateOrCreate(
            ['budget_code' => 'EDR-2024-001'],
            [
                'name' => 'Emergency Drought Response',
                'daily_meal_limit_per_beneficiary' => 2,
                'is_active' => true,
                'description' => 'Standard Emergency Rations schedule.'
            ]
        );

        Project::updateOrCreate(
            ['budget_code' => 'SFP-2024-001'],
            [
                'name' => 'School Feeding Program',
                'daily_meal_limit_per_beneficiary' => 1,
                'is_active' => true,
                'description' => 'Weekly nutritional standard menu rules.'
            ]
        );
    }
}