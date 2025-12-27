<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\Facades\Image;
use App\Http\Requests\SettingsRequest;
use App\Http\Requests\SmsSettingsRequest;

class SettingsController extends Controller
{
    public function updateSettings(SettingsRequest $request)
    {
        try {
            $data = $this->getValidatedData($request);

            if ($request->hasFile('logo')) {
                $data['logo'] = $this->uploadLogo($request->file('logo'), 'logo');
            }

            if ($request->hasFile('company_logo')) {
                $data['company_logo'] = $this->uploadLogo($request->file('company_logo'), 'company_logo');
            }

            $this->updateSettingsInDatabase($data);

            if ($request->ajax() || $request->wantsJson()) {
                $updatedSettings = DB::table('settings')->where('id', 1)->first();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Tüm ayarlar başarıyla güncellendi',
                    'settings' => $updatedSettings
                ], 200);
            }

            return redirect()->back()->with('success', 'Tüm ayarlar başarıyla güncellendi');
        
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ayarlar güncellenirken bir hata oluştu: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', 'Ayarlar güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function updateSMS(SmsSettingsRequest $request)
    {
        try {
            $updateData = [
                'booking_sms_message' => $request->booking_sms_message,
                'reminder_sms_message' => $request->reminder_sms_message,
                'updater_sms_message' => $request->updater_sms_message,
                'delete_sms_message' => $request->delete_sms_message,
                'booking_sms_message_status'=> $request->booking_sms_message_status,
                'updated_at' => now(),
            ];

            DB::table('settings')->where('id', 1)->update($updateData);

            if ($request->ajax() || $request->wantsJson()) {
                $updatedSettings = DB::table('settings')->where('id', 1)->first();
                return response()->json([
                    'success' => true,
                    'message' => 'SMS ayarları başarıyla güncellendi',
                    'settings' => $updatedSettings
                ], 200);
            }

            return redirect()->back()->with('success', 'SMS ayarları başarıyla güncellendi');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'SMS ayarları güncellenirken bir hata oluştu: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', 'SMS ayarları güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    private function getValidatedData(SettingsRequest $request)
    {
        $allowedColumns = [
            'salon_name', 'phone_number', 'email', 'address', 'logo', 'company_logo',
            'work_start', 'work_end', 'room_based_working', 'parapuan',
            'parapuan_system_enabled', 'employee_commission',
            'consent_approved_text', 'sms_username', 'sms_password',
            'sms_header', 'remaining_sms_limit',
            'tax_office', 'tax_number',
            'notification_sms_campaign', 'notification_sms_birthday', 'notification_sms_appointment',
            'booking_sms_message', 'reminder_sms_message', 'updater_sms_message', 'delete_sms_message', 'link_sms_message','booking_sms_message_status',
            'currency', 'currency_symbol', 'social_media_links', 'area_code', 'number_length', 'interval_time',
            'low_stock_threshold',
            'is_promoted_publicly'
        ];
        
        $data = $request->only($allowedColumns);
        $data['updated_at'] = now();
        
        $data['room_based_working'] = $request->input('room_based_working') == '1' ? 1 : 0;
        $data['parapuan_system_enabled'] = $request->input('parapuan_system_enabled') == '1' ? 1 : 0;
        
        $data['notification_sms_campaign'] = $request->input('notification_sms_campaign') == '1' ? 1 : 0;
        $data['notification_sms_birthday'] = $request->input('notification_sms_birthday') == '1' ? 1 : 0;
        $data['notification_sms_appointment'] = $request->input('notification_sms_appointment') == '1' ? 1 : 0;
        $data['booking_sms_message_status'] = $request->input('booking_sms_message_status') == '1' ? 1 : 0;
        
        $data['is_promoted_publicly'] = $request->input('is_promoted_publicly') == '1' ? 1 : 0;
        
        $lowStockThreshold = $request->input('low_stock_threshold');
        if ($lowStockThreshold !== null) {
            $lowStockThreshold = (int) $lowStockThreshold;
            $data['low_stock_threshold'] = max(1, min(100, $lowStockThreshold));
        }
        
        return $data;
    }

    private function uploadLogo($logo, $type = 'logo')
    {
        try {
            $uploadPath = public_path('images');
            

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            

            if (!$logo->isValid()) {
                throw new \Exception('Geçersiz dosya yüklendi.');
            }
            

            $oldSettings = DB::table('settings')->where('id', 1)->first();
            if ($type === 'logo' && $oldSettings && $oldSettings->logo && file_exists(public_path('images/' . $oldSettings->logo))) {
                unlink(public_path('images/' . $oldSettings->logo));
            } elseif ($type === 'company_logo' && $oldSettings && $oldSettings->company_logo && file_exists(public_path('images/' . $oldSettings->company_logo))) {
                unlink(public_path('images/' . $oldSettings->company_logo));
            }
            
            $logoName = $type . '_' . time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move($uploadPath, $logoName);
            
            return $logoName;
        } catch (\Exception $e) {
            throw new \Exception('Logo yükleme hatası: ' . $e->getMessage());
        }
    }

    private function updateSettingsInDatabase(array $data)
    {
        $updated = DB::table('settings')->where('id', 1)->update($data);
        
        if (!$updated) {
            throw new \Exception('Ayarlar veritabanında güncellenemedi.');
        }
        
        return $updated;
    }


    public function getSalonInformation()
    {
        try {
            $settings = $this->getSalonSettings();
            $currentDateTime = now();
            
            $data = [
                'salon' => $this->buildSalonData($settings),
                'menu_items' => $this->getActiveServices(),
                'availability' => $this->getAvailabilityData($settings, $currentDateTime),
                'metadata' => $this->getMetadata($currentDateTime)
            ];

            return response()->json([
                'data' => ['getSalonInformation' => $data],
                'errors' => null
            ], 200);

        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    private function getSalonSettings()
    {
        $settings = DB::table('settings')->where('id', 1)->first();
        
        if (!$settings) {
            throw new \Exception('Salon ayarları bulunamadı', 404);
        }
        
        return $settings;
    }

    private function buildSalonData($settings)
    {
        return [
            'id' => $settings->id,
            'name' => $settings->salon_name,
            'contact' => [
                'phone' => $settings->phone_number,
                'email' => $settings->email,
                'address' => $settings->address
            ],
            'branding' => [
                'logoUrl' => $this->getImageUrl($settings->logo),
                'companyLogoUrl' => $this->getImageUrl($settings->company_logo)
            ],
            'workingHours' => [
                'start' => $settings->work_start ?? '09:00',
                'end' => $settings->work_end ?? '18:00',
                'lunchBreak' => [
                    'start' => '12:30',
                    'end' => '13:30'
                ]
            ],
            'currency' => [
                'code' => $settings->currency ?? 'TL',
                'symbol' => $settings->currency_symbol ?? '₺'
            ],
            'features' => [
                'roomBasedWorking' => (bool) $settings->room_based_working,
                'pointSystem' => (bool) $settings->parapuan_system_enabled,
                'intervalTime' => $settings->interval_time ?? 30
            ]
        ];
    }

    private function getActiveServices()
    {
        return DB::table('menu_items')
            ->where('is_active', 1)
            ->where(function($query) {
                $query->where('is_stock', 0)->orWhereNull('is_stock');
            })
            ->select('id', 'name', 'code', 'description', 'price', 'discount_price', 'image', 'category_id', 'tax_rate', 'total_price')
            ->orderBy('name')
            ->get()
            ->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'code' => $service->code,
                    'description' => $service->description,
                    'pricing' => [
                        'price' => (float) $service->price,
                        'discountPrice' => $service->discount_price ? (float) $service->discount_price : null,
                        'taxRate' => (float) $service->tax_rate,
                        'totalPrice' => (float) $service->total_price,
                        'hasDiscount' => !is_null($service->discount_price) && $service->discount_price < $service->price
                    ],
                    'imageUrl' => $this->getImageUrl($service->image, 'menu_items'),
                    'categoryId' => $service->category_id
                ];
            })
            ->toArray();
    }

    private function getAvailabilityData($settings, $currentDateTime)
    {
        $employees = $this->getEmployeesWithAvailability($settings, $currentDateTime);
        $tables = $this->getTablesWithStatus($currentDateTime);
        
        return [
            'employees' => $employees,
            'tables' => $tables,
            'summary' => [
                'totalEmployees' => count($employees),
                'availableEmployees' => count(array_filter($employees, fn($emp) => $emp['availability']['isAvailable'])),
                'totalTables' => count($tables),
                'availableTables' => count(array_filter($tables, fn($table) => $table['isAvailable'])),
                'nextAvailableSlot' => $this->getNextAvailableSlot($employees),
                'busyPeriods' => $this->getBusyPeriods($employees)
            ]
        ];
    }

    private function getEmployeesWithAvailability($settings, $currentDateTime)
    {
        $employees = DB::table('employees')
            ->where('is_active', 1)
            ->select('id', 'name', 'phone', 'avatar', 'table_id', 'skills', 'position')
            ->orderBy('name')
            ->get();

        $todayReservations = $this->getTodayReservations($currentDateTime->format('Y-m-d'));

        return $employees->map(function($employee) use ($settings, $currentDateTime, $todayReservations) {
            $employeeReservations = $todayReservations->where('employee_id', $employee->id);
            $availableSlots = $this->calculateSmartTimeSlots($settings, $employeeReservations, $currentDateTime);
            
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'contact' => [
                    'phone' => $employee->phone,
                ],
                'avatarUrl' => $this->getImageUrl($employee->avatar, 'avatars'),
                'tableId' => $employee->table_id,
                'skills' => $this->parseSkills($employee->skills),
                'position' => $employee->position,
                'availability' => [
                    'isAvailable' => count($availableSlots) > 0,
                    'availableSlots' => $availableSlots,
                    'totalSlots' => count($availableSlots),
                    'nextSlot' => $availableSlots[0] ?? null,
                    'workload' => $this->calculateWorkload($employeeReservations)
                ]
            ];
        })->toArray();
    }

