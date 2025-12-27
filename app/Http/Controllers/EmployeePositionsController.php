<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EmployeePositionsController extends Controller
{
    public function index()
    {
        $positions = DB::table('employee_positions')
            ->join('employee_groups', 'employee_positions.group_id', '=', 'employee_groups.id')
            ->select('employee_positions.*', 'employee_groups.name as group_name')
            ->orderBy('employee_groups.name')
            ->orderBy('employee_positions.name')
            ->get();
        
        $groups = DB::table('employee_groups')
            ->orderBy('name')
            ->get();
        
        return view('hr.positions.index', compact('positions', 'groups'));
    }

    public function create()
    {
        $groups = DB::table('employee_groups')
            ->orderBy('name')
            ->get();
        
        return view('hr.positions.create', compact('groups'));
    }

    public function store(Request $request)
    {
        // Çoklu ekleme desteği
        if ($request->has('positions') && is_array($request->positions)) {
            return $this->storeMultiple($request);
        }
        
        // Tekli ekleme (geriye dönük uyumluluk)
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:employee_groups,id',
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = DB::table('employee_positions')
                        ->where('group_id', $request->group_id)
                        ->where('name', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Bu gruba ait bu pozisyon zaten mevcut!');
                    }
                }
            ],
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Form doğrulama hatası!');
        }

        try {
            DB::table('employee_positions')->insert([
                'group_id' => $request->group_id,
                'name' => $request->name,
                'description' => $request->description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('hr.positions.index')
                ->with('success', 'Pozisyon başarıyla eklendi');
        } catch (\Exception $e) {
            Log::error('Pozisyon ekleme hatası: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Pozisyon eklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    private function storeMultiple(Request $request)
    {
        $positions = $request->positions;
        $errors = [];
        $successCount = 0;
        $failedCount = 0;

        foreach ($positions as $index => $position) {
            // Boş satırları atla
            if (empty($position['group_id']) || empty($position['name']) || trim($position['name']) === '') {
                continue;
            }

            $validator = Validator::make($position, [
                'group_id' => 'required|exists:employee_groups,id',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($position) {
                        $exists = DB::table('employee_positions')
                            ->where('group_id', $position['group_id'])
                            ->where('name', $value)
                            ->exists();
                        if ($exists) {
                            $fail('Bu gruba ait bu pozisyon zaten mevcut!');
                        }
                    }
                ],
                'description' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                $errors[] = "Satır " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                $failedCount++;
                continue;
            }

            try {
                DB::table('employee_positions')->insert([
                    'group_id' => $position['group_id'],
                    'name' => $position['name'],
                    'description' => $position['description'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $successCount++;
            } catch (\Exception $e) {
                Log::error('Pozisyon ekleme hatası (Satır ' . ($index + 1) . '): ' . $e->getMessage());
                $errors[] = "Satır " . ($index + 1) . ": " . $e->getMessage();
                $failedCount++;
            }
        }

        if ($successCount > 0) {
            $message = $successCount . ' pozisyon başarıyla eklendi.';
            if ($failedCount > 0) {
                $message .= ' ' . $failedCount . ' pozisyon eklenemedi.';
            }
            return redirect()->route('hr.positions.index')
                ->with('success', $message)
                ->with('errors', $errors);
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Hiçbir pozisyon eklenemedi!')
                ->with('errors', $errors);
        }
    }

    public function edit($id)
    {
        $position = DB::table('employee_positions')
            ->join('employee_groups', 'employee_positions.group_id', '=', 'employee_groups.id')
            ->select('employee_positions.*', 'employee_groups.name as group_name')
            ->where('employee_positions.id', $id)
            ->first();
        
        if (!$position) {
            return redirect()->route('hr.positions.index')
                ->with('error', 'Pozisyon bulunamadı!');
        }

        $groups = DB::table('employee_groups')
            ->orderBy('name')
            ->get();

        return view('hr.positions.edit', compact('position', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:employee_groups,id',
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $id) {
                    $exists = DB::table('employee_positions')
                        ->where('group_id', $request->group_id)
                        ->where('name', $value)
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('Bu gruba ait bu pozisyon zaten mevcut!');
                    }
                }
            ],
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Form doğrulama hatası!');
        }

        try {
            DB::table('employee_positions')
                ->where('id', $id)
                ->update([
                    'group_id' => $request->group_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'updated_at' => now(),
                ]);

            return redirect()->route('hr.positions.index')
                ->with('success', 'Pozisyon başarıyla güncellendi');
        } catch (\Exception $e) {
            Log::error('Pozisyon güncelleme hatası: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Pozisyon güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // Bu pozisyona ait personeller var mı kontrol et
            $employeesCount = DB::table('employees')
                ->where('position_id', $id)
                ->count();

            if ($employeesCount > 0) {
                return redirect()->back()
                    ->with('error', 'Bu pozisyona ait ' . $employeesCount . ' personel bulunmaktadır. Önce personelleri başka pozisyona taşıyın!');
            }

            DB::table('employee_positions')->where('id', $id)->delete();

            return redirect()->route('hr.positions.index')
                ->with('success', 'Pozisyon başarıyla silindi');
        } catch (\Exception $e) {
            Log::error('Pozisyon silme hatası: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Pozisyon silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}

