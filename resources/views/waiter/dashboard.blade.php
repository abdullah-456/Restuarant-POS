@extends('layouts.waiter')

@section('title', 'Waiter Dashboard')
@section('page-title', 'Waiter Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Welcome back, <span class="font-semibold text-gray-800">{{ auth()->user()->name }}</span>! Select a table to start an order.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('waiter.orders.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-sm font-semibold shadow-sm transition">
                <i class="fas fa-list text-gray-500"></i> My Orders
            </a>
            <a href="{{ route('waiter.orders.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                <i class="fas fa-plus"></i> New Order
            </a>
        </div>
    </div>

    {{-- Status legend --}}
    <div class="flex flex-wrap gap-3">
        @php
            $statuses = [
                ['label' => 'Available', 'dot' => 'bg-green-500', 'border' => 'border-green-300', 'bg' => 'bg-green-50'],
                ['label' => 'Occupied',  'dot' => 'bg-red-500',   'border' => 'border-red-300',   'bg' => 'bg-red-50'],
                ['label' => 'Reserved',  'dot' => 'bg-yellow-500','border' => 'border-yellow-300','bg' => 'bg-yellow-50'],
                ['label' => 'Cleaning',  'dot' => 'bg-gray-400',  'border' => 'border-gray-300',  'bg' => 'bg-gray-50'],
            ];
        @endphp
        @foreach($statuses as $s)
            <div class="flex items-center gap-2 px-3 py-1.5 {{ $s['bg'] }} border {{ $s['border'] }} rounded-lg">
                <span class="w-2 h-2 rounded-full {{ $s['dot'] }}"></span>
                <span class="text-xs font-medium text-gray-600">{{ $s['label'] }}</span>
            </div>
        @endforeach
    </div>

    {{-- Table grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-3 md:gap-4">
        @forelse($tables ?? [] as $table)
            @php
                $tableStatusConfig = [
                    'occupied' => ['border' => 'border-l-red-500', 'badge' => 'bg-red-100 text-red-800', 'hover' => 'hover:border-red-400'],
                    'reserved' => ['border' => 'border-l-yellow-500', 'badge' => 'bg-yellow-100 text-yellow-800', 'hover' => 'hover:border-yellow-400'],
                    'cleaning' => ['border' => 'border-l-gray-400', 'badge' => 'bg-gray-100 text-gray-700', 'hover' => 'hover:border-gray-400'],
                ][$table->status] ?? ['border' => 'border-l-green-500', 'badge' => 'bg-green-100 text-green-800', 'hover' => 'hover:border-green-400'];
            @endphp
            <a href="{{ route('waiter.orders.create', ['table_id' => $table->id]) }}"
               class="group flex flex-col items-center justify-center p-4 bg-white rounded-2xl border-2 border-l-4 {{ $tableStatusConfig['border'] }} border-gray-100 {{ $tableStatusConfig['hover'] }} shadow-sm hover:shadow-md transition-all duration-200 aspect-square">
                <div class="w-10 h-10 bg-gray-100 group-hover:bg-emerald-50 rounded-xl flex items-center justify-center mb-2 transition-colors">
                    <i class="fas fa-chair text-gray-400 group-hover:text-emerald-600 transition-colors"></i>
                </div>
                <h3 class="text-sm font-black text-gray-900 text-center leading-tight mb-1">{{ $table->name }}</h3>
                <p class="text-[10px] text-gray-400 mb-1.5">{{ $table->capacity }} pax</p>
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $tableStatusConfig['badge'] }}">
                    {{ ucfirst($table->status) }}
                </span>
                @if(isset($table->activeOrders) && $table->activeOrders->count() > 0)
                    <p class="text-[10px] mt-1.5 text-blue-500 font-semibold">
                        {{ $table->activeOrders->count() }} order(s)
                    </p>
                @endif
            </a>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                <i class="fas fa-table text-4xl text-gray-200 mb-4 block"></i>
                <p class="text-gray-400 font-medium">No tables configured</p>
                <p class="text-gray-300 text-sm mt-1">Ask your admin to add tables.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
