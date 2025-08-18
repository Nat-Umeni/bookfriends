<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FriendsController extends Controller
{
    public function index(Request $request)
    {
        $pendingRequests = $request->user()->pendingFriendsOfMine;
        return view('friends.index', compact('pendingRequests'));
    }
}
