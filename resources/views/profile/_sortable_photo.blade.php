<li class="ui-state-default" id="user-photo-{{ $photo->id }}">
    <div>
        <img src="/images/thumbnails/{{ $photo->path }}"> <br>
        <form method="POST" action="/photos/{{ $photo->id }}">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="submit" class="btn btn-link">delete</button>
        </form>
    </div>
</li>