@extends('layouts.admin')

@section('title', 'Manage Users')
@section('page-title', 'Manage Users')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Users</h2>
            <p class="text-sm text-gray-500">Manage all staff accounts and roles.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm md:text-base shadow-sm hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Create User
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm md:text-base">
            <thead>
                <tr class="bg-gray-50 text-left text-xs md:text-sm font-semibold text-gray-600">
                    <th class="px-3 md:px-4 py-2 md:py-3">Name</th>
                    <th class="px-3 md:px-4 py-2 md:py-3">Email</th>
                    <th class="px-3 md:px-4 py-2 md:py-3">Role</th>
                    <th class="px-3 md:px-4 py-2 md:py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr>
                        <td class="px-3 md:px-4 py-2 md:py-3">{{ $user->name }}</td>
                        <td class="px-3 md:px-4 py-2 md:py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-3 md:px-4 py-2 md:py-3">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($user->role === 'admin') bg-purple-100 text-purple-800
                                @elseif($user->role === 'waiter') bg-blue-100 text-blue-800
                                @elseif($user->role === 'kitchen') bg-orange-100 text-orange-800
                                @elseif($user->role === 'cashier') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-3 md:px-4 py-2 md:py-3 text-right">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-xs font-medium text-gray-700">
                                    Edit
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 rounded text-xs font-medium text-red-700">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 md:px-4 py-6 text-center text-gray-500 text-sm">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection

