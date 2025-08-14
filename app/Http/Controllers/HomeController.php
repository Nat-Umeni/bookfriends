<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $books = $user
            ? $user
                ->books()
                ->get()
                ->groupBy('pivot.status')
            : collect();

        $wantToRead = $books->get('WANT_TO_READ', collect());
        $reading = $books->get('READING', collect());
        $read = $books->get('READ', collect());
        
        return view('home', compact('wantToRead', 'reading', 'read'));
    }
}
