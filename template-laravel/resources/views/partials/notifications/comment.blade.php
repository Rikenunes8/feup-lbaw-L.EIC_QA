@php
$comment = $notification->intervention()->first();
$question = $comment->parent->parent;
$str = str_replace("<p>", "", $comment->text);
$str = str_replace("</p>", " ", $str);
$str = str_replace("&nbsp;", "", $str);
@endphp
Há um novo comentário em {{ $question->uc->code }}, na tua resposta à questão "<b>{!! $question->title !!}</b>", vai lá ver!
<p>
  {!! substr($str, 0, 100) !!}
  @if (strlen($str) > 100)
  ...
  @endif
</p>