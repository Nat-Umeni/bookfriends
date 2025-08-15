<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $booksByStatus = $user
            ? $user->books()->get()->groupBy('pivot.status')
            : collect();

        // Build a dumb, renderable structure for the view
        $sections = collect(BookUser::rawAllowedStatuses())
            ->map(fn(string $label, string $key) => [
                'key' => $key,
                'label' => $label,
                'books' => $booksByStatus->get($key, collect()),
            ])
            ->values();

        return view('home', compact('sections'));
    }

    public function create()
    {
        return view('books.create', [
            'statuses' => BookUser::rawAllowedStatuses(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:1',
            'author' => 'required|string|min:1',
            'status' => ['required', 'string', Rule::in(BookUser::allowedStatuses())],
        ]);

        // create the book (only title/author belong on the book)
        $book = Book::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
        ]);

        // attach pivot status to the current user
        $request->user()->books()->attach($book->id, [
            'status' => $validated['status'],
        ]);

        return redirect()->route('home');
    }

    public function edit(Request $request, Book $book)
    {
        Gate::authorize('update', $book);

        $userBook = $request->user()
            ? $request->user()->books()->whereKey($book->id)->first()
            : null;

        $selectedStatus = $userBook?->pivot?->status;

        return view('books.edit', [
            'book' => $book,
            'statuses' => BookUser::rawAllowedStatuses(),
            'selectedStatus' => $selectedStatus,
        ]);
    }
}
