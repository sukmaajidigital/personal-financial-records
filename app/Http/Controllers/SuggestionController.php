<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuggestionRequest;
use App\Models\Suggestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SuggestionController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['focus', 'status']);

        $suggestions = Suggestion::query()
            ->with('user:id,name,avatar')
            ->when($filters['focus'] ?? null, function ($query, $focus) {
                $query->where('focus', $focus);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('suggestions/Index', [
            'suggestions' => $suggestions,
            'filters' => $filters,
            'authUserId' => $request->user()->id,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('suggestions/Create');
    }

    public function store(SuggestionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('suggestions', 'public');
        }

        $request->user()->suggestions()->create($data);

        return to_route('suggestions.index')
            ->with('success', 'Saran berhasil dikirim. Terima kasih!');
    }

    public function destroy(Request $request, Suggestion $suggestion): RedirectResponse
    {
        // Only the owner can delete
        if ($suggestion->user_id !== $request->user()->id) {
            abort(403);
        }

        // Delete associated image
        if ($suggestion->image) {
            Storage::disk('public')->delete($suggestion->image);
        }

        $suggestion->delete();

        return to_route('suggestions.index')
            ->with('success', 'Saran berhasil dihapus.');
    }
}
