@extends('layouts.app')

@section('title', 'New Ticket')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <x-card :header="'Submit a New Ticket'">
            <form method="POST" action="{{ route('tickets.store') }}" id="create-ticket-form" data-ajax="true">
                @csrf
                <x-input name="title" label="Title" :value="old('title')" :required="true" :autofocus="true" />
                <x-input name="category" label="Category" :value="old('category')" :required="true" />
                <x-textarea name="description" label="Description" :value="old('description')" rows="5" :required="true" />
                <div class="ajax-feedback mb-2"></div>
                <x-button type="submit" icon="send" class="card-button w-100" id="create-ticket-btn">
                    Submit Ticket
                </x-button>
            </form>
        </x-card>
    </div>
</div>
@endsection 