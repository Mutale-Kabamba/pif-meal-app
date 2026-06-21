<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Services\MealValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    protected MealValidationService $validationService;

    public function __construct(MealValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    /**
     * Record a meal for a beneficiary via QR token or shortcode.
     */
    public function record(Request $request): JsonResponse
    {
        $request->validate([
            'qr_token'  => 'required_without:shortcode|string',
            'shortcode' => 'required_without:qr_token|string|size:5',
            'meal_type' => 'nullable|in:breakfast,lunch,dinner,standard_ration',
        ]);

        $mealType = $request->input('meal_type', 'standard_ration');

        // Find beneficiary
        if ($request->has('qr_token')) {
            $beneficiary = Beneficiary::where('qr_token', $request->qr_token)->first();
        } else {
            $beneficiary = Beneficiary::where('shortcode', strtoupper($request->shortcode))->first();
        }

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'status'  => 'invalid',
                'message' => 'Invalid card: No beneficiary found with this code.',
            ], 404);
        }

        $result = $this->validationService->validate($beneficiary, Auth::user(), $mealType);

        $statusCode = $result->success ? 200 : 422;

        return response()->json($result->toArray(), $statusCode);
    }
}
