<?php

  namespace App\Http\Controllers\Api;

  use App\Http\Controllers\Controller;
  use App\Models\Requirement;
  use Illuminate\Http\Request;

  class RequirementController extends Controller
  {
      public function index()
      {
          $requirements = Requirement::with('user')->get();
          return response()->json($requirements);
      }

      public function store(Request $request)
      {
          $request->validate([
              'title' => 'required|string|max:255',
              'description' => 'required|string',
          ]);

          $requirement = Requirement::create([
              'title' => $request->title,
              'description' => $request->description,
              'user_id' => auth()->id(),
          ]);

          return response()->json($requirement, 201);
      }

      public function show(Requirement $requirement)
      {
          $requirement->load('replies.user');
          return response()->json($requirement);
      }
  }