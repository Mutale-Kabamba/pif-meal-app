<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    /**
     * Search beneficiaries by shortcode or name.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $beneficiaries = Beneficiary::query()
            ->with('project:id,name')
            ->active()
            ->where(function ($q) use ($query) {
                $q->where('shortcode', 'like', $query . '%')
                  ->orWhere('name', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get(['id', 'name', 'shortcode', 'project_id']);

        return response()->json($beneficiaries);
    }
}
