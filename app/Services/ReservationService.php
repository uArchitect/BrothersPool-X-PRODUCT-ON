<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReservationService extends BaseService
{
    /**
     * Create a new reservation
     */
    public function createReservation(array $data): int
    {
        return DB::transaction(function () use ($data) {
            // Generate reservation number
            $reservationNumber = 'RES-' . str_pad(DB::table('reservations')->max('id') + 1, 4, '0', STR_PAD_LEFT);

            $reservationId = DB::table('reservations')->insertGetId([
                'reservation_number' => $reservationNumber,
                'table_id' => $data['table_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'employee_id' => $data['employee_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'start_date' => $data['start_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'] ?? null,
                'party_size' => $data['party_size'] ?? 1,
                'status' => 'pending',
                'special_requests' => $data['special_requests'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total_price' => $data['total_price'] ?? 0,
                'discount_type' => $data['discount_type'] ?? null,
                'discount_percent' => $data['discount_percent'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'color' => $data['color'] ?? '#3d85c6',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add reservation items if provided
            if (isset($data['menu_items']) && !empty($data['menu_items'])) {
                $this->addReservationItems($reservationId, $data['menu_items']);
            }

            Log::info('Reservation created', [
                'reservation_id' => $reservationId,
                'reservation_number' => $reservationNumber,
                'table_id' => $data['table_id'] ?? null,
                'user_id' => Auth::id(),
            ]);

            return $reservationId;
        });
    }

    /**
     * Add items to a reservation
     */
    private function addReservationItems(int $reservationId, array $menuItems): void
    {
        foreach ($menuItems as $item) {
            $menuItem = DB::table('menu_items')->find($item['menu_item_id']);

            if (!$menuItem) {
                continue;
            }

            $unitPrice = $item['price'] ?? $menuItem->price;
            $totalPrice = $unitPrice * ($item['quantity'] ?? 1);

            DB::table('reservations_items')->insert([
                'reservation_id' => $reservationId,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'notes' => $item['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Update reservation status
     */
    public function updateReservationStatus(int $reservationId, string $status, string $notes = null): bool
    {
        return DB::transaction(function () use ($reservationId, $status, $notes) {
            $updated = DB::table('reservations')->where('id', $reservationId)->update([
                'status' => $status,
                'updated_at' => now(),
            ]);

            if ($updated) {
                // Update table status based on reservation status
                $this->updateTableStatusFromReservation($reservationId, $status);

                Log::info('Reservation status updated', [
                    'reservation_id' => $reservationId,
                    'new_status' => $status,
                    'user_id' => Auth::id(),
                ]);
            }

            return $updated > 0;
        });
    }

    /**
     * Update table status based on reservation status
     */
    private function updateTableStatusFromReservation(int $reservationId, string $status): void
    {
        $reservation = DB::table('reservations')->where('id', $reservationId)->first();

        if (!$reservation || !$reservation->table_id) {
            return;
        }

        $tableStatus = match($status) {
            'confirmed' => 'reserved',
            'completed', 'no_show' => 'available',
            default => 'available',
        };

        DB::table('tables')->where('id', $reservation->table_id)->update([
            'status' => $tableStatus,
            'updated_at' => now(),
        ]);
    }

    /**
     * Get table availability for a specific time slot
     */
    public function getTableAvailability(string $date, string $time, int $duration = 120): array
    {
        $startDateTime = $date . ' ' . $time;
        $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime . ' +' . $duration . ' minutes'));

        // Get all active tables
        $allTables = DB::table('tables')
            ->where('is_active', 1)
            ->where('status', '!=', 'maintenance')
            ->get();

        // Get reserved tables for the time period
        $reservedTables = DB::table('reservations')
            ->where('status', 'confirmed')
            ->where(function($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('start_date', [$startDateTime, $endDateTime])
                      ->orWhere(function($q) use ($startDateTime, $endDateTime) {
                          $q->where('start_date', '<=', $startDateTime)
                            ->whereRaw('DATE_ADD(start_date, INTERVAL duration MINUTE) >= ?', [$startDateTime]);
                      });
            })
            ->pluck('table_id')
            ->toArray();

        // Get occupied tables (orders from last 4 hours)
        $occupiedTables = DB::table('orders')
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->where('created_at', '>=', now()->subHours(4))
            ->pluck('table_id')
            ->toArray();

        $unavailableTables = array_unique(array_merge($reservedTables, $occupiedTables));

        $availableTables = $allTables->filter(function($table) use ($unavailableTables) {
            return !in_array($table->id, $unavailableTables);
        });

        return [
            'available_tables' => $availableTables->values(),
            'unavailable_tables' => $allTables->whereIn('id', $unavailableTables)->values(),
            'total_tables' => $allTables->count(),
            'available_count' => $availableTables->count(),
            'requested_datetime' => $startDateTime,
            'duration_minutes' => $duration,
        ];
    }

    /**
     * Check for conflicting reservations
     */
    public function hasConflictingReservations(int $tableId, string $startDateTime, string $endDateTime, int $excludeReservationId = null): bool
    {
        $query = DB::table('reservations')
            ->where('table_id', $tableId)
            ->where('status', 'confirmed')
            ->where(function($q) use ($startDateTime, $endDateTime) {
                $q->whereBetween('start_date', [$startDateTime, $endDateTime])
                  ->orWhere(function($query) use ($startDateTime, $endDateTime) {
                      $query->where('start_date', '<=', $startDateTime)
                            ->whereRaw('DATE_ADD(start_date, INTERVAL duration MINUTE) >= ?', [$startDateTime]);
                  });
            });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query->exists();
    }

    /**
     * Get reservation items
     */
    public function getReservationItems(int $reservationId): array
    {
        return DB::table('reservations_items')
            ->join('menu_items', 'reservations_items.menu_item_id', '=', 'menu_items.id')
            ->select(
                'reservations_items.*',
                'menu_items.name as item_name',
                'menu_items.price as original_price'
            )
            ->where('reservations_items.reservation_id', $reservationId)
            ->get()
            ->toArray();
    }

    /**
     * Update reservation items
     */
    public function updateReservationItems(int $reservationId, array $items): float
    {
        // Remove existing items
        DB::table('reservations_items')->where('reservation_id', $reservationId)->delete();

        $totalAmount = 0;

        foreach ($items as $item) {
            $menuItem = DB::table('menu_items')->find($item['menu_item_id']);

            if (!$menuItem) {
                continue;
            }

            $unitPrice = $item['price'] ?? $menuItem->price;
            $quantity = $item['quantity'] ?? 1;
            $totalPrice = $unitPrice * $quantity;
            $totalAmount += $totalPrice;

            DB::table('reservations_items')->insert([
                'reservation_id' => $reservationId,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'notes' => $item['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update reservation total
        DB::table('reservations')->where('id', $reservationId)->update([
            'total_price' => $totalAmount,
            'updated_at' => now(),
        ]);

        return $totalAmount;
    }

    /**
     * Get today's reservations
     */
    public function getTodayReservations(): array
    {
        return DB::table('reservations')
            ->whereDate('start_date', now()->toDateString())
            ->orderBy('start_time')
            ->get()
            ->toArray();
    }

    /**
     * Send SMS notification for reservation
     */
    public function sendReservationSMS(int $reservationId, string $messageType): bool
    {
        $reservation = DB::table('reservations')->where('id', $reservationId)->first();

        if (!$reservation || !$reservation->customer_phone) {
            return false;
        }

        $settings = DB::table('settings')->first();

        if (!$settings || empty($settings->sms_username) || empty($settings->sms_password)) {
            return false;
        }

        $phoneNumber = $reservation->customer_phone;
        $date = date('d.m.Y', strtotime($reservation->start_date));
        $time = $reservation->start_time;
        $companyPhone = $settings->phone_number ?? '';

        $message = $settings->{$messageType} ?? '';
        $message = str_replace('[Tarih]', $date, $message);
        $message = str_replace('[Saat]', $time, $message);
        $message = str_replace('[Telefon Numarası]', $companyPhone, $message);

        // Add base URL for online reservation link if needed
        $baseUrl = request()->getSchemeAndHttpHost();
        $message = str_replace('[Link]', $baseUrl, $message);

        // Get reservation items for SMS
        $items = $this->getReservationItems($reservationId);
        $itemNames = array_column($items, 'item_name');
        $message = str_replace('[HİZMETLER]', implode(', ', $itemNames), $message);

        try {
            // Use SMS service (assuming Guzzle HTTP client)
            $client = new \GuzzleHttp\Client();
            $response = $client->post('http://api.mesajpaneli.com/index.php', [
                'form_params' => [
                    'islem' => 1,
                    'user' => $settings->sms_username,
                    'pass' => $settings->sms_password,
                    'mesaj' => $message,
                    'numaralar' => $phoneNumber,
                    'baslik' => $settings->sms_header ?? 'RESTAURANT',
                ]
            ]);

            // Log SMS sent
            DB::table('send_sms_code')->insert([
                'phone' => $phoneNumber,
                'status' => 'Gönderildi',
                'type' => 'reservation',
                'contents' => $message,
                'created_at' => now(),
            ]);

            // Decrement SMS limit
            DB::table('settings')->where('id', 1)->decrement('remaining_sms_limit', 1);

            Log::info('Reservation SMS sent', [
                'reservation_id' => $reservationId,
                'phone' => $phoneNumber,
                'message_type' => $messageType,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Reservation SMS failed', [
                'reservation_id' => $reservationId,
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
