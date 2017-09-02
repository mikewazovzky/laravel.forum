@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="page-header">
                <h1>
                    {{ $profileUser->name }}
                </h1>
                <small>created {{ $profileUser->created_at->diffForHumans() }}</small>
            </div>   

            <section>
                @foreach($threads as $thread)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="level">
                                <span class="flex">
                                    <a href="#">{{ $thread->user->name }}</a> posted:
                                    {{ $thread->title }}                            
                                </span>
                                <span>{{ $thread->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            {{ $thread->body }}
                        </div>
                    </div>
                @endforeach  
                {{ $threads->links() }}      
            </section>
        </div>
    </div>
</div>
 
@endsection