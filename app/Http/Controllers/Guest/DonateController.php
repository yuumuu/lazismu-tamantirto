<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Enums\DonationStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DonateController extends Controller
{
    public function form($campaign_slug = null)
    {
        $campaign = null;
        if ($campaign_slug) {
            $campaign = Campaign::where('slug', $campaign_slug)->first();
        }
        return Inertia::render('Donate/Form', ['campaign' => $campaign]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'message' => 'nullable|string',
            'is_anonymous' => 'boolean',
            'campaign_id' => 'nullable|exists:campaigns,id',
        ]);

        $campaign = null;
        if ($request->campaign_id) {
            $campaign = Campaign::find($request->campaign_id);
            if ($campaign && $campaign->end_date && new \DateTime($campaign->end_date) < new \DateTime()) {
                return back()->with('error', 'Campaign ini telah berakhir.');
            }
        }

        $donation = new Donation([
            'transaction_id' => Donation::generateTransactionId(),
            'campaign_id' => $request->campaign_id,
            'donor_name' => $request->name,
            'donor_email' => $request->email,
            'donor_phone' => $request->phone,
            'amount' => $request->amount,
            'donation_type' => $campaign ? $campaign->type->value : 'infaq',
            'donor_message' => $request->message,
            'is_anonymous' => $request->is_anonymous ?? false,
            'status' => DonationStatus::Pending,
        ]);

        $donation->branch_id = $campaign ? $campaign->branch_id : session('active_branch_id', 1);
        $donation->save();

        return redirect()->route('guest.donate.payment', ['donation_id' => $donation->id]);
    }

    public function payment($donation_id)
    {
        $donation = Donation::with('campaign')->findOrFail($donation_id);
        return Inertia::render('Donate/Payment', ['donation' => $donation]);
    }

    public function selectPayment(Request $request, $donation_id)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $donation = Donation::findOrFail($donation_id);
        $donation->payment_method = $request->payment_method;
        $donation->save();

        return redirect()->route('guest.donate.confirm', ['donation_id' => $donation->id]);
    }

    public function confirm($donation_id)
    {
        $donation = Donation::with('campaign')->findOrFail($donation_id);
        return Inertia::render('Donate/Confirm', ['donation' => $donation]);
    }

    public function success(Donation $donation)
    {
        $donation->load('campaign');
        return Inertia::render('Donate/Success', ['donation' => $donation]);
    }
}
