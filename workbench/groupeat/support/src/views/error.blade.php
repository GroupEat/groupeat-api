@extends('layout.main')

@section('content')
	<div id="error-status-code" class="col-md-4 col-md-offset-4">
        <p>
            <span class="whoops text-danger">Whoops...</span><br>
            <span class="status-code text-warning">{{ floor($code / 100) }}</span>
            <span class="status-code text-danger">{{ floor($code / 10) % 10 }}</span>
            <span class="status-code text-warning">{{ $code % 10 }}</span>
        </p>
	</div>
@stop
