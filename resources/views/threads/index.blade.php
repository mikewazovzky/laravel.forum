@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @include('threads/list')
            {{ $threads->render() }}
        </div>
        <div class="col-md-4">
            @if(count($trending))
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Top 5 trending threads
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                            @foreach($trending as $thread)
                                <li class="list-group-item">
                                    <a href="{{ $thread->path }}">{{ substr($thread->title, 0, 30) . ' ...' }}</a>
                                </li>                        
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>        
    </div>
</div>
@endsection
