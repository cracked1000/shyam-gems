<x-app-layout>
      <x-slot name="header">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">Requirements Feed</h2>
      </x-slot>
      <div class="py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                  <a href="{{ route('requirements.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Post Requirement</a>
                  @foreach ($requirements as $requirement)
                      <div class="mt-4 p-4 border-b">
                          <h3>{{ $requirement->title }}</h3>
                          <p>{{ $requirement->description }}</p>
                          <p>Posted by: {{ $requirement->user->name }}</p>
                          <a href="{{ route('requirements.show', $requirement) }}" class="text-blue-500">View/Replies</a>
                      </div>
                  @endforeach
              </div>
          </div>
      </div>
  </x-app-layout>