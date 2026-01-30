<?php

namespace App\Livewire\Guest;

use App\Enums\DonationStatus;
use App\Models\Donation;
use App\Services\Media\MediaService;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadProof extends Component
{
    use WithFileUploads;

    public Donation $donation;
    public $proof_image;

    public function mount(Donation $donation)
    {
        $this->donation = $donation;
    }

    public function save(MediaService $mediaService)
    {
        $this->validate([
            'proof_image' => 'required|image|max:2048',
        ]);

        $media = $mediaService->upload($this->proof_image, null); // Upload as guest

        $this->donation->update([
            'proof_image' => $media->file_path,
            'status' => DonationStatus::PendingManual,
        ]);

        $this->dispatch('notify', message: 'Bukti pembayaran berhasil diunggah.', type: 'success');
        $this->redirect(route('guest.donate.success'), navigate: true);
    }

    public function render()
    {
        return view('livewire.guest.upload-proof');
    }
}
