@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="page-header">
                <h1>
                    {{ $profileUser->name }}
                </h1>
                @can('update', $profileUser)
                    <form method="POST" action="{{ route('avatar', $profileUser) }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="file" name="avatar">
                        </div>                        
                        <button class="btn btn-default">Add Avatar</button>
                    </form>
                @endcan
                <img src="/storage/{{ $profileUser->avatar() }}" width="80" height="80">
                <p>
                    <small>created {{ $profileUser->created_at->diffForHumans() }}</small>
                </p>                
            </div>   

            <section>
                @forelse($activities as $date => $dataSet)
                    <h3 class="page-header">{{ $date }}</h3> 
                    @foreach($dataSet as $activity)
                        @if(view()->exists("profiles.activities.{$activity->type}"))
                            @include("profiles.activities.{$activity->type}")
                        @endif
                    @endforeach
                @empty
                    <p>There is no activities recoreded for the user at the moment.</p>
                @endforelse  

            </section>
        </div>
    </div>
</div>
 
@endsection