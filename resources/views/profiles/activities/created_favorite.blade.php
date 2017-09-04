@component('profiles.activities.activity')
    @slot('heading')
        <a href="{{ route('profile', $activity->user) }}">{{ $activity->user->name }}</a>  
        favorited a 
        <a href="{{ $activity->subject->favorited->path() }}">reply</a>
    @endslot
    
    @slot('body')
        {{ $activity->subject->favorited->body }}
    @endslot    
@endcomponent
