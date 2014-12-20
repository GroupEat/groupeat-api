@extends('app::layout.master')

@section('css')
    <style>
        body {
                margin:0;
                text-align:center;
                background-color: #2c2c2c;
            }

            .welcome img {
                width: 60em;
                height: 40em;
                position: absolute;
                left: 50%;
                top: 50%;
                margin-left: -30em;
                margin-top: -20em;
            }
    </style>
@stop

@section('content')
	<div class="welcome">
        <img src="/logo.svg" alt="logo GroupEat"/>
	</div>
@stop
