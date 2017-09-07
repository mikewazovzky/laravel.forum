@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h1>Forum Threads</h1>
            @forelse($threads as $thread)        
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="level">
                            <h4 class="flex">                                
                                @if(auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                                    <span class="label label-info">Updated</span>
                                    <a href="{{ $thread->path() }}">
                                        <strong>
                                            {{ $thread->title }}
                                        </strong>
                                    </a>
                                @else
                                    <a href="{{ $thread->path() }}">
                                        {{ $thread->title }}
                                    </a>
                                @endif                                  
                            </h4>                                
                            <a href="{{ $thread->path() }}">
                                {{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}.
                            </a>
                        </div>                        
                    </div>
                    <div class="panel-body">
                        <div class="body">{{ $thread->body }}</div>
                    </div>
                </div>
            @empty
                <p>There are no threads in this channel.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection