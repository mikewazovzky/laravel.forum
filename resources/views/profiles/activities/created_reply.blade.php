@component('profiles.activities.activity')
    @slot('heading')
        <a href="{{ route('profile', $activity->user) }}">{{ $activity->user->name }}</a> 
        replied to 
        <a href="{{ $activity->subject->thread->path() }}">{{ $activity->subject->thread->title }}</a>       
    @endslot
    
    @slot('body')
        {{ $activity->subject->body }}
    @endslot    
@endcomponent
