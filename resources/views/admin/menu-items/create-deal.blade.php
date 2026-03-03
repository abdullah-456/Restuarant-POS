@extends('layouts.admin')

@section('title', 'Create a Deal')
@section('page-title', 'Create a Deal')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('admin.menu-items.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="is_deal" value="1">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left side: Basic Info --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-emerald-500"></i>Deal Information
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Deal Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition shadow-sm"
                                    placeholder="e.g. Family Bundle Special">
                                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deal Price (Rs.)</label>
                                    <input type="number" name="price" value="{{ old('price') }}" min="0" step="0.01"
                                        required
                                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition shadow-sm font-bold text-emerald-600">
                                    @error('price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition shadow-sm"
                                    placeholder="Tell customers what's in this deal...">{{ old('description') }}</textarea>
                                @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Bundle Items --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-layer-group text-emerald-500"></i>Included Items
                            </h3>
                            <button type="button" onclick="addDealItemRow()"
                                class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition shadow-sm">
                                <i class="fas fa-plus mr-2"></i>Add Item
                            </button>
                        </div>

                        <div id="dealItemsContainer" class="space-y-3">
                            {{-- Rows added via JS --}}
                        </div>
                        @error('deal_items') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Right side: Status & Image --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-4">Settings</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deal
                                    Image</label>
                                <div class="relative group">
                                    <input type="file" name="image" accept="image/*" id="imageInput" class="hidden">
                                    <label for="imageInput" class="cursor-pointer block">
                                        <div
                                            class="border-2 border-dashed border-gray-200 rounded-2xl p-4 text-center group-hover:border-emerald-500 transition bg-gray-50">
                                            <i
                                                class="fas fa-cloud-upload-alt text-3xl text-gray-300 group-hover:text-emerald-500 mb-2 transition"></i>
                                            <p class="text-xs text-gray-500 font-medium">Click to upload image</p>
                                        </div>
                                    </label>
                                </div>
                                @error('image') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600">
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Available for Order</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="submit"
                            class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                            <i class="fas fa-save text-lg"></i>Save Deal
                        </button>
                        <a href="{{ route('admin.menu-items.index') }}"
                            class="w-full py-4 bg-white text-gray-700 border border-gray-200 rounded-2xl font-bold hover:bg-gray-50 transition flex items-center justify-center">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let dealItemIndex = 0;
        const allMenuItems = {!! json_encode($all_items) !!};

        function addDealItemRow(itemId = '', qty = 1) {
            const container = document.getElementById('dealItemsContainer');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 transition hover:border-emerald-200';

            let options = '<option value="">Select Item</option>';
            allMenuItems.forEach(item => {
                options += `<option value="${item.id}" ${item.id == itemId ? 'selected' : ''}>${item.name} (Rs. ${item.price})</option>`;
            });

            row.innerHTML = `
                    <div class="flex-1">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-utensils text-xs"></i>
                            </span>
                            <select name="deal_items[${dealItemIndex}][menu_item_id]" required
                                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white shadow-sm appearance-none">
                                ${options}
                            </select>
                        </div>
                    </div>
                    <div class="w-24">
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-400">QTY</span>
                            <input type="number" name="deal_items[${dealItemIndex}][quantity]" value="${qty}" min="1" required
                                class="w-full pl-10 pr-3 py-2 text-sm text-center font-bold border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white shadow-sm" placeholder="1">
                        </div>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" 
                        class="w-10 h-10 flex items-center justify-center text-red-500 hover:text-white hover:bg-red-500 rounded-xl transition border border-red-100 bg-white shadow-sm">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                `;

            container.appendChild(row);
            dealItemIndex++;
        }

        // Auto-add one row on load if none exist
        window.addEventListener('load', function () {
            @if(old('deal_items'))
                @foreach(old('deal_items') as $di)
                    addDealItemRow('{{ $di['menu_item_id'] }}', '{{ $di['quantity'] }}');
                @endforeach
            @else
                addDealItemRow();
            @endif
            });
    </script>
@endpush