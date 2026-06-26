<?php

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\MealLog;
use App\Models\AnomalyLog;
use App\Models\User;
use App\ValueObjects\ValidationResult;
use Carbon\Carbon;

class MealValidationService
{
    public function validate(
        Beneficiary $beneficiary,
        User $cook,
        string $mealType = 'standard_ration'
    ): ValidationResult {
        // 1. Check if beneficiary is active
        if (!$beneficiary->is_active) {
            $this->logAnomaly($beneficiary, $cook, $mealType, 'Beneficiary account is suspended');
            return ValidationResult::suspended(
                "Card suspended: {$beneficiary->name} is not active.",
                ['beneficiary' => $beneficiary->name]
            );
        }

        // 2. Check for duplicate
        if ($this->hasReceivedMealToday($beneficiary, $mealType)) {
            $existingLog = MealLog::where('beneficiary_id', $beneficiary->id)
                ->where('meal_type', $mealType)
                ->whereDate('served_at', today())
                ->first();

            $time = $existingLog ? $existingLog->served_at->format('h:i A') : 'earlier today';

            $this->logAnomaly($beneficiary, $cook, $mealType, "Duplicate: Already received {$mealType} today at {$time}");

            return ValidationResult::duplicate(
                "Duplicate: {$beneficiary->name} already received {$mealType} today at {$time}.",
                [
                    'beneficiary' => $beneficiary->name,
                    'meal_type' => $mealType,
                    'previous_time' => $time,
                ]
            );
        }

        // 3. Record the meal
        $mealLog = $this->recordMeal($beneficiary, $cook, $mealType);

        return ValidationResult::approved(
            "Success: {$beneficiary->name} - Meal recorded!",
            $mealLog,
            [
                'beneficiary' => $beneficiary->name,
                'shortcode' => $beneficiary->shortcode,
                'meal_type' => $mealType,
                'served_at' => $mealLog->served_at->toDateTimeString(),
            ]
        );
    }

    public function hasReceivedMealToday(Beneficiary $beneficiary, string $mealType): bool
    {
        return MealLog::where('beneficiary_id', $beneficiary->id)
            ->where('meal_type', $mealType)
            ->whereDate('served_at', today())
            ->exists();
    }

    public function isWithinMealWindow(string $mealType): bool
    {
        // Optional: implement time windows
        // For now, always allow
        return true;

        /* Example implementation:
        $now = Carbon::now();
        $windows = [
            'breakfast' => ['06:00', '10:00'],
            'lunch'     => ['11:00', '15:00'],
            'dinner'    => ['17:00', '21:00'],
            'standard_ration' => ['06:00', '22:00'],
        ];

        if (!isset($windows[$mealType])) return true;

        [$start, $end] = $windows[$mealType];
        return $now->between(
            Carbon::parse($start),
            Carbon::parse($end)
        );
        */
    }

    public function recordMeal(Beneficiary $beneficiary, User $cook, string $mealType): MealLog
    {
        return MealLog::create([
            'beneficiary_id'    => $beneficiary->id,
            'project_id'        => $beneficiary->project_id,
            'served_by_user_id' => $cook->id,
            'meal_type'         => $mealType,
            'served_at'         => now(),
        ]);
    }

    public function logAnomaly(
        Beneficiary $beneficiary,
        User $cook,
        string $mealType,
        string $reason
    ): AnomalyLog {
        return AnomalyLog::create([
            'beneficiary_id'    => $beneficiary->id,
            'project_id'        => $beneficiary->project_id,
            'served_by_user_id' => $cook->id,
            'meal_type'         => $mealType,
            'attempted_at'      => now(),
            'reason'            => $reason,
        ]);
    }
}
