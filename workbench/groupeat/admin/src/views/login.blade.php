@extends('layout.main')

@section('content')
    <div class="col-md-4 col-md-offset-4">
        <div id="admin-login-form" class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Admin zone</h3>
            </div>
            <div class="panel-body">
                @if (isset($errors) && !$errors->isEmpty())
                    <div class="alert alert-warning" role="alert">{{ $errors->first() }}</div>
                @endif
                {{ Form::open(['url' => 'admin/check']) }}
                    {{ Form::token() }}
                    <div class="form-group">
                        <input class="form-control" placeholder="Email" name="email" type="email" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Password" name="password" type="password" required>
                    </div>
                    {{ Form::submit('Log in', ['class' => 'btn btn-lg btn-danger btn-block']) }}
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
