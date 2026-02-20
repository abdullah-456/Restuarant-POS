@extends('layouts.admin')

@section('title', 'Add Category')
@section('page-title', 'Add Category')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">New Category</h2>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-gray-300 text-blue-600" @checked(old('is_active', true))>
                <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

