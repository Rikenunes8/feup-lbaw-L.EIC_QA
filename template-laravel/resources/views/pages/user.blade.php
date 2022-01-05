@extends('layouts.app')

@section('title', (Auth::check() && Auth::user()->id == $user->id)?'O meu Perfil':'Perfil do Utilizador '.$user->username )

@section('content')

<section id="user-profile-page">
  <div class="row user-profile" data-id="{{ $user->id }}"> 
    <div class="col-12 position-relative">
      @if ( Auth::check() && (Auth::user()->id == $user->id || Auth::user()->isAdmin()) )
        <div class="float-end">
          <a href="{{ url('users/'.$user->id.'/edit') }}" class="btn btn-primary text-white">Editar<i class="far fa-edit ms-2"></i></a>
          <!-- <a href="{{ url('users/'.$user->id.'/delete') }}" class="btn btn-danger text-white">Eliminar<i class="far fa-trash-alt ms-2"></i></a> -->
          <button type="button" class="btn btn-danger text-white" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
            Eliminar<i class="far fa-trash-alt ms-2"></i>
          </button>

          @include('partials.modal', ['id' => 'deleteUserModal', 
                                      'title' => 'Eliminar '.$user->name , 
                                      'body' => 'Tem a certeza que quer eliminar permanentemente esta Conta?',
                                      'href' => route('users.delete', $user->id),
                                      'action' => '',
                                      'cancel' => 'Cancelar',
                                      'confirm' => 'Sim'])
        </div>
      @endif
      <h2 class="me-4">{{ $user->name }}</h2> 
      
      <section>
        <div class="row mt-4">
          <div class="col-md-4 mb-2">
            @if ( Auth::check() && !is_null($user->photo) && file_exists( public_path('images/users/'.$user->photo) ) )
            <img src="{{ asset('images/users/'.$user->photo) }}" alt="profile-photo-big" id="profile-photo-big" class="d-block w-100">
            @else
            <img src="{{ asset('images/users/default.jpg') }}" alt="profile-photo-big" id="profile-photo-big" class="d-block w-100">
            @endif
            @if (!$user->isAdmin())
            <p class="h6 text-center m-0 py-2 bg-light">Pontuação: {{$user->score}}</p>
            @endif
          </div>
          <div class="col-md-8 mb-2">
            <h4>
              {{ $user->username }}
              @if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->id == $user->id) && ($user->blocked))
              <i class="fas fa-user-lock"></i>
              @endif 
            </h4> 
            @php 
              $bg = 'bg-info text-dark';
              if ($user->isTeacher()) $bg = 'bg-warning text-dark';
              elseif ($user->isAdmin()) $bg = 'bg-danger text-white';
            @endphp
            <span class="badge {{ $bg }} me-2">{{ $user->type }}</span>
            <span class="text-muted">Aderiu a {{ date('d/m/Y', strtotime($user->registry_date)); }}</span>
            
            @if (!is_null($user->about) || !is_null($user->birthdate) || $user->isStudent())
            <h5 class="mt-4">Sobre mim</h5>
            <div class="ms-3"> 
              @if (!is_null($user->about))
              <p>{{ $user->about }}</p>
              @endif
              @if (!is_null($user->birthdate))
              <p><i class="fas fa-birthday-cake me-2"></i>Aniversário: {{ date('d/m/Y', strtotime($user->birthdate)); }}</p>
              @endif
              @if ($user->isStudent()) 
              <p><i class="fas fa-user-graduate me-2"></i>Ano de Ingresso: {{ $user->entry_year }}</p>
              @endif 
            </div>
            @endif

            <h5 class="mt-4">Contactos</h5> 
            <div class="ms-3">
              <p>Email: {{ $user->email }}</p>
            </div>
          </div>
        </div>
      </section>

      @if (!$user->isAdmin())
      <hr>
      <section class="mt-4">
        @if ( Auth::check() && Auth::user()->id == $user->id )
          <div>
            <div class="float-end">
              <a href="{{ url('questions/create') }}" class="btn btn-primary text-white">Nova Questão <i class="fas fa-plus ms-2"></i></a>
            </div>
            <h5 class="me-4">As minhas Questões</h5>
          </div>
        @else
          <h5>Questões</h5> 
        @endif
        
        @if ( count($questions) != 0 )
        <div class="row mt-4  clear">
          @each('partials.question', $questions, 'question') 
        </div>
        
        <div class="row">
          <div class="col-12 d-flex justify-content-end">
            {!! $questions->appends(['answersPage' => $answers->currentPage(), 'validatedAnswersPage' => $validatedAnswers->currentPage(), 'associatedUcsPage' => $associatedUcs->currentPage()])->links() !!}
          </div>
        </div>
        @else 
        <p>Ainda não realizou Questões</p>
        @endif
      </section>

      <hr>
      <section class="mt-4">
        @if ( Auth::check() && Auth::user()->id == $user->id )
          <h5>As minhas Respostas</h5>
        @else
          <h5>Respostas</h5> 
        @endif

        @if ( count($questions) != 0 )
        <div class="row mt-2">
          @each('partials.answer', $answers, 'answer') 
        </div>

        <div class="row">
          <div class="col-12 d-flex justify-content-end">
            {!! $answers->appends(['questionsPage' => $questions->currentPage(), 'validatedAnswersPage' => $validatedAnswers->currentPage(), 'associatedUcsPage' => $associatedUcs->currentPage()])->links() !!}
          </div>
        </div>
        @else 
        <p>Ainda não realizou Respostas</p>
        @endif
      </section>


      @if ($user->isTeacher()) 
        <hr>
        <section class="mt-4">
          @if ( Auth::check() && Auth::user()->id == $user->id )
            <h5>As minhas Respostas Validadas</h5>
          @else
            <h5>Respostas Validadas</h5> 
          @endif

          @if ( count($validatedAnswers) != 0 )
          <div class="row mt-2">
            @each('partials.answer', $validatedAnswers, 'answer') 
          </div>

          <div class="row">
            <div class="col-12 d-flex justify-content-end">
              {!! $validatedAnswers->appends(['questionsPage' => $questions->currentPage(), 'answersPage' => $answers->currentPage(), 'associatedUcsPage' => $associatedUcs->currentPage()])->links() !!}
            </div>
          </div>
          @else 
          <p>Ainda não validou Respostas</p>
          @endif
        </section>
      @endif

      <hr>
      <section class="mt-4">
        @if ($user->isStudent()) 
          <h5>Unidades Curriculares que Segue</h5> 
        @else
          <h5>Unidades Curriculares que Leciona</h5> 
        @endif

        @if ( count($associatedUcs) != 0 )
        <div class="row mt-2">
          @each('partials.uc', $associatedUcs, 'uc') 
        </div>

        <div class="row">
          <div class="col-12 d-flex justify-content-end">
            {!! $associatedUcs->appends(['questionsPage' => $questions->currentPage(), 'answersPage' => $answers->currentPage(), 'validatedAnswersPage' => $validatedAnswers->currentPage()])->links() !!}
          </div>
        </div>
        @else 
        <p>Ainda não está associado a nenhuma UC</p>
        @endif
      </section>

      @endif
    
    </div>
  </div>
</section>

@endsection
