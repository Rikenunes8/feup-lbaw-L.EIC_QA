@extends('layouts.app')

@section('title', 'Alterar Password')

@section('content')

<div class="flex-center position-ref full-height">
     <h2>Alterar Password?</h2>
    
     <p>
         Please click resert.....
         <a href="{{route('reset.password.get', $token)}}">reset password</a>
    </p>
</div>
@endsection