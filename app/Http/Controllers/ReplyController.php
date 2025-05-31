<?php
namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'seller') {
            abort(403, 'Only sellers can reply.');
        }

        $request->validate([
            'content' => 'required|string',
            'requirement_id' => 'required|exists:requirements,id',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('replies', 'public');
        }

        Reply::create([
            'content' => $request->content,
            'image' => $imagePath,
            'user_id' => auth()->id(),
            'requirement_id' => $request->requirement_id,
        ]);

        return back()->with('success', 'Reply posted.');
    }
}