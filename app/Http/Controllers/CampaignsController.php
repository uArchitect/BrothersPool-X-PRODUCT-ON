<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignRequest;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class CampaignsController extends Controller
{
    public function add(CampaignRequest $request)
    {
        DB::beginTransaction();
        try {
            $campaignData = [
                'campaign_name' => $request->campaign_name,
                'campaign_details' => $request->campaign_details,
                'campaign_type' => $request->send_type,
                'send_type' => $request->send_type,
                'date' => Carbon::parse($request->date)->format('Y-m-d H:i:s'),
            ];

            $campaignId = DB::table('campaigns')->insertGetId($campaignData);
            $smsResult = $this->sendCampaignSMS($request, $campaignId);
            DB::commit();

            $campaign = DB::table('campaigns')->where('id', $campaignId)->first();
            $campaign->date_formatted = Carbon::parse($campaign->date)->locale('tr')->diffForHumans();

            $responseData = [
                'message' => 'SMS başarıyla gönderildi.',
                'campaign' => $campaign,
                'sms_result' => $smsResult
            ];

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($responseData, 201);
            }
            return redirect()->back()->with('success', $responseData['message']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SMS ekleme hatası: ' . $e->getMessage());
            $errorMsg = 'SMS gönderilirken bir hata oluştu: ' . $e->getMessage();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $errorMsg], 500);
            }
            return redirect()->back()->with('error', $errorMsg);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $isDeleted = DB::table('campaigns')->where('id', $id)->delete();
        $successMsg = 'SMS kaydı başarıyla silindi.';
        $errorMsg = 'SMS kaydı silinirken bir hata oluştu.';
        if ($request->ajax() || $request->wantsJson()) {
            return $isDeleted
                ? response()->json(['message' => $successMsg])
                : response()->json(['message' => $errorMsg], 422);
        }
        return $isDeleted
            ? redirect()->back()->with('success', $successMsg)
            : redirect()->back()->with('error', $errorMsg);
    }

    private function sendCampaignSMS(Request $request, int $campaignId): array
    {
        $settings = DB::table('settings')->first();
        if (!$settings || empty($settings->sms_username) || empty($settings->sms_password)) {
            return [
                'success' => false,
                'message' => 'SMS ayarları eksik',
                'sent_count' => 0
            ];
        }

        $customers = $this->getCampaignCustomers($request);
        if ($customers->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Gönderilecek müşteri bulunamadı',
                'sent_count' => 0
            ];
        }

        // SMS karakter sayısına göre kaç SMS olacağını hesapla
        $messageLength = mb_strlen($request->campaign_details, 'UTF-8');
        $smsCount = 1;
        if ($messageLength > 160) {
            $smsCount = ceil(($messageLength - 160) / 153) + 1;
        }
        
        // Toplam SMS maliyetini hesapla
        $totalSmsCost = $customers->count() * $smsCount;
        
        // Kalan SMS limitini kontrol et
        if ($settings->remaining_sms_limit < $totalSmsCost) {
            return [
                'success' => false,
                'message' => "Yetersiz SMS bakiyesi. Gereken: {$totalSmsCost}, Mevcut: {$settings->remaining_sms_limit}",
                'sent_count' => 0,
                'required_sms' => $totalSmsCost,
                'available_sms' => $settings->remaining_sms_limit
            ];
        }

        $sentCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($customers as $customer) {
            $message = $this->prepareCampaignMessage($request->campaign_details, $customer, $settings);
            try {
                $this->sendSingleSMS($customer->phone, $message, $settings);
                $this->logSms($customer->phone, 'Gönderildi', $message);
                
                // Her müşteri için karakter sayısına göre SMS düşür
                $customerMessageLength = mb_strlen($message, 'UTF-8');
                $customerSmsCost = 1;
                if ($customerMessageLength > 153) {
                    $customerSmsCost = ceil(($customerMessageLength - 153) / 153) + 1;
                }
                DB::table('settings')->where('id', 1)->decrement('remaining_sms_limit', $customerSmsCost);
                
                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = "Müşteri {$customer->name} ({$customer->phone}): " . $e->getMessage();
                $this->logSms($customer->phone, 'Hata: ' . $e->getMessage(), $message);
            }
        }

        $result = [
            'success' => $sentCount > 0,
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'total_customers' => $customers->count(),
            'message_length' => $messageLength,
            'sms_per_message' => $smsCount,
            'total_sms_used' => $sentCount * $smsCount,
            'message' => "Toplu SMS: {$sentCount} başarılı, {$failedCount} başarısız. Her mesaj {$smsCount} SMS kullandı."
        ];
        if (!empty($errors)) {
            $result['errors'] = $errors;
        }
        return $result;
    }

    private function getCampaignCustomers(Request $request): Collection
    {
        $customerType = $request->customer_type;
        if ($customerType === 'all') {
            return DB::table('customers')
                ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'phone')
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->get();
        }
        if ($customerType === 'specific' && $request->has('customers')) {
            $selectedCustomers = $request->customers;
            return DB::table('customers')
                ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'phone')
                ->whereIn(DB::raw("CONCAT(first_name, ' ', last_name)"), $selectedCustomers)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->get();
        }
        return collect();
    }

    private function prepareCampaignMessage(string $message, $customer, $settings): string
    {
        $companyPhone = $settings->phone_number ?? '';
        $customerName = $customer->name ?? 'Değerli Müşterimiz';
        $message = str_replace('[MÜŞTERI ADI]', $customerName, $message);
        $message = str_replace('[TELEFON NUMARASI]', $companyPhone, $message);
        $message = str_replace('[TARIH]', now()->format('d.m.Y'), $message);
        $message = str_replace('[SAAT]', now()->format('H:i'), $message);
        return $message;
    }

    private function sendSingleSMS(string $phoneNumber, string $message, $settings): void
    {
        if (empty($phoneNumber)) {
            throw new \Exception('Telefon numarası boş');
        }
        $client = new Client();
        $response = $client->post('http://api.mesajpaneli.com/index.php', [
            'form_params' => [
                'islem' => 1,
                'user' => $settings->sms_username,
                'pass' => $settings->sms_password,
                'mesaj' => $message,
                'numaralar' => $phoneNumber,
                'baslik' => $settings->sms_header,
            ],
            'timeout' => 30
        ]);
        $responseBody = $response->getBody()->getContents();
        if (strpos($responseBody, 'HATA') !== false) {
            throw new \Exception('SMS API Hatası: ' . $responseBody);
        }
    }

    private function logSms(string $phone, string $status, string $contents): void
    {
        DB::table('send_sms_code')->insert([
            'phone' => $phone,
            'status' => $status,
            'type' => 'campaign',
            'contents' => $contents,
            'created_at' => now(),
        ]);
    }
}
