@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default" style="text-align: center;">
                <h2>
                    <a href="{{ $user->profilePath }}">
                        {{ $user->username }}
                    </a>
                </h2>
                
                <img src="{{ $user->avatar }}" class="profile_image">
            </div>
        </div>

        <div class="col-md-9">
            <div class="panel panel-default">
                @if ($user->tagline )
                    <div class="panel-heading">
                        {{ $user->tagline }}
                    </div>
                @endif

                <div class="panel-body">
                    {!! $user->formattedProfile !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
