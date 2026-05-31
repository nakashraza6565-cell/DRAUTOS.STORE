<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class WhatsAppCampaignController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all distinct cities from users with role 'user'
        $cities = User::where('role', 'user')
                      ->whereNotNull('city')
                      ->where('city', '!=', '')
                      ->distinct()
                      ->pluck('city')
                      ->sort()
                      ->values();

        $customers = collect();
        $selectedCity = $request->get('city');
        $visitDate = $request->get('visit_date', date('Y-m-d'));
        $salesman = $request->get('salesman', '');
        $campaignType = $request->get('campaign_type', 'payment');

        if ($selectedCity) {
            $customers = User::where('role', 'user')
                             ->where('city', $selectedCity)
                             ->get();
        }

        return view('backend.marketing.whatsapp_campaign', compact('cities', 'customers', 'selectedCity', 'visitDate', 'salesman', 'campaignType'));
    }
}
