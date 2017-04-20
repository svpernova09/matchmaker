@extends('layouts.app')

@section('content')
<div class="container">

    @include ('profile.menu', ['page'=>'photos'])

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form method="POST" action="/photos" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="photo">Upload Photo:</label>
                            <input type="file" name="photo" id="photo" class="form-control" value="{{ old('photo') }}" required>
                            <div class="text-danger">{{ $errors->first('photo') }}</div>
                        </div>

                        <button class="btn btn-success pull-right">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-body">
                    @forelse ($photos as $photo)
                        <img src="/images/thumbnails/{{ $photo->path }}"> 
                    @empty
                        You haven't uploaded any photos yet.
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
