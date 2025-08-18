<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FriendsController extends Controller
{
    public function index(Request $request)
    {
        return view('friends.index', [
            'pendingRequests' => $request->user()->pendingFriendsOfMine,
            'requestingFriends' => $request->user()->pendingFriendsOf,
            'friends' => $request->user()->friends
        ]);
    }
}
