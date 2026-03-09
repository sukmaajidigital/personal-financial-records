<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        // Parse filters
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $month = $request->input('month');
        $year = $request->input('year', now()->year);
        $categoryId = $request->input('category_id');
        $type = $request->input('type'); // income, expense, or null (all)

        // Build base query with filters
        $baseQuery = $user->transactions();

        if ($dateFrom && $dateTo) {
            $baseQuery->whereBetween('date', [$dateFrom, $dateTo]);
        } elseif ($month && $year) {
            $baseQuery->whereYear('date', $year)->whereMonth('date', $month);
        } elseif ($year) {
            $baseQuery->whereYear('date', $year);
        }

        if ($categoryId) {
            $baseQuery->where('category_id', $categoryId);
        }

        if ($type && in_array($type, ['income', 'expense'])) {
            $baseQuery->where('type', $type);
        }

        // Summary
        $summary = (clone $baseQuery)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense,
                COUNT(*) as total_transactions,
                COALESCE(AVG(CASE WHEN type = 'expense' THEN amount END), 0) as avg_expense
            ")
            ->first();

        $balance = (float) $summary->total_income - (float) $summary->total_expense;

        // Highest single transactions
        $highestExpense = (clone $baseQuery)->where('type', 'expense')
            ->with('category:id,name,color')
            ->orderByDesc('amount')
            ->first();

        $highestIncome = (clone $baseQuery)->where('type', 'income')
            ->with('category:id,name,color')
            ->orderByDesc('amount')
            ->first();

        // Daily trend (for line chart)
        $dailyTrend = (clone $baseQuery)
            ->selectRaw("
                DATE(date) as day,
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense
            ")
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Monthly trend (for bar chart)
        $monthlyTrend = (clone $baseQuery)
            ->selectRaw("
                DATE_FORMAT(date, '%Y-%m') as month,
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Expense by category
        $expenseByCategory = $this->getCategoryBreakdown($user, $baseQuery, 'expense');

        // Income by category
        $incomeByCategory = $this->getCategoryBreakdown($user, $baseQuery, 'income');

        // Top 5 highest expenses
        $topExpenses = (clone $baseQuery)->where('type', 'expense')
            ->with('category:id,name,color')
            ->orderByDesc('amount')
            ->limit(5)
            ->get();

        // Top 5 highest incomes
        $topIncomes = (clone $baseQuery)->where('type', 'income')
            ->with('category:id,name,color')
            ->orderByDesc('amount')
            ->limit(5)
            ->get();

        // Available categories for filter
        $categories = $user->categories()->select('id', 'name', 'color')->orderBy('name')->get();

        return Inertia::render('Analytics', [
            'summary' => [
                'totalIncome' => (float) $summary->total_income,
                'totalExpense' => (float) $summary->total_expense,
                'balance' => $balance,
                'totalTransactions' => (int) $summary->total_transactions,
                'avgExpense' => (float) $summary->avg_expense,
                'highestExpense' => $highestExpense,
                'highestIncome' => $highestIncome,
            ],
            'dailyTrend' => $dailyTrend,
            'monthlyTrend' => $monthlyTrend,
            'expenseByCategory' => $expenseByCategory,
            'incomeByCategory' => $incomeByCategory,
            'topExpenses' => $topExpenses,
            'topIncomes' => $topIncomes,
            'categories' => $categories,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'month' => $month,
                'year' => $year,
                'category_id' => $categoryId,
                'type' => $type,
            ],
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $baseQuery
     */
    private function getCategoryBreakdown($user, $baseQuery, string $type): \Illuminate\Support\Collection
    {
        return (clone $baseQuery)
            ->where('transactions.type', $type)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, categories.color, SUM(transactions.amount) as total, COUNT(*) as count')
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();
    }
}
