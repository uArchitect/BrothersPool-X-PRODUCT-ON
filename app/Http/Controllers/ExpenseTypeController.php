<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenseTypes = DB::table('expense_types')
            ->orderBy('name')
            ->paginate(20);

        return view('financial.expense_types.index', compact('expenseTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('financial.expense_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // No validation - direct processing

        DB::beginTransaction();
        
        try {
            DB::table('expense_types')->insert([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();
            
            Log::info('[Expense Type] Created successfully', [
                'name' => $request->name,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('expense_types.index')
                ->with('success', 'Gider tipi başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('[Expense Type] Creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gider tipi oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $expenseType = DB::table('expense_types')->where('id', $id)->first();
        
        if (!$expenseType) {
            return redirect()->route('expense_types.index')
                ->with('error', 'Gider tipi bulunamadı.');
        }

        return view('financial.expense_types.show', compact('expenseType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $expenseType = DB::table('expense_types')->where('id', $id)->first();
        
        if (!$expenseType) {
            return redirect()->route('expense_types.index')
                ->with('error', 'Gider tipi bulunamadı.');
        }

        return view('financial.expense_types.edit', compact('expenseType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $expenseType = DB::table('expense_types')->where('id', $id)->first();
        
        if (!$expenseType) {
            return redirect()->route('expense_types.index')
                ->with('error', 'Gider tipi bulunamadı.');
        }

        // No validation - direct processing

        DB::beginTransaction();
        
        try {
            DB::table('expense_types')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'is_active' => $request->has('is_active') ? 1 : 0,
                    'updated_at' => now()
                ]);

            DB::commit();
            
            Log::info('[Expense Type] Updated successfully', [
                'id' => $id,
                'name' => $request->name,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('expense_types.index')
                ->with('success', 'Gider tipi başarıyla güncellendi.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('[Expense Type] Update failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gider tipi güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expenseType = DB::table('expense_types')->where('id', $id)->first();
        
        if (!$expenseType) {
            return redirect()->route('expense_types.index')
                ->with('error', 'Gider tipi bulunamadı.');
        }

        // Check if expense type is used in expenses
        $expenseCount = DB::table('expenses')->where('expense_type_id', $id)->count();
        
        if ($expenseCount > 0) {
            return redirect()->route('expense_types.index')
                ->with('error', 'Bu gider tipi kullanımda olduğu için silinemez. (' . $expenseCount . ' gider kaydında kullanılıyor)');
        }

        DB::beginTransaction();
        
        try {
            DB::table('expense_types')->where('id', $id)->delete();

            DB::commit();
            
            Log::info('[Expense Type] Deleted successfully', [
                'id' => $id,
                'name' => $expenseType->name,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('expense_types.index')
                ->with('success', 'Gider tipi başarıyla silindi.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('[Expense Type] Delete failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Gider tipi silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}