<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'type', 'category_id', 'date_from', 'date_to']);

        $transactions = $request->user()
            ->transactions()
            ->with('category:id,name,color')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('description', 'like', "%{$search}%");
            })
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($filters['date_from'] ?? null, function ($query, $dateFrom) {
                $query->whereDate('date', '>=', $dateFrom);
            })
            ->when($filters['date_to'] ?? null, function ($query, $dateTo) {
                $query->whereDate('date', '<=', $dateTo);
            })
            ->latest('date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $categories = $request->user()
            ->categories()
            ->select('id', 'name', 'color')
            ->orderBy('name')
            ->get();

        return Inertia::render('transactions/Index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): Response
    {
        $categories = $request->user()
            ->categories()
            ->select('id', 'name', 'color')
            ->orderBy('name')
            ->get();

        return Inertia::render('transactions/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        $request->user()->transactions()->create($request->validated());

        return to_route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Request $request, Transaction $transaction): Response
    {
        $this->authorize('update', $transaction);

        $categories = $request->user()
            ->categories()
            ->select('id', 'name', 'color')
            ->orderBy('name')
            ->get();

        return Inertia::render('transactions/Edit', [
            'transaction' => $transaction->load('category:id,name,color'),
            'categories' => $categories,
        ]);
    }

    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        $transaction->update($request->validated());

        return to_route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);

        $transaction->delete();

        return to_route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
