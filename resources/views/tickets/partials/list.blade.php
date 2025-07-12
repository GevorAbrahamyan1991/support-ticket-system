<x-table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Status</th>
            <th>Category</th>
            <th>Customer</th>
            <th>Agent</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tickets as $ticket)
            <tr>
                <td>{{ $ticket->id }}</td>
                <td>{{ $ticket->title }}</td>
                <td>
                    @if($ticket->status == 'open')
                        <x-badge type="success" icon="unlock">Open</x-badge>
                    @elseif($ticket->status == 'in_progress')
                        <x-badge type="warning" class="text-dark" icon="hourglass-split">In Progress</x-badge>
                    @elseif($ticket->status == 'closed')
                        <x-badge type="secondary" icon="lock">Closed</x-badge>
                    @endif
                </td>
                <td>{{ $ticket->category }}</td>
                <td>{{ $ticket->customer->name ?? '-' }}</td>
                <td>{{ $ticket->agent->name ?? '-' }}</td>
                <td>
                    <x-button class="btn-info btn-sm" :href="route('tickets.show', $ticket)" icon="eye">View</x-button>
                </td>
            </tr>
        @empty
            <tr><td colspan="7">No tickets found.</td></tr>
        @endforelse
    </tbody>
</x-table>

<div class="d-flex justify-content-center">
    {{ $tickets->links() }}
</div> 