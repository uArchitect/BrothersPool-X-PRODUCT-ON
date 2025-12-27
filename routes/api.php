<?php
// sTAN - 07.07.2025
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

define('DATABASE_PATH', '/var/www/vhosts/eniyisalonapp.com/databases/');
define('IMAGE_URL', '/images/');
define('EMPLOYEES_URL', '/uploads/employees/');
define('CATEGORY_URL', '/uploads/Categories/');
define('ALTF4_API_URL', 'http://altf4.masterbm.com/api/v4/');

$userId = 0;
function apikeyVerify($request) {
	global $userId;
    $user = DB::table('users')->where('api_key', $request->header('Authorization'))->first();
    $userId = $user ? $user->id : 0;
    return $userId > 0;
}
function getDateTimeZone($date = 'now', $format = 'Y-m-d H:i:s', $timezone = 'Europe/Istanbul') {
	$timeZone = new DateTimeZone($timezone);
	$dateTime = new DateTime($date, $timeZone);
	return $dateTime->format($format);
}
function getDateTimeZoneOffset($offset = 0, $date = 'now', $format = 'Y-m-d H:i:s', $timezone = 'Europe/Istanbul') {
    $timeZone = new DateTimeZone($timezone);
    $dateTime = new DateTime($date, $timeZone);
    if ($offset !== 0) {
        $interval = new DateInterval('PT' . abs($offset) . 'M');
        $offset > 0 ? $dateTime->add($interval) : $dateTime->sub($interval);
    }
    return $dateTime->format($format);
}
/*********************************************************************************************************************************
													DEMO İŞLEMLERİ
*********************************************************************************************************************************/
Route::post('/install-demo', function (Request $request) {
	$demoData = $request->all();
	$result = installDatabase($demoData);
	return $result;
});
function installDatabase($data) {
    $filePath = DATABASE_PATH.'default_demo.sql';
    if (!file_exists($filePath)) {
        return response()->json([
			'status'  => false,
            'message' => 'SQL dosyası bulunamadı.',
            'details' => $filePath,
        ], 400);
    }
    try {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $tables = DB::select('SHOW TABLES');
        $dbNameKey = 'Tables_in_' . DB::getDatabaseName();
        foreach ($tables as $table) {
            $tableName = $table->$dbNameKey;
            DB::statement("DROP TABLE IF EXISTS `$tableName`");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $sql = file_get_contents($filePath);
		DB::unprepared($sql);
		$data['password'] = Hash::make($data['password']);
		$userId = DB::table('users')->insertGetId([
            'name'     => $data['name'],
            'username' => $data['name'],
            'phone'    => $data['phone'],
            'password' => $data['password'],
        ]);
        if (!$userId) {
            return response()->json([
				'status'  => false,
				'message' => 'Kullanıcı oluşturulamadı.',
			], 401);
        }
        $permissions = DB::table('permissions')->get();
        $userPermissions = $permissions->map(function ($permission) use ($userId) {
            return [
                'user_id' => $userId,
                'permission_id' => $permission->id
            ];
        })->toArray();
        DB::table('user_permissions')->insert($userPermissions);
		if (!empty($data['package_features'])) {
			DB::table('package_features')->truncate();
			DB::table('package_features')->insert($data['package_features']);
		}
		DB::table('settings')->where('id', 1)->update([
			'salon_name'   		  => $data['company'] ?? $data['name'],
			'phone_number' 		  => $data['phone'],
			'address' 			  => $data['address'],
			'tax_office' 		  => $data['tax_flat'],
			'tax_number' 		  => $data['tax_no'],
			'package_name' 		  => $data['package_features']['name'],
			'remaining_sms_limit' => $data['package_features']['total_sms_limit'],
		]);
        return response()->json([
			'status'  => true,
			'message' => 'Tüm tablolar silindi ve database import edildi.',
		], 200);
    } catch (\Throwable $e) {
		Log::error('Yükleme hatası: ' . $e->getMessage());
        return response()->json([
			'status'  => false,
            'message' => 'Kurulum sırasında bir hata oluştu.',
            'details' => $e->getMessage(),
        ], 402);
    }
}
/*********************************************************************************************************************************
													GİRİŞ İŞLEMLERİ
*********************************************************************************************************************************/
// Installation route removed - controller deleted
Route::post('/login', function (Request $request) {
    $credentials = $request->only('phone', 'password');
    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Login Failed'], 401);
	}
	$remainingPackageDay = getRemainingPackageDays();
    if ($remainingPackageDay < 0) {
        return response()->json(['message' => 'Paket süreniz dolmuştur. Lütfen aboneliğinizi kontrol edin.'], 403);
    }
	$user = DB::table('users')
		->where('phone', $request->phone)
		->where('status', 1)
		->select('id', 'employee_id', 'name', 'username', 'phone', 'email', 'api_key')
		->first();
	if (!$user) {
		return response()->json(['message' => 'Kullanıcı bulunamadı veya kapalı durumda.'], 402);
	}
    $userArray = (array) $user;
    $newApiKey = Str::random(32);
    $userManipulate = array_merge($userArray, [
		'api_key' 		=> $newApiKey,
		'remaining_day' => $remainingPackageDay,
	]);
    DB::table('users')->where('id', $user->id)->update(['api_key' => $newApiKey]);
	$settings = DB::table('settings')
		->select('work_start', 'work_end', 'interval_time', 'currency_symbol', 'area_code', 'number_length', 'package_name',
				 'room_based_working', 'account_deleted', 'package_information')
		->first();
	$packageFeatures = DB::table('package_features')
		->select('expired_date')
		->first();
	$permissions = DB::table('user_permissions')
		->select('user_permissions.id', 'user_permissions.permission_id', 'permissions.name')
		->leftJoin('permissions', 'permissions.id', '=', 'user_permissions.permission_id')
        ->where('user_id', $user->id)
		->get();
	$userManipulate = [
		...$userManipulate,
		...(array) $settings,
		...(array) $packageFeatures
	];
	$userManipulate['permissions'] = $permissions;
	return response()->json($userManipulate, 200);
});
Route::post('/check-apikey', function (Request $request) {
	global $userId;
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
	}
	$remainingPackageDay = getRemainingPackageDays();
    if ($remainingPackageDay < 0) {
        return response()->json(['message' => 'Paket süreniz dolmuştur. Lütfen aboneliğinizi kontrol edin.'], 403);
    }
	$oneSignalId = $request->onesignal_id;
	$phone = DB::table('users')->where('id', $userId)->value('phone');
	$requestBody = postRequest('postOneSignalId', ['username' => $phone, 'onesignal_id' => $oneSignalId]);
    DB::table('users')->where('id', $userId)->update(['onesignal_id' => $oneSignalId]);
    return response()->json([
		'message' 		=> 'API anahtarının doğrulaması başarıyla tamamlandı.',
		'remaining_day' => $remainingPackageDay,
	], 200);
});
function getRemainingPackageDays() {
    $expiredDate = DB::table('package_features')->where('id', 1)->value('expired_date');
    if ($expiredDate) {
        $now = new DateTime('now', new DateTimeZone('Europe/Istanbul'));
        $expired = new DateTime($expiredDate, new DateTimeZone('Europe/Istanbul'));
        $interval = $now->diff($expired);
        $days = (int)$interval->format('%r%a');
        return $days;
    }
    return -1;
}
/*********************************************************************************************************************************
														KULLANICILAR
*********************************************************************************************************************************/
Route::get('/users', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $users = DB::table('users')
        ->select('id', 'name', 'email', 'phone')
        ->orderBy('id', 'desc')
        ->get();
    $usersWithPermissions = $users->map(function ($user) {
        $permissions = DB::table('user_permissions')
            ->select('user_permissions.*', 'permissions.name')
            ->leftJoin('permissions', 'permissions.id', '=', 'user_permissions.permission_id')
            ->where('user_permissions.user_id', $user->id)
            ->get();
        $user->permissions = $permissions;
        return $user;
    });
    return response()->json($usersWithPermissions, 200);
});
Route::post('/add-user', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $userData = (array) json_decode($request->getContent());
	$phoneNumber = $userData['phone'];
	$phoneExists = DB::table('users')->where('phone', $phoneNumber)->exists();
	if ($phoneExists) {
		return response()->json(['message' => 'Kullanıcı telefon numarası kayıtlı.'], 402);
	}
    $requestBody = postRequest('postFSAddUser', ['username' => $phoneNumber, 'pro' => 0]);
    if (empty($requestBody)) {
        return response()->json(['message' => 'Ekleme başarısız.'], 403);
    }
    $userData['password'] = Hash::make($userData['password']);
    $userId = DB::table('users')->insertGetId($userData);
    if ($userId) {
        $groupPermission = DB::table('group_permission')
			->select('permission_id')
			->where('group_id', $userData['group_id'])
			->get();
        $userPermissions = $groupPermission->map(function ($permission) use ($userId) {
            return [
                'user_id' => $userId,
                'permission_id' => $permission->permission_id
            ];
        })->toArray();
        DB::table('user_permissions')->insert($userPermissions);
        $response = DB::table('users')->where('id', $userId)->first();
        return response()->json($response, 200);
    } else {
        return response()->json(['message' => 'Ekleme başarısız.'], 201);
    }
});
Route::post('/update-user', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$userData = (array) json_decode($request->getContent());
	unset($userData['group_id']);
	$userId = $userData['id'];
	$newUsername = $userData['phone'];
	$password = $userData['password'] ?? null;
    if (empty($userData) || !isset($userId)) {
        return response()->json(['message' => 'Eksik veri'], 400);
    }
    if (!empty($password)) {
        $userData['password'] = Hash::make($password);
    } else {
        unset($userData['password']);
    }
	$userDetail = DB::table('users')->where('id', $userId)->first();
	$currentUsername = $userDetail->phone;
	if ($currentUsername != $newUsername) {
		$body = [
			'username' => $currentUsername,
			'new_username' => $newUsername,
			'pro' => 0,
		];
		$requestBody = postRequest('postFSUpdateUser', $body);
		if (empty($requestBody)) {
			return response()->json(['message' => 'Güncelleme başarısız.'], 403);
		}
	}
    $updated = DB::table('users')->where('id', $userId)->update($userData);
	if ($updated) {
		$response = DB::table('users')->where('id', $userId)->first();
		return response()->json($response, 200);
	} else {
		return response()->json(['message' => 'Güncelleme başarısız.'], 201);
	}
});
Route::post('/delete-user', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$userData = DB::table('users')->where('id', $request->user_id)->first();
	$requestBody = postRequest('postFSDeleteUser', ['username' => $userData->phone, 'pro' => 0]);
	if (!isset($requestBody) || empty($requestBody)) {
        return response()->json(['message' => 'Silme başarısız!'], 400);
    }
	$deleted = DB::table('users')->where('id', $request->user_id)->delete();
	if ($deleted) {
		DB::table('user_permissions')->where('user_id', $request->user_id)->delete();
		return response()->json(['message' => 'Silme başarılı!'], 200);
	} else {
		return response()->json(['message' => 'Silme başarısız!'], 201);
	}
});
Route::post('/update-user-permission', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
	}
	$userData = (array) json_decode($request->getContent());
	DB::table('user_permissions')->where('user_id', $request->user_id)->delete();
	$permissionsData = $userData['permissions'] ?? [];
	$permissions = [];
	foreach ($permissionsData as $permissionName) {
		$permission = DB::table('permissions')->where('name', 'LIKE', "%{$permissionName}%")->first();
		if ($permission) {
			$permissions[] = ['user_id' => $request->user_id, 'permission_id' => $permission->id];
		}
	}
	if ($permissions) {
		DB::table('users')->where('id', $request->user_id)->update(['api_key' => null]);
		DB::table('user_permissions')->insert($permissions);
		return response()->json(['message' => 'Ekleme başarılı.'], 200);
	} else {
		return response()->json(['message' => 'Ekleme başarısız.'], 201);
	}
});
Route::post('/update-user-status', function (Request $request) {
	global $userId;
	if (!apikeyVerify($request)) {
		return response()->json(['message' => 'Unauthorized'], 401);
	}
	$updated = DB::table('users')->where('id', $userId)->update(['status' => $request->status]);
	if ($updated) {
		DB::table('users')->where('id', $request->user_id)->update(['api_key' => null]);
		return response()->json(['message' => 'Güncelleme başarılı.'], 200);
	} else {
		return response()->json(['message' => 'Güncelleme başarısız.'], 201);
	}
});
Route::post('/change-password', function (Request $request) {
    global $userId;
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $currentPassword = $request->password;
    $newPassword = $request->new_password;
    if (empty($currentPassword) || empty($newPassword)) {
        return response()->json(['message' => 'Eksik veri'], 400);
    }
    $currentPasswordHash = DB::table('users')->where('id', $userId)->value('password');
    if (!Hash::check($currentPassword, $currentPasswordHash)) {
        return response()->json(['message' => 'Mevcut şifre hatalı'], 202);
    }
    if (Hash::check($newPassword, $currentPasswordHash)) {
        return response()->json(['message' => 'Yeni şifre eski şifre ile aynı olamaz'], 203);
    }
    $updated = DB::table('users')->where('id', $userId)->update(['password' => Hash::make($newPassword)]);
    if ($updated) {
        return response()->json(['message' => 'Şifre başarıyla güncellendi'], 200);
    }
    return response()->json(['message' => 'Şifre güncellenirken bir hata oluştu'], 201);
});
/*********************************************************************************************************************************
														ŞİFREMİ UNUTTUM
*********************************************************************************************************************************/
Route::post('/forgot-password', function (Request $request) {
    $phoneNumber = $request->phone;
    if (!$phoneNumber) {
        return response()->json(['message' => 'Telefon numarası gerekli.'], 400);
    }
    $user = DB::table('users')->where('phone', $phoneNumber)->first();
    if (!$user) {
        return response()->json(['message' => 'Kullanıcı bulunamadı.'], 401);
    }
	$now = getDateTimeZone();
    $existing = DB::table('forgot_password')
        ->where('phone', $phoneNumber)
        ->where('status', 0)
        ->where('validity_date', '>', $now)
        ->first();
    if ($existing) {
		$currentCode = DB::table('forgot_password')->where('phone', $phoneNumber)->value('code');
        return response()->json([
			'code' => $currentCode,
			'message' => 'Zaten bir kod gönderildi. Lütfen gönderilen kodu kullanın.',
		], 402);
	}
	$code = rand(100000, 999999);
	$validity = getDateTimeZoneOffset(15);
	DB::table('forgot_password')->updateOrInsert(
		['phone' => $phoneNumber],
        [
            'code' => $code,
            'validity_date' => $validity,
            'status' => 0
        ]
    );
    $message = "Şifre sıfırlama için doğrulama kodunuz: $code";
    $status = sendSMSMessage($phoneNumber, $message, 'verification_code', $code);
    if ($status) {
        return response()->json([
			'code' => $code,
			'message' => 'Doğrulama kodu gönderildi.',
		], 200);
    } else {
        return response()->json(['message' => 'Doğrulama kodu gönderilemedi.'], 201);
    }
});
Route::post('/verification-code', function (Request $request) {
	$phoneNumber = $request->phone;
    $code = $request->code;
    if (!isset($phoneNumber) || !isset($code)) {
		return response()->json(['message' => 'Eksik veri.'], 400);
	}
	$phoneExists = DB::table('users')->where('phone', $phoneNumber)->exists();
	if (!$phoneExists) {
		return response()->json(['message' => 'Kullanıcı bulunamadı.'], 401);
	}
	$newPassword = rand(100000, 999999);
    $updated = DB::table('users')->where('phone', $phoneNumber)->update(['password' => Hash::make($newPassword)]);
	if ($updated) {
		DB::table('forgot_password')->where('phone', $phoneNumber)->update(['status' => 1]);
		$message = "Şifreniz başarıyla oluşturulmuştur. Yeni şifreniz: $newPassword";
		sendSMSMessage($phoneNumber, $message, 'password', $newPassword);
		return response()->json(['message' => 'Yeni şifre gönderildi.'], 200);
	} else {
        return response()->json(['message' => 'Yeni şifre gönderilemedi.'], 201);
    }
});
/*********************************************************************************************************************************
														ÇALIŞANLAR
*********************************************************************************************************************************/
Route::get('/employees', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$search = $request->query('search');
	$employeesQuery = DB::table('employees');
	if (isset($search)) {
		$employeesQuery->where(function ($query) use ($search) {
			$query->where('name', 'like', '%' . $search . '%')
				->orWhere('email', 'like', '%' . $search . '%');
		});
	}
    $employees = $employeesQuery->orderBy('id', 'desc')->get();
	$employees->each(function ($item) {
		if (isset($item->avatar) && !empty($item->avatar)) {
			$item->avatar = EMPLOYEES_URL . $item->avatar;
		}
		$item->user_id = DB::table('users')->where('employee_id', $item->id)->value('id');
		$item->speciality = DB::table('specializations')->where('id', $item->skills)->value('name');
		$item->table_name = DB::table('tables')->where('id', $item->table_id)->value('name');
		$totalCount = DB::table('employee_commissions')->where('employee_id', $item->id)->count();
		$item->commission_count = (int) $totalCount;
		$totalAmount = DB::table('employee_commissions')->where('employee_id', $item->id)->sum('amount');
		$item->commission_amount = (double) $totalAmount;
		// Randevular
        $reservationsQuery = DB::table('reservations')
            ->leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('employees', 'reservations.employee_id', '=', 'employees.id')
            ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
            ->select(
                'reservations.*',
                DB::raw('CONCAT(customers.first_name, " ", customers.last_name) AS customer_name'),
                'employees.name AS employee_name',
                'employees.position AS employee_position',
                'tables.name AS table_name'
            );
        $reservations = $reservationsQuery->where('reservations.employee_id', $item->id)
			->orderBy('start_date', 'desc')
			->get();
        // İşlemler
        foreach ($reservations as $reservation) {
            $reservation->services = DB::table('reservations_items')
                ->join('services', 'reservations_items.service_id', '=', 'services.id')
                ->where('reservations_items.reservation_id', $reservation->id)
                ->select('services.id', 'services.name', 'services.description', 'services.price', 'services.discount_price')
                ->get();
        }
        $item->reservations = $reservations;
	});
    return response()->json($employees, 200);
});
Route::post('/add-employee', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $max_employees = DB::table('package_features')->where('id', 1)->value('max_employees') ?? 0;
    $employee_count = DB::table('employees')->count();
    if ($employee_count >= $max_employees) {
        return response()->json(['message' => 'Maksimum çalışan sayısına ulaşıldı'], 400);
    }
    $employeeData = (array) json_decode($request->getContent());
    $existing = DB::table('employees')
        ->where('name', $employeeData['name'])
        ->orWhere('phone', $employeeData['phone'])
        ->orWhere('email', $employeeData['email'])
        ->first();
    if ($existing) {
		return response()->json(['message' => 'Aynı isim, telefon veya e-posta zaten kayıtlı.'], 402);
	}
    if ($employeeData['avatar'] != '0') {
        $avatarName = createAvatarEmployee($employeeData);
        $employeeData['avatar'] = $avatarName ?: null;
    } else {
        unset($employeeData['avatar']);
    }
    $skills = $employeeData['skills'] ?? null;
    if (isset($skills) && is_array($skills)) {
        $employeeData['skills'] = json_encode($skills);
    }
    $inserted = DB::table('employees')->insertGetId($employeeData);
    if ($inserted) {
        $response = DB::table('employees')->where('id', $inserted)->first();
        $response->speciality = DB::table('specializations')->where('id', $response->skills)->value('name');
        $response->table_name = DB::table('tables')->where('id', $response->table_id)->value('name');
        if (!empty($response->avatar) || isset($response->avatar)) {
            $response->avatar = EMPLOYEES_URL . $response->avatar;
        }
        return response()->json($response, 200);
    } else {
        return response()->json(['message' => 'Ekleme başarısız!'], 201);
    }
});
Route::post('/update-employee', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $employeeData = (array) json_decode($request->getContent());
    if (empty($employeeData) || !isset($employeeData['id'])) {
        return response()->json(['message' => 'Eksik veri'], 400);
	}
	$existing = DB::table('employees')
		->where('name', $employeeData['name'])
		->orWhere('phone', $employeeData['phone'])
		->orWhere('email', $employeeData['email'])
		->first();
	if ($existing) {
		return response()->json(['message' => 'Aynı isim, telefon veya e-posta zaten kayıtlı.'], 402);
	}
	if ($employeeData['avatar'] != '0') {
		$avatarName = createAvatarEmployee($employeeData);
		$employeeData['avatar'] = $avatarName ?: null;
	} else {
		unset($employeeData['avatar']);
	}
    $employeeId = $employeeData['id'];
	$updated = DB::table('employees')->where('id', $employeeId)->update($employeeData);
	$response = DB::table('employees')->where('id', $employeeId)->first();
	$response->speciality = DB::table('specializations')->where('id', $response->skills)->value('name');
	$response->table_name = DB::table('tables')->where('id', $response->table_id)->value('name');
	if (!empty($response->avatar) || isset($response->avatar)) {
		$response->avatar = EMPLOYEES_URL . $response->avatar;
	}
    return response()->json($response, 200);
});
Route::post('/delete-employee', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$employee = DB::table('employees')->where('id', $request->employee_id)->first();
	if (!$employee) {
		return response()->json(['message' => 'Çalışan bulunamadı.'], 402);
	}
	$reservationsExists = DB::table('reservations')->where('employee_id', $employee->id)->exists();
	if ($reservationsExists) {
        return response()->json([
            'message' => 'Çalışan kaydını silemezsiniz, bu çalışan ile ilişkili rezervasyon bulunuyor!'
        ], 400);
    }
	DB::beginTransaction();
    try {
        $avatarName = DB::table('employees')->where('id', $employee->id)->value('avatar');
        if ($avatarName) {
            deleteImage(EMPLOYEES_URL, $avatarName); 
        }
		if ($employee->table_id) {
			DB::table('tables')->where('id', $employee->table_id)->update(['employee_id' => null]);
		}
        DB::table('employee_commissions')->where('employee_id', $employee->id)->delete();
        $deleted = DB::table('employees')->where('id', $employee->id)->delete();
        DB::commit();
        if ($deleted) {
            return response()->json(['message' => 'Silme başarılı!'], 200);
        } else {
            return response()->json(['message' => 'Silme başarısız!'], 201);
        }
    } catch (\Exception $e) {
        DB::rollBack();
		Log::error($e->getMessage());
        return response()->json(['message' => 'Bir hata oluştu: ' . $e->getMessage()], 201);
    }
});
Route::get('/service-commissions', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $employeeId = $request->query('employee_id');
    if (!$employeeId) {
        return response()->json(['message' => 'Çalışan ID’si gereklidir.'], 402);
    }
    $services = DB::table('services as s')
        ->leftJoin('employee_service_commissions as sc', function ($join) use ($employeeId) {
            $join->on('sc.service_id', '=', 's.id')
                 ->where('sc.employee_id', '=', $employeeId);
        })
        ->select('s.id', 's.name', 'sc.commission_rate as rate')
		->where('s.is_stock', 1)
        ->orderBy('s.id', 'desc')
        ->get();
    return response()->json($services, 200);
});
Route::post('/update-service-commissions', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $employeeId = $request->employee_id;
    $commissions = $request->commissions;
    DB::beginTransaction();
    try {
        DB::table('employee_service_commissions')->where('employee_id', $employeeId)->delete();
        foreach ($commissions as $item) {
            $serviceId = $item['service_id'];
            $rate = array_key_exists('rate', $item) ? $item['rate'] : null;
            if ($rate !== null && $rate <= 0) continue;
            DB::table('employee_service_commissions')
                ->updateOrInsert(
                    ['employee_id' => $employeeId, 'service_id' => $serviceId],
                    ['commission_rate' => $rate]
                );
        }
        DB::commit();
        return response()->json(['message' => 'Primler başarıyla güncellendi.'], 200);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['message' => 'Sunucu hatası'], 201);
    }
});
function createAvatarEmployee($data) {
	if ($data['avatar'] != '0') {
		$avatarName = DB::table('employees')->where('id', $data['id'])->value('avatar');
		if ($avatarName) {
			deleteImage(EMPLOYEES_URL, $avatarName); 
		}
		$avatarName = createImage(EMPLOYEES_URL, $data['avatar']);
		return $avatarName;
	}
	return null;
}
/*********************************************************************************************************************************
														MÜŞTERİLER
*********************************************************************************************************************************/
Route::get('/customers', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $search = $request->query('search');
    $customersQuery = DB::table('customers');
    if (isset($search)) {
        $customersQuery->where(function ($query) use ($search) {
            $query->where('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');
        });
    }
    $customers = $customersQuery->orderBy('id', 'desc')->limit(100)->get();
    $customers->each(function ($customer) {
        unset($customer->password);
		// Müşteri İşlem Tutarları
		$process = DB::table('sales')
			->where('sales.customer_id', $customer->id)
			->selectRaw('
				COUNT(sales.id) AS sales_count,
				COALESCE(SUM(sales.grand_total), 0) AS grand_total,
				COALESCE(SUM(sales.paid), 0) AS paid,
				COALESCE(SUM(sales.grand_total - sales.paid), 0) AS remaining_price
			')
			->first();
		$customer->sales_count = (int) $process->sales_count;
		$customer->grand_total = (float) $process->grand_total;
		$customer->paid = (float) $process->paid;
		$customer->remaining_price = (float) $process->remaining_price;
        // Randevular
        $reservationsQuery = DB::table('reservations')
            ->leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('employees', 'reservations.employee_id', '=', 'employees.id')
            ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
            ->select(
                'reservations.*',
                DB::raw('CONCAT(customers.first_name, " ", customers.last_name) AS customer_name'),
                'employees.name AS employee_name',
                'employees.position AS employee_position',
                'tables.name AS table_name'
            );
        $reservations = $reservationsQuery->where('reservations.customer_id', $customer->id)
			->orderBy('start_date', 'desc')
			->get();
        // Randevu İşlemleri
        foreach ($reservations as $reservation) {
            $reservation->services = DB::table('reservations_items')
                ->join('services', 'reservations_items.service_id', '=', 'services.id')
                ->where('reservations_items.reservation_id', $reservation->id)
                ->select('services.id', 'services.name', 'services.description', 'services.price', 'services.discount_price')
                ->get();
        }
        $customer->reservations = $reservations;
        // Satışlar
        $salesQuery = DB::table('sales')
            ->leftJoin('sale_status', 'sale_status.id', '=', 'sales.sale_status')
            ->select('sales.*', 'sale_status.name AS status_name');
        $sales = $salesQuery->where('sales.customer_id', $customer->id)->orderBy('sales.id', 'desc')->get();
        // Satış İşlemleri
        foreach ($sales as $sale) {
            $sale->items = DB::table('sale_items')->where('sale_items.id', $sale->id)->get();
        }
        $customer->sales = $sales;
    });
    return response()->json($customers, 200);
});
Route::post('/add-customer', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$max_customers = DB::table('package_features')->where('id', 1)->value('max_customers') ?? 0;
	$customer_count = DB::table('customers')->count();
	if ($customer_count >= $max_customers) {
		return response()->json(['message' => 'Maksimum müşteri sayısına ulaşıldı'], 400);
	}
    $customerData = (array) json_decode($request->getContent());
    $inserted = DB::table('customers')->insertGetId($customerData);
    if ($inserted) {
        $response = DB::table('customers')->where('id', $inserted)->first();
        unset($response->password);
        return response()->json($response, 200);
    } else {
        return response()->json(['message' => 'Ekleme başarısız!'], 201);
    }
});
Route::post('/update-customer', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $customerData = (array) json_decode($request->getContent());
    if (empty($customerData) || !isset($customerData['id'])) {
        return response()->json(['message' => 'Eksik veri'], 400);
    }
    $customerId = $customerData['id'];
    $updated = DB::table('customers')->where('id', $customerId)->update($customerData);
    $response = DB::table('customers')->where('id', $customerId)->first();
    unset($response->password);
    return response()->json($response, 200);
});
Route::post('/delete-customer', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $salesExists = DB::table('sales')->where('customer_id', $request->customer_id)->exists();
    $reservationsExists = DB::table('reservations')->where('customer_id', $request->customer_id)->exists();
    if ($salesExists || $reservationsExists) {
        return response()->json(['message' => 'Müşteri kaydını silemezsiniz, bu müşteri ile ilişkili satış veya rezervasyon bulunuyor!'], 400);
    }
    $deleted = DB::table('customers')->where('id', $request->customer_id)->delete();
    if ($deleted) {
        return response()->json(['message' => 'Silme başarılı!'], 200);
    } else {
        return response()->json(['message' => 'Silme başarısız!'], 201);
    }
});
/*********************************************************************************************************************************
														BANKA & KASA
*********************************************************************************************************************************/
Route::get('/accounts', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$accounts = DB::table('accounts')->get();
	foreach ($accounts as $account) {
		$account->total_income = DB::table('transactions')
			->where('account_id', $account->id)
			->where('type', 'Gelir')
			->sum('amount') ?? 0;
		$account->total_expense = DB::table('transactions')
			->where('account_id', $account->id)
			->where('type', 'Gider')
			->sum('amount') ?? 0;
		$account->total_balance = $account->balance + ($account->total_income - $account->total_expense);
		$transactions = DB::table('transactions')->where('account_id', $account->id)->get();
		$account->transactions = $transactions;
	}
	$response = [
		'accounts' => $accounts
	];
    return response()->json($response, 200);
});
Route::post('/add-account', function (Request $request) {
	if (!apikeyVerify($request)) {
		return response()->json(['message' => 'Unauthorized'], 401);
	}
	$data = (array) json_decode($request->getContent());
	$accountId = DB::table('accounts')->insertGetId([
		'name' => $data['name'],
		'description' => $data['description'],
		'balance' => $data['balance'],
		'iban' => $data['iban'],
	]);
	if ($accountId) {
		$account = DB::table('accounts')->where('id', $accountId)->first();
		$account->total_income = DB::table('transactions')
			->where('account_id', $accountId)
			->where('type', 'Gelir')
			->sum('amount') ?? 0;
		$account->total_expense = DB::table('transactions')
			->where('account_id', $accountId)
			->where('type', 'Gider')
			->sum('amount') ?? 0;
		$account->total_balance = $account->balance + ($account->total_income - $account->total_expense);
		$transactions = DB::table('transactions')->where('account_id', $accountId)->get();
		$account->transactions = $transactions;
		return response()->json($account, 200);
	} else {
		return response()->json(['message' => 'Ekleme başarısız!'], 201);
	}
});
Route::post('/update-account', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $accountData = (array) json_decode($request->getContent());
	unset($accountData['balance']);
	$accountId = $accountData['id'];
	if (empty($accountData) || !isset($accountId)) {
		return response()->json(['message' => 'Eksik veri'], 400);
    }
    $updated = DB::table('accounts')->where('id', $accountId)->update($accountData);
	if ($updated) {
		$account = DB::table('accounts')->where('id', $accountId)->first();
		$account->total_income = DB::table('transactions')
			->where('account_id', $accountId)
			->where('type', 'Gelir')
			->sum('amount') ?? 0;
		$account->total_expense = DB::table('transactions')
			->where('account_id', $accountId)
			->where('type', 'Gider')
			->sum('amount') ?? 0;
		$account->total_balance = $account->balance; // Use existing balance, don't double calculate
		$transactions = DB::table('transactions')->where('account_id', $accountId)->get();
		$account->transactions = $transactions;
		return response()->json($account, 200);
	} else {
		return response()->json(['message' => 'Güncelleme başarısız!'], 201);
	}
});
Route::post('/delete-account', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$transactionsExists = DB::table('transactions')->where('account_id', $request->account_id)->exists();
	if ($transactionsExists) {
		return response()->json([
			'message' => 'Hesap kaydını silemezsiniz, bu hesap ile ilişkili gelir veya gider bulunuyor!'
		], 400);
	}
	$deleted = DB::table('accounts')->where('id', $request->account_id)->delete();
	if ($deleted) {
		return response()->json(['message' => 'Silme başarılı!'], 200);
	} else {
		return response()->json(['message' => 'Silme başarısız!'], 201);
	}
});
/*********************************************************************************************************************************
														SATIŞLAR
*********************************************************************************************************************************/
Route::get('/sales', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $search = $request->query('search');
    $salesQuery = DB::table('sales')
        ->leftJoin('sale_status', 'sale_status.id', '=', 'sales.sale_status')
        ->select('sales.*', 'sale_status.name AS status_name');
    if (!empty($search)) {
        $salesQuery->where(function ($query) use ($search) {
            $query->where('customer', 'LIKE', '%' . $search . '%');
        });
    }
    $sales = $salesQuery->orderBy('sales.id', 'desc')->get();
    foreach ($sales as $sale) {
        $sale->items = DB::table('sale_items')
            ->where('sale_id', $sale->id)
            ->get();
    }
    return response()->json($sales, 200);
});
Route::post('/add-sale', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $saleData = json_decode($request->getContent(), true);
	DB::beginTransaction();
    try {
        $now = getDateTimeZone();
        $customerId = $saleData['customer_id'];
        $employeeId = $saleData['employee_id'];
        $accountId = $saleData['account_id'];
        $paymentMethodId = $saleData['payment_method_id'];
		$paymentMethod = DB::table('payment_methods')->where('id', $paymentMethodId)->value('name');
		$discountType = $saleData['discount_type'];
        $discountPercent = $saleData['discount_percent'] ?? 0;
        $discountAmount = $saleData['discount_amount'] ?? 0;
        $total = $saleData['total_price'] ?? 0;
		$totalPrice = $saleData['total_price'] ?? 0;
        $note = $saleData['note'];
        $services = $saleData['services'] ?? [];
        $quantities = $saleData['quantities'] ?? [];
        $customer = DB::table('customers')->where('id', $customerId)->first();
        if (!$customer) {
            return response()->json(['message' => 'Müşteri bulunamadı'], 402);
        }
        $saleId = DB::table('sales')->insertGetId([
            'date' => $now,
            'customer_id' => $customerId,
            'customer' => $customer->first_name . ' ' . $customer->last_name,
            'total' => $totalPrice,
			'discount_type' => $discountType,
            'discount_percent' => $discountPercent,
            'product_discount' => $discountAmount,
            'total_discount' => $discountAmount,
            'grand_total' => $totalPrice - $discountAmount,
            'paid' => $totalPrice - $discountAmount,
            'invoice_status' => 2,
            'sale_status' => 1,
			'seller_id' => $employeeId,
            'payment_method_id' => $paymentMethodId,
            'payment_method' => $paymentMethod,
            'note' => $note,
            'created_at' => $now,
        ]);
        foreach ($services as $index => $serviceId) {
            $service = DB::table('services')->where('id', $serviceId)->first();
            if (!$service) continue;
            $quantity = isset($quantities[$index]) ? (int) $quantities[$index] : 1;
            DB::table('sale_items')->insert([
                'sale_id' => $saleId,
                'service_id' => $serviceId,
                'product_name' => $service->name,
                'net_unit_price' => $service->price,
                'unit_price' => $service->total_price,
                'item_tax' => $service->tax_amount ?? 0,
                'tax_id' => $service->tax_id,
                'tax_rate' => $service->tax_rate ?? 0,
                'discount' => 0,
                'subtotal' => $service->total_price * $quantity,
                'quantity' => $quantity,
                'created_at' => $now,
            ]);
        }
        DB::table('transactions')->insert([
            'account_id' => $accountId,
            'customer_id' => $customerId,
            'sale_id' => $saleId,
            'type' => 'Gelir',
            'amount' => $totalPrice - $discountAmount,
            'description' => 'Satış Ödemesi',
            'created_at' => $now,
        ]);
		// Komisyon ve ParaPuan Ekle
		addCommissionToSeller($saleId);
		addParapuanToCustomer($customerId);
        DB::commit();
        return response()->json(['message' => 'Satış başarıyla eklendi!'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
		Log::error('Satış ekleme hatası', [
			'message' => $e->getMessage(),
			'line'    => $e->getLine(),
			'file'    => $e->getFile(),
		]);
        return response()->json(['message' => 'Ekleme başarısız!'], 201);
    }
});
Route::post('/cancel-sale', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
	}
	$saleId = $request->sale_id;
    DB::beginTransaction();
    try {
		$sale = DB::table('sales')->where('id', $saleId)->first();
		if (!$sale) {
			return response()->json(['message' => 'Satış bulunamadı!'], 400);
		}
        $updated = DB::table('sales')->where('id', $saleId)->update(['sale_status' => 2]);
        if (!$updated) {
            DB::rollBack();
            return response()->json(['message' => 'Satış iptali başarısız!'], 402);
		}
		$transactions = DB::table('transactions')
			->where('sale_id', $saleId)
			->orWhere('reservation_id', $sale->reservation_id)
            ->get();
        foreach ($transactions as $transaction) {
            if ($transaction->payment_id) {
                DB::table('payments')->where('id', $transaction->payment_id)->delete();
            }
        }
		DB::table('transactions')
			->where('sale_id', $saleId)
			->orWhere('reservation_id', $sale->reservation_id)
			->delete();
		DB::table('employee_commissions')
			->where('sale_id', $saleId)
			->orWhere('reservation_id', $sale->reservation_id)
			->delete();
		DB::table('reservations')->where('id', $sale->reservation_id)->update(['status' => 'cancelled']);
        DB::commit();
        return response()->json(['message' => 'Satış iptali başarılı!'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Satış iptali başarısız!'], 201);
    }
});
/*********************************************************************************************************************************
														GİDERLER
*********************************************************************************************************************************/
Route::get('/expenses', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$typeId = $request->query('type_id');
	$accountId = $request->query('account_id');
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');
    $expensesQuery = DB::table('expenses');
	if ($typeId) {
		$expensesQuery->where('expense_type_id', '=', $typeId);
	}
	if ($accountId) {
		$expensesQuery->where('account_id', '=', $accountId);
	}
    if ($startDate) {
        $expensesQuery->where('date', '>=', $startDate);
    }
    if ($endDate) {
        $expensesQuery->where('date', '<=', $endDate);
    }
    $expenses = $expensesQuery->get();
    foreach ($expenses as $expense) {
        $expense->note = strip_tags($expense->note);
		if (isset($expense->invoice_photo) && !empty($expense->invoice_photo)) {
			$expense->invoice_photo = IMAGE_URL . $expense->invoice_photo;
		}
        $expenseItems = DB::table('expense_items')->where('expense_items.expense_id', $expense->id)->get();
        $expense->expense_items = $expenseItems;
    }
    $expenseCategories = DB::table('expense_categories')->select('id', 'name')->get();
	$expenseTypes = DB::table('expense_types')->select('id', 'name')->get();
    $accounts = DB::table('accounts')->select('*')->get();
    $employees = DB::table('employees')->where('is_active', 1)->select('id', 'name')->get();
    $lastExpenseId = DB::table('expenses')->latest('id')->value('id');
    $lastId = $lastExpenseId ? $lastExpenseId + 1 : 1;
    $totalQuery = DB::table('expenses');
	if ($typeId) {
		$totalQuery->where('expense_type_id', '=', $typeId);
	}
	if ($accountId) {
		$totalQuery->where('account_id', '=', $accountId);
	}
    if ($startDate) {
        $totalQuery->where('date', '>=', $startDate);
    }
    if ($endDate) {
        $totalQuery->where('date', '<=', $endDate);
    }
    $total = $totalQuery->sum('total') ?? 0;
    $monthlyTotalsQuery = DB::table('expenses')
        ->select(
            DB::raw('YEAR(date) as year'),
            DB::raw('MONTH(date) as month'),
            DB::raw('SUM(total) as total')
        )
        ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc');
    if ($startDate) {
        $monthlyTotalsQuery->where('date', '>=', $startDate);
    }
    if ($endDate) {
        $monthlyTotalsQuery->where('date', '<=', $endDate);
    }
    $monthlyTotals = $monthlyTotalsQuery->get();
    $monthlyTotalsArray = $monthlyTotals->toArray();
    $response = [
        'total' => $total,
        'document_number' => $lastId,
        'monthly_totals' => $monthlyTotalsArray,
        'expenses' => $expenses,
        'expense_categories' => $expenseCategories,
		'expense_types' => $expenseTypes,
        'accounts' => $accounts,
        'employees' => $employees,
    ];
    return response()->json($response, 200);
});
Route::post('/add-expense', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $expenseData = json_decode($request->getContent(), true);
	DB::beginTransaction();
	try {
		$now = getDateTimeZone();
		$totalAmount = array_sum(array_column($expenseData['expense_items'], 'amount'));
		if ($expenseData['invoice_photo'] != '0') {
			$invoicePhotoName = createInvoicePhoto($expenseData);
		}
        $expenseId = DB::table('expenses')->insertGetId([
            'date' 			  => $expenseData['date'],
            'document_number' => $expenseData['document_number'],
            'expense_type_id' => $expenseData['expense_type_id'],
            'employee_id' 	  => $expenseData['employee_id'] ?? null,
            'note' 			  => $expenseData['note'] ?? '',
            'total' 		  => $totalAmount ?? 0,
            'account_id' 	  => $expenseData['account_id'],
			'invoice_photo'   => $invoicePhotoName ?? null,
        ]);
        if (!empty($expenseData['expense_items'])) {
            foreach ($expenseData['expense_items'] as $item) {
                DB::table('expense_items')->insert([
                    'expense_id' 		  => $expenseId,
                    'expense_category_id' => $item['expense_category_id'],
                    'expense' 			  => $item['expense'],
                    'amount'			  => $item['amount'],
                    'total' 			  => $item['amount'],
                    'date' 				  => $expenseData['date'],
                    'description' 		  => $item['description'],
					'created_at'  		  => $now,
                ]);
            }
        }
        $transactionData = [
            'account_id'  => $expenseData['account_id'],
			'expense_id'  => $expenseId,
            'type' 		  => 'Gider',
            'amount' 	  => $totalAmount ?? 0,
            'description' => 'Gider Eklendi',
			'created_at'  => $now,
        ];
        DB::table('transactions')->insert($transactionData);
        DB::commit();
        return response()->json(['message' => 'Gider başarıyla eklendi!'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Ekleme başarısız!'], 201);
    }
});
Route::post('/delete-expense', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	DB::beginTransaction();
    try {
        $expense = DB::table('expenses')->where('id', $request->expense_id)->first();
        $expenseItems = DB::table('expense_items')->where('expense_id', $request->expense_id)->get();
        $totalAmount = 0;
        foreach ($expenseItems as $item) {
            $totalAmount += $item->amount;
        }
		$photoName = DB::table('expenses')->where('id', $request->expense_id)->value('invoice_photo');
		if ($photoName) {
			deleteImage(IMAGE_URL, $photoName); 
		}
        DB::table('expenses')->where('id', $request->expense_id)->delete();
        DB::table('expense_items')->where('expense_id', $request->expense_id)->delete();
        DB::table('transactions')->where('expense_id', $request->expense_id)->delete();
        DB::commit();
        return response()->json(['message' => 'Silme başarılı!'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Silme başarısız!'], 201);
    }
});
function createInvoicePhoto($data) {
	if ($data['invoice_photo'] != '0') {
		$photoName = DB::table('expenses')->where('id', $data['id'])->value('invoice_photo');
		if ($photoName) {
			deleteImage(IMAGE_URL, $photoName); 
		}
		$photoName = createImage(IMAGE_URL, $data['invoice_photo']);
		return $photoName;
	}
	return null;
}
/*********************************************************************************************************************************
														HİZMETLER
*********************************************************************************************************************************/
Route::get('/services', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
	}
	$search = $request->query('search');
	$type = $request->query('type');
	$servicesQuery = DB::table('services')
		->where('is_active', 1)
		->when(!empty($search), function($query) use ($search) {
			return $query->where(function($value) use ($search) {
				$value->where('name', 'like', "%{$search}%")
					->orWhere('description', 'like', "%{$search}%");
			});
		});
	if ($type >= 0) {
		$servicesQuery->where('is_stock', '=', $type);
	}
	$services = $servicesQuery->orderBy('id', 'desc')->get();
    $categories = DB::table('categories')->where('is_active', 1)->get();
    $employees = DB::table('employees')->where('is_active', 1)->select('id', 'name')->get();
    $taxRates = DB::table('tax_rates')->get();
	$units = DB::table('units')->get();
	$warehouses = DB::table('warehouses')->get();
    $response = [
        'services' 	 => $services,
        'categories' => $categories,
        'employees'  => $employees,
        'tax_rates'  => $taxRates,
		'units' 	 => $units,
		'warehouses' => $warehouses,
    ];
    return response()->json($response, 200);
});
Route::post('/add-service', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $serviceData = (array) json_decode($request->getContent());
    $inserted = DB::table('services')->insertGetId($serviceData);
    if ($inserted) {
        $response = DB::table('services')->where('id', $inserted)->first();
        return response()->json($response, 200);
    } else {
        return response()->json(['message' => 'Ekleme başarısız!'], 201);
    }
});
Route::post('/update-service', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $serviceData = (array) json_decode($request->getContent());
	$serviceId = $serviceData['id'];
    if (empty($serviceData) || !isset($serviceId)) {
        return response()->json(['message' => 'Eksik veri'], 400);
    }
    $updated = DB::table('services')->where('id', $serviceId)->update($serviceData);
    $response = DB::table('services')->where('id', $serviceId)->first();
    return response()->json($response, 200);
});
Route::post('/delete-service', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $saleItemsExists = DB::table('sale_items')->where('service_id', $request->service_id)->exists();
    $reservationItemsExists = DB::table('reservations_items')->where('service_id', $request->service_id)->exists();
    if ($saleItemsExists || $reservationItemsExists) {
        return response()->json([
			'message' => 'Hizmet kaydını silemezsiniz, bu müşteri ile ilişkili satış veya rezervasyon bulunuyor!'
		], 400);
    }
    $deleted = DB::table('services')->where('id', $request->service_id)->delete();
    if ($deleted) {
        return response()->json(['message' => 'Silme başarılı!'], 200);
    } else {
        return response()->json(['message' => 'Silme başarısız!'], 201);
    }
});
/*********************************************************************************************************************************
														    DEPOLAR
*********************************************************************************************************************************/
Route::get('/warehouses', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $warehouses = DB::table('warehouses')->get();
    return response()->json(warehouses, 200);
});
/*********************************************************************************************************************************
														UZMANLIK ALANLARI
*********************************************************************************************************************************/
Route::get('/specializations', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $specializations = DB::table('specializations')->get();
    return response()->json($specializations, 200);
});
Route::post('/add-speciality', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $specialityData = (array) json_decode($request->getContent());
    $inserted = DB::table('specializations')->insertGetId($specialityData);
    if ($inserted) {
        $response = DB::table('specializations')->where('id', $inserted)->first();
        return response()->json($response, 200);
    } else {
        return response()->json(['message' => 'Ekleme başarısız!'], 201);
    }
});
Route::post('/update-speciality', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $specialityData = (array) json_decode($request->getContent());
	$specialityId = $specialityData['id'];
	if (empty($specialityData) || !isset($specialityId)) {
		return response()->json(['message' => 'Eksik veri'], 400);
	}
	$updated = DB::table('specializations')->where('id', $specialityId)->update($specialityData);
    if ($updated) {
        $response = DB::table('specializations')->where('id', $specialityId)->first();
        return response()->json($response, 200);
    } else {
        return response()->json(['message' => 'Güncelleme başarısız!'], 201);
    }
});
Route::post('/delete-speciality', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$employeeExists = DB::table('employees')->where('skills', $request->speciality_id)->exists();
	if ($employeeExists) {
		return response()->json(['message' => 'Uzmanlık alanını silemezsiniz!'], 400);
	}
	$deleted = DB::table('specializations')->where('id', $request->speciality_id)->delete();
	if ($deleted) {
		return response()->json(['message' => 'Silme başarılı!'], 200);
	} else {
		return response()->json(['message' => 'Silme başarısız!'], 201);
	}
});
/*********************************************************************************************************************************
															GRUPLAR
*********************************************************************************************************************************/
Route::get('/groups', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $groups = DB::table('groups')->get();
    $groupsWithPermissions = $groups->map(function ($group) {
        $permissions = DB::table('group_permission')
            ->join('permissions', 'group_permission.permission_id', '=', 'permissions.id')
            ->where('group_permission.group_id', $group->id)
            ->pluck('permissions.name');
        $group->permissions = $permissions;
        return $group;
    });
    return response()->json($groupsWithPermissions, 200);
});
Route::post('/add-group', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $groupData = (array) json_decode($request->getContent());
    $insertData = [
        'name' => $groupData['name'] ?? null,
        'description' => $groupData['description'] ?? null,
    ];
    $groupId = DB::table('groups')->insertGetId($insertData);
    if ($groupId) {
		$permissionsData = $groupData['permissions'] ?? [];
		$permissions = [];
        foreach ($permissionsData as $permissionName) {
            $permission = DB::table('permissions')->where('name', 'LIKE', "%{$permissionName}%")->first();
            if ($permission) {
                $permissions[] = ['group_id' => $groupId, 'permission_id' => $permission->id];
            }
        }
        if ($permissions) {
            DB::table('group_permission')->insert($permissions);
        }
		$group = DB::table('groups')->where('id', $groupId)->first();
		$permissions = DB::table('group_permission')
			->join('permissions', 'group_permission.permission_id', '=', 'permissions.id')
			->where('group_permission.group_id', $group->id)
			->pluck('permissions.name');
		$group->permissions = $permissions;
		return response()->json($group, 200);
    }
    return response()->json(['message' => 'Ekleme başarısız!'], 201);
});
Route::post('/update-group', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $groupData = (array) json_decode($request->getContent());
    $groupId = $groupData['id'] ?? null;
    $permissionsData = $groupData['permissions'] ?? [];
    if (empty($groupData) || !isset($groupId)) {
        return response()->json(['message' => 'Eksik veri'], 400);
    }
	$updated = DB::table('groups')->where('id', $groupId)->update([
		'name' 		  => $groupData['name'] ?? null,
		'description' => $groupData['description'] ?? null,
		'updated_at'  => DB::raw('CURRENT_TIMESTAMP'),
	]);
    if ($updated) {
        if (!empty($permissionsData)) {
            DB::table('group_permission')->where('group_id', $groupId)->delete();
            $permissions = [];
            foreach ($permissionsData as $permissionName) {
                $permission = DB::table('permissions')->where('name', 'LIKE', "%{$permissionName}%")->first();
                if ($permission) {
                    $permissions[] = ['group_id' => $groupId, 'permission_id' => $permission->id];
                }
            }
            if (!empty($permissions)) {
                DB::table('group_permission')->insert($permissions);
            }
        }
        $group = DB::table('groups')->where('id', $groupId)->first();
        $permissions = DB::table('group_permission')
            ->join('permissions', 'group_permission.permission_id', '=', 'permissions.id')
            ->where('group_permission.group_id', $group->id)
            ->pluck('permissions.name');
        $group->permissions = $permissions;
        return response()->json($group, 200);
    }
    return response()->json(['message' => 'Güncelleme başarısız!'], 201);
});
Route::post('/delete-group', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$groupId = $request->group_id;
	$userExists = DB::table('users')->where('group_id', $groupId)->exists();
	if ($userExists) {
		return response()->json(['message' => 'Grubu silemezsiniz!'], 400);
	}
	try {
		DB::transaction(function () use ($groupId) {
			DB::table('group_permission')->where('group_id', $groupId)->delete();
			DB::table('groups')->where('id', $groupId)->delete();
		});
		return response()->json(['message' => 'Silme başarılı!'], 200);
	} catch (\Exception $e) {
		return response()->json(['message' => 'Silme başarısız!'], 201);
	}
});
/*********************************************************************************************************************************
														  KATEGORİLER
*********************************************************************************************************************************/
Route::get('/categories', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
	}
	$search = $request->query('search');
	$stockType = $request->query('stock_type');
	$categoriesQuery = DB::table('categories')
		->when(!empty($search), function($query) use ($search) {
			return $query->where(function($value) use ($search) {
				$value->where('name', 'like', "%{$search}%")
					->orWhere('description', 'like', "%{$search}%");
			});
		});
	if ($stockType >= 0) {
		$categoriesQuery->where('stock_type', '=', $stockType);
	}
	$categories = $categoriesQuery->orderBy('id', 'desc')->get();
	$categories->each(function ($category) {
		if (isset($category->image) && !empty($category->image)) {
			$category->image = CATEGORY_URL . $category->image;
		}
	});
    return response()->json($categories, 200);
});
Route::post('/add-category', function (Request $request) {
	if (!apikeyVerify($request)) {
		return response()->json(['message' => 'Unauthorized'], 401);
	}
	$categoryData = (array) json_decode($request->getContent());
	if ($categoryData['image'] != '0') {
		$imageName = createCategoryImage($categoryData);
	}
	$categoryId = DB::table('categories')->insertGetId([
		'name' => $categoryData['name'],
		'description' => $categoryData['description'],
		'image' => $imageName ?? null,
		'stock_type' => $categoryData['stock_type'],
		'is_active' => $categoryData['is_active'],
	]);
	if ($categoryId) {
		$response = DB::table('categories')->where('id', $categoryId)->first();
		if (isset($response->image) || !empty($response->image)) {
			$response->image = CATEGORY_URL . $response->image;
		}
		return response()->json($response, 200);
	} else {
		return response()->json(['message' => 'Ekleme başarısız!'], 201);
	}
});
Route::post('/update-category', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $categoryData = (array) json_decode($request->getContent());
	$categoryId = $categoryData['id'];
	if (empty($categoryData) || !isset($categoryId)) {
		return response()->json(['message' => 'Eksik veri'], 400);
	}
	if ($categoryData['image'] != '0') {
		$imageName = createCategoryImage($categoryData);
		$categoryData['image'] = $imageName ?: null;
	} else {
		unset($categoryData['image']);
	}
	$updated = DB::table('categories')->where('id', $categoryId)->update($categoryData);
	$response = DB::table('categories')->where('id', $categoryId)->first();
	if (isset($response->image) || !empty($response->image)) {
		$response->image = CATEGORY_URL . $response->image;
	}
	return response()->json($response, 200);
});
Route::post('/delete-category', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$reservationsExists = DB::table('services')->where('category_id', $request->category_id)->exists();
	if ($reservationsExists) {
		return response()->json(['message' => 'Kategoriyi silemezsiniz!'], 400);
	}
	$imageName = DB::table('categories')->where('id', $request->category_id)->value('image');
	if ($imageName) {
		deleteImage(CATEGORY_URL, $imageName); 
	}
	$deleted = DB::table('categories')->where('id', $request->category_id)->delete();
	if ($deleted) {
		return response()->json(['message' => 'Silme başarılı!'], 200);
	} else {
		return response()->json(['message' => 'Silme başarısız!'], 201);
	}
});
function createCategoryImage($data) {
	if ($data['image'] != '0') {
		$imageName = DB::table('categories')->where('id', $data['id'])->value('image');
		if ($imageName) {
			deleteImage(CATEGORY_URL, $imageName); 
		}
		$imageName = createImage(CATEGORY_URL, $data['image']);
		return $imageName;
	}
	return null;
}
/*********************************************************************************************************************************
															ODALAR
*********************************************************************************************************************************/
Route::get('/tables', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $tables = DB::table('tables')
		->leftJoin('employees', 'employees.id', '=', 'tables.employee_id')
		->select('tables.*', 'employees.name as employee_name')
		->get();
    return response()->json($tables, 200);
});
Route::post('/add-table', function (Request $request) {
	if (!apikeyVerify($request)) {
		return response()->json(['message' => 'Unauthorized'], 401);
	}
	$data = (array) json_decode($request->getContent());
	$tableId = DB::table('tables')->insertGetId([
		'name' => $data['name'],
		'capacity' => $data['capacity'],
		'status' => $data['status'],
	]);
	if ($tableId) {
		$response = DB::table('tables')->where('id', $tableId)->first();
		return response()->json($response, 200);
	} else {
		return response()->json(['message' => 'Ekleme başarısız!'], 201);
	}
});
Route::post('/update-table', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $data = (array) json_decode($request->getContent());
	$tableId = $data['id'];
	if (empty($data) || !isset($tableId)) {
		return response()->json(['message' => 'Eksik veri'], 400);
    }
    $updated = DB::table('tables')->where('id', $tableId)->update($data);
    $response = DB::table('tables')->where('id', $tableId)->first();
    return response()->json($response, 200);
});
Route::post('/delete-table', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$reservationsExists = DB::table('reservations')->where('table_id', $request->table_id)->exists();
	if ($reservationsExists) {
		return response()->json(['message' => 'Odayı silemezsiniz, bu oda ile ilişkili randevu bulunuyor!'], 400);
	}
	$deleted = DB::table('tables')->where('id', $request->table_id)->delete();
	if ($deleted) {
		return response()->json(['message' => 'Silme başarılı!'], 200);
	} else {
		return response()->json(['message' => 'Silme başarısız!'], 201);
	}
});
/*********************************************************************************************************************************
														REZERVASYONLAR
*********************************************************************************************************************************/
Route::get('/reservations', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$search = $request->query('search');
	$date = $request->query('date');
	$filterDate = $request->query('filter_date');
	$customerId = $request->query('customer_id');
	$employeeId = $request->query('employee_id');
	$tableId = $request->query('table_id');
	$status = $request->query('status');
	// Odalar
    $tables = DB::table('tables')->get();
	// Çalışanlan
    $employees = DB::table('employees')->get();
    // Randevular
	$reservationsQuery = DB::table('reservations')
		->leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
		->leftJoin('employees', 'reservations.employee_id', '=', 'employees.id')
		->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
		->leftJoin(DB::raw('
			(SELECT reservation_id, CAST(SUM(payment_amount) AS DECIMAL(10,2)) AS paid_price 
			FROM payments GROUP BY reservation_id) AS payments_sum'), 'payments_sum.reservation_id', '=', 'reservations.id')
		->select(
			'reservations.*',
			DB::raw('CONCAT(customers.first_name, " ", customers.last_name) AS customer_name'),
			'employees.name AS employee_name',
			'employees.position AS employee_position',
			'tables.name AS table_name',
			'payments_sum.paid_price'
		);
	if (isset($search)) {
		$reservationsQuery->having('customer_name', 'LIKE', '%' . $search . '%');
	}
	if (isset($date) || strtotime($date)) {
		$reservationsQuery->whereDate('reservations.start_date', date('Y-m-d', strtotime($date)));
	}
	if (isset($filterDate) || strtotime($filterDate)) {
		$reservationsQuery->whereYear('reservations.start_date', date('Y', strtotime($filterDate)));
		$reservationsQuery->whereMonth('reservations.start_date', date('m', strtotime($filterDate)));
	}
	if (isset($customerId)) {
        $reservationsQuery->where('reservations.customer_id', $customerId);
    }
    if (isset($employeeId)) {
        $reservationsQuery->where('reservations.employee_id', $employeeId);
    }
    if (isset($tableId)) {
        $reservationsQuery->where('reservations.table_id', $tableId);
    }
	if (isset($status)) {
		$reservationsQuery->where('reservations.status', $status);
	}
    $reservations = $reservationsQuery->orderBy('start_date', 'desc')->get();
    // İşlemler
    foreach ($reservations as $reservation) {
        $reservation->services = DB::table('reservations_items')
            ->join('services', 'reservations_items.service_id', '=', 'services.id')
            ->where('reservations_items.reservation_id', $reservation->id)
            ->select('services.id', 'services.name', 'services.description', 'services.price', 'services.discount_price')
            ->get();
    }
    $response = [
        'tables' => $tables,
		'employees' => $employees,
        'reservations' => $reservations
    ];
    return response()->json($response, 200);
});
Route::get('/reservation-details', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $customers = DB::table('customers')
        ->select(
            'customers.id',
            DB::raw('CONCAT(customers.first_name, " ", customers.last_name) AS customer_name')
        )
        ->get();
	$tables = DB::table('tables')->get();
    $employees = DB::table('employees')->get();
	$services = DB::table('services')->get();
	$accounts = DB::table('accounts')->get();
	$paymentMethods = DB::table('payment_methods')->get();
	$response = [
		'customers' => $customers,
		'tables' => $tables,
		'employees' => $employees,
        'services' => $services,
		'accounts' => $accounts,
		'payment_methods' => $paymentMethods,
    ];
    return response()->json($response, 200);
});
Route::post('/add-reservation', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $customerId = $request->customer_id;
    if ($customerId == 0) {
        $customerId = DB::table('customers')->insertGetId([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ]);
    }
    if (!$customerId) {
        return response()->json(['status' => 'error', 'message' => 'Geçerli bir müşteri seçilmelidir.'], 400);
    }
    $menuItemId = json_decode($request->menu_item_id);
    $totalPrice = DB::table('services')->whereIn('id', $servicesId)->sum('total_price');
    $reservationId = DB::table('reservations')->insertGetId([
        'customer_id' => $customerId,
        'table_id' => $request->table_id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'employee_id' => $request->employee_id,
        'total_price' => $totalPrice,
    ]);
    if (!$reservationId) {
        return response()->json(['status' => 'error', 'message' => 'Rezervasyon kaydedilemedi.'], 201);
	}
	// Hizmetleri Ekle
    $services = DB::table('services')->whereIn('id', $servicesId)->get(['id', 'total_price']);
    foreach ($services as $service) {
        DB::table('reservations_items')->insert([
            'reservation_id' => $reservationId,
            'service_id' => $service->id,
            'price' => $service->total_price,
            'quantity' => 1
        ]);
    }
	// SMS Gönder
	sendSMSReservation($reservationId, 'booking_sms_message');
    return response()->json(['status' => 'success', 'message' => 'Rezervasyon başarıyla eklendi'], 200);
});
Route::post('/update-reservation', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $reservationId = $request->reservation_id;
    if (empty($reservationId)) {
        return response()->json(['status' => false, 'message' => 'Geçerli bir rezervasyon seçilmelidir.'], 402);
    }
    $menuItemId = json_decode($request->menu_item_id, true);
    if (empty($menuItemId) || !is_array($menuItemId)) {
        return response()->json(['status' => false, 'message' => 'Geçerli hizmetler seçilmelidir.'], 402);
    }
    $totalPrice = DB::table('menu_items')->whereIn('id', $menuItemId)->sum('price');
    DB::beginTransaction();
    try {
        $updated = DB::table('reservations')
            ->where('id', $reservationId)
            ->update([
                'table_id'    => $request->table_id,
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date,
                'employee_id' => $request->employee_id,
                'total_price' => $totalPrice,
				'updated_at'  => DB::raw('CURRENT_TIMESTAMP'),
            ]);
        if (!$updated) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Rezervasyon güncellenemedi.'], 403);
        }
        DB::table('reservations_items')->where('reservation_id', $reservationId)->delete();
        $services = DB::table('services')->whereIn('id', $servicesId)->get(['id', 'total_price']);
        foreach ($services as $service) {
            DB::table('reservations_items')->insert([
                'reservation_id' => $reservationId,
                'service_id' => $service->id,
                'price' => $service->total_price,
                'quantity' => 1
            ]);
        }
        DB::commit();
        return response()->json([
			'status'  => true, 
			'message' => 'Rezervasyon başarıyla güncellendi.'
		], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
			'status'  => false, 
			'message' => 'Hata:' . $e->getMessage()
		], 201);
    }
});
Route::post('/update-reservation-status', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$reservationData = (array) json_decode($request->getContent());
	DB::beginTransaction();
	try {
		if (empty($reservationData) || !isset($reservationData['id'])) {
			return response()->json(['message' => 'Eksik veri'], 400);
		}
		$reservationId = $reservationData['id'];
		$status = $reservationData['status'] ?? null;
		if ($status === 'cancelled' || $status === 'pending') {
			// Tüm İşlemleri Sil			
            $sale = DB::table('sales')->where('reservation_id', $reservationId)->first();
			if ($sale) {
                DB::table('sale_items')->where('sale_id', $sale->id)->delete();
            }
			DB::table('sales')->where('reservation_id', $reservationId)->delete();
			DB::table('transactions')->where('reservation_id', $reservationId)->delete();
			DB::table('payments')->where('reservation_id', $reservationId)->delete();
			DB::table('employee_commissions')->where('reservation_id', $reservationId)->delete();
			if ($status === 'cancelled') {
				// SMS Gönder
				sendSMSReservation($reservationId, 'delete_sms_message');
			}
		}
		$updated = DB::table('reservations')->where('id', $reservationId)->update($reservationData);
		$response = DB::table('reservations')->where('id', $reservationId)->first();;
		DB::commit();
		return response()->json($response, 200);
	} catch (\Exception $e) {
		DB::rollback();
		Log::error('Randevu güncelleme hatası', [
			'message' => $e->getMessage(),
			'line'    => $e->getLine(),
			'file'    => $e->getFile(),
		]);
		return response()->json(['message' => 'Randevu güncelleme başarısız!'], 201);
	}
});
Route::post('/delete-reservation', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $id = $request->reservation_id;
    $deleted = DB::transaction(function () use ($id) {
        DB::table('reservations_items')->where('reservation_id', $id)->delete();
        DB::table('sale_items')->whereIn('sale_id', function ($query) use ($id) {
            $query->select('id')->from('sales')->where('reservation_id', $id);
        })->delete();
        DB::table('sales')->where('reservation_id', $id)->delete();
        DB::table('transactions')->where('reservation_id', $id)->delete();
        DB::table('reservations')->where('id', $id)->delete();
        return true;
    });
    if ($deleted) {
        return response()->json(['message' => 'Silme başarılı!'], 200);
    } else {
        return response()->json(['message' => 'Silme başarısız!'], 201);
    }
});
/*********************************************************************************************************************************
													GÖSTERGE PANELİ
*********************************************************************************************************************************/
Route::get('/dashboard', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    // Tarihi al, yoksa bugünün tarihini kullan
	$now = getDateTimeZone();
	$filterDate = $request->query('date');
	$limit = $request->query('limit');
	if (!$filterDate || !strtotime($filterDate)) {
		$filterDate = getDateTimeZone('now', 'Y-m-d');
	}
    // Randevular
    $reservationsQuery = DB::table('reservations')
        ->leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
        ->leftJoin('employees', 'reservations.employee_id', '=', 'employees.id')
        ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
        ->select(
            'reservations.*',
            DB::raw('CONCAT(customers.first_name, " ", customers.last_name) AS customer_name'),
            'employees.name AS employee_name',
            'employees.position AS employee_position',
            'tables.name AS table_name'
        )
        ->whereDate('reservations.start_date', $filterDate)
		->where('reservations.start_date', '>', $now)
        ->where('reservations.status', 'pending')
        ->orderBy('reservations.start_date', 'asc');
	if ($limit > 0) {
		$reservationsQuery->limit($limit);
	}
	$reservations = $reservationsQuery->get();
    // İşlemler
    foreach ($reservations as $reservation) {
        // Kalan Süreyi Hesapla
		$now = new DateTime(getDateTimeZone('now'));
		$startDate = new DateTime(getDateTimeZone($reservation->start_date));
		if ($startDate > $now) {
            $interval = $startDate->diff($now);
            $reservation->time_left = [
                'years' => $interval->y,
                'months' => $interval->m,
                'days' => $interval->d,
                'hours' => $interval->h,
                'minutes' => $interval->i,
                'seconds' => $interval->s
            ];
        } else {
            $reservation->time_left = [
                'years' => 0,
                'months' => 0,
                'days' => 0,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0
            ];
        }
        // Hizmetler
        $reservation->services = DB::table('reservations_items')
            ->join('services', 'reservations_items.service_id', '=', 'services.id')
            ->where('reservations_items.reservation_id', $reservation->id)
            ->select('services.id', 'services.name', 'services.description', 'services.price', 'services.discount_price')
            ->get();
    }
    // Belirtilen tarih için toplam randevu sayıları
    $totals = DB::table('reservations')
        ->select(DB::raw('
        sum(status = "pending") as pending_count,
        sum(status = "started") as started_count,
        sum(status = "completed") as completed_count,
        sum(status = "cancelled") as cancelled_count'))
        ->whereDate('start_date', $filterDate)
        ->first();
    $response = [
        'filter_date' => $filterDate,
        'reservations' => $reservations,
        'totals' => $totals,
    ];
    return response()->json($response, 200);
});
/*********************************************************************************************************************************
													SATIŞ & ÖDEME İŞLEMLERİ
*********************************************************************************************************************************/
Route::post('/add-payment', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$paymentData = (array) json_decode($request->getContent());
	DB::beginTransaction();
	try {
		$reservationId = $paymentData['reservation_id'];
		$customerId = $paymentData['customer_id'];
		$employeeId = $paymentData['employee_id'];
		$paid = $paymentData['paid'] ?? 0;
		// Müşteri ID'sinden Müşteri Verilerini Al
		$customer = DB::table('customers')->find($customerId);
		$now = getDateTimeZone();
		// Satış Ekle
		$saleData = [
			'date'             => $now,
			'reservation_id'   => $reservationId,
			'customer_id'      => $customerId,
			'grand_total'      => $paymentData['grand_total'],
			'paid'             => $paid,
			'total_discount'   => $paymentData['discount_amount'],
			'product_discount' => $paymentData['discount_amount'],
			'total_tax'        => 0,
			'total'            => $paymentData['total'],
			'discount_type'    => $paymentData['discount_type'],
			'discount_percent' => $paymentData['discount_percent'],
			'payment_method_id'   => $paymentData['payment_method_id'],
			'payment_method'   => $paymentData['payment_method'],
			'customer'         => "{$customer->first_name} {$customer->last_name}",
			'seller_id'        => $employeeId,
			'invoice_status'   => 1,
			'sale_status' 	   => 1,
		];
		$existingSale = DB::table('sales')->where('reservation_id', $reservationId)->first();
		if (!$existingSale) {
			$saleId = DB::table('sales')->insertGetId($saleData);
		} else {
			$saleId = $existingSale->id;
			DB::table('sales')->where('reservation_id', $reservationId)->update($saleData);
		}
		$saleItems = DB::table('reservations_items')
			->where('reservation_id', $reservationId)
			->join('services', 'services.id', '=', 'reservations_items.service_id')
			->select(
				'services.id as service_id',
				'services.name as product_name',
				'services.price as net_unit_price',
				'services.total_price as unit_price',
				'services.tax_id',
				'services.tax_rate')
			->get();
		foreach ($saleItems as $item) {
			$itemTax  = ($item->net_unit_price * $item->tax_rate) / 100;
			$subtotal = $item->net_unit_price + $itemTax;
			DB::table('sale_items')->insert([
				'sale_id'        => $saleId,
				'service_id'     => $item->service_id,
				'product_name'   => $item->product_name,
				'net_unit_price' => $item->net_unit_price,
				'unit_price'     => $item->unit_price,
				'item_tax'       => $itemTax,
				'tax_id'         => $item->tax_id,
				'tax_rate'       => $item->tax_rate,
				'discount'       => 0,
				'subtotal'       => $subtotal,
				'quantity'       => 1,
				'created_at'     => $now,
			]);
		}
		// Ödeme Durumunu Güncelle
		$paymentStatus = updatePaymentStatus($reservationId);
		if ($paid > 0) {
			// Ödeme Ekle
			$paymentId = DB::table('payments')->insertGetId([
				'reservation_id' => $reservationId,
				'customer_id'    => $customerId,
				'payment_note'   => 'Otomatik oluşturulan ödeme',
				'payment_method' => $paymentData['payment_method'],
				'payment_amount' => $paid,
				'payment_status' => $paymentStatus,
				'invoice_status' => 1,
				'created_at'     => $now,
			]);
			// İşlemlere Ekle
			DB::table('transactions')->insert([
				'account_id'     => 1,
				'reservation_id' => $reservationId,
				'customer_id'    => $customerId,
				'payment_id'     => $paymentId,
				'sale_id'        => $saleId,
				'type'           => 'Gelir',
				'amount'         => $paid,
				'description'    => generateTransactionDescription($customer, $paid),
				'created_at'     => $now,
			]);
		}
		DB::commit();
		return response()->json(['message' => 'Ödeme ekleme başarılı!'], 200);
	} catch (\Exception $e) {
		DB::rollback();
		Log::error('Ödeme ekleme hatası', [
			'message' => $e->getMessage(),
			'line'    => $e->getLine(),
			'file'    => $e->getFile(),
		]);
		return response()->json(['message' => 'Ödeme ekleme başarısız!'], 201);
	}
});
Route::get('/payment-methods', function (Request $request) {
	if (!apikeyVerify($request)) {
		return response()->json(['message' => 'Unauthorized'], 401);
	}
	$paymentMethods = DB::table('payment_methods')->get();
	return response()->json($paymentMethods, 200);
});
function updatePaymentStatus($reservationId) {
	$sale = DB::table('sales')->where('reservation_id', $reservationId)->first();
    $totalPrice = floatval($sale->grand_total); // Borç
    $totalPaidAmount = floatval($sale->paid); // Müşterinin ödediği
    if ($totalPaidAmount > $totalPrice) {
        $paymentStatus = 3;    // Fazla ödeme
    } elseif ($totalPaidAmount == $totalPrice) {
        $paymentStatus = 1;    // Tam ödeme
    } else {
        $paymentStatus = 4;    // Eksik ödeme
    }
	$status = in_array($paymentStatus, [1, 3, 4]) ? 'completed' : 'pending';
	if ($status == 'completed') {
		addCommissionToEmployee($reservationId);
	}
	if (in_array($paymentStatus, [1, 3])) {
		addParapuanToCustomer($sale->customer_id);
	}
	DB::table('reservations')
			->where('id', $reservationId)
			->update([
				'discount_type'    => $sale->discount_type,
				'discount_percent' => $sale->discount_percent,
				'discount'         => $sale->total_discount,
				'status'		   => $status,
			]);
	DB::table('sales')
			->where('reservation_id', $reservationId)
			->update(['sale_status' => $paymentStatus]);
    return $paymentStatus;
}
function deletePayment($id) {
	DB::beginTransaction();
	try {
		$payment = DB::table('payments')->find($id);
		$sale = DB::table('sales')->find($payment->reservation_id);
		if (!$payment) {
			return [
				'status' => 401,
				'message' => 'Ödeme bulunamadı',
			];
		}
		DB::table('customers')->where('id', $payment->customer_id)->decrement('total_spent', $payment->payment_amount);
		DB::table('transactions')->where('payment_id', $id)->delete();
		DB::table('payments')->where('id', $id)->delete();
		updatePaymentStatus($payment->reservation_id);
		DB::commit();
		return [
            'status' => 200,
            'message' => 'Ödeme silindi',
        ];
	} catch (\Exception $e) {
		DB::rollback();
		return [
			'status' => 201,
			'message' => 'Ödeme silinirken bir hata oluştu'
		];
	}
}
function addParapuanToCustomer($customerId) {
	$customer = DB::table('customers')->where('id', $customerId)->first();
	$settings = DB::table('settings')->where('id', 1)->first();
	$isParapuan = $settings->parapuan_system_enabled ?? 0;
	if ($customer && $isParapuan) {
		$settingsParapuan = $settings->parapuan ?? 0;
		$newParapuan = ($customer->parapuan ?? 0) + $settingsParapuan;
		DB::table('customers')->where('id', $customerId)->update(['parapuan' => $newParapuan]);
	}
}
function addCommissionToEmployee($reservationId) {
	$now = getDateTimeZone();
    $reservation = DB::table('reservations')->find($reservationId);
    if (!$reservation) {
        return [
            'status' => 401,
            'message' => 'Rezervasyon bulunamadı'
        ];
	}
	try {
        $employeeId = $reservation->employee_id;
        $services = DB::table('reservations_items')
            ->where('reservation_id', $reservationId)
            ->join('services', 'services.id', '=', 'reservations_items.service_id')
            ->select('services.id as service_id', 'services.is_stock', 'reservations_items.price')
            ->get();
        foreach ($services as $service) {
            if ($service->is_stock == 1) {
                $commissionRate = DB::table('employee_service_commissions')
                    ->where('service_id', $service->service_id)
                    ->value('commission_rate');
                if ($commissionRate === null) continue;
                $commissionAmount = ($commissionRate / 100) * $service->price;
                DB::table('employee_commissions')->insert([
                    'reservation_id' => $reservationId,
                    'employee_id' => $employeeId,
                    'service_id' => $service->service_id,
                    'amount' => $commissionAmount,
                    'status' => 0,
                    'created_at' => $now,
                ]);
            }
        }
        return [
            'reservation_id' => $reservationId,
            'employee_id' => $employeeId,
            'created_at' => $now,
        ];
    } catch (\Exception $e) {
        return [
            'status' => 201,
            'message' => 'Toplam komisyon kaydı oluşturulamadı'
        ];
    }
}
function addCommissionToSeller($saleId) {
	$now = getDateTimeZone();
    $commissionPercentage = DB::table('settings')->where('id', 1)->value('employee_commission');
    $sale = DB::table('sales')->find($saleId);
    if (!$sale) {
        return [
            'status' => 401,
            'message' => 'Satış bulunamadı'
        ];
    }
    $employeeId = $sale->seller_id ?? 0;
    $totalPrice = $sale->grand_total ?? 0;
    $totalCommission = $totalPrice * $commissionPercentage / 100;
    try {
        DB::table('employee_commissions')->insert([
            'sale_id' => $saleId,
			'employee_id' => $employeeId,
			'amount' => $totalCommission,
			'created_at' => $now,
        ]);
        return [
            'sale_id' => $saleId,
            'employee_id' => $employeeId,
            'amount' => $totalCommission,
			'created_at' => $now,
        ];
    } catch (\Exception $e) {
        return [
            'status' => 403,
            'message' => 'Toplam komisyon kaydı oluşturulamadı'
        ];
    }
}
function generateTransactionDescription($customer, $amount) {
	$now = getDateTimeZone();
	return "{$customer->first_name} {$customer->last_name} müşterisi tarafından $amount TL ödeme yapıldı. İşlem Tarihi: $now";
}
/*********************************************************************************************************************************
														GERİ BİLDİRİMLER
*********************************************************************************************************************************/
Route::get('/feedbacks', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $search = $request->query('search');
    $feedbacksQuery = DB::table('feedback')
        ->select(
            'feedback.*',
            DB::raw('CONCAT(customers.first_name, " ", customers.last_name) AS customer_name'))
        ->join('customers', 'customers.id', '=', 'feedback.customer_id');
    if (isset($search)) {
        $feedbacksQuery->where(function ($query) use ($search) {
            $query->where(DB::raw('CONCAT(customers.first_name, " ", customers.last_name)'), 'LIKE', '%' . $search . '%')
                  ->orWhere('feedback.feedback', 'LIKE', '%' . $search . '%');
        });
    }
    $feedbacks = $feedbacksQuery->orderBy('feedback.id', 'desc')->get();
    return response()->json($feedbacks, 200);
});
/*********************************************************************************************************************************
														KAMPANYALAR
*********************************************************************************************************************************/
Route::get('/campaigns', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $campaigns = DB::table('campaigns')->get();
    return response()->json($campaigns, 200);
});
Route::post('/add-campaign', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$data = (array) json_decode($request->getContent());
	DB::beginTransaction();
	try {
		$now = getDateTimeZone('now', 'Y-m-d');
		$campaignData = [
            'date' => $now,
            'campaign_type' => $data['campaign_type'],
            'campaign_name' => $data['campaign_name'],
            'campaign_details' => $data['campaign_details'],
            'send_type' => 'sms',
        ];
        $campaignId = DB::table('campaigns')->insertGetId($campaignData);
		$smsResult = sendCampaignSMS($data);
		//Log::info($smsResult);
        DB::commit();
        $response = DB::table('campaigns')->where('id', $campaignId)->first();
        return response()->json($response, 200);
	} catch (\Exception $e) {
		DB::rollBack();
		return response()->json(['message' => 'SMS gönderilirken bir hata oluştu!'], 402);
	}
});
Route::post('/update-campaign', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $campaignData = (array) json_decode($request->getContent());
    if (empty($campaignData) || !isset($campaignData['id'])) {
        return response()->json(['message' => 'Eksik veri'], 400);
    }
    $campaignId = $campaignData['id'];
    $updated = DB::table('campaigns')->where('id', $campaignId)->update($campaignData);
    $response = DB::table('campaigns')->where('id', $campaignId)->first();
    return response()->json($response, 200);
});
Route::post('/delete-campaign', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$deleted = DB::table('campaigns')->where('id', $request->campaign_id)->delete();
	if ($deleted) {
		return response()->json(['message' => 'Silme başarılı!'], 200);
	} else {
		return response()->json(['message' => 'Silme başarısız!'], 201);
	}
});
function sendCampaignSMS($data) {
	$message = $data['campaign_details'];
	$customers = getCampaignCustomers($data);
	if ($customers->isEmpty()) {
		return [
			'success' => false,
			'message' => 'Gönderilecek müşteri bulunamadı',
			'sent_count' => 0
		];
	}
	$sentCount = 0;
	$failedCount = 0;
	foreach ($customers as $customer) {
		try {
			sendSMSMessage($customer->phone, $message, 'bulk_sms');
			$sentCount++;
		} catch (\Exception $e) {
			$failedCount++;
		}
	}
	$result = [
		'success' => $sentCount > 0,
		'sent_count' => $sentCount,
		'failed_count' => $failedCount,
		'total_customers' => $customers->count(),
		'message' => "Toplu SMS: {$sentCount} başarılı, {$failedCount} başarısız"
	];
	return $result;
}
function getCampaignCustomers($data) {
    $customerType = $data['campaign_type'] ?? null;
    $customers = $data['customers'] ?? [];
    if ($customerType === 'all') {
        return DB::table('customers')
            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'phone')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();
    }
    if ($customerType === 'specific' && is_array($customers) && count($customers) > 0) {
        return DB::table('customers')
            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'phone')
            ->whereIn('phone', $customers)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();
    }
    if ($customerType === 'vip') {
        return DB::table('customers')
            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'phone')
            ->where('is_vip', 1)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();
    }
    return collect();
}
/*********************************************************************************************************************************
														MÜŞTERİ MİZAN RAPORU
*********************************************************************************************************************************/
Route::get('/customers/{customerId}/mizan', function (Request $request, $customerId) {
    // Geçici olarak API key kontrolü devre dışı
    // if (!apikeyVerify($request)) {
    //     return response()->json(['message' => 'Unauthorized'], 401);
    // }
    
    $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->query('end_date', now()->format('Y-m-d'));
    
    // Müşteri bilgilerini getir
    $customer = DB::table('customers')->where('id', $customerId)->first();
    if (!$customer) {
        return response()->json(['message' => 'Müşteri bulunamadı'], 404);
    }
    
    // Müşteri hesap hareketlerini getir
    $transactions = DB::table('customers_account_transactions')
        ->where('customer_id', $customerId)
        ->whereBetween('date', [$startDate, $endDate])
        ->orderBy('date', 'asc')
        ->get();
    
    // Basit mizan hesaplaması
    $totalDebit = 0;
    $totalCredit = 0;
    
    // Hareketleri işle
    foreach ($transactions as $transaction) {
        if ($transaction->type === 'Gelir') {
            $totalCredit += $transaction->amount;
        } else {
            $totalDebit += $transaction->amount;
        }
    }
    
    // Mizan hesapları oluştur
    $accounts = [
        [
            'code' => '100',
            'name' => 'Kasa',
            'debit_balance' => $totalCredit,
            'credit_balance' => 0,
            'net_balance' => $totalCredit
        ],
        [
            'code' => '120',
            'name' => 'Müşteri Cari Hesap (' . ($customer->title ?? 'Müşteri') . ')',
            'debit_balance' => 0,
            'credit_balance' => $totalCredit,
            'net_balance' => -$totalCredit
        ],
        [
            'code' => '600',
            'name' => 'Satış Gelirleri',
            'debit_balance' => 0,
            'credit_balance' => $totalCredit,
            'net_balance' => $totalCredit
        ],
        [
            'code' => '700',
            'name' => 'Satış Giderleri',
            'debit_balance' => $totalDebit,
            'credit_balance' => 0,
            'net_balance' => -$totalDebit
        ]
    ];
    
    $netBalance = $totalCredit - $totalDebit;
    
    return response()->json([
        'customer' => $customer,
        'period' => [
            'start_date' => $startDate,
            'end_date' => $endDate
        ],
        'accounts' => $accounts,
        'totals' => [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
            'net' => $netBalance
        ]
    ], 200);
});

