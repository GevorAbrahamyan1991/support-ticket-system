@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <div class="container" id="welcome-component">
        <div class="row">
            <div class="col-xl-8 offset-2">
                <img src="{{ asset("images/CoverImage.webp") }}" class="img-fluid w-full h-full" alt="">
            </div>
        </div>
    </div>
@endsection
