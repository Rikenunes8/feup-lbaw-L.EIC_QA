@component('mail::message')

Mensagem L.EIC Q&A

<strong>Nome:</strong> {{ $data['name'] }}

<strong>Email:</strong> {{ $data['email'] }}

<strong>Assunto:</strong> {{ $data['subject'] }}

<strong>Mensagem:</strong> {{ $data['message'] }}

@endcomponent
