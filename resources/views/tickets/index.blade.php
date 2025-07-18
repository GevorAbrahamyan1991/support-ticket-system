@extends('layouts.app')

@section('title', 'Tickets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-card-list"></i> Tickets</h2>
    @if(auth()->user()->isCustomer())
        <x-button class="btn-primary" :href="route('tickets.create')" icon="plus-circle">New Ticket</x-button>
    @endif
</div>
<form id="filter-form" method="GET" class="row g-3 mb-3">
    <div class="col-md-3">
        <x-select name="status" label="Status">
            <option value="">All Statuses</option>
            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
        </x-select>
    </div>
    <div class="col-md-3">
        <x-input name="category" label="Category" :value="request('category')" />
    </div>
    <div class="col-md-3 mt-5">
        <x-button type="submit" class="btn-secondary" icon="funnel">Filter</x-button>
        <x-button :href="route('tickets.index')" class="btn-danger" icon="x-circle">Reset</x-button>
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
<!-- Ticket filter and pagination logic moved to app.js -->
@endpush 