<?php

namespace App\Livewire\Guest;

use App\Enums\DonationStatus;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;

class DonateWizard extends Component
{
    use WithFileUploads;

    public $step = 1;

    // Form Fields
    public $branch_id;

    public $campaign_id;

    public $amount;

    public $donor_name = '';

    public $donor_email = '';

    public $donor_phone = '';

    public $donor_message = '';

    public $is_anonymous = false;

    public $payment_method = 'bank_transfer';

    public $bank_accounts = [];

    public $donation_type = 'infaq';

    public $donation_subtype = null;

    public $from_calculator = false;

    public $is_specific_campaign = false;

    public $selected_bank = null;

    public function selectSpecificCampaign(): void
    {
        $this->is_specific_campaign = true;
    }

    protected function queryString(): array
    {
        return [
            'campaign_id' => ['except' => ''],
            'amount' => ['except' => ''],
            'donation_type' => ['as' => 'type', 'except' => ''],
            'donation_subtype' => ['as' => 'subtype', 'except' => ''],
            'from_calculator' => ['as' => 'from', 'except' => false],
            'is_specific_campaign' => ['as' => 'specific', 'except' => false],
        ];
    }

    public function mount($campaign_slug = null)
    {
        $this->branch_id = session('active_branch_id', 1);

        if ($campaign_slug) {
            $campaign = Campaign::withoutGlobalScope('branch')->where('slug', $campaign_slug)->first();
            if ($campaign) {
                $this->campaign_id = $campaign->id;
                $this->branch_id = $campaign->branch_id;
                $this->donation_type = $campaign->type->value;
            }
        }

        // Handle pre-filled amount from calculator
        if (request('amount') && ! $this->amount) {
            $this->amount = request('amount');
        }

        // Handle pre-filled campaign type via query string
        if (request('type')) {
            $this->donation_type = request('type');
        }

        // Handle zakat subtype from calculator
        if (request('subtype')) {
            $this->donation_subtype = request('subtype');
        }

        // Check if coming from calculator
        if (request('from') === 'calculator') {
            $this->from_calculator = true;
        }

        if ($this->campaign_id) {
            $this->is_specific_campaign = true;
        }

        $this->bank_accounts = Setting::getValue('bank_accounts', []);
    }

    public function setAmount($value)
    {
        $this->amount = $value;
    }

    public function updatedCampaignId($value)
    {
        if ($value) {
            $campaign = Campaign::withoutGlobalScope('branch')->find($value);
            if ($campaign) {
                $this->donation_type = $campaign->type->value;
                $this->branch_id = $campaign->branch_id;
            }
        }
    }

    public function selectGeneralDonation(): void
    {
        $this->is_specific_campaign = false;
        $this->campaign_id = null;
    }

    public function setDonationType($type): void
    {
        $this->donation_type = $type;
    }

    public function selectBankTransfer($bankIndex): void
    {
        $this->payment_method = 'bank_transfer';
        $this->selected_bank = $bankIndex;
    }

    public function selectQris(): void
    {
        $this->payment_method = 'qris';
        $this->selected_bank = null;
    }

    public function getZakatSubtypeLabel(): string
    {
        return match ($this->donation_subtype) {
            'profesi' => 'Zakat Profesi',
            'maal' => 'Zakat Maal',
            'fitrah' => 'Zakat Fitrah',
            default => 'Zakat',
        };
    }

    public function nextStep()
    {
        $this->validateStep();
        $this->step++;
    }

    public function previousStep()
    {
        $this->step--;
    }

    public function validateStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'branch_id' => 'required|exists:branches,id',
                'amount' => 'required|numeric|max:10000000',
                'donation_type' => 'required|string',
                'campaign_id' => $this->is_specific_campaign ? 'required|exists:campaigns,id' : 'nullable|exists:campaigns,id',
            ]);
        } elseif ($this->step === 2) {
            $this->validate([
                'donor_name' => 'required|string|max:255',
                'donor_email' => 'required|email|max:255',
                'donor_phone' => 'required|string|max:20',
            ]);
        }
    }

    public function submit()
    {
        $this->validate([
            'payment_method' => 'required|in:qris,bank_transfer,manual',
        ]);

        $campaign = $this->campaign_id ? Campaign::find($this->campaign_id) : null;

        $bankInfo = $this->payment_method === 'bank_transfer' && $this->selected_bank !== null
            ? $this->bank_accounts[$this->selected_bank]
            : null;

        $donation = new Donation([
            'transaction_id' => Donation::generateTransactionId(),
            'campaign_id' => $this->campaign_id,
            'donor_name' => $this->donor_name,
            'donor_email' => $this->donor_email,
            'donor_phone' => $this->donor_phone,
            'amount' => $this->amount,
            'donation_type' => $this->donation_type,
            'payment_method' => $this->payment_method,
            'bank_name' => $bankInfo['bank_name'] ?? null,
            'account_number' => $bankInfo['account_number'] ?? null,
            'donor_message' => $this->donor_message,
            'is_anonymous' => $this->is_anonymous,
            'status' => DonationStatus::Pending,
        ]);

        $donation->branch_id = $this->branch_id;
        $donation->save();

        $this->redirect(route('guest.donate.success', $donation->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.guest.donate-wizard', [
            'selectedCampaign' => Campaign::withoutGlobalScope('branch')->find($this->campaign_id),
        ]);
    }
}
