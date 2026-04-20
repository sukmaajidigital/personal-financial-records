<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlannedTransactionRequest;
use App\Models\PlannedTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlannedTransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'type']);
        $today = now()->toDateString();

        $plannedTransactions = $request->user()
            ->plannedTransactions()
            ->with('category:id,name,color')
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->where('type', $type);
            })
            ->orderByRaw("CASE WHEN status = 'posted' THEN 1 ELSE 0 END")
            ->orderByRaw("CASE WHEN status = 'draft' AND planned_date >= ? THEN 0 WHEN status = 'draft' THEN 1 ELSE 2 END", [$today])
            ->orderByRaw("CASE WHEN status = 'draft' AND planned_date >= ? THEN planned_date END ASC", [$today])
            ->orderByRaw("CASE WHEN status = 'draft' AND planned_date < ? THEN planned_date END DESC", [$today])
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $categories = $request->user()
            ->categories()
            ->select('id', 'name', 'color')
            ->orderBy('name')
            ->get();

        return Inertia::render('planned-transactions/Index', [
            'plannedTransactions' => $plannedTransactions,
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

        return Inertia::render('planned-transactions/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(PlannedTransactionRequest $request): RedirectResponse
    {
        $request->user()->plannedTransactions()->create($request->validated());

        return to_route('planned-transactions.index')
            ->with('success', 'Rencana transaksi berhasil ditambahkan.');
    }

    public function edit(Request $request, PlannedTransaction $plannedTransaction): Response
    {
        $this->authorize('update', $plannedTransaction);

        $categories = $request->user()
            ->categories()
            ->select('id', 'name', 'color')
            ->orderBy('name')
            ->get();

        return Inertia::render('planned-transactions/Edit', [
            'plannedTransaction' => $plannedTransaction->load('category:id,name,color'),
            'categories' => $categories,
        ]);
    }

    public function update(PlannedTransactionRequest $request, PlannedTransaction $plannedTransaction): RedirectResponse
    {
        $this->authorize('update', $plannedTransaction);

        $plannedTransaction->update($request->validated());

        return to_route('planned-transactions.index')
            ->with('success', 'Rencana transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, PlannedTransaction $plannedTransaction): RedirectResponse
    {
        $this->authorize('delete', $plannedTransaction);

        $plannedTransaction->delete();

        return to_route('planned-transactions.index')
            ->with('success', 'Rencana transaksi berhasil dihapus.');
    }

    /**
     * Post a planned transaction — creates a real transaction and marks as posted.
     */
    public function post(Request $request, PlannedTransaction $plannedTransaction): RedirectResponse
    {
        $this->authorize('post', $plannedTransaction);

        // Validate optional overrides from the review modal
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', 'string', 'in:income,expense'],
            'date' => ['required', 'date'],
        ]);

        // Create the actual transaction
        $request->user()->transactions()->create([
            'category_id' => $data['category_id'],
            'description' => $data['description'],
            'amount' => $data['amount'],
            'type' => $data['type'],
            'date' => $data['date'],
        ]);

        // Mark planned transaction as posted
        $plannedTransaction->update([
            'status' => 'posted',
            'posted_at' => now(),
        ]);

        return to_route('planned-transactions.index')
            ->with('success', 'Rencana transaksi berhasil diposting ke transaksi.');
    }
}
