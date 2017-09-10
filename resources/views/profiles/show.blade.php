@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="page-header">
                <avatar-form :user="{{ $profileUser }}"></avatar-form>
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