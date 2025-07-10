@extends('layouts.app')

@section('title', 'New Ticket')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Submit a New Ticket</div>
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.store') }}" id="create-ticket-form">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category') }}" required>
                        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn card-button w-100">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="create-ticket-spinner"></span>
                        Submit Ticket
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).on('submit', 'form#create-ticket-form', function(e) {
    e.preventDefault();
    var form = $(this);
    var btn = form.find('button[type=submit]');
    var spinner = btn.find('.spinner-border');
    btn.prop('disabled', true);
    spinner.removeClass('d-none');
    $('#global-loading').fadeIn(150);
    $.post(form.attr('action'), form.serialize(), function(data) {
        if (data.redirect) {
            window.location.href = data.redirect;
        }
    }).fail(function(xhr) {
        alert('Failed to create ticket.');
    }).always(function() {
        btn.prop('disabled', false);
        spinner.addClass('d-none');
        $('#global-loading').fadeOut(150);
    });
});
</script>
@endpush 