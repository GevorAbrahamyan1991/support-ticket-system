@extends('layouts.app')

@section('title', 'Ticket #'.$ticket->id)

@section('content')
<div id="ticket-page" data-ticket-id="{{ $ticket->id }}">
    <div class="mb-3" data-ticket-id="{{ $ticket->id }}">
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Back to Tickets</a>
    </div>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><strong>{{ $ticket->title }}</strong> ({{ ucfirst($ticket->status) }})</span>
            @if(auth()->user()->isAgent() && !$ticket->agent_id)
                <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="d-inline assign-agent-form">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="assign-spinner"></span>
                        Assign to Me
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
            <p><strong>Category:</strong> {{ $ticket->category }}</p>
            <p><strong>Description:</strong> {{ $ticket->description }}</p>
            <p><strong>Customer:</strong> {{ $ticket->customer->name ?? '-' }}</p>
            <p><strong>Status:</strong> <span id="ticket-status">{{ ucfirst($ticket->status) }}</span></p>
            <p><strong>Agent:</strong> <span id="ticket-agent">{{ $ticket->agent->name ?? '-' }}</span></p>
            <p><strong>Created:</strong> {{ $ticket->created_at->diffForHumans() }}</p>
            <p><strong>Metadata:</strong> <code>{{ json_encode($ticket->metadata) }}</code></p>
            @if(auth()->user()->isAgent())
                <form method="POST" action="{{ route('tickets.updateStatus', $ticket) }}" class="d-inline status-update-form">
                    @csrf
                    <select name="status" class="form-select d-inline w-auto">
                        <option value="open" @if($ticket->status=='open') selected @endif>Open</option>
                        <option value="in_progress" @if($ticket->status=='in_progress') selected @endif>In Progress</option>
                        <option value="closed" @if($ticket->status=='closed') selected @endif>Closed</option>
                    </select>
                    <button type="submit" class="btn btn-sm card-button">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="status-spinner"></span>
                        Update Status
                    </button>
                </form>
            @endif
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">Comments</div>
        <div class="card-body">
            <div id="comments-section">
                @include('tickets.partials.comments', ['ticket' => $ticket])
            </div>
            <form method="POST" action="{{ route('tickets.addComment', $ticket) }}" class="add-comment-form">
                @csrf
                <div class="mb-3">
                    <label for="content" class="form-label">Add a Comment</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="3" required></textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn card-button w-100">Post Comment</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateTicketDetails(ticket) {
    $("#ticket-status").text(ticket.status.charAt(0).toUpperCase() + ticket.status.slice(1));
    $("#ticket-agent").text(ticket.agent ? ticket.agent.name : '-');
    if (ticket.agent_id) {
        $(".assign-agent-form").remove();
    }
}

$(document).on('submit', 'form.assign-agent-form', function(e) {
    e.preventDefault();
    var form = $(this);
    var btn = form.find('button[type=submit]');
    var spinner = btn.find('.spinner-border');
    btn.prop('disabled', true);
    spinner.removeClass('d-none');
    $('#global-loading').fadeIn(150);
    $.post(form.attr('action'), form.serialize(), function(data) {
        if (data.success && data.ticket) {
            updateTicketDetails(data.ticket);
        }
    }).fail(function() {
        alert('Failed to assign ticket.');
    }).always(function() {
        btn.prop('disabled', false);
        spinner.addClass('d-none');
        $('#global-loading').fadeOut(150);
    });
});

$(document).on('submit', 'form.status-update-form', function(e) {
    e.preventDefault();
    var form = $(this);
    var btn = form.find('button[type=submit]');
    var spinner = btn.find('.spinner-border');
    btn.prop('disabled', true);
    spinner.removeClass('d-none');
    $('#global-loading').fadeIn(150);
    $.post(form.attr('action'), form.serialize(), function(data) {
        if (data.success && data.ticket) {
            updateTicketDetails(data.ticket);
        }
    }).fail(function() {
        alert('Failed to update status.');
    }).always(function() {
        btn.prop('disabled', false);
        spinner.addClass('d-none');
        $('#global-loading').fadeOut(150);
    });
});

$(document).on('submit', 'form.add-comment-form', function(e) {
    e.preventDefault();
    var form = $(this);
    var btn = form.find('button[type=submit]');
    btn.prop('disabled', true);
    $('#global-loading').fadeIn(150);
    $.post(form.attr('action'), form.serialize(), function(data) {
        if (data.comments_html) {
            $('#comments-section').html(data.comments_html);
            form[0].reset();
        }
    }).fail(function() {
        alert('Failed to add comment.');
    }).always(function() {
        btn.prop('disabled', false);
        $('#global-loading').fadeOut(150);
    });
});
</script>
@endpush 