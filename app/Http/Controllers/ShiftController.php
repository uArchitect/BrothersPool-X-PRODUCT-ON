<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftController extends Controller
{
    /**
     * Display the shift management page
     */
    public function index()
    {
        $shifts = DB::table('staff_shifts')
            ->join('employees', 'staff_shifts.employee_id', '=', 'employees.id')
            ->select('staff_shifts.*', 'employees.name as employee_name')
            ->orderBy('staff_shifts.shift_date', 'desc')
            ->orderBy('staff_shifts.start_time', 'asc')
            ->get();

        $employees = DB::table('employees')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $shiftStats = [
            'total_shifts' => $shifts->count(),
            'active_shifts' => $shifts->where('status', 'active')->count(),
            'completed_shifts' => $shifts->where('status', 'completed')->count(),
            'today_shifts' => $shifts->where('shift_date', now()->toDateString())->count()
        ];

        return view('shifts.index', compact('shifts', 'employees', 'shiftStats'));
    }

    /**
     * Create a new shift
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_duration' => 'nullable|integer|min:0|max:480', // minutes
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            // Check for overlapping shifts
            $overlappingShift = DB::table('staff_shifts')
                ->where('employee_id', $request->employee_id)
                ->where('shift_date', $request->shift_date)
                ->where('status', '!=', 'cancelled')
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                          ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                          ->orWhere(function($q) use ($request) {
                              $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                          });
                })
                ->first();

            if ($overlappingShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu çalışan için belirtilen saatlerde zaten bir vardiya var.'
                ], 400);
            }

            $shiftId = DB::table('staff_shifts')->insertGetId([
                'employee_id' => $request->employee_id,
                'shift_date' => $request->shift_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'break_duration' => $request->break_duration ?? 0,
                'notes' => $request->notes,
                'status' => 'scheduled',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vardiya başarıyla oluşturuldu.',
                'data' => ['id' => $shiftId]
            ]);

        } catch (\Exception $e) {
            Log::error('Shift creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update shift status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:scheduled,active,completed,cancelled'
        ]);

        try {
            $shift = DB::table('staff_shifts')->where('id', $id)->first();
            
            if (!$shift) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vardiya bulunamadı.'
                ], 404);
            }

            $updateData = [
                'status' => $request->status,
                'updated_at' => now()
            ];

            // Set actual times when starting or completing shift
            if ($request->status === 'active') {
                $updateData['actual_start_time'] = now()->format('H:i:s');
            } elseif ($request->status === 'completed') {
                $updateData['actual_end_time'] = now()->format('H:i:s');
            }

            DB::table('staff_shifts')->where('id', $id)->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Vardiya durumu güncellendi.'
            ]);

        } catch (\Exception $e) {
            Log::error('Shift status update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee shift history
     */
    public function getEmployeeShifts($employeeId)
    {
        $shifts = DB::table('staff_shifts')
            ->where('employee_id', $employeeId)
            ->orderBy('shift_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $shifts
        ]);
    }

    /**
     * Get today's shifts
     */
    public function getTodayShifts()
    {
        $shifts = DB::table('staff_shifts')
            ->join('employees', 'staff_shifts.employee_id', '=', 'employees.id')
            ->select('staff_shifts.*', 'employees.name as employee_name')
            ->where('shift_date', now()->toDateString())
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $shifts
        ]);
    }

    /**
     * Get shift statistics
     */
    public function getShiftStats(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $stats = DB::table('staff_shifts')
            ->join('employees', 'staff_shifts.employee_id', '=', 'employees.id')
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->selectRaw('
                employees.name as employee_name,
                COUNT(*) as total_shifts,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_shifts,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_shifts,
                AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time) - COALESCE(break_duration, 0)) as avg_shift_duration
            ')
            ->groupBy('employee_id', 'employees.name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Delete shift
     */
    public function destroy($id)
    {
        try {
            $shift = DB::table('staff_shifts')->where('id', $id)->first();
            
            if (!$shift) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vardiya bulunamadı.'
                ], 404);
            }

            if ($shift->status === 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktif vardiya silinemez.'
                ], 400);
            }

            DB::table('staff_shifts')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vardiya silindi.'
            ]);

        } catch (\Exception $e) {
            Log::error('Shift deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
