<?php
namespace App\Http\Controllers;

use App\Models\Requirement;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    public function index()
    {
        $requirements = Requirement::with('user')->get();
        return view('requirements.index', compact('requirements'));
    }

    public function create()
    {
        return view('requirements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('requirements', 'public');
        }

        Requirement::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('requirements.index')->with('success', 'Requirement posted.');
    }

    public function show(Requirement $requirement)
    {
        $requirement->load('replies.user');
        return view('requirements.show', compact('requirement'));
    }

    public function feed()
    {
        $requirements = Requirement::with(['user', 'replies.user'])->get();
        return view('feed', compact('requirements'));
    }
}