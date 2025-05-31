<x-app-layout>
      <x-slot name="header">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Gems</h2>
      </x-slot>
      <div class="py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                  <a href="{{ route('gems.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Add Gem</a>
                  <table class="min-w-full mt-4">
                      <thead>
                          <tr>
                              <th>Name</th>
                              <th>Description</th>
                              <th>Image</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach ($gems as $gem)
                              <tr>
                                  <td>{{ $gem->name }}</td>
                                  <td>{{ $gem->description }}</td>
                                  <td><img src="{{ asset('storage/' . $gem->image) }}" width="100" alt="{{ $gem->name }}"></td>
                              </tr>
                          @endforeach
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  </x-app-layout>