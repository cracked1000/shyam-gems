<x-app-layout>
      <x-slot name="header">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add New Gem</h2>
      </x-slot>
      <div class="py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                  <form method="POST" action="{{ route('gems.store') }}" enctype="multipart/form-data">
                      @csrf
                      <div>
                          <x-label for="name" value="Name" />
                          <x-input id="name" type="text" name="name" required />
                      </div>
                      <div class="mt-4">
                          <x-label for="description" value="Description" />
                          <textarea id="description" name="description" class="block mt-1 w-full" required></textarea>
                      </div>
                      <div class="mt-4">
                          <x-label for="image" value="Image" />
                          <input type="file" id="image" name="image" required />
                      </div>
                      <div class="mt-4">
                          <x-button type="submit">Save</x-button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </x-app-layout>