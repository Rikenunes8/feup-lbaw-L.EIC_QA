@php
$question = $notification->intervention()->first()
@endphp
Há uma nova questão em {{ $question->uc->code }}, vai lá ver!
<b>{!! $question->title !!}</b>