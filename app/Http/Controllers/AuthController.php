<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    protected $data = [];

    private const SMS_TIMEOUT = 10;
    private const PASSWORD_LENGTH = 6;
    private const SMS_API_URL = 'http://api.mesajpaneli.com/index.php';
    private const SMS_TYPE_PASSWORD_RESET = 'Şifre Sıfırlama';

    public function showLoginForm()
    {
        $this->data['settings'] = $this->getSettings();
        return view('login', $this->data);
    }

  
    public function postLogin(LoginRequest $request)
    {
        $phone = $this->cleanPhoneNumber($request->input('phone'));
        $credentials = [
            'phone' => $phone,
            'password' => $request->input('password')
        ];

        $remember = $request->has('remember');
        
        // Debug: Kullanıcıyı kontrol et
        $user = DB::table('users')->where('phone', $phone)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Telefon numarası bulunamadı.');
        }
        
        if (Auth::attempt($credentials, $remember)) {
            return redirect()->route('dashboard');
        }

        return redirect()->back()->with('error', 'Şifre hatalı.');
    }

    
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.form');
    }

  
    public function showPasswordReset(Request $request)
    {
        $phone = $this->cleanPhoneNumber($request->input('phone'));
        
        $user = $this->findUserByPhone($phone);
        if (!$user) {
            return redirect()->back()->with('error', 'Bu telefon numarasına sahip bir kullanıcı bulunamadı.');
        }

        $newPassword = $this->generateSecurePassword();
        $settings = $this->getSettings();

        try {
            $this->updateUserPassword($phone, $newPassword);
            $this->logSmsRequest($phone, $newPassword);
            $this->sendPasswordResetSms($phone, $newPassword, $settings);

            return redirect()->back()->with('success', 
                "Yeni şifreniz oluşturuldu ve {$phone} numaralı telefonunuza gönderildi."
            );

        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage(), [
                'phone' => $phone,
                'user_id' => $user->id ?? null
            ]);

            return redirect()->back()->with('error', 
                'Şifre sıfırlama sırasında bir hata oluştu. Lütfen tekrar deneyiniz.'
            );
        }
    }

  
    private function cleanPhoneNumber($phone)
    {
        return preg_replace('/\D/', '', $phone);
    }

  
    private function getSettings()
    {
        return DB::table('settings')->where('id', 1)->first();
    }

   
    private function findUserByPhone($phone)
    {
        return DB::table('users')->where('phone', $phone)->first();
    }

 
    private function generateSecurePassword()
    {
        return str_pad(rand(0, 999999), self::PASSWORD_LENGTH, '0', STR_PAD_LEFT);
    }

   
    private function updateUserPassword($phone, $newPassword)
    {
        DB::table('users')
            ->where('phone', $phone)
            ->update([
                'password' => Hash::make($newPassword),
                'updated_at' => now()
            ]);
    }

  
    private function logSmsRequest($phone, $newPassword)
    {
        $smsMessage = $this->buildPasswordResetMessage($newPassword);
        
        DB::table('send_sms_code')->insert([
            'phone' => $phone,
            'content' => $smsMessage,
            'status' => 0,
            'code' => $newPassword,
            'type' => self::SMS_TYPE_PASSWORD_RESET,
            'created_at' => now()
        ]);
    }


    private function buildPasswordResetMessage($newPassword)
    {
        return "En İyi Salon App - Yeni şifreniz: {$newPassword} - Güvenliğiniz için şifrenizi değiştirmeyi unutmayın.";
    }


    private function sendPasswordResetSms($phone, $newPassword, $settings)
    {
        $smsMessage = $this->buildPasswordResetMessage($newPassword);
        
        $smsData = [
            'islem' => 1,
            'user' => $settings->sms_username,
            'pass' => $settings->sms_password,
            'mesaj' => $smsMessage,
            'numaralar' => $phone,
            'baslik' => $settings->sms_header,
        ];

        try {
            $client = new Client();
            $response = $client->request('POST', self::SMS_API_URL, [
                'form_params' => $smsData,
                'timeout' => self::SMS_TIMEOUT
            ]);

            Log::info('SMS sent successfully', [
                'phone' => $phone,
                'response_status' => $response->getStatusCode()
            ]);

        } catch (RequestException $e) {
            Log::error('SMS sending failed: ' . $e->getMessage(), [
                'phone' => $phone,
                'sms_data' => $smsData
            ]);
            throw $e;
        }
    }
}


