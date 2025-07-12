@forelse($ticket->comments as $comment)
    <div class="mb-2">
        <i class="bi bi-person-circle"></i> <strong>{{ $comment->user->name }}</strong> <span class="text-muted">({{ $comment->created_at->diffForHumans() }})</span>
        <div>{{ $comment->content }}</div>
    </div>
    <hr>
@empty
    <p>No comments yet.</p>
@endforelse 