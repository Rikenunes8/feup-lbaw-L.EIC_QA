@extends('layouts.app')

@section('title', 'Registar Conta')

@section('content')
<h2 class="text-center">
    Registo
    @include('partials.help', ['placement' => 'right', 'content' => 'Escolha o seu tipo de conta e preencha os campos necessários! Vai ser necessário verificar o seu email e o Administrador ativar a sua conta para concluir o seu registo.'])
</h2> 

<form method="POST" action="{{ route('register') }}" id="form-register" class="row" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="mb-3 col-12">
        <label for="usertype" class="form-label required">Tipo de Conta</label>
        <select id="usertype" class="form-select" name="usertype" onchange="showRegisterFormFields()" aria-label="usertype">
            <option value="Student" {{ ( is_null(old('usertype')) )?'selected':'' }}>Estudante</option>
            <option value="Teacher" {{ ( old('usertype') == "Teacher" )?'selected':'' }}>Docente</option>
            <option value="Admin" {{ ( old('usertype') == "Admin" )?'selected':'' }}>Administrador</option>
        </select>
    </div>

    <div class="mb-3 col-12 col-lg-6">
        <label for="email" class="form-label required">Email</label>
        <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
        @if ($errors->has('email'))
            @include('layouts.error', ['error' => $errors->first('email')])
        @endif
    </div>

    <div class="mb-3 col-12 col-lg-6">
        <label for="username" class="form-label required">Username</label>
        <input type="text" id="username" class="form-control" name="username" value="{{ old('username') }}" required>
        @if ($errors->has('username'))
            @include('layouts.error', ['error' => $errors->first('username')])
        @endif
    </div>

    <div class="mb-3 col-12 col-lg-6">
        <label for="password" class="form-label required">Password</label>
        <input type="password" id="password" class="form-control" name="password" required>
        @if ($errors->has('password'))
            @include('layouts.error', ['error' => $errors->first('password')])
        @endif
    </div>

    <div class="mb-3 col-12 col-lg-6">
      <label for="password-confirm" class="form-label required">Confirme Password</label>
      <input type="password" id="password-confirm" class="form-control" name="password_confirmation" required>
    </div>

    <div class="col-12 col-lg-6 teacher-student-extra-fields">
      <div class="mb-3 row">
          <div class="col-12">
              <label for="name" class="form-label required">Nome</label>
              <input type="text" id="name" class="form-control" name="name" value="{{ old('name') }}" required>
              @if ($errors->has('name'))
                  @include('layouts.error', ['error' => $errors->first('name')])
              @endif
          </div>
      </div>

      <div class="mb-3 row">
          <div class="col-12">
              <label for="photo" class="form-label">Foto</label>
              <input type="file" id="photo" class="form-control" name="photo">
              @if ($errors->has('photo'))
                  @include('layouts.error', ['error' => $errors->first('photo')])
              @endif
          </div>
      </div>

      <div class="mb-3 row">
          <div class="col-12">
              <label for="birthdate" class="form-label">Aniversário</label>
              <input type="date" id="birthdate" class="form-control" name="birthdate" value="{{ old('birthdate') }}">
              @if ($errors->has('birthdate'))
                  @include('layouts.error', ['error' => $errors->first('birthdate')])
              @endif
          </div>
      </div>

      <div class="mb-3 row student-extra-fields">
          <div class="col-12">
              <label for="entryyear" class="form-label required">Ano de Ingresso</label>
              <input type="number" id="entryyear" min=1990 class="form-control" name="entryyear" value="{{ old('entryyear') }}" required>
              @if ($errors->has('entryyear'))
                  @include('layouts.error', ['error' => $errors->first('entryyear')])
              @endif
          </div>
      </div>
    </div>

    <div class="mb-3 col-12 col-lg-6 teacher-student-extra-fields">
        <label for="about" class="form-label">Sobre mim</label>
        <textarea rows="11" id="about" class="form-control" name="about">{{ old('about') }}</textarea>
        @if ($errors->has('about'))
            @include('layouts.error', ['error' => $errors->first('about')])
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Registar Conta</button>
    </div>

    <div class ="col-12" style="padding-top:5px;">
        <a href="{{ route('google.login') }}" class="btn btn-google btn-user btn-block">
            <i class="fab fa-google fa-fw"></i> Registar com Google
        </a>
    </div>

    <div class="col-12 d-flex justify-content-center mt-3">
        Receberá um email de confirmação após a submissão deste formulário.
    </div>

</form>
@endsection