Route::get('/customers/{customerId}/mizan/pdf', function (Request $request, $customerId) {
    // Geçici olarak API key kontrolü devre dışı
    // if (!apikeyVerify($request)) {
    //     return response()->json(['message' => 'Unauthorized'], 401);
    // }
    
    // PDF oluşturma işlemi burada yapılacak
    // Şimdilik JSON response döndürüyoruz
    return response()->json([
        'message' => 'PDF oluşturma özelliği yakında eklenecek',
        'customer_id' => $customerId,
        'start_date' => $request->query('start_date'),
        'end_date' => $request->query('end_date')
    ], 200);
});

/*********************************************************************************************************************************
														RAPORLAR
*********************************************************************************************************************************/
Route::get('/reports', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');
    // Gelirler
    $salesQuery = DB::table('sales')
        ->select(
            'sales.customer',
            DB::raw('count(sales.id) as count'),
            DB::raw('sum(sales.paid) as paid'))
        ->groupBy('sales.customer');
    if ($startDate) {
        $salesQuery->where('date', '>=', $startDate);
    }
    if ($endDate) {
        $salesQuery->where('date', '<=', $endDate);
    }
    $sales = $salesQuery->get();
    // Giderler
    $expensesQuery = DB::table('expenses')
        ->select(
            'expenses.expense_type_id',
            'expense_types.name as expense_type_name',
            DB::raw('count(expenses.id) as count'),
            DB::raw('sum(expenses.total) as total'))
        ->leftJoin('expense_types', 'expense_types.id', '=', 'expenses.expense_type_id')
        ->groupBy('expenses.expense_type_id', 'expense_types.name');
    if ($startDate) {
        $expensesQuery->where('date', '>=', $startDate);
    }
    if ($endDate) {
        $expensesQuery->where('date', '<=', $endDate);
    }
    $expenses = $expensesQuery->get();
    // Toplam Gelir Hesaplaması
    $totalIncomeQuery = DB::table('sales');
    if ($startDate) {
        $totalIncomeQuery->where('date', '>=', $startDate);
    }
    if ($endDate) {
        $totalIncomeQuery->where('date', '<=', $endDate);
    }
    $totalIncome = $totalIncomeQuery->sum('paid') ?? 0;
    // Toplam Gider Hesaplaması
    $totalExpenseQuery = DB::table('expenses');
    if ($startDate) {
        $totalExpenseQuery->where('date', '>=', $startDate);
    }
    if ($endDate) {
        $totalExpenseQuery->where('date', '<=', $endDate);
    }
    $totalExpense = $totalExpenseQuery->sum('total') ?? 0;
    // Toplam Fiyat ve Bakiye Hesaplamaları
    $totalPrice = $totalIncome + $totalExpense; // Toplam fiyat (gelir + gider)
    $totalBalance = $totalIncome - $totalExpense; // Toplam bakiye (gelir - gider)
    $response = [
        'total_income' => $totalIncome,
        'total_expense' => $totalExpense,
        'total_price' => $totalPrice,
        'total_balance' => $totalBalance,
        'sales' => $sales,
        'expenses' => $expenses,
    ];
    return response()->json($response, 200);
});
/*********************************************************************************************************************************
													PAKET İŞLEMLERİ
*********************************************************************************************************************************/
Route::get('/package-features', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $response = DB::table('package_features')->first();
    return response()->json($response, 200);
});
/*********************************************************************************************************************************
														AYARLAR
*********************************************************************************************************************************/
Route::get('/settings', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $response = DB::table('settings')->first();
	if (isset($response->logo) && !empty($response->logo)) {
		$response->logo = IMAGE_URL . $response->logo;
	}
	$currencies = DB::table('currencies')->orderBy('id', 'asc')->get();
	$response->currencies = $currencies;
    return response()->json($response, 200);
});
Route::post('/update-settings', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $settingsData = json_decode($request->getContent(), true);
    if (empty($settingsData)) {
        return response()->json(['message' => 'Gerekli veriler bulunamadı.'], 400);
    }
    $updated = DB::table('settings')->where('id', 1)->update($settingsData);
    $response = DB::table('settings')->first();
	$currencies = DB::table('currencies')->orderBy('id', 'asc')->get();
	$response->currencies = $currencies;
    return response()->json($response, 200);
});
/*********************************************************************************************************************************
													ARAMA KAYDI İŞLEMLERİ
*********************************************************************************************************************************/
Route::get('/call-logs', function (Request $request) {
	global $userId;
	if (!apikeyVerify($request)) {
		return response()->json(['message' => 'Unauthorized'], 401);
	}
	$search = $request->query('search');
	$callType = $request->query('call_type');
	$callLogsQuery = DB::table('call_logs')
		->leftJoin('users', 'users.id', '=', 'call_logs.user_id')
		->leftJoin('customers', function($join) {
			$settings = DB::table('settings')->first();
			$numberLength = $settings->number_length ?? 10;
			$join->on(DB::raw('SUBSTRING(REGEXP_REPLACE(call_logs.phone, \'[^0-9]\', \'\'), -'.$numberLength.')'), 
					  '=', 
					  DB::raw('SUBSTRING(REGEXP_REPLACE(customers.phone, \'[^0-9]\', \'\'), -'.$numberLength.')'));
		})
		->select(
			'call_logs.*', 
			'users.name as name', 
			'users.username as username',
			'customers.id as customer_id',
			DB::raw('CONCAT(customers.first_name, " ", customers.last_name) as customer_name'))
		->when(!empty($search), function($query) use ($search) {
			return $query->where(function($value) use ($search) {
				$value->where('users.name', 'like', "%{$search}%")
					->orWhere('call_logs.phone', 'like', "%{$search}%")
					->orWhere(DB::raw('CONCAT(customers.first_name, " ", customers.last_name)'), 'like', "%{$search}%");
			});
		})
		->where('call_logs.user_id', $userId);
	if ($callType > 0) {
		$callLogsQuery->where('call_logs.call_type', '=', $callType);
	}
	$callLogs = $callLogsQuery->orderBy('call_logs.id', 'desc')->get();
	return response()->json($callLogs, 200);
});
Route::get('/caller-id', function (Request $request) {
	global $userId; 
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$phone = $request->query('phone');
	$callType = $request->query('call_type') ?? 0;
	$phoneNumber = phoneSplit($phone);
	if ($phoneNumber) {
		$response = DB::table('customers')
			->leftJoin('reservations', 'customers.id', '=', 'reservations.customer_id')
			->leftJoin('employees', 'reservations.employee_id', '=', 'employees.id')
			->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
			->select('customers.id', 'customers.address_line1', 'customers.address_line2', 'customers.state', 'customers.city',
				DB::raw('CONCAT(customers.first_name, " ", customers.last_name) AS name'),
				'reservations.start_date', 'reservations.end_date', 'reservations.status',
				'employees.name AS employee_name', 'employees.position AS employee_position',
                'tables.name AS table_name')
			->where('customers.phone', 'like', '%'.$phoneNumber.'%')
			->orderBy('reservations.id', 'desc')
			->first();
		if ($response !== null && isset($response->status)) {
			$response->status = getStatusName($response->status);
		}
		if ($callType > 0) {
			$callerLogId = DB::table('call_logs')->insertGetId([
				'phone' => $phone,
				'user_id' => $userId ?? 0,
				'call_type' => $callType,
			]);
		}
	}
    return response()->json($response, 200);
});
function getStatusName($status) {
	$translations = [
		'completed' => 'Tamamlandı',
		'started' => 'Başlatıldı',
		'cancelled' => 'İptal Edildi',
		'pending' => 'Beklemede',
		'unknown' => 'Bilinmiyor'
	];
	return $translations[$status] ?? $translations['unknown'];
}
function phoneSplit($phone) {
	$settings = DB::table('settings')->first();
	$numberLength = $settings->number_length ?? 10;
	$phoneNumber = substr(preg_replace('/[^0-9]/', '', $phone), -$numberLength);
	return $phoneNumber;
}
/*********************************************************************************************************************************
													SMS İŞLEMLERİ
*********************************************************************************************************************************/
Route::get('/sms-logs', function (Request $request) {
	if (!apikeyVerify($request)) {
		return response()->json(['message' => 'Unauthorized'], 401);
	}
	$smsLogs = DB::table('send_sms_code')
		->leftJoin('customers', function($join) {
			$settings = DB::table('settings')->first();
			$numberLength = $settings->number_length ?? 10;
			$join->on(
				DB::raw("SUBSTRING(REGEXP_REPLACE(send_sms_code.phone, '[^0-9]', ''), -$numberLength)"),
				'=',
				DB::raw("SUBSTRING(REGEXP_REPLACE(customers.phone, '[^0-9]', ''), -$numberLength)"));
		})
		->select(
			'send_sms_code.*',
			'customers.id as customer_id',
			DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) as customer_name"))
		->orderBy('send_sms_code.id', 'desc')
		->get();
	return response()->json($smsLogs, 200);
});
Route::get('/sms-settings', function (Request $request) {
	if (!apikeyVerify($request)) {
		return response()->json(['message' => 'Unauthorized'], 401);
	}
	$response = DB::table('settings')
		->select(
			'booking_sms_message_status', 'booking_sms_message',
			'reminder_sms_message_status', 'reminder_sms_message',
			'updater_sms_message_status', 'updater_sms_message',
			'delete_sms_message_status', 'delete_sms_message',
			'link_sms_message_status', 'link_sms_message')
		->first();
	return response()->json($response, 200);
});
Route::post('/update-sms-settings', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $smsSettingsData = json_decode($request->getContent(), true);
    if (empty($smsSettingsData)) {
        return response()->json(['message' => 'Gerekli veriler bulunamadı.'], 400);
    }
    $updated = DB::table('settings')->where('id', 1)->update($smsSettingsData);
    $response = DB::table('settings')->first();
    return response()->json($response, 200);
});
Route::post('/link-sms', function (Request $request) {
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$customerId = $request->customer_id;
	$type = $request->type ?? 'share';
    if (empty($customerId)) {
        return response()->json(['message' => 'Gerekli veriler bulunamadı.'], 400);
    }
	$customer = DB::table('customers')->where('id', $customerId)->first();
	$settings = DB::table('settings')->first();
	if ($type === 'send' && (!isset($settings->link_sms_message_status) || $settings->link_sms_message_status != 1)) {
		return response()->json(['message' => 'Link SMS durumu kapalı.'], 402);
	}
	$phoneNumber = $customer->phone;
	$message = str_replace(
		['[Link]'],
		[request()->getSchemeAndHttpHost() . '/online/randevu/' . $customerId],
		$settings->link_sms_message
	);
	if ($type === 'send') {
		$success = sendSMSMessage($phoneNumber, $message, 'link');
		if ($success) {
			return response()->json(['message' => 'SMS gönderildi.'], 200);
		} else {
			return response()->json(['message' => 'SMS gönderilemedi.'], 201);
		}
	}
    return response()->json(['message' => $message], 200);
});
function sendSMSReservation($reservationId, $action) {
	$reservation = DB::table('reservations')->where('id', $reservationId)->first();
	$customer = DB::table('customers')->where('id', $reservation->customer_id)->first();
	$settings = DB::table('settings')->first();
	$statusField = $action . '_status';
	if (!isset($settings->$statusField) || $settings->$statusField != 1) {
		return false;
	}
	$date = getDateTimeZone($reservation->start_date, 'd.m.Y');
	$hour = getDateTimeZone($reservation->start_date, 'H:i');
	$companyPhone = $settings->phone_number;
	$phoneNumber = $customer->phone;
	// Hizmetleri al
	$services = DB::table('reservations_items')
		->join('services', 'reservations_items.service_id', '=', 'services.id')
		->where('reservations_items.reservation_id', $reservationId)
		->pluck('services.name')
		->implode(', ');
	$message = str_replace(
		['[Tarih]', '[Saat]', '[Telefon Numarası]', '[HİZMETLER]', '[Link]'],
		[$date, $hour, $companyPhone, $services, request()->getSchemeAndHttpHost() . '/online/rezervasyon/' . $reservationId],
		$settings->$action
	);
	$status = sendSMSMessage($phoneNumber, $message, 'booking');
	return $status;
}
// smsx
function sendSMSMessage($phoneNumber, $message, $type, $code = null) {
    try {
		$settings = DB::table('settings')->first();
		$client = new Client();
        $response = $client->post('http://api.mesajpaneli.com/index.php', [
            'form_params' => [
                'islem' => 1,
                'user' => $settings->sms_username,
                'pass' => $settings->sms_password,
                'mesaj' => $message,
                'numaralar' => $phoneNumber,
                'baslik' => $settings->sms_header,
            ]
        ]);
        $body = (string) $response->getBody();
        $success = strpos($body, 'OK') !== false;
    } catch (RequestException $e) {
        $success = false;
    }
    DB::table('send_sms_code')->insert([
        'phone' => $phoneNumber,
		'contents' => $message,
		'code' => $code,
        'type' => $type,
        'status' => $success ? 'Gönderildi' : 'Başarısız',
    ]);
	if ($success) {
		$smsCredit = calculateSmsSegmentCount($message);
		DB::table('settings')->where('id', 1)->decrement('remaining_sms_limit', $smsCredit);
	}
	return $success;
}
function calculateSmsSegmentCount($text) {
    $length = strlen($text);
	return ceil($length / 155);
}
/*********************************************************************************************************************************
													DESTEK İŞLEMLERİ
*********************************************************************************************************************************/
Route::get('/support-types', function (Request $request) {
	global $userId;
	if (!apikeyVerify($request)) {
		return response()->json(['message' => 'Unauthorized'], 401);
	}
	$response = postRequest('postFeedBackTypes');
	if ($response) {
		return response()->json($response, 200);
	} else {
		return response()->json(['message' => 'Destek talebi tipleri bulunamadı.'], 201);
	}
});
Route::get('/supports', function (Request $request) {
	global $userId;
	if (!apikeyVerify($request)) {
		return response()->json(['message' => 'Unauthorized'], 401);
	}
	$response = postRequest('postFeedbacks');
	if ($response) {
		return response()->json($response, 200);
	} else {
		return response()->json(['message' => 'Destek talebi bulunamadı.'], 201);
	}
});
Route::post('/add-support', function (Request $request) {
	global $userId;
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$typeId = $request->type_id;
	$subject = $request->subject;
	$message = $request->message;
    if (empty($typeId) || empty($subject) || empty($message)) {
        return response()->json(['message' => 'Gerekli veriler bulunamadı.'], 400);
    }
	$name = DB::table('users')->where('id', $userId)->value('name');
	$user = DB::table('users')->where('id', $userId)->value('phone');
	$body = [
		'type_id' => $typeId,
		'sender' => 'user',
		'user_id' => $userId,
		'name' => $name,
		'user' => $user,
		'subject' => $subject,
		'message' => $message,
	];
	$success = postRequest('postAddFeedback', $body);
	return response()->json(['message' => 'Destek talebi gönderildi.'], 200);
});
Route::post('/add-support-message', function (Request $request) {
	global $userId;
    if (!apikeyVerify($request)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
	$feedbackId = $request->feedback_id;
	$message = $request->message;
    if (empty($subject) && empty($message)) {
        return response()->json(['message' => 'Gerekli veriler bulunamadı.'], 400);
    }
	$name = DB::table('users')->where('id', $userId)->value('name');
	$user = DB::table('users')->where('id', $userId)->value('phone');
	$body = [
		'feedback_id' => $feedbackId,
		'sender' => 'user',
		'user_id' => $userId,
		'name' => $name,
		'user' => $user,
		'message' => $message,
	];
	$response = postRequest('postAddFeedbackMessage', $body);
	return response()->json($response, 200);
});
/*********************************************************************************************************************************
													EĞİTİM VİDEOLARI
*********************************************************************************************************************************/
Route::get('/training-videos', function (Request $request) {
	global $userId;
	if (!apikeyVerify($request)) {
		return response()->json(['message' => 'Unauthorized'], 401);
	}
	$response = postRequest('postTrainingVideos');
	if (isset($response)) {
		return response()->json($response, 200);
	} else {
		return response()->json(['message' => 'Eğitim videoları bulunamadı.'], 201);
	}
});
/*********************************************************************************************************************************
												ALTF4 MASTERBM İŞLEMLERİ
*********************************************************************************************************************************/
function requestTokenKey() {
	try {
		$client = new Client();
		$response = $client->post(ALTF4_API_URL . 'postTokenKey', [
			'form_params' => ['password' => 'altf4!3570']
		]);
		$statusCode = $response->getStatusCode();
		$body = (string) $response->getBody();
		$data = json_decode($body, true);
		if ($statusCode === 200) {
			return $data['message'];
		} else {
			return null;
		}
    } catch (RequestException $e) {
		Log::error('Error', ['message' => $e->getMessage()]);
        return null;
    }
}
function postRequest($url, $body = []) {
	try {
		$body['domain'] = 'eniyisalonapp';
		$body['subdomain'] = getSubdomain();
		$client = new Client();
		$response = $client->post(ALTF4_API_URL . $url, [
			'form_params' => $body,
			'headers' => ['Authorization' => requestTokenKey()]
		]);
		$statusCode = $response->getStatusCode();
		$body = (string) $response->getBody();
		$data = json_decode($body, true);
		if ($statusCode === 200) {
			return $data;
		} else {
			return null;
		}
    } catch (RequestException $e) {
		Log::error('Error', ['message' => $e->getMessage()]);
        return null;
	}
}
function getSubdomain() {
	return explode('.', request()->getHost())[0];
}
/*********************************************************************************************************************************
													RESİM İŞLEMLERİ
*********************************************************************************************************************************/
function createImage($path, $base64Image) {
    try {
        $format = getImageFormat($base64Image);
        $image = cleanBase64Data($base64Image);
		$publicPath = public_path(trim($path, '/'));
		$imageName = uniqid().'.'.$format;
		$imagePath = $publicPath.'/'.$imageName;
        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }
        file_put_contents($imagePath, base64_decode($image));
        if (!file_exists($imagePath)) {
            return null;
        }
        return $imageName;
    } catch (\Exception $e) {
		Log::error('Error', ['message' => $e->getMessage()]);
        return null;
    }
}
function deleteImage($path, $imageName) {
    $oldPath = public_path(trim($path, '/').'/'.$imageName);
    return file_exists($oldPath) ? unlink($oldPath) : false;
}
function getImageFormat($base64Image) {
    if (strpos($base64Image, 'data:image/') === 0) {
        return explode('/', explode(';', $base64Image)[0])[1];
    }
    return 'png';
}
function cleanBase64Data($base64Image) {
    if (strpos($base64Image, 'data:image/') === 0) {
        $format = $this->getImageFormat($base64Image);
        return str_replace('data:image/'.$format.';base64,', '', $base64Image);
    }
    return $base64Image;
}
/*********************************************************************************************************************************
													SATIŞ İŞLEMLERİ
*********************************************************************************************************************************/
Route::post('/addDate', function (Request $request) {
	$new_expired_date = $request->input('tarih');
    if (empty($new_expired_date)) {
        return response()->json(['message' => 'Eksik veri'], 400);
    }
    $updated = DB::table('package_features')->where('id', 1)->update(['expired_date' => $new_expired_date]);
	if ($updated) {
		$response = DB::table('package_features')->where('id', 1)->first();
		return response()->json($response, 200);
	} else {
		return response()->json(['message' => 'Güncelleme başarısız.'], 201);
	}
});
Route::post('/addSms', function (Request $request) {
	$eklenecek_sms = $request->input('sms_miktari');
	if (empty($eklenecek_sms)) {
		return response()->json(['message' => 'Eksik veri'], 400);
	}
	$settings = DB::table('settings')->where('id', 1)->first();
	$remaining_sms_limit = $settings->remaining_sms_limit;
	$sms = $remaining_sms_limit + $eklenecek_sms;
    $updated = DB::table('settings')->where('id', 1)->update(['remaining_sms_limit' => $sms]);
	if ($updated) {
		$response = DB::table('settings')->where('id', 1)->first();
		return response()->json($response, 200);
	} else {
		return response()->json(['message' => 'Güncelleme başarısız.'], 201);
	}
});
