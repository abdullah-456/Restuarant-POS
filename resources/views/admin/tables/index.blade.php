@extends('layouts.admin')

@section('title', 'Manage Tables')
@section('page-title', 'Manage Tables')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Tables</h2>
            <p class="text-sm text-gray-500">Configure restaurant tables and capacity.</p>
        </div>
        <a href="{{ route('admin.tables.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm md:text-base shadow-sm hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Add Table
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm md:text-base">
            <thead>
                <tr class="bg-gray-50 text-left text-xs md:text-sm font-semibold text-gray-600">
                    <th class="px-3 md:px-4 py-2 md:py-3">Name</th>
                    <th class="px-3 md:px-4 py-2 md:py-3">Capacity</th>
                    <th class="px-3 md:px-4 py-2 md:py-3">Status</th>
                    <th class="px-3 md:px-4 py-2 md:py-3">Active</th>
                    <th class="px-3 md:px-4 py-2 md:py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tables as $table)
                    <tr>
                        <td class="px-3 md:px-4 py-2 md:py-3">{{ $table->name }}</td>
                        <td class="px-3 md:px-4 py-2 md:py-3">{{ $table->capacity }}</td>
                        <td class="px-3 md:px-4 py-2 md:py-3">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($table->status === 'occupied') bg-red-100 text-red-800
                                @elseif($table->status === 'reserved') bg-yellow-100 text-yellow-800
                                @elseif($table->status === 'cleaning') bg-gray-100 text-gray-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($table->status) }}
                            </span>
                        </td>
                        <td class="px-3 md:px-4 py-2 md:py-3">
                            @if($table->is_active)
                                <span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-700 font-semibold">Active</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-50 text-gray-600 font-semibold">Inactive</span>
                            @endif
                        </td>
                        <td class="px-3 md:px-4 py-2 md:py-3 text-right">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('admin.tables.edit', $table) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-xs font-medium text-gray-700">
                                    Edit
                                </a>
                                <form id="del-table-{{ $table->id }}" action="{{ route('admin.tables.destroy', $table) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button"
                                        onclick="showConfirm('Delete Table', 'Delete table \'{{ addslashes($table->name) }}\'? This cannot be undone.', function(){ document.getElementById('del-table-{{ $table->id }}').submit(); })"
                                        class="px-3 py-1.5 bg-red-100 hover:bg-red-200 rounded text-xs font-medium text-red-700">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 md:px-4 py-6 text-center text-gray-500 text-sm">
                            No tables defined.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tables->links() }}
    </div>
</div>
@endsection

