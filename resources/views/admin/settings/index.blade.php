@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Billing & Tax Settings</h2>
        <p class="text-sm text-gray-500 mb-4">
            These values are used when calculating service charge and tax for orders.
        </p>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Charge (%)</label>
                <input type="number" name="service_charge_percent" value="{{ old('service_charge_percent', $settings['service_charge_percent']) }}" min="0" max="100" step="0.01" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('service_charge_percent') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tax (%)</label>
                <input type="number" name="tax_percent" value="{{ old('tax_percent', $settings['tax_percent']) }}" min="0" max="100" step="0.01" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('tax_percent') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

