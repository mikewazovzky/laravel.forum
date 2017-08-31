@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create a new Thread</div>
                <div class="panel-body">
                    <form method="POST" action="/threads">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="channel_id">Channel</label>
                            <select type="text" id="channel_id" name="channel_id" class="form-control" required> 
                                <option value="">Choose one...</option>                           
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                        {{ $channel->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="body">Body</label>
                            <textarea id="body" name="body" class="form-control" rows="10" required>{{ old('body') }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Publish</button>
                        </div>

                        @if ($errors->any())
                            @foreach($errors->all() as $error)
                                <div class = "alert alert-danger" style="margin-top: 2px; margin-bottom: 2px; padding: 5px;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close" >&times;</a>              
                                    {{ $error }}
                                </div>
                            @endforeach
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
