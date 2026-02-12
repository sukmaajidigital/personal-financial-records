<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        // Summary totals for current month
        $currentMonth = now()->format('Y-m');

        $monthlySummary = $user->transactions()
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
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
            ->selectRaw("
                DATE_FORMAT(date, '%Y-%m') as month,
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Expense by category (current month)
        $expenseByCategory = $user->transactions()
            ->where('type', 'expense')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
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

        return Inertia::render('Dashboard', [
            'summary' => [
                'totalIncome' => (float) $monthlySummary->total_income,
                'totalExpense' => (float) $monthlySummary->total_expense,
                'balance' => (float) $balance,
                'totalTransactions' => (int) $monthlySummary->total_transactions,
            ],
            'monthlyTrend' => $monthlyTrend,
            'expenseByCategory' => $expenseByCategory,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