    private function getTablesWithStatus($currentDateTime)
    {
        $tables = DB::table('tables')->get();
        $activeReservations = DB::table('reservations')
            ->where('start_date', '<=', $currentDateTime)
            ->where('end_date', '>=', $currentDateTime)
            ->where('status', 'confirmed')
            ->pluck('table_id')
            ->toArray();

        return $tables->map(function($table) use ($activeReservations) {
            $isOccupied = in_array($table->id, $activeReservations);
            
            return [
                'id' => $table->id,
                'name' => $table->name,
                'capacity' => $table->capacity,
                'employeeId' => $table->employee_id,
                'status' => $isOccupied ? 'occupied' : $table->status,
                'isAvailable' => !$isOccupied && $table->status === 'available'
            ];
        })->toArray();
    }

    private function calculateSmartTimeSlots($settings, $reservations, $currentDateTime)
    {
        $workStart = $settings->work_start ?? '09:00';
        $workEnd = $settings->work_end ?? '18:00';
        $slotDuration = $settings->interval_time ?? 30;
        
        $startTime = max(
            strtotime($currentDateTime->format('Y-m-d') . ' ' . $workStart),
            $currentDateTime->timestamp
        );
        $endTime = strtotime($currentDateTime->format('Y-m-d') . ' ' . $workEnd);
        $lunchStart = strtotime($currentDateTime->format('Y-m-d') . ' 12:30');
        $lunchEnd = strtotime($currentDateTime->format('Y-m-d') . ' 13:30');
        
        $bookedSlots = $this->getBookedTimeSlots($reservations, $currentDateTime);
        $availableSlots = [];
        
        for ($time = $startTime; $time < $endTime; $time += ($slotDuration * 60)) {
            $slotEnd = $time + ($slotDuration * 60);
            

            if ($this->isInLunchBreak($time, $slotEnd, $lunchStart, $lunchEnd)) {
                continue;
            }
            

            if (!$this->hasTimeConflict($time, $slotEnd, $bookedSlots)) {
                $availableSlots[] = [
                    'startTime' => date('H:i', $time),
                    'endTime' => date('H:i', $slotEnd),
                    'timestamp' => $time,
                    'duration' => $slotDuration,
                    'isPeak' => $this->isPeakHour($time),
                    'confidence' => $this->calculateSlotConfidence($time, $bookedSlots)
                ];
            }
        }
        
        return $availableSlots;
    }

