<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IncomeTypeController extends Controller
{
    /**
     * Display a listing of income types.
     */
    public function index()
    {
        $incomeTypes = DB::table('income_types')
            ->orderBy('name')
            ->get();

        return view('financial.income_types.index', compact('incomeTypes'));
    }

    /**
     * Show the form for creating a new income type.
     */
    public function create()
    {
        return view('financial.income_types.create');
    }

    /**
     * Store a newly created income type.
     */
    public function store(Request $request)
    {
        // No validation - direct processing

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
            'sort_order' => $request->sort_order ?? 0,
            'created_at' => now(),
            'updated_at' => now()
        ];

        $incomeTypeId = DB::table('income_types')->insertGetId($data);

        return redirect()->route('income_types.index')
            ->with('success', 'Gelir tipi başarıyla oluşturuldu.');
    }

    /**
     * Display the specified income type.
     */
    public function show($id)
    {
        $incomeType = DB::table('income_types')->where('id', $id)->first();

        if (!$incomeType) {
            return redirect()->route('income_types.index')
                ->with('error', 'Gelir tipi bulunamadı.');
        }

        // Get related incomes count
        $incomesCount = DB::table('incomes')
            ->where('income_type_id', $id)
            ->count();

        // Get total amount for this income type
        $totalAmount = DB::table('incomes')
            ->where('income_type_id', $id)
            ->sum('amount');

        return view('financial.income_types.show', compact('incomeType', 'incomesCount', 'totalAmount'));
    }

    /**
     * Show the form for editing the specified income type.
     */
    public function edit($id)
    {
        $incomeType = DB::table('income_types')->where('id', $id)->first();

        if (!$incomeType) {
            return redirect()->route('income_types.index')
                ->with('error', 'Gelir tipi bulunamadı.');
        }

        return view('financial.income_types.edit', compact('incomeType'));
    }

    /**
     * Update the specified income type.
     */
    public function update(Request $request, $id)
    {
        $incomeType = DB::table('income_types')->where('id', $id)->first();

        if (!$incomeType) {
            return redirect()->route('income_types.index')
                ->with('error', 'Gelir tipi bulunamadı.');
        }

        // No validation - direct processing

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
            'sort_order' => $request->sort_order ?? 0,
            'updated_at' => now()
        ];

        DB::table('income_types')->where('id', $id)->update($data);

        return redirect()->route('income_types.index')
            ->with('success', 'Gelir tipi başarıyla güncellendi.');
    }

    /**
     * Remove the specified income type.
     */
    public function destroy($id)
    {
        $incomeType = DB::table('income_types')->where('id', $id)->first();

        if (!$incomeType) {
            return redirect()->route('income_types.index')
                ->with('error', 'Gelir tipi bulunamadı.');
        }

   

        DB::table('income_types')->where('id', $id)->delete();

        return redirect()->route('income_types.index')
            ->with('success', 'Gelir tipi başarıyla silindi.');
    }

    /**
     * Toggle active status of income type.
     */
    public function toggleStatus($id)
    {
        $incomeType = DB::table('income_types')->where('id', $id)->first();

        if (!$incomeType) {
            return response()->json(['success' => false, 'message' => 'Gelir tipi bulunamadı.']);
        }

        $newStatus = !$incomeType->is_active;
        
        DB::table('income_types')->where('id', $id)->update([
            'is_active' => $newStatus,
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gelir tipi durumu başarıyla güncellendi.',
            'is_active' => $newStatus
        ]);
    }
}

