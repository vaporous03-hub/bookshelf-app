<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReadingPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->readingPlans()->with('book');

        $currentStatus = $request->input('status');
        if ($currentStatus) {
            $query->where('status', $currentStatus);
        }

        $readingPlans = $query->latest()->get();

        return view('reading-plans.index', compact('readingPlans', 'currentStatus'));
    }

    public function create()
    {
        $books = Book::orderBy('title')->get();
        return view('reading-plans.create', compact('books'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'target_date' => ['required', 'date'],
        ]);

        Auth::user()->readingPlans()->create([
            'book_id' => $validated['book_id'],
            'target_date' => $validated['target_date'],
            'status' => ReadingPlanStatus::InProgress,
        ]);

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を作成しました。');
    }
    

   public function edit(ReadingPlan $readingPlan)
    {
        $this->authorizePlan($readingPlan);

        $readingPlan->load('book');

        return view('reading-plans.edit', compact('readingPlan'));
    }

    public function update(Request $request, ReadingPlan $readingPlan)
    {
        $this->authorizePlan($readingPlan);

        $validated = $request->validate([
            'target_date' => ['required', 'date'],
        ]);

        $readingPlan->update([
            'target_date' => $validated['target_date'],
        ]);

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を更新しました。');
    }

    public function complete(ReadingPlan $readingPlan)
    {
        $this->authorizePlan($readingPlan);

        $readingPlan->update([
            'status' => ReadingPlanStatus::Completed,
            'completed_at' => now(),
        ]);

        return redirect()->route('reading-plans.index')->with('success', '書籍を読了しました！');
    }

    public function destroy(ReadingPlan $readingPlan)
    {
        $this->authorizePlan($readingPlan);

        $readingPlan->delete();

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を削除しました。');
    }
    private function authorizePlan(ReadingPlan $readingPlan): void
    {
        if ($readingPlan->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
