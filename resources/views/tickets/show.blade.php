@extends('layouts.app')

@section('title', 'Ticket #'.$ticket->id)

@section('content')
<div id="ticket-page" data-ticket-id="{{ $ticket->id }}">
    <div class="mb-3" data-ticket-id="{{ $ticket->id }}">
        <x-button class="btn-secondary" :href="route('tickets.index')" icon="arrow-left">Back to Tickets</x-button>
    </div>
    <x-card class="mb-3" :header="$ticket->title">
        <p><strong>Title:</strong> {{ $ticket->title }}</p>
        <p><strong>Category:</strong> {{ $ticket->category }}</p>
        <p><strong>Description:</strong> {{ $ticket->description }}</p>
        <p><strong>Customer:</strong> {{ $ticket->customer->name ?? '-' }}</p>
        <p><strong>Status:</strong> <span id="ticket-status">
            @if($ticket->status == 'open')
                <x-badge type="success" icon="unlock">Open</x-badge>
            @elseif($ticket->status == 'in_progress')
                <x-badge type="warning text-dark" icon="hourglass-split">In Progress</x-badge>
            @elseif($ticket->status == 'closed')
                <x-badge type="secondary" icon="lock">Closed</x-badge>
            @endif
        </span></p>
        <p><strong>Agent:</strong> <span id="ticket-agent">{{ $ticket->agent->name ?? '-' }}</span></p>
        <p><strong>Created:</strong> {{ $ticket->created_at->diffForHumans() }}</p>
        <p><strong>Metadata:</strong> <code>{{ json_encode($ticket->metadata) }}</code></p>
        @if(auth()->user()->isAgent() && !$ticket->agent_id)
            <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="d-inline assign-agent-form" data-ajax="true">
                @csrf
                <input type="hidden" name="agent_id" value="{{ auth()->user()->id }}">
                <x-button type="submit" class="btn-success" icon="person-plus">Assign to Me</x-button>
                <div class="ajax-feedback mb-2" id="assign-feedback"></div>
            </form>
        @endif
        @if(auth()->user()->isAgent())
            <form method="POST" action="{{ route('tickets.updateStatus', $ticket) }}" class="d-inline status-update-form" data-ajax="true" id="status-update-form">
                @csrf
                <x-select name="status" class="d-inline w-auto" id="status-select">
                    <option value="open" @if($ticket->status=='open') selected @endif>Open</option>
                    <option value="in_progress" @if($ticket->status=='in_progress') selected @endif>In Progress</option>
                    <option value="closed" @if($ticket->status=='closed') selected @endif>Closed</option>
                </x-select>
                <div class="ajax-feedback mb-2"></div>
            </form>
        @endif
    </x-card>
    <x-card class="mb-3" header="Comments">
        <div id="comments-section">
            @include('tickets.partials.comments', ['ticket' => $ticket])
        </div>
        <form method="POST" action="{{ route('tickets.addComment', $ticket) }}" class="add-comment-form" data-ajax="true">
            @csrf
            <x-textarea name="content" label="Add a Comment" rows="3" :required="true" />
            <div class="ajax-feedback mb-2"></div>
            <x-button type="submit" class="card-button w-100" icon="chat-dots">Post Comment</x-button>
        </form>
    </x-card>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let assignSelect = document.getElementById('assign-agent-select');
    let assignForm = document.querySelector('.assign-agent-form');
    if (assignSelect && assignForm) {
        assignSelect.addEventListener('change', function () {
            if (!assignSelect.value) return;
            let btn = assignForm.querySelector('button[type=submit]');
            let feedback = document.getElementById('assign-feedback');
            feedback.innerHTML = '';
            let formData = new FormData(assignForm);
            fetch(assignForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': assignForm.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.ticket) {
                        updateTicketDetails(data.ticket);
                        feedback.classList.remove('alert-danger');
                        feedback.classList.add('alert-info');
                        feedback.textContent = 'Ticket assigned successfully!';
                        feedback.classList.remove('d-none');
                    } else if (data.message) {
                        feedback.classList.remove('alert-info');
                        feedback.classList.add('alert-danger');
                        feedback.textContent = data.message;
                        feedback.classList.remove('d-none');
                    }
                })
                .catch(() => {
                    feedback.classList.remove('alert-info');
                    feedback.classList.add('alert-danger');
                    feedback.textContent = 'Failed to assign ticket.';
                    feedback.classList.remove('d-none');
                });
        });
    }
});
</script>
@endpush 