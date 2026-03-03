@extends('layouts.admin')

@section('title', 'Manage Menu Items')
@section('page-title', 'Manage Menu Items')

@section('content')
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <div>
                <h2 class="text-lg md:text-xl font-semibold text-gray-800">Menu Items</h2>
                <p class="text-sm text-gray-500">Manage all items available for ordering.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.menu-items.create-deal') }}"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm md:text-base shadow-sm hover:bg-emerald-700 transition font-bold">
                    <i class="fas fa-handshake mr-2"></i>Create a Deal
                </a>
                <a href="{{ route('admin.menu-items.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm md:text-base shadow-sm hover:bg-blue-700 transition font-bold">
                    <i class="fas fa-plus mr-2"></i>Add Menu Item
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm md:text-base">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs md:text-sm font-semibold text-gray-600">
                        <th class="px-3 md:px-4 py-2 md:py-3">Name</th>
                        <th class="px-3 md:px-4 py-2 md:py-3">Category</th>
                        <th class="px-3 md:px-4 py-2 md:py-3">Price</th>
                        <th class="px-3 md:px-4 py-2 md:py-3">Active</th>
                        <th class="px-3 md:px-4 py-2 md:py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-3 md:px-4 py-2 md:py-3">
                                <div class="flex items-center gap-3">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                            class="w-10 h-10 rounded object-cover hidden sm:block">
                                    @endif
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-medium text-gray-800">{{ $item->name }}</p>
                                            @if($item->is_deal)
                                                <span
                                                    class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">Deal</span>
                                            @endif
                                        </div>
                                        @if($item->description)
                                            <p class="text-xs text-gray-500 truncate max-w-xs">{{ $item->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 md:px-4 py-2 md:py-3">{{ $item->category?->name ?? '-' }}</td>
                            <td class="px-3 md:px-4 py-2 md:py-3">Rs. {{ number_format($item->price, 2) }}</td>
                            <td class="px-3 md:px-4 py-2 md:py-3">
                                @if($item->is_active)
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-700 font-semibold">Active</span>
                                @else
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-gray-50 text-gray-600 font-semibold">Inactive</span>
                                @endif
                            </td>
                            <td class="px-3 md:px-4 py-2 md:py-3 text-right">
                                <div class="inline-flex gap-2">
                                    @if($item->is_deal)
                                        <a href="{{ route('admin.menu-items.edit-deal', $item) }}"
                                            class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 rounded text-xs font-medium text-blue-700 transition">
                                            Edit
                                        </a>
                                    @else
                                        <a href="{{ route('admin.menu-items.edit', $item) }}"
                                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-xs font-medium text-gray-700 transition">
                                            Edit
                                        </a>
                                    @endif
                                    <form id="del-item-{{ $item->id }}" action="{{ route('admin.menu-items.destroy', $item) }}"
                                        method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button"
                                        onclick="showConfirm('Delete Menu Item', 'Delete \'{{ addslashes($item->name) }}\'? This cannot be undone.', function(){ document.getElementById('del-item-{{ $item->id }}').submit(); })"
                                        class="px-3 py-1.5 bg-red-100 hover:bg-red-200 rounded text-xs font-medium text-red-700 transition">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 md:px-4 py-6 text-center text-gray-500 text-sm">
                                No menu items defined.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
@endsection