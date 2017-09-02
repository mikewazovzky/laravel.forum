@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="{{ route('profile', $thread->user) }}">{{ $thread->user->name }}</a> posted:
                    {{ $thread->title }}
                </div>
                <div class="panel-body">
                    {{ $thread->body }}
                </div>
            </div>

            @foreach($replies as $reply)
                @include('threads.reply')
            @endforeach
            {{ $replies->links() }}

            @if(auth()->check())
                <form method="POST" action="{{ $thread->path() . '/replies'}}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <textarea class="form-control" name="body" placeholder="post a reply..."  rows="4"></textarea>
                    </div>
                    <button class="btn btn-primary">Submit</button>
                </form>
            @else
                <p class="text-center">Pls. <a href="{{ route('login') }}">sign in</a> to participate in this discussion.</p>     
            @endif            
        </div><!-- col-md-8 -->

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p>
                        This thread was published 
                        <strong>{{ $thread->created_at->diffForHumans() }}</strong> by
                        <a href="{{ route('profile', $thread->user) }}">{{ $thread->user->name }}</a>
                        and currently has <strong>{{ $thread->replies_count }}</strong>
                        {{ str_plural('reply', $thread->replies_count) }}.                      
                    </p>
                </div>
            </div>
        </div>       
    </div><!-- row -->
</div>
@endsection
