<x-app-layout>
      <x-slot name="header">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">Post New Requirement</h2>
      </x-slot>
      <div class="py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                  <form method="POST" action="{{ route('requirements.store') }}">
                      @csrf
                      <div>
                          <x-label for="title" value="Title" />
                          <x-input id="title" type="text" name="title" required />
                      </div>
                      <div class="mt-4">
                          <x-label for="description" value="Description" />
                          <textarea id="description" name="description" class="block mt-1 w-full" required></textarea>
                      </div>
                      <div class="mt-4">
                          <x-button type="submit">Post</x-button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </x-app-layout>