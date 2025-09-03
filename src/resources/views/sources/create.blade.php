@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ __('Add Source') }}</h1>
        <form action="{{ route('sources.store') }}" method="POST">
            @include('sources._form')
        </form>
    </div>
@endsection
