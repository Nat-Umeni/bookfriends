<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BookController extends Controller
{

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
}
