<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Alice Johnson',
            'email' => 'alice@nms.local',
            'password' => Hash::make('password'),
            'role' => User::ROLE_HEAD_OF_PROGRAMMES,
            'assigned_project_id' => null,
        ]);

        User::create([
            'name' => 'Bob Smith',
            'email' => 'bob@nms.local',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SYSTEM_MANAGER,
            'assigned_project_id' => null,
        ]);

        User::create([
            'name' => 'Cook Alpha',
            'email' => 'cook.a@nms.local',
            'password' => Hash::make('password'),
            'role' => User::ROLE_COOK,
            'assigned_project_id' => 1,
        ]);

        User::create([
            'name' => 'Cook Beta',
            'email' => 'cook.b@nms.local',
            'password' => Hash::make('password'),
            'role' => User::ROLE_COOK,
            'assigned_project_id' => 2,
        ]);
    }
}
