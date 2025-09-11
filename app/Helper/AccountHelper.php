<?php

namespace App\Helper;

use App\Models\UpgradeFeature;

class AccountHelper
{
    public static function getUpgradeFeatures($accountType = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = UpgradeFeature::query();

        if ($accountType !== null) {
            $query->where('group', $accountType);
        }

        return $query->orderBy('order')->get();
    }


    public static function getBuyerFinancingFormData()
    {
        $data['step1'] = [
            "I'm thinking about buying",
            "I'm ready to buy when I find the right deal",
            "I'm making an offer on this property",
            "I'm under contract on a property"
        ];

        $data['step2'] = [
            "Under 2 weeks (FAST)",
            "1 Month",
            "1-2 Months",
            "3-6 Months",
        ];

        $data['step3'] = [
            "Bridge Loan - Fix & Flip",
            "Bridge Loan - Rental Rehab",
            "Long Term Rental",
            "STR / AirBnb",
            "Transactional Funding",
            "Cash Out Refi",
            "Not sure"
        ];

        $data['step4'] = [
            "One of our top lending partners will reach out to you. We will NOT sell your data to outside parties. "
        ];

        return response()->json(['data' => $data]);
    }
}
