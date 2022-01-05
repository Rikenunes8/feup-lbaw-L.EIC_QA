<!-- Modal : id, title, body, action, cancel, confirm -->
<div class="modal fade" id="{{ $id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-4">
        {{ $body }}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">{{ $cancel }}</button>
        <a href="{{ $href }}" class="btn btn-primary {{ $action }}" data-bs-dismiss="modal">{{ $confirm }}</a>
      </div>
    </div>
  </div>
</div>