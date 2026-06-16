<?php

declare(strict_types=1);

namespace App\Http\Controllers\Guest;

use App\Enums\CampaignType;
use App\Enums\DonationStatus;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Setting;
use App\Services\Media\MediaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DonateController extends Controller
{
    public function form(?string $campaign_slug = null)
    {
        $campaign = null;
        if ($campaign_slug) {
            $campaign = Campaign::withoutGlobalScope('branch')
                ->with(['category'])
                ->withSum('verifiedDonations', 'amount')
                ->where('slug', $campaign_slug)
                ->first();
        }

        $bankAccounts = Setting::getValue('bank_accounts', []);
        $activeBranches = Branch::where('is_active', true)->get(['id', 'name', 'slug']);
        $campaignTypes = collect(CampaignType::cases())->map(fn ($type) => [
            'value' => $type->value,
            'label' => $type->label(),
            'description' => $type->description(),
            'icon' => $type->icon(),
        ]);

        $qrisImage = setting('site_qris');

        return Inertia::render('Donate/Form', [
            'campaign' => $campaign ? [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'slug' => $campaign->slug,
                'type' => $campaign->type?->value,
                'target_amount' => $campaign->target_amount,
                'verified_donations_sum_amount' => $campaign->verified_donations_sum_amount,
                'end_date' => $campaign->end_date?->toDateString(),
                'featured_image' => $campaign->featured_image ? asset('storage/'.$campaign->featured_image) : null,
                'category' => $campaign->category ? ['name' => $campaign->category->name] : null,
            ] : null,
            'bankAccounts' => $bankAccounts,
            'activeBranches' => $activeBranches,
            'campaignTypes' => $campaignTypes,
            'calculatorAmount' => request('amount'),
            'calculatorType' => request('type'),
            'calculatorSubtype' => request('subtype'),
            'fromCalculator' => request('from') === 'calculator',
            'qrisImage' => $qrisImage,
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'donation_type' => 'required|string',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'message' => 'nullable|string',
            'is_anonymous' => 'boolean',
            'branch_id' => 'required|exists:branches,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'payment_method' => 'required|in:manual,qris',
        ]);

        $campaign = null;
        if ($request->campaign_id) {
            $campaign = Campaign::find($request->campaign_id);
            if ($campaign && $campaign->end_date && now()->greaterThan($campaign->end_date)) {
                return back()->with('error', 'Campaign ini telah berakhir.');
            }
        }

        $data = [
            'transaction_id' => Donation::generateTransactionId(),
            'campaign_id' => $request->campaign_id,
            'branch_id' => $request->branch_id,
            'donor_name' => $request->name,
            'donor_email' => $request->email,
            'donor_phone' => $request->phone,
            'amount' => $request->amount,
            'donation_type' => $request->donation_type,
            'donor_message' => $request->message,
            'is_anonymous' => $request->is_anonymous ?? false,
            'payment_method' => $request->payment_method,
            'status' => DonationStatus::Pending,
        ];

        $donation = Donation::create($data);

        return redirect()->route('guest.donate.success', $donation->id);
    }

    public function payment($donation_id)
    {
        $donation = Donation::with('campaign')->findOrFail($donation_id);

        $bankAccounts = Setting::getValue('bank_accounts', []);

        return Inertia::render('Donate/Payment', [
            'donation' => $donation,
            'bankAccounts' => $bankAccounts,
        ]);
    }

    public function selectPayment(Request $request, $donation_id)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'bank_index' => 'nullable|integer',
        ]);

        $donation = Donation::findOrFail($donation_id);

        if ($request->payment_method === 'bank_transfer' && $request->bank_index !== null) {
            $bankAccounts = Setting::getValue('bank_accounts', []);
            $bank = $bankAccounts[$request->bank_index] ?? null;
            if ($bank) {
                $donation->payment_method = $request->payment_method;
                $donation->bank_name = $bank['bank_name'];
                $donation->account_number = $bank['account_number'];
            }
        } else {
            $donation->payment_method = $request->payment_method;
        }

        $donation->save();

        return redirect()->route('guest.donate.confirm', ['donation_id' => $donation->id]);
    }

    public function confirm($donation_id)
    {
        $donation = Donation::with('campaign')->findOrFail($donation_id);

        $bankAccounts = Setting::getValue('bank_accounts', []);
        $qrisImage = setting('site_qris');

        return Inertia::render('Donate/Confirm', [
            'donation' => $donation,
            'bankAccounts' => $bankAccounts,
            'qrisImage' => $qrisImage,
            'siteName' => setting('site_name', 'Lazismu Tamantirto'),
            'contactWhatsapp' => setting('contact_whatsapp', '6281234567890'),
        ]);
    }

    public function status($donation_id)
    {
        $donation = Donation::with('campaign')->findOrFail($donation_id);

        $bankAccounts = Setting::getValue('bank_accounts', []);
        $qrisImage = setting('site_qris');

        return Inertia::render('Donate/Status', [
            'donation' => $donation,
            'bankAccounts' => $bankAccounts,
            'qrisImage' => $qrisImage,
        ]);
    }

    public function uploadProof(Request $request, $donation_id, MediaService $mediaService)
    {
        $request->validate([
            'proof_image' => 'required|image|max:2048',
        ]);

        $donation = Donation::findOrFail($donation_id);

        $media = $mediaService->upload($request->file('proof_image'), null);

        $donation->update([
            'proof_image' => $media->file_path,
            'status' => DonationStatus::PendingManual,
        ]);

        return redirect()->route('guest.donate.success', $donation->id);
    }

    public function success(Donation $donation)
    {
        $donation->load('campaign');

        $bankAccounts = Setting::getValue('bank_accounts', []);
        $qrisImage = setting('site_qris');

        return Inertia::render('Donate/Success', [
            'donation' => $donation,
            'bankAccounts' => $bankAccounts,
            'qrisImage' => $qrisImage,
            'siteName' => setting('site_name', 'Lazismu Tamantirto'),
            'contactWhatsapp' => setting('contact_whatsapp', '6281234567890'),
        ]);
    }
}
