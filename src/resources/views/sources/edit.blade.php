@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ __('Edit Source') }}</h1>
        <form action="{{ route('sources.update', $source) }}" method="POST">
            @method('PUT')
            @include('sources._form')
        </form>
    </div>
@endsection
