@extends('layout.main')

@section('content')
    <div class="col-md-4 col-md-offset-4">
        <div id="admin-login-form" class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Admin zone</h3>
            </div>
            <div class="panel-body">
                <form accept-charset="UTF-8" role="form" action="/admin/check" method="POST">
                <fieldset>
                    <div class="form-group">
                        <input class="form-control" placeholder="Email" name="email" type="text">
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Password" name="password" type="password" value="">
                    </div>
                    <input class="btn btn-lg btn-danger btn-block" type="submit" value="Login">
                </fieldset>
                </form>
            </div>
        </div>
    </div>
@stop
