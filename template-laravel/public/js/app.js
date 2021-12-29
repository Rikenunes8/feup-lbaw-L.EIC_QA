function addEventListeners() {

  // LEIC Q&A
  let ucFollowers = document.querySelectorAll('div.uc-card p.uc-card-icon a.uc-card-icon-follow');
  [].forEach.call(ucFollowers, function(follower) {
    follower.addEventListener('click', sendFollowUcRequest);
  });

  let ucDeleters = document.querySelectorAll('td.admin-table-uc-actions a.admin-table-delete');
  [].forEach.call(ucDeleters, function(deleter) {
    deleter.addEventListener('click', sendDeleteUcRequest);
  });

  let ucTeacherDeleters = document.querySelectorAll('td.admin-table-teacher-actions a.admin-table-delete');
  [].forEach.call(ucTeacherDeleters, function(deleter) {
    deleter.addEventListener('click', sendDeleteUcTeacherRequest);
  });

  let ucTeacherAdders = document.querySelectorAll('td.admin-table-teacher-actions a.admin-table-add');
  [].forEach.call(ucTeacherAdders, function(adder) {
    adder.addEventListener('click', sendAddUcTeacherRequest);
  });

  let userDeleters = document.querySelectorAll('td.admin-table-user-actions a.admin-table-delete');
  [].forEach.call(userDeleters, function(deleter) {
    deleter.addEventListener('click', sendDeleteUserRequest);
  });

  let userBlockers = document.querySelectorAll('td.admin-table-user-actions a.admin-table-block');
  [].forEach.call(userBlockers, function(blocker) {
    blocker.addEventListener('click', sendBlockUserRequest);
  });

  let interventionDeleters = document.querySelectorAll('.intervention-detail div.question-page-actions a.question-page-delete');
  [].forEach.call(interventionDeleters, function(deleter) {
    deleter.addEventListener('click', sendDeleteInterventionRequest);
  });

  let interventionUpVoters = document.querySelectorAll('.intervention-detail .intervention-votes a.intervention-upvote');
  [].forEach.call(interventionUpVoters, function(voter) {
    voter.addEventListener('click', sendUpVoteInterventionRequest);
  });

  let interventionDownVoters = document.querySelectorAll('.intervention-detail .intervention-votes a.intervention-downvote');
  [].forEach.call(interventionDownVoters, function(voter) {
    voter.addEventListener('click', sendDownVoteInterventionRequest);
  });

}

function encodeForAjax(data) {
  if (data == null) return null;
  return Object.keys(data).map(function(k){
    return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
  }).join('&');
}

function sendAjaxRequest(method, url, data, handler) {
  let request = new XMLHttpRequest();

  request.open(method, url, true);
  request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
  request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  request.addEventListener('load', handler);
  request.send(encodeForAjax(data));
}

function sendFollowUcRequest() {
  let id = this.closest('div.uc-card').getAttribute('data-id');
  let element = document.querySelector('div.uc-card[data-id="' + id + '"] a.uc-card-icon-follow i');
  
  sendAjaxRequest('post', '/api/ucs/follow/' + id, {follow: element.classList.contains('far')}, ucFollowHandler);
}

function sendDeleteUcRequest() {
  let id = this.closest('tr').getAttribute('data-id');

  sendAjaxRequest('delete', '/api/ucs/' + id + '/delete', null, ucDeletedHandler);
}

function sendDeleteUcTeacherRequest() {
  let uc_id = this.closest('table').getAttribute('data-id');
  let teacher_id = this.closest('tr').getAttribute('data-id');

  sendAjaxRequest('delete', '/api/ucs/' + uc_id + '/teachers/' + teacher_id + '/delete', null, ucTeacherDeletedHandler);
}

function sendAddUcTeacherRequest() {
  let uc_id = this.closest('table').getAttribute('data-id');
  let teacher_id = this.closest('tr').getAttribute('data-id');

  sendAjaxRequest('put', '/api/ucs/' + uc_id + '/teachers/' + teacher_id + '/add', null, ucTeacherAddedHandler);
}

function sendDeleteUserRequest() {
  let id = this.closest('tr').getAttribute('data-id');

  sendAjaxRequest('delete', '/api/users/' + id + '/delete', null, userDeletedHandler);
}

function sendBlockUserRequest(event) {
  let user = this.closest('tr');
  let id = user.getAttribute('data-id');
  let reason = user.querySelector('input[name=reason]').value;

  if (reason != '')
    sendAjaxRequest('post', '/api/users/' + id + '/block', {block_reason: reason}, userBlockedHandler);

  event.preventDefault();
}

function sendDeleteInterventionRequest() {
  let id = this.closest('section').getAttribute('data-id');

  sendAjaxRequest('delete', '/api/interventions/' + id + '/delete', null, interventionDeletedHandler);
}

