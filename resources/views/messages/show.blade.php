<x-app-layout>
      <x-slot name="header">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">Conversation with {{ $user->name }}</h2>
      </x-slot>
      <div class="py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                  @foreach ($messages as $message)
                      <div class="mt-2 p-2 {{ $message->sender_id == auth()->id() ? 'text-right' : 'text-left' }}">
                          <p>{{ $message->content }}</p>
                          <p class="text-sm text-gray-500">{{ $message->sender->name }} at {{ $message->created_at }}</p>
                      </div>
                  @endforeach
                  <form method="POST" action="{{ route('messages.store', $user) }}" class="mt-4">
                      @csrf
                      <textarea name="content" class="block w-full" required></textarea>
                      <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Send</button>
                  </form>
              </div>
          </div>
      </div>
  </x-app-layout>