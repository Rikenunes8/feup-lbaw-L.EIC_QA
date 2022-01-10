@extends('layouts.app')

@section('title', 'Alterar Password')

@section('content')

<div class="flex-center position-ref full-height">
    <form  action="{{ route('forget.password.post')}}" method="POST">
        <h2>Alterar Password?</h2>
        
        {{csrf_field()}}

        @if(session('error'))
        <div>  {{session('error')}} </div>
        @endif

        @if(session('success'))
         <div> {{session('success')}} </div>
        @endif

        <input name="email" placeholder="Inserir email" value="{{request()->get('email')}}">
        <button type="submit">Alterar</button>
    </form>
</div>
@endsection