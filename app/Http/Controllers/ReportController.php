<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('financial.reports.index');
    }

    public function income(Request $request)
    {
        $query = DB::table('incomes')
            ->leftJoin('income_categories', 'incomes.income_category_id', '=', 'income_categories.id')
            ->select([
                'incomes.*',
                'income_categories.name as category_name'
            ]);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('incomes.date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('incomes.date', '<=', $request->end_date);
        }
        if ($request->filled('income_category_id')) {
            $query->where('incomes.income_category_id', $request->income_category_id);
        }

        $incomes = $query->orderBy('incomes.date', 'desc')
            ->orderBy('incomes.created_at', 'desc')
            ->get();

        // Calculate summary statistics
        $totalIncome = $incomes->sum('amount');
        $averageIncome = $incomes->count() > 0 ? $incomes->avg('amount') : 0;
        $count = $incomes->count();

        // Get top income categories
        $topCategories = DB::table('incomes')
            ->leftJoin('income_categories', 'incomes.income_category_id', '=', 'income_categories.id')
            ->select('income_categories.name as category_name', DB::raw('SUM(incomes.amount) as total'))
            ->groupBy('income_categories.id', 'income_categories.name');

        if ($request->filled('start_date')) {
            $topCategories->where('incomes.date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $topCategories->where('incomes.date', '<=', $request->end_date);
        }
        if ($request->filled('income_category_id')) {
            $topCategories->where('incomes.income_category_id', $request->income_category_id);
        }

        $topCategories = $topCategories->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Get monthly income trend
        $monthlyTrend = DB::table('incomes')
            ->select(DB::raw('DATE_FORMAT(date, "%Y-%m") as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Get income categories for filter
        $incomeCategories = DB::table('income_categories')->orderBy('name')->get();

        return view('financial.reports.income', compact(
            'incomes',
            'totalIncome',
            'averageIncome',
            'count',
            'topCategories',
            'monthlyTrend',
            'incomeCategories'
        ));
    }

    public function expense(Request $request)
    {
        $query = DB::table('expenses')
            ->leftJoin('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->select([
                'expenses.*',
                'expense_types.name as type_name',
                'expense_categories.name as category_name'
            ]);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('expenses.date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('expenses.date', '<=', $request->end_date);
        }
        if ($request->filled('expense_type_id')) {
            $query->where('expenses.expense_type_id', $request->expense_type_id);
        }
        if ($request->filled('expense_category_id')) {
            $query->where('expenses.expense_category_id', $request->expense_category_id);
        }

        $expenses = $query->orderBy('expenses.date', 'desc')
            ->orderBy('expenses.created_at', 'desc')
            ->get();

        // Calculate summary statistics
        $totalExpense = $expenses->sum('amount');
        $averageExpense = $expenses->count() > 0 ? $expenses->avg('amount') : 0;
        $count = $expenses->count();

        // Get top expense types
        $topTypes = DB::table('expenses')
            ->leftJoin('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->select('expense_types.name as type_name', DB::raw('SUM(expenses.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('expense_types.id', 'expense_types.name');

        if ($request->filled('start_date')) {
            $topTypes->where('expenses.date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $topTypes->where('expenses.date', '<=', $request->end_date);
        }
        if ($request->filled('expense_type_id')) {
            $topTypes->where('expenses.expense_type_id', $request->expense_type_id);
        }
        if ($request->filled('expense_category_id')) {
            $topTypes->where('expenses.expense_category_id', $request->expense_category_id);
        }

        $topTypes = $topTypes->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Get top expense categories
        $topCategories = DB::table('expenses')
            ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name as category_name', DB::raw('SUM(expenses.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('expense_categories.id', 'expense_categories.name');

        if ($request->filled('start_date')) {
            $topCategories->where('expenses.date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $topCategories->where('expenses.date', '<=', $request->end_date);
        }
        if ($request->filled('expense_type_id')) {
            $topCategories->where('expenses.expense_type_id', $request->expense_type_id);
        }
        if ($request->filled('expense_category_id')) {
            $topCategories->where('expenses.expense_category_id', $request->expense_category_id);
        }

        $topCategories = $topCategories->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Get monthly expense trend
        $monthlyTrend = DB::table('expenses')
            ->select(DB::raw('DATE_FORMAT(date, "%Y-%m") as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Get expense types and categories for filter
        $expenseTypes = DB::table('expense_types')->orderBy('name')->get();
        $expenseCategories = DB::table('expense_categories')->orderBy('name')->get();

        return view('financial.reports.expense', compact(
            'expenses',
            'totalExpense',
            'averageExpense',
            'count',
            'topTypes',
            'topCategories',
            'monthlyTrend',
            'expenseTypes',
            'expenseCategories'
        ));
    }
}

