<x-app-layout>
      <x-slot name="header">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">Requirement: {{ $requirement->title }}</h2>
      </x-slot>
      <div class="py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                  <p>{{ $requirement->description }}</p>
                  <p>Posted by: {{ $requirement->user->name }}</p>
                  <h3 class="mt-4">Replies</h3>
                  @foreach ($requirement->replies as $reply)
                      <div class="mt-2 p-2 border-b">
                          <p>{{ $reply->content }}</p>
                          <p>By: {{ $reply->user->name }}</p>
                      </div>
                  @endforeach
                  @if (auth()->user()->role === 'seller')
                      <form method="POST" action="{{ route('replies.store') }}" class="mt-4">
                          @csrf
                          <input type="hidden" name="requirement_id" value="{{ $requirement->id }}">
                          <textarea name="content" class="block w-full" required></textarea>
                          <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Reply</button>
                      </form>
                  @endif
              </div>
          </div>
      </div>
  </x-app-layout>