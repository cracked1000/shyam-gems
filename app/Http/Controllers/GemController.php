<?php

namespace App\Http\Controllers;

use App\Models\Gem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GemController extends Controller
{
    public function index()
    {
    $gems = Gem::where('seller_id', auth()->id())->get();
    return view('gems.index', [
        'gems' => $gems,
    ])->layout('layouts.app');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        Gem::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'seller_id' => Auth::id(),
        ]);

        return redirect()->route('gems.index')->with('success', 'Gem added successfully.');
    }
}