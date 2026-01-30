<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('campaign.create') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'integer',
                'exists:campaign_categories,id',
            ],
            'type' => [
                'required',
                'string',
                'in:'.implode(',', CampaignType::values()),
            ],
            'title' => [
                'required',
                'string',
                'min:10',
                'max:255',
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('campaigns', 'slug'),
            ],
            'short_description' => [
                'required',
                'string',
                'min:50',
                'max:500',
            ],
            'description' => [
                'required',
                'string',
                'min:100',
            ],
            'target_amount' => [
                'required',
                'integer',
                'min:100000',
            ],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
            'status' => [
                'nullable',
                'string',
                'in:'.implode(',', CampaignStatus::values()),
            ],
            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'is_featured' => [
                'nullable',
                'boolean',
            ],
            'is_urgent' => [
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
            'category_id.required'          => 'Kategori wajib dipilih.',
            'title.required'                => 'Judul campaign wajib diisi.',
            'title.min'                     => 'Judul campaign minimal 10 karakter.',
            'short_description.required'    => 'Deskripsi singkat wajib diisi.',
            'short_description.min'         => 'Deskripsi singkat minimal 50 karakter.',
            'description.required'          => 'Deskripsi lengkap wajib diisi.',
            'description.min'               => 'Deskripsi lengkap minimal 100 karakter.',
            'target_amount.required'        => 'Target donasi wajib diisi.',
            'target_amount.min'             => 'Target donasi minimal Rp 100.000.',
            'start_date.required'           => 'Tanggal mulai wajib diisi.',
            'start_date.after_or_equal'     => 'Tanggal mulai tidak boleh di masa lalu.',
            'end_date.required'             => 'Tanggal selesai wajib diisi.',
            'end_date.after'                => 'Tanggal selesai harus setelah tanggal mulai.',
        ];
    }
}
