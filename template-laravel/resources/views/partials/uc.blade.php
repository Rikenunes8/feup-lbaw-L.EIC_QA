<article class="card" data-id="{{ $uc->id }}">
<header>
  <h2><a href="/ucs/{{ $uc->id }}">{{ $uc->name }}</a></h2>
  <p>{{ $uc->code }}</p>
  <p>{{ $uc->description }}</p>
  <a href="#" class="delete">&#10761;</a>
  <!-- list of the techers of the uc -->
</header>

</article>
