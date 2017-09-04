<div id="reply-{{ $reply->id }}" class="panel panel-default">
    <div class="panel-heading">
        <div class="level">
            <h5 class="flex">
                <a href="{{ route('profile', $reply->owner) }}">{{ $reply->owner->name }}</a> wrote 
                <em>{{ $reply->created_at->diffForHumans() }}</em>...                  
            </h5>
            <form method="POST" action="/replies/{{ $reply->id}}/favorites" >
                {{ csrf_field() }}
                <button class="btn btn-sm btn-default" {{ $reply->isFavorited() ? 'disabled' : '' }}>
                    {{ $reply->favorites_count }} {{ str_plural('Like', $reply->favorites_count )}}
                </button>
            </form>
               
        </div>
                    
    </div>
    <div class="panel-body">
        <div>{{ $reply->body }}</div>
    </div>

    @can('update', $reply)
        <div class="panel-footer">
            <form method="POST" action="/replies/{{ $reply->id }}">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="submit" class="btn btn-danger btn-xs">Delete</button>
            </form>
        </div>
    @endcan
</div>