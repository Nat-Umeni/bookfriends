<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFriendRequest;
use Illuminate\Http\Request;
use App\Models\User;

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

    public function store(StoreFriendRequest $request)
    {
        $friendToAdd = User::whereEmail($request->email)->first();
        $request->user()->addFriend($friendToAdd);

        return back();
    }
}
