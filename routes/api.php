<?php

use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\BeneficiaryController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'time' => now()->toDateTimeString()]);
});

Route::middleware('auth')->group(function () {
    Route::post('/meals/record', [MealController::class, 'record']);
    Route::get('/beneficiaries/search', [BeneficiaryController::class, 'search']);
    Route::get('/terminal/stats', function () {
        $user = auth()->user();
        $query = \App\Models\MealLog::query()->whereDate('served_at', today());
        if ($user->assigned_project_id) {
            $query->where('project_id', $user->assigned_project_id);
        }
        return response()->json([
            'total_fed_today' => $query->count(),
            'project_name' => $user->assignedProject?->name ?? 'All Projects',
            'cook_name' => $user->name,
        ]);
    });
});
