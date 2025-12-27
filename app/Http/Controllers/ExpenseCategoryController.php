<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenseCategories = DB::table('expense_categories')
            ->orderBy('name')
            ->paginate(20);

        return view('financial.expense_categories.index', compact('expenseCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('financial.expense_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            DB::table('expense_categories')->insert([
                'name'        => $request->name,
                'description' => $request->description,
                'is_active'   => $request->has('is_active') ? 1 : 0,
                'created_at'  => now(),
                'updated_at'  => now()
            ]);
        });

        Log::info('[Expense Category] Created successfully', [
            'name'    => $request->name,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('expense_categories.index')
            ->with('success', 'Gider kategorisi başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $expenseCategory = DB::table('expense_categories')->where('id', $id)->first();
        
        if (!$expenseCategory) {
            return redirect()->route('expense_categories.index')
                ->with('error', 'Gider kategorisi bulunamadı.');
        }

        return view('financial.expense_categories.show', compact('expenseCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $expenseCategory = DB::table('expense_categories')->where('id', $id)->first();
        
        if (!$expenseCategory) {
            return redirect()->route('expense_categories.index')
                ->with('error', 'Gider kategorisi bulunamadı.');
        }

        return view('financial.expense_categories.edit', compact('expenseCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $expenseCategory = DB::table('expense_categories')->where('id', $id)->first();
        
        if (!$expenseCategory) {
            return redirect()->route('expense_categories.index')
                ->with('error', 'Gider kategorisi bulunamadı.');
        }

        DB::transaction(function () use ($request, $id) {
            DB::table('expense_categories')
                ->where('id', $id)
                ->update([
                    'name'        => $request->name,
                    'description' => $request->description,
                    'is_active'   => $request->has('is_active') ? 1 : 0,
                    'updated_at'  => now()
                ]);
        });

        Log::info('[Expense Category] Updated successfully', [
            'id'      => $id,
            'name'    => $request->name,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('expense_categories.index')
            ->with('success', 'Gider kategorisi başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expenseCategory = DB::table('expense_categories')->where('id', $id)->first();
        
        if (!$expenseCategory) {
            return redirect()->route('expense_categories.index')
                ->with('error', 'Gider kategorisi bulunamadı.');
        }

        $expenseCount = DB::table('expenses')->where('expense_category_id', $id)->count();
        
        if ($expenseCount > 0) {
            return redirect()->route('expense_categories.index')
                ->with('error', 'Bu gider kategorisi kullanımda olduğu için silinemez. ('
                    . $expenseCount . ' gider kaydında kullanılıyor)');
        }

        DB::transaction(function () use ($id) {
            DB::table('expense_categories')->where('id', $id)->delete();
        });

        Log::info('[Expense Category] Deleted successfully', [
            'id'      => $id,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('expense_categories.index')
            ->with('success', 'Gider kategorisi başarıyla silindi.');
    }
}
