<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Table;
use App\Traits\ApiResponse;

class TableController extends Controller
{
    use ApiResponse;
    /**
     * Display the table management page
     */
    public function index()
    {
        $tables = Table::orderBy('table_number')->get();

        $tableStats = [
            'total' => $tables->count(),
            'available' => $tables->where('status', 'available')->count(),
            'occupied' => $tables->where('status', 'occupied')->count(),
            'reserved' => $tables->where('status', 'reserved')->count(),
            'maintenance' => $tables->where('status', 'maintenance')->count(),
            'active' => $tables->where('is_active', true)->count(),
            'inactive' => $tables->where('is_active', false)->count()
        ];

        return view('tables.index', compact('tables', 'tableStats'));
    }

    /**
     * Store a newly created table
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|max:50|unique:tables,table_number',
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:255',
            'table_type' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'description' => 'nullable|string'
        ]);

        $table = Table::create([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'table_type' => $request->table_type ?? 'dine_in',
            'is_reservable' => true,
            'is_smoking_allowed' => false,
            'status' => 'available',
            'is_active' => $request->has('is_active'),
            'notes' => $request->description,
        ]);

        return $this->createdResponse(['id' => $table->id], 'Masa başarıyla oluşturuldu.');
    }

    /**
     * Update the specified table
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'table_number' => 'required|string|max:50|unique:tables,table_number,' . $id,
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:255',
            'table_type' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'status' => 'required|in:available,occupied,reserved,maintenance'
        ]);

        $table = Table::find($id);

        if (!$table) {
            return $this->notFoundResponse('Masa bulunamadı.');
        }

        $table->update([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'table_type' => $request->table_type ?? 'dine_in',
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
            'notes' => $request->description,
        ]);

        return $this->updatedResponse(null, 'Masa başarıyla güncellendi.');
    }

    /**
     * Remove the specified table
     */
    public function destroy($id)
    {
        $table = Table::find($id);

        if (!$table) {
            return $this->notFoundResponse('Masa bulunamadı.');
        }

        // Check if table has active orders or reservations
        $activeOrders = $table->orders()
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->count();

        $activeReservations = $table->reservations()
            ->where('status', 'confirmed')
            ->where('start_date', '>=', now())
            ->count();

        if ($activeOrders > 0 || $activeReservations > 0) {
            return $this->errorResponse('Bu masada aktif sipariş veya rezervasyon bulunduğu için silinemez.', 400);
        }

        $table->delete();

        return $this->deletedResponse('Masa başarıyla silindi.');
    }

    /**
     * Toggle table status
     */
    public function toggleStatus($id)
    {
        try {
            $table = Table::find($id);

            if (!$table) {
                return $this->notFoundResponse('Masa bulunamadı.');
            }

            $newStatus = !$table->is_active;

            $table->update([
                'is_active' => $newStatus,
            ]);

            return response()->json([
                'success' => true,
                'message' => $newStatus ? 'Masa aktif edildi.' : 'Masa pasif edildi.',
                'is_active' => $newStatus
            ]);

        } catch (\Exception $e) {
            \Log::error('Table toggle status error: ' . $e->getMessage());
            return $this->serverErrorResponse('Bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Update table status (available, occupied, reserved, maintenance)
     */
    public function updateTableStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,maintenance'
        ]);

        $table = Table::find($id);

        if (!$table) {
            return $this->notFoundResponse('Masa bulunamadı.');
        }

        $table->update([
            'status' => $request->status,
        ]);

        return $this->updatedResponse(['status' => $request->status], 'Masa durumu başarıyla güncellendi.');
    }

    /**
     * Get tables for API
     */
    public function getTables(Request $request)
    {
        $query = Table::query();

        // Filter by active status
        if ($request->has('active_only') && $request->active_only) {
            $query->where('is_active', true);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search by table number
        if ($request->has('search') && $request->search) {
            $query->where('table_number', 'like', '%' . $request->search . '%');
        }

        $tables = $query->orderBy('table_number', 'asc')->get();

        return $this->successResponse($tables);
    }

    /**
     * Get single table
     */
    public function getTable($id)
    {
        $table = Table::find($id);

        if (!$table) {
            return $this->notFoundResponse('Masa bulunamadı.');
        }

        return $this->successResponse($table);
    }

    /**
     * Get table availability for a specific date/time
     */
    public function getTableAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'duration' => 'nullable|integer|min:30|max:480' // minutes
        ]);

        $date = $request->date;
        $time = $request->time;
        $duration = $request->duration ?? 120; // default 2 hours

        $startDateTime = $date . ' ' . $time;
        $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime . ' +' . $duration . ' minutes'));

        // Get all active tables
        $allTables = Table::where('is_active', true)
            ->where('status', '!=', 'maintenance')
            ->get();

        // Get reserved tables for the time period
        $reservedTables = DB::table('reservations')
            ->where('status', 'confirmed')
            ->where(function($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('reservation_date', [$startDateTime, $endDateTime])
                      ->orWhere(function($q) use ($startDateTime, $endDateTime) {
                          $q->where('reservation_date', '<=', $startDateTime)
                            ->whereRaw('DATE_ADD(reservation_date, INTERVAL duration MINUTE) >= ?', [$startDateTime]);
                      });
            })
            ->pluck('table_id')
            ->toArray();

        // Get occupied tables
        $occupiedTables = DB::table('orders')
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->where('created_at', '>=', now()->subHours(4)) // Consider orders from last 4 hours
            ->pluck('table_id')
            ->toArray();

        $unavailableTables = array_unique(array_merge($reservedTables, $occupiedTables));

        $availableTables = $allTables->filter(function($table) use ($unavailableTables) {
            return !in_array($table->id, $unavailableTables);
        });

        return $this->successResponse([
            'available_tables' => $availableTables->values(),
            'unavailable_tables' => $allTables->whereIn('id', $unavailableTables)->values(),
            'total_tables' => $allTables->count(),
            'available_count' => $availableTables->count()
        ]);
    }
}
