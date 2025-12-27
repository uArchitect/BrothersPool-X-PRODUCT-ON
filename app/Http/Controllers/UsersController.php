<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//auth , hash
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{

    public function addUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:15|unique:users,phone',
            'email' => 'nullable|email',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'Ad Soyad alanı zorunludur.',
            'name.max' => 'Ad Soyad en fazla 255 karakter olabilir.',
            'phone.required' => 'Telefon alanı zorunludur.',
            'phone.min' => 'Telefon en az 10 karakter olmalıdır.',
            'phone.max' => 'Telefon en fazla 15 karakter olabilir.',
            'phone.unique' => 'Bu telefon numarası zaten kayıtlı.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
        ]);

        $userId = DB::table('users')->insertGetId([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = DB::table('users')->where('id', $userId)->first();

        if ($user) {
            $url = "https://mobil.eniyisalonapp.com";
            $subdomain = explode('.', parse_url($url, PHP_URL_HOST))[0];
            $this->addMainUser(2, $subdomain, $user->phone, 0);
        }

        if ($request->ajax()) {
            return response()->json(['user' => $user, 'message' => 'Kullanıcı başarıyla eklendi.']);
        }
        return redirect()->back()->with('success', 'Kullanıcı başarıyla eklendi.');
    }



    public function updateUser(Request $request)
    {
        $user = DB::table('users')->where('id', $request->id)->first();
        if (!$user) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Kullanıcı bulunamadı.'], 404);
            }
            return redirect()->back()->with('error', 'Kullanıcı bulunamadı.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:15|unique:users,phone,' . $user->id,
            'email' => 'nullable|email',
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Ad Soyad alanı zorunludur.',
            'name.max' => 'Ad Soyad en fazla 255 karakter olabilir.',
            'phone.required' => 'Telefon alanı zorunludur.',
            'phone.min' => 'Telefon en az 10 karakter olmalıdır.',
            'phone.max' => 'Telefon en fazla 15 karakter olabilir.',
            'phone.unique' => 'Bu telefon numarası zaten kayıtlı.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
        ]);

        $data = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'updated_at' => now(),
        ];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        DB::table('users')->where('id', $user->id)->update($data);
        $user = DB::table('users')->where('id', $user->id)->first();
        if ($request->ajax()) {
            return response()->json(['user' => $user, 'message' => 'Kullanıcı başarıyla güncellendi.']);
        }
        return redirect()->back()->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    public function deleteUser(Request $request)
    {
        $id = $request->id;
        $username = DB::table('users')->where('id', $id)->value('username');
        if (!$username) {
            return redirect()->back()->with('error', 'Kullanıcı bulunamadı.');
        }
        $subdomain = explode('.', request()->getHost())[0];
        $apiData = [
            'domain_id' => 2,
            'subdomain' => $subdomain,
            'username' => $username
        ];
        $client = new \GuzzleHttp\Client([
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false
        ]);
        try {
            $response = $client->post('http://altf4.masterbm.com/api/v3/deleteFSUser', [
                'form_params' => $apiData,
                'headers' => [
                    'User-Agent' => 'Laravel-App/1.0',
                    'Accept' => 'application/json'
                ]
            ]);
            $statusCode = $response->getStatusCode();
            $responseBody = (string) $response->getBody();
            $responseData = json_decode($responseBody, true);
            if ($statusCode !== 200 || json_last_error() !== JSON_ERROR_NONE || empty($responseData['status'])) {
                return redirect()->back()->with('error', $responseData['message'] ?? 'API başarısız ya da JSON hatalı.');
            }
            DB::table('users')->where('id', $id)->delete();
            return redirect()->back()->with('success', 'Kullanıcı başarıyla silindi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'API bağlantı hatası: ' . $e->getMessage());
        }
    }

    public function updatePermissions(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $permission_name = trim($request->permission_name);

            // İzni bul
            $permission = DB::table('permissions')
                ->where('name', 'LIKE', "%{$permission_name}%")
                ->first();

            if (!$permission) {
                return response()->json(['message' => 'İzin bulunamadı'], 404);
            }

            if ($request->status == 1) {
                $exists = DB::table('user_permissions')
                    ->where('user_id', $user_id)
                    ->where('permission_id', $permission->id)
                    ->exists();

                if (!$exists) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $user_id,
                        'permission_id' => $permission->id
                    ]);
                }
            } else {
                DB::table('user_permissions')
                    ->where('user_id', $user_id)
                    ->where('permission_id', $permission->id)
                    ->delete();
            }

            // Tek bir response dönüyoruz
            return response()->json(['message' => 'İşlem başarılı']);
        } catch (\Exception $e) {
            \Log::error('İzin güncelleme hatası: ' . $e->getMessage());
            return response()->json([
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }





    public function addMainUser($domain_id, $url, $username, $pro)
    {

        $apiData = [
            'domain_id' => $domain_id,
            'url' => $url,
            'username' => $username,
            'pro' => $pro,
        ];

        $client = new \GuzzleHttp\Client([
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false
        ]);

        try {
            $response = $client->post('http://altf4.masterbm.com/api/v3/postFSAddUser', [
                'form_params' => $apiData,
                'headers' => [
                    'User-Agent' => 'Laravel-App/1.0',
                    'Accept' => 'application/json'
                ]
            ]);
            $statusCode = $response->getStatusCode();
            $responseBody = (string) $response->getBody();
            $responseData = json_decode($responseBody, true);

            if ($statusCode !== 200 || json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => $responseData['message'] ?? 'API error or invalid JSON.'], 500);
            }

            return response()->json(['message' => 'User added successfully.', 'api_response' => $responseData]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'API connection error: ' . $e->getMessage()], 500);
        }
    }
}
