@forelse($threads as $thread)        
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <div class="flex">
                    <h4>                                
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

                    <h5>Posted by: <a href="{{ route('profile', $thread->user) }}">{{ $thread->user->name }}</a></h5>        
                </div>                       

                <a href="{{ $thread->path() }}">
                    {{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}.
                </a>
            </div>                        
        </div>
        <div class="panel-body">
            <div class="body">{{ $thread->body }}</div>
        </div>

        <div class="panel-footer">
            The thread has {{ $thread->visits() }} visits.
        </div>
    </div>
@empty
    <p>There are no threads in this channel.</p>
@endforelse
