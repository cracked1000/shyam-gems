<x-app-layout>
      <x-slot name="header">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">Messages</h2>
      </x-slot>
      <div class="py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                  <h3>Select a user to message:</h3>
                  <ul>
                      @foreach ($users as $user)
                          <li><a href="{{ route('messages.show', $user) }}" class="text-blue-500">{{ $user->name }}</a></li>
                      @endforeach
                  </ul>
              </div>
          </div>
      </div>
  </x-app-layout>