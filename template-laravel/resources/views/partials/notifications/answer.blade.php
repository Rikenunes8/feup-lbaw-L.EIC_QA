@php
$answer = $notification->intervention()->first();
$question = $answer->parent;
$str = str_replace("<p>", "", $answer->text);
$str = str_replace("</p>", " ", $str);
$str = str_replace("&nbsp;", "", $str);
@endphp
Há uma nova resposta em {{ $question->uc->code }}, à questão "<b>{!! $question->title !!}</b>", vai lá ver!
<p>
  {!! substr($str, 0, 100) !!}
  @if (strlen($str) > 100)
  ...
  @endif
</p>
