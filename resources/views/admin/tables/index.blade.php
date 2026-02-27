@extends('layouts.admin')

@section('title', 'Manage Tables')
@section('page-title', 'Manage Tables')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Restaurant Tables</h2>
            <p class="text-sm text-gray-500 mt-0.5">Configure and manage all restaurant tables and their capacity.</p>
        </div>
        <a href="{{ route('admin.tables.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
            <i class="fas fa-plus"></i> Add New Table
        </a>
    </div>

    {{-- Stats row --}}
    @php
        $totalTables = $tables->total();
        $availableTables = $tables->getCollection()->where('status','available')->count();
        $occupiedTables  = $tables->getCollection()->where('status','occupied')->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-gray-900">{{ $tables->total() }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Tables</p>
        </div>
        <div class="bg-white rounded-2xl border border-green-200 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-green-600">{{ $tables->getCollection()->where('status','available')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Available</p>
        </div>
        <div class="bg-white rounded-2xl border border-red-200 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-red-600">{{ $tables->getCollection()->where('status','occupied')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Occupied</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-blue-600">{{ $tables->getCollection()->where('is_active',true)->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Active</p>
        </div>
    </div>

    {{-- Mobile & Tablet (< 1024px) - Card Grid --}}
    <div class="lg:hidden">
        @if($tables->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
                <i class="fas fa-table text-4xl text-gray-200 mb-3 block"></i>
                <p class="text-gray-500">No tables defined yet.</p>
                <a href="{{ route('admin.tables.create') }}" class="mt-3 inline-flex items-center gap-1 text-blue-600 text-sm hover:underline"><i class="fas fa-plus"></i> Add your first table</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($tables as $table)
                    @php
                        $statusConfig = [
                            'occupied' => ['bg' => 'bg-red-100 text-red-800', 'dot' => 'bg-red-500'],
                            'reserved' => ['bg' => 'bg-yellow-100 text-yellow-800', 'dot' => 'bg-yellow-500'],
                            'cleaning' => ['bg' => 'bg-gray-100 text-gray-700', 'dot' => 'bg-gray-400'],
                        ][$table->status] ?? ['bg' => 'bg-green-100 text-green-800', 'dot' => 'bg-green-500'];
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-table text-blue-600 text-sm"></i>
                                    </div>
                                    <h3 class="font-bold text-gray-900 text-lg">{{ $table->name }}</h3>
                                </div>
                                <p class="text-sm text-gray-500 ml-10"><i class="fas fa-users mr-1"></i>Capacity: {{ $table->capacity }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusConfig['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                    {{ ucfirst($table->status) }}
                                </span>
                                @if($table->is_active)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-semibold border border-blue-100">Active</span>
                                @else
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-semibold">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2 pt-3 border-t border-gray-100">
                            <a href="{{ route('admin.tables.edit', $table) }}"
                               class="flex-1 text-center py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-xs font-semibold text-gray-700 transition">
                                <i class="fas fa-pen mr-1"></i>Edit
                            </a>
                            <form id="del-mobile-{{ $table->id }}" action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button"
                                    onclick="showConfirm('Delete Table', 'Delete table \'{{ addslashes($table->name) }}\'? This cannot be undone.', function(){ document.getElementById('del-mobile-{{ $table->id }}').submit(); })"
                                    class="flex-1 py-2 bg-red-50 hover:bg-red-100 rounded-xl text-xs font-semibold text-red-600 transition">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Desktop (>= 1024px) - Table View --}}
    <div class="hidden lg:block bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Table</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Capacity</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tables as $table)
                    @php
                        $statusConfig = [
                            'occupied' => ['bg' => 'bg-red-100 text-red-800', 'dot' => 'bg-red-500'],
                            'reserved' => ['bg' => 'bg-yellow-100 text-yellow-800', 'dot' => 'bg-yellow-500'],
                            'cleaning' => ['bg' => 'bg-gray-100 text-gray-700', 'dot' => 'bg-gray-400'],
                        ][$table->status] ?? ['bg' => 'bg-green-100 text-green-800', 'dot' => 'bg-green-500'];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-table text-blue-600 text-xs"></i>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $table->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $table->capacity }} persons</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $statusConfig['bg'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                {{ ucfirst($table->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($table->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold"><i class="fas fa-check text-[10px]"></i> Active</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-semibold"><i class="fas fa-minus text-[10px]"></i> Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.tables.edit', $table) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700 transition">
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                                <form id="del-desktop-{{ $table->id }}" action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button"
                                        onclick="showConfirm('Delete Table', 'Delete table \'{{ addslashes($table->name) }}\'? This cannot be undone.', function(){ document.getElementById('del-desktop-{{ $table->id }}').submit(); })"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg text-xs font-medium text-red-600 transition">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                            <i class="fas fa-table text-3xl mb-2 block"></i>
                            No tables defined yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $tables->links() }}
    </div>
</div>
@endsection
