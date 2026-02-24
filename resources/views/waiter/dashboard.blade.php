@extends('layouts.waiter')

@section('title', 'Waiter Dashboard')
@section('page-title', 'Waiter Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-4 md:mb-6">
        <p class="text-sm md:text-base text-gray-600">Select a table to start or add to an order.</p>
        <a href="{{ route('waiter.orders.create') }}" class="inline-flex items-center px-4 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg shadow-sm hover:from-blue-700 hover:to-blue-800 transition font-medium text-sm md:text-base w-full sm:w-auto justify-center">
            <i class="fas fa-plus-circle mr-2"></i>Create New Order
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4">
        @forelse($tables ?? [] as $table)
            <a href="{{ route('waiter.orders.create', ['table_id' => $table->id]) }}" 
               class="block p-4 md:p-6 bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition
                      @if($table->status === 'occupied') border-l-4 border-l-red-500
                      @elseif($table->status === 'reserved') border-l-4 border-l-yellow-500
                      @elseif($table->status === 'cleaning') border-l-4 border-l-gray-500
                      @else border-l-4 border-l-green-500
                      @endif">
                <div class="text-center">
                    <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-1 md:mb-2">{{ $table->name }}</h3>
                    <p class="text-xs md:text-sm text-gray-600">Capacity: {{ $table->capacity }}</p>
                    <span class="inline-block mt-2 px-2 md:px-3 py-1 rounded-full text-xs font-semibold
                       @if($table->status === 'occupied') bg-red-100 text-red-800
                       @elseif($table->status === 'reserved') bg-yellow-100 text-yellow-800
                       @elseif($table->status === 'cleaning') bg-gray-100 text-gray-700
                       @else bg-green-100 text-green-800
                       @endif">
                        {{ ucfirst($table->status) }}
                    </span>
                    @if(isset($table->activeOrders) && $table->activeOrders->count() > 0)
                        <p class="text-xs mt-2 text-blue-600 font-medium">
                            <i class="fas fa-receipt mr-1"></i>{{ $table->activeOrders->count() }} active order(s)
                        </p>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-12 bg-white rounded-lg border border-gray-200">
                <i class="fas fa-table text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No tables available</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
