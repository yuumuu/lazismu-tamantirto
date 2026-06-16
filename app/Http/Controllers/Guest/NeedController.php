<?php

declare(strict_types=1);

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Need;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NeedController extends Controller
{
    public function create()
    {
        return Inertia::render('Needs/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'applicant_phone' => 'required|string|max:20',
            'applicant_address' => 'required|string',
            'applicant_email' => 'nullable|email|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:health,education,business,basic_needs,other',
            'amount_requested' => 'required|numeric|min:0',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('needs', 'public');
        }

        $need = Need::create([
            'tracking_token' => strtoupper(Str::random(8)),
            'applicant_name' => $validated['applicant_name'],
            'applicant_phone' => $validated['applicant_phone'],
            'applicant_address' => $validated['applicant_address'],
            'applicant_email' => $validated['applicant_email'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'amount_requested' => $validated['amount_requested'],
            'attachment' => $attachmentPath,
        ]);

        return Inertia::render('Needs/Success', [
            'trackingToken' => $need->tracking_token,
        ]);
    }

    public function checkStatusForm()
    {
        return Inertia::render('Needs/CheckStatus');
    }

    public function checkStatus(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'token' => 'required|string|size:8',
        ]);

        $need = Need::withoutGlobalScope('branch')
            ->where('applicant_phone', $validated['phone'])
            ->where('tracking_token', $validated['token'])
            ->first();

        if (! $need) {
            return back()->withErrors(['not_found' => 'Data tidak ditemukan. Periksa kembali nomor HP dan kode token.']);
        }

        return Inertia::render('Needs/CheckStatus', [
            'result' => [
                'title' => $need->title,
                'category' => $need->category,
                'amount_requested' => $need->amount_requested,
                'status' => $need->status,
                'admin_notes' => $need->admin_notes,
                'created_at' => $need->created_at->toDateString(),
            ],
        ]);
    }
}
