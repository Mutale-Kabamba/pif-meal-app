<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'alice@nms.local'],
            [
                'name' => 'Alice Johnson',
                'password' => Hash::make('password'),
                'role' => User::ROLE_HEAD_OF_PROGRAMMES,
                'assigned_project_id' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'bob@nms.local'],
            [
                'name' => 'Bob Smith',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SYSTEM_MANAGER,
                'assigned_project_id' => null,
            ]
        );

        // Assign sample cooks to first two football teams (looked up by budget code)
        $seniorBoys  = \App\Models\Project::where('budget_code', 'FB-SB-2024')->value('id');
        $seniorGirls = \App\Models\Project::where('budget_code', 'FB-SG-2024')->value('id');

        User::updateOrCreate(
            ['email' => 'cook.a@nms.local'],
            [
                'name'                => 'Cook Alpha',
                'password'            => Hash::make('password'),
                'role'                => User::ROLE_COOK,
                'assigned_project_id' => $seniorBoys,
            ]
        );

        User::updateOrCreate(
            ['email' => 'cook.b@nms.local'],
            [
                'name'                => 'Cook Beta',
                'password'            => Hash::make('password'),
                'role'                => User::ROLE_COOK,
                'assigned_project_id' => $seniorGirls,
            ]
        );
    }
}
