<div class="panel panel-default">
    <div class="panel-heading">
        <a href="#">{{ $reply->owner->name }}</a> wrote 
        <em>{{ $reply->created_at->diffForHumans() }}</em>...                         
    </div>
    <div class="panel-body">
        <div>{{ $reply->body }}</div>
    </div>
</div>