<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public can donate
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'campaign_id' => [
                'required',
                'uuid',
                'exists:campaigns,id',
            ],
            'donor_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'donor_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'donor_phone' => [
                'required',
                'string',
                'regex:/^(\+62|62|0)[0-9]{9,12}$/',
            ],
            'amount' => [
                'required',
                'integer',
                'min:10000',
            ],
            'donation_type' => [
                'nullable',
                'string',
                'in:zakat,infaq,sedekah,wakaf,fidyah',
            ],
            'payment_method' => [
                'required',
                'string',
                'in:'.implode(',', PaymentMethod::values()),
            ],
            'bank_name' => [
                'nullable',
                'required_if:payment_method,bank_transfer',
                'string',
                'max:50',
            ],
            'account_number' => [
                'nullable',
                'string',
                'max:50',
            ],
            'proof_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'donor_message' => [
                'nullable',
                'string',
                'max:500',
            ],
            'is_anonymous' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'campaign_id.required' => 'Silakan pilih program donasi.',
            'campaign_id.exists' => 'Program donasi tidak ditemukan.',
            'donor_name.required' => 'Nama donatur wajib diisi.',
            'donor_name.min' => 'Nama donatur minimal 3 karakter.',
            'donor_email.required' => 'Email wajib diisi.',
            'donor_email.email' => 'Format email tidak valid.',
            'donor_phone.required' => 'Nomor telepon wajib diisi.',
            'donor_phone.regex' => 'Format nomor telepon tidak valid. Gunakan format: 08xx atau +62xx.',
            'amount.required' => 'Jumlah donasi wajib diisi.',
            'amount.min' => 'Donasi minimal adalah Rp 10.000.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'proof_image.required' => 'Bukti pembayaran wajib diunggah.',
            'proof_image.image' => 'File harus berupa gambar.',
            'proof_image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'donor_phone' => $this->normalizePhoneNumber($this->donor_phone),
            'is_anonymous' => filter_var($this->is_anonymous, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    /**
     * Normalize phone number.
     */
    private function normalizePhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        return preg_replace(`/[\s\-]/`, '', $phone);
    }
}
