@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Users</h2>
</div>
<x-table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->role === 'agent')
                        <x-badge type="primary">Agent</x-badge>
                    @elseif($user->role === 'customer') 
                        <x-badge type="secondary">Customer</x-badge>
                    @else
                        <x-badge type="info">{{ ucfirst($user->role) }}</x-badge>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="4">No users found.</td></tr>
        @endforelse
    </tbody>
</x-table>
<div class="d-flex justify-content-center">
    {{ $users->links() }}
</div>
@endsection 