<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class FeedbackController extends Controller
{
    public function addFeedback(Request $request)
    {
        $request->validate([
            'feedback_title' => 'required|string|max:100',
            'feedback' => 'required|string|max:2000',
            'feedback_type' => 'required|integer'
        ]);

        $title = $request->input('feedback_title');
        $description = $request->input('feedback');
        $type_id = $request->input('feedback_type');
        $subdomain = explode('.', $request->getHost())[0];
        $domain_id = 1;

        $data = [
            'date' => now()->toISOString(),
            'type_id' => $type_id,
            'domain_id' => $domain_id,
            'subdomain' => $subdomain,
            'konu' => $title,
            'durum' => 0,
            'message' => $description,
            'user_id' => Auth::id(),
            'user' => Auth::user() ? Auth::user()->email : null,
            'name' => Auth::user() ? Auth::user()->name : null,
        ];

        $client = new Client([
            'base_uri' => 'http://altf4.masterbm.com',
            'timeout'  => 10.0,
        ]);

        try {
            $response = $client->post('/api/v3/addFeedback', [
                'form_params' => $data,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'User-Agent' => 'Laravel-Feedback-Client/1.0'
                ]
            ]);
            
            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);
            
            // Cache'i temizle
            Cache::forget('feedback_list_' . $subdomain);
            
            return response()->json([
                'success' => true,
                'message' => 'Destek talebi başarıyla oluşturuldu.',
                'feedback_id' => $result['id'] ?? null
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Destek talebi oluşturulamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    public function replyFeedback(Request $request) 
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'feedback_id' => 'required|integer'
        ]);

        $feedbackId = $request->input('feedback_id');
        $message = $request->input('message');
        $subdomain = explode('.', $request->getHost())[0];

        $data = [
            'date' => now()->toISOString(),
            'feedback_id' => $feedbackId,
            'sender' => 'users',
            'user_id' => Auth::id(),
            'user' => Auth::user() ? Auth::user()->email : null,
            'name' => Auth::user() ? Auth::user()->name : null,
            'message' => $message,
        ];

        $client = new Client([
            'base_uri' => 'http://altf4.masterbm.com',
            'timeout'  => 10.0,
        ]);

        try {
            $response = $client->post('/api/v3/addReply', [
                'form_params' => $data,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'User-Agent' => 'Laravel-Feedback-Client/1.0'
                ]
            ]);
            
            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);
            
            // Cache'i temizle
            Cache::forget('feedback_list_' . $subdomain);
            Cache::forget('feedback_messages_' . $feedbackId);

            return back()->with('success', 'Mesaj başarıyla gönderildi.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Mesaj gönderilemedi: ' . $e->getMessage());
        }
    }

    public function getFeedbackList(Request $request)
    {
        $subdomain = explode('.', $request->getHost())[0];
        $cacheKey = 'feedback_list_' . $subdomain;
        
        try {
            $client = new Client([
                'base_uri' => 'http://altf4.masterbm.com',
                'timeout'  => 5.0,
            ]);
            
            $response = $client->get('/api/v3/getFeedback?password=1234');
            $feedback = json_decode($response->getBody()->getContents(), true);
            
            $currentCount = count($feedback);
            $cachedCount = Cache::get($cacheKey . '_count', 0);
            
            Cache::put($cacheKey . '_count', $currentCount, 300); // 5 dakika
            
            return response()->json([
                'success' => true,
                'hasUpdates' => $currentCount > $cachedCount,
                'totalCount' => $currentCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Liste güncellenemedi'
            ], 500);
        }
    }

    public function getFeedbackMessages(Request $request, $feedbackId)
    {
        $cacheKey = 'feedback_messages_' . $feedbackId;
        $lastCount = Cache::get($cacheKey . '_count', 0);
        
        try {
            $client = new Client([
                'base_uri' => 'http://altf4.masterbm.com',
                'timeout'  => 5.0,
            ]);
            
            $response = $client->get('/api/v3/getFeedback?password=1234');
            $allFeedback = json_decode($response->getBody()->getContents(), true);
            
            $feedbackMessages = collect($allFeedback)
                ->where('id', $feedbackId)
                ->values()
                ->all();
            
            $currentCount = count($feedbackMessages);
            $newMessages = [];
            
            if ($currentCount > $lastCount) {
                $newMessages = array_slice($feedbackMessages, $lastCount);
                Cache::put($cacheKey . '_count', $currentCount, 300);
            }
            
            return response()->json([
                'success' => true,
                'newMessages' => $newMessages,
                'totalMessages' => $currentCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mesajlar getirilemedi'
            ], 500);
        }
    }
}
