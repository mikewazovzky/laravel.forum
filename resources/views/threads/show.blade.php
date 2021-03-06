@extends('layouts.app')

@section('header')
    <link rel="stylesheet" href="/css/vendor/jquery.atwho.css">
@endsection

@section('content')
<thread-view :data-replies-count="{{ $thread->replies_count }}" inline-template>    
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="level">
                            <img src="{{ $thread->user->avatar_path }}" alt="{{ $thread->user->name }}" class="mr-1" width="50" height="50">
                            <div class="flex">
                                <a href="{{ route('profile', $thread->user) }}">{{ $thread->user->name }}</a> posted:
                                {{ $thread->title }}                            
                            </div>
                            @can('update', $thread)
                                <form method="POST" action="{{ $thread->path() }}">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn btn-link">Delete</button>
                                </form>               
                            @endcan         
                        </div>

                    </div>
                    <div class="panel-body">
                        {{ $thread->body }}
                    </div>
                </div>

                <replies @added="repliesCount++" @removed="repliesCount--"></replies>
            </div><!-- col-md-8 -->

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>
                            This thread was published 
                            <strong>{{ $thread->created_at->diffForHumans() }}</strong> by
                            <a href="{{ route('profile', $thread->user) }}">{{ $thread->user->name }}</a>
                            and currently has <span v-text="repliesCount"></span>
                            {{ str_plural('reply', $thread->replies_count) }}.                      
                        </p>
                        <p>
                            {{-- <subscribe-button :subscribed="{{ $thread->isSubscribedTo ? 'true' : 'false'}}"></subscribe-button> --}}
                            <subscribe-button :subscribed="{{ json_encode($thread->isSubscribedTo) }}"></subscribe-button>                   
                        </p>
                    </div>
                </div>
            </div>       
        </div><!-- row -->
    </div>
</thread-view>
@endsection
