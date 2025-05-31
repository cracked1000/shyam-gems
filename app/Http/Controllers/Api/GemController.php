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
          $gems = Gem::where('seller_id', auth()->id())->get();
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
          if ($gem->seller_id !== auth()->id()) {
              abort(403, 'Unauthorized');
          }
          return response()->json($gem);
      }
  }