<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Models\BookUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

    public function store(BookRequest $request)
    {
        // create the book (only title/author belong on the book)
        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
        ]);

        // attach pivot status to the current user
        $request->user()->books()->attach($book->id, [
            'status' => $request->status,
        ]);

        return redirect()->route('home');
    }

    public function edit(Request $request, Book $book)
    {
        Gate::authorize('update', $book);

        $selectedStatus = $book->users()
            ->whereKey($request->user()->id)
            ->value('book_user.status');

        return view('books.edit', [
            'book' => $book,
            'statuses' => BookUser::rawAllowedStatuses(),
            'selectedStatus' => $selectedStatus,
        ]);
    }

    public function update(BookRequest $request, Book $book)
    {
        $book->update($request->only(['title', 'author']));

        $request->user()->books()->updateExistingPivot($book->id, [
            'status' => $request->status
        ]);

        return redirect()->route('home');
    }
}
