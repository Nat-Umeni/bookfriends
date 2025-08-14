<?php

namespace App\Http\Controllers;

use App\Models\BookUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $booksByStatus = $user
            ? $user
                ->books()
                ->get()
                ->groupBy('pivot.status')
            : collect();
            
        // Build a dumb, renderable structure for the view
        $sections = collect(BookUser::rawAllowedStatuses())
            ->map(fn(string $label, string $key) => [
                'key' => $key,
                'label' => $label,
                'items' => $booksByStatus->get($key, collect()),
            ])
            ->values();

        
        return view('home', compact('sections'));
    }
}
