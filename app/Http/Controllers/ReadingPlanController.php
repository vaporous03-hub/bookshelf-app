<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $books = Auth::user()->books; 
        return view('reading-plans.create', compact('books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'target_date' => 'required|date',
        ]);

        Auth::user()->readingPlans()->create([
            'book_id' => $request->book_id,
            'target_date' => $request->target_date,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を作成しました。');
    }

    public function edit(ReadingPlan $readingPlan)
    {
        return view('reading-plans.edit', compact('readingPlan'));
    }

    public function update(Request $request, ReadingPlan $readingPlan)
    {
        $request->validate([
            'target_date' => 'required|date',
        ]);

        $readingPlan->update([
            'target_date' => $request->target_date,
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を更新しました。');
    }

    public function complete(ReadingPlan $readingPlan)
    {
        $readingPlan->update([
            'status' => ReadingPlanStatus::Completed,
            'completed_at' => now(),
        ]);

        return redirect()->route('reading-plans.index')->with('success', '書籍を読了しました！');
    }

    public function destroy(ReadingPlan $readingPlan)
    {
        $readingPlan->delete();
        return redirect()->route('reading-plans.index')->with('success', '読書計画を削除しました。');
    }
}