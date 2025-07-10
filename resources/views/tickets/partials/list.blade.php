<table class="table table-bordered">
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
                <td>{{ ucfirst($ticket->status) }}</td>
                <td>{{ $ticket->category }}</td>
                <td>{{ $ticket->customer->name ?? '-' }}</td>
                <td>{{ $ticket->agent->name ?? '-' }}</td>
                <td>
                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-info">View</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7">No tickets found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $tickets->links() }}
</div> 