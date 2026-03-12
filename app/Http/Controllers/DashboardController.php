<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        // Summary totals for current month
        $now = now();

        $monthlySummary = $user->transactions()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense,
                COUNT(*) as total_transactions
            ")
            ->first();

        $balance = $user->transactions()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END), 0) as balance
            ")
            ->value('balance');

        // Monthly trend (last 6 months)
        $monthlyTrend = $user->transactions()
            ->whereDate('date', '>=', now()->subMonths(5)->startOfMonth())
            ->get();

        $monthlyTrend = $monthlyTrend
            ->groupBy(fn ($transaction) => $transaction->date->format('Y-m'))
            ->map(fn ($transactions, $month) => [
                'month' => $month,
                'income' => (float) $transactions->where('type', 'income')->sum('amount'),
                'expense' => (float) $transactions->where('type', 'expense')->sum('amount'),
            ])
            ->sortBy('month')
            ->values();

        // Expense by category (current month)
        $expenseByCategory = $user->transactions()
            ->where('type', 'expense')
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, categories.color, SUM(transactions.amount) as total')
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        // Income by category (current month)
        $incomeByCategory = $user->transactions()
            ->where('type', 'income')
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, categories.color, SUM(transactions.amount) as total')
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        // Recent transactions (latest 5)
        $recentTransactions = $user->transactions()
            ->with('category:id,name,color')
            ->latest('date')
            ->latest('id')
            ->limit(5)
            ->get();

        // Planned income (draft, upcoming)
        $plannedIncome = $user->plannedTransactions()
            ->with('category:id,name,color')
            ->where('type', 'income')
            ->where('status', 'draft')
            ->orderBy('planned_date')
            ->limit(5)
            ->get();

        // Planned expense (draft, upcoming)
        $plannedExpense = $user->plannedTransactions()
            ->with('category:id,name,color')
            ->where('type', 'expense')
            ->where('status', 'draft')
            ->orderBy('planned_date')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'summary' => [
                'totalIncome' => (float) $monthlySummary->total_income,
                'totalExpense' => (float) $monthlySummary->total_expense,
                'balance' => (float) $balance,
                'totalTransactions' => (int) $monthlySummary->total_transactions,
            ],
            'monthlyTrend' => $monthlyTrend,
            'expenseByCategory' => $expenseByCategory,
            'incomeByCategory' => $incomeByCategory,
            'recentTransactions' => $recentTransactions,
            'plannedIncome' => $plannedIncome,
            'plannedExpense' => $plannedExpense,
        ]);
    }
}
