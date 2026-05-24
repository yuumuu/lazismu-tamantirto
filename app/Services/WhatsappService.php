<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    /**
     * Send a WhatsApp message using Fonnte API.
     *
     * @param string $target Phone number (e.g. 08123456789)
     * @param string $message The message body
     * @param int|null $branchId Tenant ID to fetch the correct token
     * @return bool
     */
    public static function sendMessage(string $target, string $message, ?int $branchId = null): bool
    {
        $branchId = $branchId ?? session('active_branch_id', 1);
        $token = Setting::getValue('fonnte_token', '', $branchId);

        if (empty($token)) {
            Log::warning("Fonnte token missing for branch_id {$branchId}. Could not send WhatsApp message.");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] == true) {
                    return true;
                }
                Log::error('Fonnte API error response', ['response' => $data]);
                return false;
            }

            Log::error('Fonnte HTTP request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Exception in Fonnte API call', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send a donation notification.
     */
    public static function sendDonationNotification(string $target, array $data, ?int $branchId = null): bool
    {
        $branchId = $branchId ?? session('active_branch_id', 1);
        
        $template = Setting::getValue('whatsapp_donation_template', 
            "Terima kasih {name} atas donasi Anda sebesar {amount} untuk {campaign}.", 
            $branchId
        );

        $message = str_replace(
            ['{name}', '{amount}', '{campaign}', '{date}'],
            [$data['name'] ?? 'Hamba Allah', 'Rp ' . number_format($data['amount'] ?? 0, 0, ',', '.'), $data['campaign'] ?? 'Donasi Umum', date('d/m/Y')],
            $template
        );

        return self::sendMessage($target, $message, $branchId);
    }
}
