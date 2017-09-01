<div class="panel panel-default">
    <div class="panel-heading">
        <div class="level">
            <h5 class="flex">
                <a href="#">{{ $reply->owner->name }}</a> wrote 
                <em>{{ $reply->created_at->diffForHumans() }}</em>...                  
            </h5>
            <form method="POST" action="/replies/{{ $reply->id}}/favorites" >
                {{ csrf_field() }}
                <button class="btn btn-sm btn-default" {{ $reply->isFavorited() ? 'disabled' : '' }}>
                    {{ $reply->favorites()->count() }} {{ str_plural('Like', $reply->favorites()->count() )}}
                </button>
            </form>
               
        </div>
                    
    </div>
    <div class="panel-body">
        <div>{{ $reply->body }}</div>
    </div>
</div>