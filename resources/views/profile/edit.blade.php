@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form method="post" action="/profile/edit">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="tagline">Tagline:</label>
                            <input type="text" name="tagline" id="tagline" class="form-control" value="{{ old('tagline') ?: $user->tagline }}">
                            <div class="text-danger">{{ $errors->first('tagline') }}</div>
                        </div>

                        <div class="form-group">
                            <label for="profile">Profile:</label>
                            <textarea name="profile" id="profile" class="form-control" rows="5">{{ old('profile') ?: $user->profile }}</textarea>
                            <div class="text-danger">{{ $errors->first('profile') }}</div>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" class="form-control" required>
                                <option 
                                    value="male" 
                                    {{ old('gender') == 'male' ? 'selected' : $user->gender == 'male' ? 'selected' : '' }}
                                >Male</option>
                                
                                <option 
                                    value="female" 
                                    {{ old('gender') == 'female' ? 'selected' : $user->gender == 'female' ? 'selected' : '' }}
                                >Female</option>
                            </select>

                            <div class="text-danger">{{ $errors->first('gender') }}</div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