    private function getTodayReservations($date)
    {
        return DB::table('reservations')
            ->whereDate('start_date', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->select('employee_id', 'table_id', 'start_date', 'end_date', 'status', 'total_price')
            ->get();
    }

    private function getBookedTimeSlots($reservations, $currentDateTime)
    {
        return $reservations->map(function($reservation) use ($currentDateTime) {
            return [
                'start' => strtotime($reservation->start_date),
                'end' => strtotime($reservation->end_date),
                'status' => $reservation->status
            ];
        })->toArray();
    }

    private function parseSkills($skills)
    {
        if (!$skills) return [];
        
        return is_string($skills) ? 
            array_map('trim', explode(',', $skills)) : 
            (array) $skills;
    }

    private function calculateWorkload($reservations)
    {
        $totalMinutes = $reservations->sum(function($res) {
            return (strtotime($res->end_date) - strtotime($res->start_date)) / 60;
        });
        
        $workDayMinutes = 8 * 60;
        $percentage = min(100, ($totalMinutes / $workDayMinutes) * 100);
        
        return [
            'percentage' => round($percentage, 1),
            'level' => $percentage > 80 ? 'high' : ($percentage > 50 ? 'medium' : 'low'),
            'totalMinutes' => $totalMinutes
        ];
    }

    private function getNextAvailableSlot($employees)
    {
        $allSlots = collect($employees)
            ->pluck('availability.availableSlots')
            ->flatten(1)
            ->sortBy('timestamp')
            ->first();
            
        return $allSlots ?: null;
    }

    private function getBusyPeriods($employees)
    {

        $slotCounts = [];
        
        foreach ($employees as $employee) {
            foreach ($employee['availability']['availableSlots'] as $slot) {
                $hour = date('H', $slot['timestamp']);
                $slotCounts[$hour] = ($slotCounts[$hour] ?? 0) + 1;
            }
        }
        
        $totalEmployees = count($employees);
        $busyHours = [];
        
        foreach ($slotCounts as $hour => $availableCount) {
            $busyPercentage = (($totalEmployees - $availableCount) / $totalEmployees) * 100;
            
            if ($busyPercentage > 70) {
                $busyHours[] = [
                    'hour' => $hour . ':00',
                    'busyPercentage' => round($busyPercentage, 1)
                ];
            }
        }
        
        return $busyHours;
    }

    private function getMetadata($currentDateTime)
    {
        return [
            'generatedAt' => $currentDateTime->toISOString(),
            'timezone' => config('app.timezone', 'UTC'),
            'version' => '2.0',
            'cacheValidUntil' => $currentDateTime->addMinutes(5)->toISOString()
        ];
    }

    private function getImageUrl($imageName, $folder = null)
    {
        if (!$imageName) return null;
        
        $path = $folder ? "/images/{$folder}/{$imageName}" : "/images/{$imageName}";
        return url($path);
    }

    private function isInLunchBreak($start, $end, $lunchStart, $lunchEnd)
    {
        return ($start >= $lunchStart && $start < $lunchEnd) || 
               ($end > $lunchStart && $end <= $lunchEnd) ||
               ($start <= $lunchStart && $end >= $lunchEnd);
    }

    private function hasTimeConflict($start, $end, $bookedSlots)
    {
        foreach ($bookedSlots as $booked) {
            if (($start >= $booked['start'] && $start < $booked['end']) ||
                ($end > $booked['start'] && $end <= $booked['end']) ||
                ($start <= $booked['start'] && $end >= $booked['end'])) {
                return true;
            }
        }
        return false;
    }

    private function isPeakHour($timestamp)
    {
        $hour = (int) date('H', $timestamp);
        return in_array($hour, [10, 11, 14, 15, 16, 17]);
    }

    private function calculateSlotConfidence($timestamp, $bookedSlots)
    {
        $hour = (int) date('H', $timestamp);
        $conflictCount = 0;
        

        foreach ($bookedSlots as $booked) {
            $bookedHour = (int) date('H', $booked['start']);
            if (abs($hour - $bookedHour) <= 1) {
                $conflictCount++;
            }
        }
        
        return max(0.3, 1 - ($conflictCount * 0.2));
    }

    private function handleError(\Exception $e)
    {
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        $code = $statusCode === 404 ? 'NOT_FOUND' : 'INTERNAL_ERROR';
        
        return response()->json([
            'errors' => [
                [
                    'message' => $e->getMessage(),
                    'code' => $code
                ]
            ],
            'data' => null
        ], $statusCode);
    }


    public function sentSMSData()
    {
        $sms = DB::table('send_sms_code')->get();
        return response()->json($sms);
    }
}