function sendUpVoteInterventionRequest() {
  let id = this.closest('section').getAttribute('data-id');

  sendAjaxRequest('post', '/api/interventions/' + id + '/vote', {vote: true}, interventionVotedHandler);
}

function sendDownVoteInterventionRequest() {
  let id = this.closest('section').getAttribute('data-id');

  sendAjaxRequest('post', '/api/interventions/' + id + '/vote', {vote: false}, interventionVotedHandler);
}

function ucFollowHandler() {
  if (this.status != 200) window.location = '/';
  let uc = JSON.parse(this.responseText);
  let element = document.querySelector('div.uc-card[data-id="' + uc.id + '"] a.uc-card-icon-follow i');

  if (element.classList.contains('fas'))
    element.classList.replace('fas', 'far');
  else 
    element.classList.replace('far', 'fas');
}

function ucDeletedHandler() {
  if (this.status != 200) window.location = '/';
  let uc = JSON.parse(this.responseText);
  let element = document.querySelector('tr[data-id="' + uc.id + '"]');
  element.remove();
}

function ucTeacherDeletedHandler() {
  if (this.status != 200) window.location = '/';
  let teacher = JSON.parse(this.responseText);
  let element = document.querySelector('tr[data-id="' + teacher.id + '"] td.admin-table-teacher-actions');
  element.removeChild(element.lastElementChild);

  let new_a = document.createElement('a');
  new_a.classList.add('btn','btn-primary','text-white', 'admin-table-add');
  new_a.setAttribute('href', '#');
  new_a.innerHTML = `<i class="fas fa-plus"></i> <span class="d-none">Adicionar</span>`;

  new_a.addEventListener('click', sendAddUcTeacherRequest);

  element.appendChild(new_a);
}

function ucTeacherAddedHandler() {
  if (this.status != 200) window.location = '/';
  let teacher = JSON.parse(this.responseText);
  let element = document.querySelector('tr[data-id="' + teacher.id + '"] td.admin-table-teacher-actions');
  element.removeChild(element.lastElementChild);

  let new_a = document.createElement('a');
  new_a.classList.add('btn','btn-danger','text-white', 'admin-table-delete');
  new_a.setAttribute('href', '#');
  new_a.innerHTML = `<i class="far fa-trash-alt"></i> <span class="d-none">Eliminar</span>`;

  new_a.addEventListener('click', sendDeleteUcTeacherRequest);

  element.appendChild(new_a);
}

function userDeletedHandler() {
  if (this.status != 200) window.location = '/';
  let user = JSON.parse(this.responseText);
  let element = document.querySelector('tr[data-id="' + user.id + '"]');
  element.remove();
}

function userBlockedHandler() {
  if (this.status != 200) window.location = '/';
  let user = JSON.parse(this.responseText);
  let input = document.querySelector('tr[data-id="' + user.id + '"] input[name=reason]');
  
  input.disabled = user.blocked;
  if (!user.blocked)
    input.setAttribute('value', '');
  
  let element = document.querySelector('tr[data-id="' + user.id + '"] td.admin-table-user-actions a.admin-table-block');
  let icon = element.querySelector('i');
  let span = element.querySelector('span');

  if (user.blocked) {
    element.classList.replace('btn-info', 'btn-warning');
    icon.classList.replace('fa-lock', 'fa-unlock');
    span.innerHTML = "Unlock";
  } else {
    element.classList.replace('btn-warning', 'btn-info');
    icon.classList.replace('fa-unlock', 'fa-lock');
    span.innerHTML = "Lock";
  }
}

function interventionDeletedHandler() {
  if (this.status != 200) window.location = '/';
  let intervention = JSON.parse(this.responseText);
  let element = document.querySelector('section.intervention-detail[data-id="' + intervention.id + '"]');
  if (element.classList.contains('question-detail'))
    location.reload();
  else
    element.remove();

}


function interventionVotedHandler() {
  console.log(this.responseText);
  if (this.status != 200) window.location = '/';
  let intervention = JSON.parse(this.responseText);

  let element = document.querySelector('section.intervention-detail[data-id="' + intervention.id + '"] .intervention-votes-number' );
  element.innerHTML = intervention.votes;
}

function showCommentCreateForm(btn) {
  let id = btn.value;
  let form = document.querySelector('section.comment-answer-form-'+ id);

  if (form.classList.contains('d-none')) {
    form.classList.replace('d-none', 'd-flex');
    form.classList.add('flex-row-reverse');
  } else {
    form.classList.remove('flex-row-reverse');
    form.classList.replace('d-flex', 'd-none');
  }
}

addEventListeners();

$(document).ready(function () {
  $('#admin-table').DataTable({
    "pagingType": "simple_numbers"
  });
  $('.dataTables_length').addClass('bs-select');

  tinymce.init({selector:'textarea.text-editor'});
});
