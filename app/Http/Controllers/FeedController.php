<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        dd($request->user()->booksOfFriends);
        
        return view('feed.index', [
            'books' => $request->user()->booksOfFriends
        ]);
    }
}
