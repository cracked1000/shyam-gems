<?php

  namespace App\Http\Controllers\Api;

  use App\Http\Controllers\Controller;
  use App\Models\Gem;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Storage;

  class GemController extends Controller
  {
      public function index()
      {
        $userId = auth()->id();
        $gems = Gem::where(function($query) use ($userId) {
            $query->where('seller_id', $userId)
                ->orWhere('seller_id', (string) $userId);
        })->get();
        return response()->json($gems);
      }

      public function store(Request $request)
      {
          $request->validate([
              'name' => 'required|string|max:255',
              'description' => 'required|string',
              'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
          ]);

          $imagePath = $request->file('image')->store('gems', 'public');

          $gem = Gem::create([
              'name' => $request->name,
              'description' => $request->description,
              'image' => $imagePath,
              'seller_id' => auth()->id(),
          ]);

          return response()->json($gem, 201);
      }

      public function show(Gem $gem)
        {
            if ($gem->seller_id != auth()->id() && $gem->seller_id != (string) auth()->id()) {
                abort(403, 'Unauthorized');
            }
            return response()->json($gem);
        }
    
    public function debug()
    {
        $allGems = \App\Models\Gem::all();
        return response()->json([
            'authenticated_user_id' => auth()->id(),
            'authenticated_user_email' => auth()->user()->email,
            'total_gems_in_db' => \App\Models\Gem::count(),
            'gems_for_this_user' => \App\Models\Gem::where('seller_id', auth()->id())->count(),
            'all_gems_with_seller_ids' => $allGems->map(function($gem) {
                return [
                    'id' => $gem->id,
                    'name' => $gem->name,
                    'seller_id' => $gem->seller_id
                ];
            })
        ]);
    }
}