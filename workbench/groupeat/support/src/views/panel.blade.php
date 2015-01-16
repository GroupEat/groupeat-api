@extends('layout.main')

@section('content')
    <div class="col-md-4 col-md-offset-4">
        <div id="{{ $panelId }}" class="groupeat-panel panel panel-{{ $panelClass }}">
            <div class="panel-heading">
                <h3 class="panel-title text-center">{{ $title }}</h3>
            </div>
            <div class="panel-body">
                {{ $panelBody }}
            </div>
        </div>
    </div>
@stop
