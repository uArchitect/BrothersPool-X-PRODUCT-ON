<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EmployeeGroupsController extends Controller
{
    public function index()
    {
        $groups = DB::table('employee_groups')
            ->orderBy('name')
            ->get();
        
        return view('hr.groups.index', compact('groups'));
    }

    public function create()
    {
        return view('hr.groups.create');
    }

    public function store(Request $request)
    {
        // Çoklu ekleme desteği
        if ($request->has('groups') && is_array($request->groups)) {
            return $this->storeMultiple($request);
        }
        
        // Tekli ekleme (geriye dönük uyumluluk)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:employee_groups,name',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Form doğrulama hatası!');
        }

        try {
            DB::table('employee_groups')->insert([
                'name' => $request->name,
                'description' => $request->description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('hr.groups.index')
                ->with('success', 'Grup başarıyla eklendi');
        } catch (\Exception $e) {
            Log::error('Grup ekleme hatası: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Grup eklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    private function storeMultiple(Request $request)
    {
        $groups = $request->groups;
        $errors = [];
        $successCount = 0;
        $failedCount = 0;

        foreach ($groups as $index => $group) {
            // Boş satırları atla
            if (empty($group['name']) || trim($group['name']) === '') {
                continue;
            }

            $validator = Validator::make($group, [
                'name' => 'required|string|max:255|unique:employee_groups,name',
                'description' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                $errors[] = "Satır " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                $failedCount++;
                continue;
            }

            try {
                DB::table('employee_groups')->insert([
                    'name' => $group['name'],
                    'description' => $group['description'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $successCount++;
            } catch (\Exception $e) {
                Log::error('Grup ekleme hatası (Satır ' . ($index + 1) . '): ' . $e->getMessage());
                $errors[] = "Satır " . ($index + 1) . ": " . $e->getMessage();
                $failedCount++;
            }
        }

        if ($successCount > 0) {
            $message = $successCount . ' grup başarıyla eklendi.';
            if ($failedCount > 0) {
                $message .= ' ' . $failedCount . ' grup eklenemedi.';
            }
            return redirect()->route('hr.groups.index')
                ->with('success', $message)
                ->with('errors', $errors);
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Hiçbir grup eklenemedi!')
                ->with('errors', $errors);
        }
    }

    public function edit($id)
    {
        $group = DB::table('employee_groups')->where('id', $id)->first();
        
        if (!$group) {
            return redirect()->route('hr.groups.index')
                ->with('error', 'Grup bulunamadı!');
        }

        return view('hr.groups.edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:employee_groups,name,' . $id,
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Form doğrulama hatası!');
        }

        try {
            DB::table('employee_groups')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'updated_at' => now(),
                ]);

            return redirect()->route('hr.groups.index')
                ->with('success', 'Grup başarıyla güncellendi');
        } catch (\Exception $e) {
            Log::error('Grup güncelleme hatası: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Grup güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // Gruba ait pozisyonlar var mı kontrol et
            $positionsCount = DB::table('employee_positions')
                ->where('group_id', $id)
                ->count();

            if ($positionsCount > 0) {
                return redirect()->back()
                    ->with('error', 'Bu gruba ait ' . $positionsCount . ' pozisyon bulunmaktadır. Önce pozisyonları silin!');
            }

            // Gruba ait personeller var mı kontrol et
            $employeesCount = DB::table('employees')
                ->where('group_id', $id)
                ->count();

            if ($employeesCount > 0) {
                return redirect()->back()
                    ->with('error', 'Bu gruba ait ' . $employeesCount . ' personel bulunmaktadır. Önce personelleri başka gruba taşıyın!');
            }

            DB::table('employee_groups')->where('id', $id)->delete();

            return redirect()->route('hr.groups.index')
                ->with('success', 'Grup başarıyla silindi');
        } catch (\Exception $e) {
            Log::error('Grup silme hatası: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Grup silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}

