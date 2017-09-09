@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h1>Forum Threads</h1>
            @include('threads/list')

            {{ $threads->render() }}
        </div>
    </div>
</div>
@endsection
