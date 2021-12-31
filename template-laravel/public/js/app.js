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

  let answerValidValidaters = document.querySelectorAll('.intervention-detail .question-card-icon a.validate-valid');
  [].forEach.call(answerValidValidaters, function(validater) {
    validater.addEventListener('click', sendValidAnswerRequest);
  });
  let answerInvalidValidaters = document.querySelectorAll('.intervention-detail .question-card-icon a.validate-invalid');
  [].forEach.call(answerInvalidValidaters, function(validater) {
    validater.addEventListener('click', sendInvalidAnswerRequest);
  });
  let answerNoneValidaters = document.querySelectorAll('.intervention-detail .question-card-icon a.invalidate');
  [].forEach.call(answerNoneValidaters, function(validater) {
    validater.addEventListener('click', sendNoneAnswerRequest);
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

function sendValidAnswerRequest() {
  let id = this.closest('section').getAttribute('data-id');

  sendAjaxRequest('post', '/api/interventions/' + id + '/validate', {valid: true}, answerValidatedHandler);
}
function sendInvalidAnswerRequest() {
  let id = this.closest('section').getAttribute('data-id');

  sendAjaxRequest('post', '/api/interventions/' + id + '/validate', {valid: false}, answerValidatedHandler);
}
function sendNoneAnswerRequest() {
  let id = this.closest('section').getAttribute('data-id');

  sendAjaxRequest('post', '/api/interventions/' + id + '/validate', {valid: null}, answerValidatedHandler);
}

function ucFollowHandler() {
  if (this.status == 403) {
    let error_section = document.querySelector('section.error-msg');
    error_section.appendChild(createError("Ação não autorizada"));
    return;
  } 
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
  if (this.status == 403) {
    let error_section = document.querySelector('section.error-msg');
    error_section.appendChild(createError("Eliminação não autorizada"));
    return;
  } 
  if (this.status != 200) window.location = '/';
  let intervention = JSON.parse(this.responseText);
  let element = document.querySelector('section.intervention-detail[data-id="' + intervention.id + '"]');
  if (element.classList.contains('question-detail'))
    location.reload();
  else
    element.remove();

}

function interventionVotedHandler() {
  if (this.status == 403) {
    let error_section = document.querySelector('section.error-msg');
    error_section.appendChild(createError("Votação não autorizada"));
    return;
  } 
  if (this.status != 200) window.location = '/';
  let intervention = JSON.parse(this.responseText);

  let element = document.querySelector('section.intervention-detail[data-id="' + intervention.id + '"] .intervention-votes-number' );
  element.innerHTML = intervention.votes;
}

function answerValidatedHandler() {
  if (this.status == 403) {
    let error_section = document.querySelector('section.error-msg');
    error_section.appendChild(createError("Validação não autorizada"));
    return;
  } 
  if (this.status != 200) window.location = '/';
  let ret = JSON.parse(this.responseText);

  let intervention = ret[0];
  let valid = ret[1];

  let cardIcon = document.querySelector('.answer-detail[data-id="' + intervention.id + '"] div.question-card-icon');
  cardIcon.innerHTML = '';

  let a1 = document.createElement('a');
  a1.setAttribute('href', "#");
  a1.setAttribute('class', "btn btn-success text-white me-1");
  a1.innerHTML = '<i class="fas fa-check"></i>'

  let a2 = document.createElement('a');
  a2.setAttribute('href', "#");
  a2.setAttribute('class', "btn btn-danger text-white me-1");
  a2.innerHTML = '<i class="fas fa-times"></i>'
  
  if (valid == null) {
    a1.classList.add("validate-valid");
    a1.addEventListener('click', sendValidAnswerRequest);
    cardIcon.appendChild(a1);
    a2.classList.add("validate-invalid");
    a2.addEventListener('click', sendInvalidAnswerRequest);
    cardIcon.appendChild(a2);
  }
  else if (valid) {
    a1.classList.add("invalidate");
    a1.addEventListener('click', sendNoneAnswerRequest);
    cardIcon.appendChild(a1);
  }
  else {
    a2.classList.add("invalidate");
    a2.addEventListener('click', sendNoneAnswerRequest);
    cardIcon.appendChild(a2);
  }
}

function focusSearchInput() {
  document.querySelector('input#search-input').focus(); 
}

function showRegisterFormFields() {
  const select = document.querySelector('select#usertype');
  if (select === null) return;

  const type = select.value;
  let teacher_student_fileds = document.querySelectorAll('div.teacher-student-extra-fields');
  let only_student_fields = document.querySelectorAll('div.student-extra-fields');
  let name_field = document.querySelector('input#name');
  let entry_year_field = document.querySelector('input#entryyear');
  let about_field = document.querySelector('textarea#about');

  switch (type) {
    case "Admin":
      [].forEach.call(teacher_student_fileds, function(field) { field.classList.add('d-none'); });
      [].forEach.call(only_student_fields, function(field) { field.classList.add('d-none'); });
      name_field.required = false;
      entry_year_field.required = false;
      break;
    case "Teacher":
      [].forEach.call(teacher_student_fileds, function(field) { field.classList.remove('d-none'); });
      [].forEach.call(only_student_fields, function(field) { field.classList.add('d-none'); });
      name_field.required = true;
      entry_year_field.required = false;
      about_field.setAttribute('rows', '8');
      break;
    case "Student":
      [].forEach.call(teacher_student_fileds, function(field) { field.classList.remove('d-none'); });
      [].forEach.call(only_student_fields, function(field) { field.classList.remove('d-none'); });
      name_field.required = true;
      entry_year_field.required = true;
      about_field.setAttribute('rows', '11');
      break;
    default:
      break;
  }
}

function showCommentCreateForm(btn) {
  let id = btn.getAttribute("data-value");
  let form = document.querySelector('section#comment-answer-form-'+ id);

  if (form.classList.contains('d-none')) {
    form.classList.replace('d-none', 'd-flex');
    form.classList.add('flex-row-reverse');
  } else {
    form.classList.remove('flex-row-reverse');
    form.classList.replace('d-flex', 'd-none');
  }
}

function showFilterForm() {
  let form = document.querySelector('div.filter-card');

  if (form.classList.contains('d-none')) {
    form.classList.replace('d-none', 'd-block');
  } else {
    form.classList.replace('d-block', 'd-none');
  }
}

function createError(msg) {
  let error_div = document.createElement('div');
  error_div.classList.add('mt-1', 'py-2', 'alert', 'alert-danger', 'alert-dismissible', 'fade', 'show');

  let close_btn = document.createElement('button');
  close_btn.setAttribute('type', 'button');
  close_btn.setAttribute('data-bs-dismiss', 'alert');
  close_btn.classList.add('h-auto','btn-close','btn-sm');

  let error_txt = document.createTextNode(`${msg}`);

  error_div.appendChild(close_btn);
  error_div.appendChild(error_txt);

  return error_div;
}

addEventListeners();

$(document).ready(function () {
  $('#admin-table').DataTable({
    "pagingType": "simple_numbers"
  });
  $('.dataTables_length').addClass('bs-select');

  tinymce.init({selector:'textarea.text-editor'});
  showRegisterFormFields();
});

$('.dropdown.dropdown-keep-open').on('hide.bs.dropdown', function (e) {
  if (!e.clickEvent) { return true; }
  var target = $(e.clickEvent.target);
  return !(target.hasClass('dropdown-keep-open') || target.parents('.dropdown-keep-open').length);
});

