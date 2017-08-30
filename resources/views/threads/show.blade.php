@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="#">{{ $thread->user->name }}</a> posted:
                    {{ $thread->title }}
                </div>
                <div class="panel-body">
                    {{ $thread->body }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @foreach($thread->replies as $reply)
                @include('threads.reply')
            @endforeach
        </div>
    </div>   

    @if(auth()->check())
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <form method="POST" action="{{ $thread->path() . '/replies'}}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <textarea class="form-control" name="body" placeholder="post a reply..."  rows="4"></textarea>
                    </div>
                    <button class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>   
    @else
        <p class="text-center">Pls. <a href="{{ route('login') }}">sign in</a> to participate in this discussion.</p>     
    @endif

</div>
@endsection