@extends('layouts.admin')

@section('title', 'Manage Categories')
@section('page-title', 'Manage Categories')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Categories</h2>
            <p class="text-sm text-gray-500">Group your menu items for easier browsing.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm md:text-base shadow-sm hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Add Category
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm md:text-base">
            <thead>
                <tr class="bg-gray-50 text-left text-xs md:text-sm font-semibold text-gray-600">
                    <th class="px-3 md:px-4 py-2 md:py-3">Name</th>
                    <th class="px-3 md:px-4 py-2 md:py-3">Active</th>
                    <th class="px-3 md:px-4 py-2 md:py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                    <tr>
                        <td class="px-3 md:px-4 py-2 md:py-3">{{ $category->name }}</td>
                        <td class="px-3 md:px-4 py-2 md:py-3">
                            @if($category->is_active)
                                <span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-700 font-semibold">Active</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-50 text-gray-600 font-semibold">Inactive</span>
                            @endif
                        </td>
                        <td class="px-3 md:px-4 py-2 md:py-3 text-right">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-xs font-medium text-gray-700">
                                    Edit
                                </a>
                                <form id="del-cat-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button"
                                        onclick="showConfirm('Delete Category', 'Delete category \'{{ addslashes($category->name) }}\'? Menu items in this category may be affected.', function(){ document.getElementById('del-cat-{{ $category->id }}').submit(); })"
                                        class="px-3 py-1.5 bg-red-100 hover:bg-red-200 rounded text-xs font-medium text-red-700">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-3 md:px-4 py-6 text-center text-gray-500 text-sm">
                            No categories defined.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection

