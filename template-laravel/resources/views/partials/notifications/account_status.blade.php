@php
if ($notification->status == 'active') $status = 'ativa';
else if ($notification->status == 'block') $status = 'bloqueada';
else $status = 'eliminada';
@endphp
Tiveste uma atualização no estado de conta. A tua conta encontra-se agora <b>{{ $status }}</b>.