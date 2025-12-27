<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IncomeCategoryController extends Controller
{
    /**
     * Display a listing of income categories.
     */
    public function index()
    {
            $incomeCategories = DB::table('income_categories')
                ->orderBy('name')
                ->get();


            return view('financial.income_categories.index', compact('incomeCategories'));
         
    }

    /**
     * Show the form for creating a new income category.
     */
    public function create()
    {
        return view('financial.income_categories.create');
    }

    /**
     * Store a newly created income category.
     */
    public function store(Request $request)
    {
       
        // No validation - direct processing

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
            'created_at' => now(),
            'updated_at' => now()
        ];

        $incomeCategoryId = DB::table('income_categories')->insertGetId($data);

        return redirect()->route('income_categories.index')
            ->with('success', 'Gelir kategorisi başarıyla oluşturuldu.');
    }

    /**
     * Display the specified income category.
     */
    public function show($id)
    {
        $incomeCategory = DB::table('income_categories')->where('id', $id)->first();

        if (!$incomeCategory) {
            return redirect()->route('income_categories.index')
                ->with('error', 'Gelir kategorisi bulunamadı.');
        }

        // Get related incomes count
        $incomesCount = DB::table('incomes')
            ->where('income_category_id', $id)
            ->count();

        // Get total amount for this income category
        $totalAmount = DB::table('incomes')
            ->where('income_category_id', $id)
            ->sum('amount');

        return view('financial.income_categories.show', compact('incomeCategory', 'incomesCount', 'totalAmount'));
    }

    /**
     * Show the form for editing the specified income category.
     */
    public function edit($id)
    {
        $incomeCategory = DB::table('income_categories')->where('id', $id)->first();

        if (!$incomeCategory) {
            return redirect()->route('income_categories.index')
                ->with('error', 'Gelir kategorisi bulunamadı.');
        }

        return view('financial.income_categories.edit', compact('incomeCategory'));
    }

    /**
     * Update the specified income category.
     */
    public function update(Request $request, $id)
    {
        $incomeCategory = DB::table('income_categories')->where('id', $id)->first();

        if (!$incomeCategory) {
            return redirect()->route('income_categories.index')
                ->with('error', 'Gelir kategorisi bulunamadı.');
        }

        // No validation - direct processing

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
            'updated_at' => now()
        ];

        DB::table('income_categories')->where('id', $id)->update($data);

        return redirect()->route('income_categories.index')
            ->with('success', 'Gelir kategorisi başarıyla güncellendi.');
    }

    /**
     * Remove the specified income category.
     */
    public function destroy($id)
    {
        $incomeCategory = DB::table('income_categories')->where('id', $id)->first();

        if (!$incomeCategory) {
            return redirect()->route('income_categories.index')
                ->with('error', 'Gelir kategorisi bulunamadı.');
        }

        // Check if income category is used in any incomes
        $incomesCount = DB::table('incomes')->where('income_category_id', $id)->count();

        if ($incomesCount > 0) {
            return redirect()->route('income_categories.index')
                ->with('error', 'Bu gelir kategorisi ' . $incomesCount . ' gelir kaydında kullanıldığı için silinemez. Önce bu gelirleri silin veya başka bir gelir kategorisine taşıyın.');
        }

        DB::table('income_categories')->where('id', $id)->delete();

        return redirect()->route('income_categories.index')
            ->with('success', 'Gelir kategorisi başarıyla silindi.');
    }

    /**
     * Toggle active status of income category.
     */
    public function toggleStatus($id)
    {
        $incomeCategory = DB::table('income_categories')->where('id', $id)->first();

        if (!$incomeCategory) {
            return response()->json(['success' => false, 'message' => 'Gelir kategorisi bulunamadı.']);
        }

        $newStatus = !$incomeCategory->is_active;
        
        DB::table('income_categories')->where('id', $id)->update([
            'is_active' => $newStatus,
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gelir kategorisi durumu başarıyla güncellendi.',
            'is_active' => $newStatus
        ]);
    }
}