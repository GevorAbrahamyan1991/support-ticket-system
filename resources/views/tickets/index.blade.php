@extends('layouts.app')

@section('title', 'Tickets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Tickets</h2>
    @if(auth()->user()->isCustomer())
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">New Ticket</a>
    @endif
</div>
<form id="filter-form" method="GET" class="row g-3 mb-3">
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">All Statuses</option>
            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="text" name="category" class="form-control" placeholder="Category" value="{{ request('category') }}">
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="{{ route('tickets.index') }}" class="btn btn-danger">Reset</a>
    </div>
</form>
<div id="tickets-loading" class="text-center my-3" style="display:none;">
    <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
</div>
<div id="tickets-list">
    @include('tickets.partials.list', ['tickets' => $tickets])
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        fetchTickets($(this).serialize());
    });
    $('#filter-form select, #filter-form input').on('change', function() {
        $('#filter-form').submit();
    });
    $(document).on('click', '#tickets-list .pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var params = url.split('?')[1] || '';
        fetchTickets(params);
    });
    function fetchTickets(params) {
        $('#tickets-loading').show();
        $.ajax({
            url: '{{ route('tickets.index') }}' + (params ? '?' + params : ''),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#tickets-list').html(data.html ?? renderTickets(data.tickets));
            },
            complete: function() {
                $('#tickets-loading').hide();
            }
        });
    }
    function renderTickets(tickets) {
        return '';
    }
});
</script>
@endpush 