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
                @if($photos->count())
                    <div class="alert alert-info">
                        Drag and drop your photos to organize them. 
                        The photo is the first position will act as your avatar.
                    </div>

                    <ul id="sortable" class="image-list">
                    @foreach ($photos as $photo)
                        @include ('profile._sortable_photo')
                    @endforeach
                    </ul>
                @else
                    You haven't uploaded any photos yet.
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    $( function() {
        $( "#sortable" ).sortable();
        $( "#sortable" ).disableSelection();
    } );

    $( "#sortable" ).sortable({
        stop: function( event, ui ) {
            reorderPhotos(event, ui);
        }
    });

    $( "#sortable" ).on( "sortchange", function( event, ui ) {} );

    function reorderPhotos(event, ui) {
        let token = $('meta[name="csrf-token"]').attr('content');
        let id = ui.item.attr('id').replace('user-photo-', '');
        let position = $('#user-photo-' + id).index() + 1;

        $.post( "/photos/" + id, {_token: token, position: position});
    }

</script>
@endsection
