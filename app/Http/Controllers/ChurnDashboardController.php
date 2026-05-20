<?php

namespace App\Http\Controllers;

use App\Models\ChurnPrediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChurnDashboardController extends Controller
{
    /**
     * Helper to retrieve high-risk premium segments
     */
    protected function getTopAtRiskData()
    {
        return ChurnPrediction::with('loyaltyMember')
            ->whereHas('loyaltyMember', function ($query) {
                $query->whereIn('tier', ['Gold', 'Platinum']);
            })
            ->orderByDesc('predicted_probability')
            ->take(50)
            ->get();
    }

    public function index()
    {
        $predictions = $this->getTopAtRiskData();
        return view('dashboard', compact('predictions'));
    }

    public function apiEndpoint()
    {
        $predictions = $this->getTopAtRiskData();

        // Return clear, structured JSON tracking payloads
        return response()->json([
            'status' => 'success',
            'count' => $predictions->count(),
            'data' => $predictions->map(function ($row) {
                return [
                    'member_id' => $row->loyalty_member_id,
                    'tier' => $row->loyaltyMember->tier,
                    'tenure_months' => $row->loyaltyMember->tenure_months,
                    'visits_30d' => $row->loyaltyMember->visits_30d,
                    'spend_30d' => $row->loyaltyMember->spend_30d,
                    'churn_probability' => (float)$row->predicted_probability,
                    'suggested_offer' => $row->generated_retention_offer,
                ];
            })
        ]);
    }

    public function triggerOffer(Request $request, $id)
    {
        $prediction = ChurnPrediction::findOrFail($id);
        $prediction->update(['status' => 'offered']);

        Log::info("Retention offer logged for member account: {$prediction->loyalty_member_id}");

        return back()->with('flash_success', 'Retention offer dispatched successfully.');
    }
}
