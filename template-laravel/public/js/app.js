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

  let ucTeacherRemovers = document.querySelectorAll('td.admin-table-teacher-actions a.admin-table-remove');
  [].forEach.call(ucTeacherRemovers, function(remover) {
    remover.addEventListener('click', sendRemoveUcTeacherRequest);
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

  let userActivaters = document.querySelectorAll('td.admin-table-user-actions a.admin-table-active');
  [].forEach.call(userActivaters, function(activater) {
    activater.addEventListener('click', sendActiveUserRequest);
  });

  let allUsersActivaters = document.querySelectorAll('section#admin-requests-page a.admin-table-active-all');
  [].forEach.call(allUsersActivaters, function(activater) {
    activater.addEventListener('click', sendActiveAllUserRequest);
  });

  let allUsersRejectors = document.querySelectorAll('section#admin-requests-page a.admin-table-reject-all');
  [].forEach.call(allUsersRejectors, function(rejector) {
    rejector.addEventListener('click', sendRejectAllUserRequest);
  });

  let interventionDeleters = document.querySelectorAll('.intervention-detail div.question-page-actions-modals a.question-page-delete');
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

  let answerValidValidaters = document.querySelectorAll('.intervention-detail .question-card-icon-validate a.validate-valid');
  [].forEach.call(answerValidValidaters, function(validater) {
    validater.addEventListener('click', sendValidAnswerRequest);
  });
  let answerInvalidValidaters = document.querySelectorAll('.intervention-detail .question-card-icon-validate a.validate-invalid');
  [].forEach.call(answerInvalidValidaters, function(validater) {
    validater.addEventListener('click', sendInvalidAnswerRequest);
  });
  let answerNoneValidaters = document.querySelectorAll('.intervention-detail .question-card-icon-validate a.invalidate');
  [].forEach.call(answerNoneValidaters, function(validater) {
    validater.addEventListener('click', sendNoneAnswerRequest);
  });

  let interventionReporters = document.querySelectorAll('#question-page .intervention-detail a.question-page-report');
  [].forEach.call(interventionReporters, function(reporter) {
    reporter.addEventListener('click', sendInterventionReportRequest);
  });

  let notificationReadMarkers = document.querySelectorAll('.notification-card .notifications-page-actions a.notifications-page-envelope');
  [].forEach.call(notificationReadMarkers, function(markers) {
    markers.addEventListener('click', sendMarkReadNotificationRequest);
  });
  let notificationRemovers = document.querySelectorAll('.notification-card .notifications-page-actions a.notifications-page-remove');
  [].forEach.call(notificationRemovers, function(removers) {
    removers.addEventListener('click', sendRemoveNotificationRequest);
  });

  let reportRemovers = document.querySelectorAll('td.admin-table-reports-actions a.reports-page-remove');
  [].forEach.call(reportRemovers, function(deleter) {
    deleter.addEventListener('click', sendRemoveReportRequest);
  });

  let receiveEmailsSwitcher = document.querySelectorAll('#user-profile-page div.form-switch input');
  [].forEach.call(receiveEmailsSwitcher, function(switcher) {
    switcher.addEventListener('click', switchReceiveEmailRequest);
  });

  let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  let popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
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



// ----------------------- REQUESTS ---------------------------

function sendFollowUcRequest() {
  let user_id = this.closest('section#ucs-page, section#uc-page, div.user-profile').getAttribute('data-id');
  let uc_id = this.closest('div.uc-card').getAttribute('data-id');
  let element = document.querySelector('div.uc-card[data-id="' + uc_id + '"] a.uc-card-icon-follow i');

  sendAjaxRequest('post', '/api/users/' + user_id + '/follow/' + uc_id, {follow: element.classList.contains('far')}, ucFollowHandler);
}

function sendDeleteUcRequest() {
  let id = this.closest('tr').getAttribute('data-id');

  sendAjaxRequest('delete', '/api/ucs/' + id + '/delete', null, ucDeletedHandler)
}

function sendRemoveUcTeacherRequest() {
  let uc_id = this.closest('table').getAttribute('data-id');
  let teacher_id = this.closest('tr').getAttribute('data-id');

  sendAjaxRequest('delete', '/api/ucs/' + uc_id + '/teachers/' + teacher_id + '/remove', null, ucTeacherRemovedHandler);
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

  if (reason != '') {
      sendAjaxRequest('post', '/api/users/' + id + '/block', {block_reason: reason}, userBlockedHandler);
  } else {
    let error_section = document.querySelector('div.error-msg');
    error_section.appendChild(createAlert('alert-danger', "Não é possível bloquear um utilizador sem uma razão."));
  }

  event.preventDefault();
}

function sendActiveUserRequest() {
  let id = this.closest('tr').getAttribute('data-id');

  sendAjaxRequest('post', '/api/users/' + id + '/active', null, userActivatedHandler);
}

function sendActiveAllUserRequest() {
  let all_requests = document.querySelectorAll('table#admin-table tbody tr');

  all_requests.forEach(request => {
    if (request.hasAttribute('data-id')) {
      let id = request.getAttribute('data-id');
      sendAjaxRequest('post', '/api/users/' + id + '/active', null, userActivatedHandler);
    }
  });
}

function sendRejectAllUserRequest() {
  let all_requests = document.querySelectorAll('table#admin-table tbody tr');

  all_requests.forEach(request => {
    if (request.hasAttribute('data-id')) {
      let id = request.getAttribute('data-id'); 
      sendAjaxRequest('delete', '/api/users/' + id + '/delete', null, userDeletedHandler);
    }
  });
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

function sendInterventionReportRequest() {
  let id = this.closest('section').getAttribute('data-id');

  sendAjaxRequest('post', '/api/interventions/' + id + '/report', {valid: null}, interventionReportHandler);
}

function sendMarkReadNotificationRequest() {
  let card = this.closest('div.notification-card');
  let id = card.getAttribute('data-id');

  sendAjaxRequest('post', '/api/notifications/' + id + '/read', {read: card.classList.contains('notification-read')}, notificationMarkReadHandler);
}

function sendRemoveNotificationRequest() {
  let id = this.closest('div.notification-card').getAttribute('data-id');

  sendAjaxRequest('post', '/api/notifications/' + id + '/remove', null, notificationRemoveHandler);
}

function sendRemoveReportRequest() {
  let id = this.closest('tr').getAttribute('data-id');

  sendAjaxRequest('post', '/api/notifications/' + id + '/remove', null, reportRemovedHandler);
}

function switchReceiveEmailRequest() {
  let id = this.closest('div.user-profile').getAttribute('data-id');

  sendAjaxRequest('post', '/api/users/' + id + '/email', null, switchReceiveEmailHandler);
}
// ------------------- END OF REQUESTS ---------------------

// ---------------------- HANDLERS -------------------------

function ucFollowHandler() {
  if (this.status == 403) {
    let error_section = document.querySelector('div.error-msg');
    error_section.appendChild(createAlert('alert-danger', "Ação não autorizada"));
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

  admin_table.row(element).remove().draw(false);
}

function ucTeacherRemovedHandler() {
  if (this.status != 200) window.location = '/';
  let teacher = JSON.parse(this.responseText);
  let element = document.querySelector('tr[data-id="' + teacher.id + '"] td.admin-table-teacher-actions');
  element.removeChild(element.lastElementChild);

  let new_a = document.createElement('a');
  new_a.classList.add('btn','btn-primary','text-white', 'admin-table-add');
  new_a.setAttribute('data-toogle', 'tooltip');
  new_a.setAttribute('title', 'Associar Docente');
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
  new_a.classList.add('btn','btn-danger','text-white', 'admin-table-remove');
  new_a.setAttribute('data-toogle', 'tooltip');
  new_a.setAttribute('title', 'Desassociar Docente');
  new_a.innerHTML = `<i class="fas fa-minus"></i> <span class="d-none">Remover</span>`;
  new_a.addEventListener('click', sendRemoveUcTeacherRequest);

  element.appendChild(new_a);
}

function userDeletedHandler() {
  if (this.status != 200) window.location = '/';
  let user = JSON.parse(this.responseText);
  let element = document.querySelector('tr[data-id="' + user.id + '"]');
  element.remove();

  admin_table.row(element).remove().draw(false);
}

function userBlockedHandler() {
  if (this.status != 200) window.location = '/';
  let user = JSON.parse(this.responseText);
  let input = document.querySelector('tr[data-id="' + user.id + '"] input[name=reason]');

  input.disabled = user.blocked;
  if (!user.blocked)
    input.value = '';

  let element = document.querySelector('tr[data-id="' + user.id + '"] td.admin-table-user-actions button.block-btn');
  let icon = element.querySelector('i');
  let span = element.querySelector('span');

  if (user.blocked) {
    element.classList.replace('btn-info', 'btn-dark');
    element.classList.replace('text-dark', 'text-white');
    icon.classList.replace('fa-lock', 'fa-unlock');
    span.innerHTML = "Unlock";
  } else {
    element.classList.replace('btn-dark', 'btn-info');
    element.classList.replace('text-white', 'text-dark');
    icon.classList.replace('fa-unlock', 'fa-lock');
    span.innerHTML = "Lock";
  }
}

function userActivatedHandler() {
  if (this.status != 200) window.location = '/';
  let user = JSON.parse(this.responseText);
  let element = document.querySelector('tr[data-id="' + user.id + '"]');
  element.remove();

  let error_section = document.querySelector('div.error-msg');
  error_section.appendChild(createAlert('alert-success', "Conta '" + user.email + "' ativada com sucesso!"));

  admin_table.row(element).remove().draw(false);
}

function interventionDeletedHandler() {
  if (this.status == 403) {
    let error_section = document.querySelector('div.error-msg');
    error_section.appendChild(createAlert('alert-danger', "Eliminação não autorizada"));
    return;
  }
  if (this.status != 200) window.location = '/';
  let intervention = JSON.parse(this.responseText);
  let element = document.querySelector('section.intervention-detail[data-id="' + intervention.id + '"]');
  if (element.classList.contains('question-detail')) {
    location.reload();
  } else if (element.classList.contains('answer-detail')) {
    element.remove();
    let answerComments = document.querySelectorAll('section.comment-parent-' + intervention.id);
    [].forEach.call(answerComments, function(comment) {
      comment.remove();
    });
  } else {
    element.remove();
  }
}

function interventionVotedHandler() {
  if (this.status == 403) {
    let error_section = document.querySelector('div.error-msg');
    error_section.appendChild(createAlert('alert-danger', "Votação não autorizada"));
    return;
  }
  if (this.status != 200) window.location = '/';
  let intervention = JSON.parse(this.responseText);

  let element = document.querySelector('section.intervention-detail[data-id="' + intervention.id + '"] .intervention-votes-number' );
  element.innerHTML = intervention.votes;
}

function answerValidatedHandler() {
  if (this.status == 403) {
    let error_section = document.querySelector('div.error-msg');
    error_section.appendChild(createAlert('alert-danger', "Validação não autorizada"));
    return;
  }
  if (this.status != 200) window.location = '/';
  let ret = JSON.parse(this.responseText);

  let intervention = ret[0];
  let valid = ret[1];

  let cardIcon = document.querySelector('.answer-detail[data-id="' + intervention.id + '"] div.question-card-icon-validate');
  cardIcon.innerHTML = '';

  let a_validate_valid = document.createElement('a');
  a_validate_valid.setAttribute('class', "btn btn-outline-success text-success me-1 validate-valid");
  a_validate_valid.setAttribute('data-toogle', "tooltip");
  a_validate_valid.setAttribute('title', "Validar");
  a_validate_valid.innerHTML = '<i class="fas fa-check"></i>';
  a_validate_valid.addEventListener('click', sendValidAnswerRequest);

  let a_validate_invalid = document.createElement('a');
  a_validate_invalid.setAttribute('class', "btn btn-outline-danger text-danger me-1 validate-invalid");
  a_validate_invalid.setAttribute('data-toogle', "tooltip");
  a_validate_invalid.setAttribute('title', "Invalidar");
  a_validate_invalid.innerHTML = '<i class="fas fa-times"></i>';
  a_validate_invalid.addEventListener('click', sendInvalidAnswerRequest);

  let a_invalidate = document.createElement('a');
  a_invalidate.setAttribute('class', "btn text-white invalidate me-1");
  a_invalidate.setAttribute('data-toogle', "tooltip");
  a_invalidate.innerHTML = '<i class="fas ' + (valid ? 'fa-check' : 'fa-times')+ '"></i>';
  a_invalidate.addEventListener('click', sendNoneAnswerRequest);

  if (valid == null) {
    cardIcon.appendChild(a_validate_valid);
    cardIcon.appendChild(a_validate_invalid);
  }
  else if (valid) {
    a_invalidate.classList.add("btn-success");
    a_invalidate.setAttribute('title', "Remover Validação");
    cardIcon.appendChild(a_invalidate);
    cardIcon.appendChild(a_validate_invalid);
  }
  else {
    cardIcon.appendChild(a_validate_valid);
    a_invalidate.classList.add("btn-danger");
    a_invalidate.setAttribute('title', "Remover Invalidação");
    cardIcon.appendChild(a_invalidate);
  }
}

function interventionReportHandler() {
  let error_section = document.querySelector('div.error-msg');
  if (this.status == 403) {
    error_section.appendChild(createAlert('alert-danger', "Denúncia não autorizada"));
    return;
  }
  if (this.status != 200) window.location = '/';
  let intervention = JSON.parse(this.responseText);
  error_section.appendChild(createAlert('alert-success', "Denúncia realizada com sucesso!"));
}

function notificationMarkReadHandler() {
  if (this.status == 403) {
    let error_section = document.querySelector('div.error-msg');
    error_section.appendChild(createAlert('alert-danger', "Ação não autorizada"));
    return;
  }
  if (this.status != 200) window.location = '/';
  let notification = JSON.parse(this.responseText);
  let card = document.querySelector('div.notification-card[data-id="' + notification.id + '"]');
  let element = card.querySelector('a.notifications-page-envelope i');
  let icon = document.querySelector('#header-notification-icon span');


  if (card.classList.contains('notification-read')) {
    card.classList.replace('notification-read', 'notification-unread');
    element.classList.replace('fa-envelope', 'fa-envelope-open');
    if (icon.innerHTML == '') icon.innerHTML = 1;
    else if (icon.innerHTML != '+99') icon.innerHTML = parseInt(icon.innerHTML) + 1;
    if (icon.innerHTML >= 100) icon.innerHTML = '+99';
  }
  else {
    card.classList.replace('notification-unread', 'notification-read');
    element.classList.replace('fa-envelope-open', 'fa-envelope');
    icon.innerHTML -= 1;
    if (icon.innerHTML == 0) icon.innerHTML = '';
  }

}

function notificationRemoveHandler() {
  if (this.status == 403) {
    let error_section = document.querySelector('div.error-msg');
    error_section.appendChild(createAlert('alert-danger', "Ação não autorizada"));
    return;
  }
  if (this.status != 200) window.location = '/';
  let notification = JSON.parse(this.responseText);
  let card = document.querySelector('div.notification-card[data-id="' + notification.id + '"]');
  let element = card.closest('section');
  let icon = document.querySelector('#header-notification-icon span');
  if (card.classList.contains('notification-unread')) icon.innerHTML -= 1;
  if (icon.innerHTML == 0) icon.innerHTML = '';

  element.remove();
}

function reportRemovedHandler() {
  if (this.status == 403) {
    let error_section = document.querySelector('div.error-msg');
    error_section.appendChild(createAlert('alert-danger', "Ação não autorizada"));
    return;
  }
  if (this.status != 200) window.location = '/';
  let report = JSON.parse(this.responseText);
  let element = document.querySelector('tr[data-id="' + report.id + '"]');
  element.remove();

  admin_table.row(element).remove().draw(false);
}

function switchReceiveEmailHandler() {
  if (this.status == 403) {
    let error_section = document.querySelector('div.error-msg');
    error_section.appendChild(createAlert('alert-danger', "Ação não autorizada"));
    return;
  }
  if (this.status != 200) window.location = '/';
}

// -------------------- END OF HANDLERS ----------------------------


function focusSearchInput() {
  document.querySelector('input#search-input').focus();
}

function focusAnswerInput() {
  tinyMCE.get('answer-textarea').focus();
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

function createAlert(type, msg) {
  let error_div = document.createElement('div');
  error_div.classList.add('mt-1', 'py-2', 'alert', type, 'alert-dismissible', 'fade', 'show');

  let close_btn = document.createElement('button');
  close_btn.setAttribute('type', 'button');
  close_btn.setAttribute('data-bs-dismiss', 'alert');
  close_btn.classList.add('h-auto','btn-close','btn-sm');

  let error_txt = document.createTextNode(`${msg}`);

  error_div.appendChild(close_btn);
  error_div.appendChild(error_txt);

  window.setTimeout(function() {
    $(error_div).fadeTo(500, 0).slideUp(500, function() {
      this.remove();
    });
  }, 4000);

  return error_div;
}

function getUrlVars() {
  var vars = {};
  var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
      vars[key] = value;
  });
  return vars;
}

addEventListeners();

function formatDetails ( details ) {
  const data = JSON.parse(details);

  let img_src = (data.photo != null) ? '/images/users/' + data.photo : '/images/users/default.jpg';
  let about = (data.about != null) ? data.about : 'Não definida';
  let birthdate = (data.birthdate != null) ? data.birthdate.substring(0, 10) : 'Não definido';
  let entry_year = (data.type != 'Student') ? '' : '<p><i class="fas fa-user-graduate me-2"></i>Ano de Ingresso: ' + data.entry_year + '</p>';

  return  '<div class="row mt-4">' +
            '<div class="col-2">' +
              '<img src="' + img_src + '" alt="profile-photo-big" id="profile-photo-big" class="d-block w-100">' +
            '</div>' +
            '<div class="col-10">' +
              '<h5>Sobre mim</h5>' +
              '<div class="ms-3">' +
                '<p>Apresentação: ' + about + '</p>' +
                '<p><i class="fas fa-birthday-cake me-2"></i>Aniversário: ' + birthdate + '</p>' +
                entry_year +
              '</div>' +
            '</div>' +
          '</div>';
}

var admin_table;
$(document).ready(function () {
  admin_table = $('#admin-table').DataTable({
    "page": 1,
    "pagingType": "simple_numbers",
    "initComplete" : function() {
      if (typeof getUrlVars()['searchDt'] !== 'undefined') 
        this.api().search(decodeURI(getUrlVars()['searchDt'])).draw();   
    },
  });
  $('.dataTables_length').addClass('bs-select');

  // Add event listener for opening and closing details of a row (used in admin requests page)
  $('#admin-table tbody').on('click', 'td.expand-button', function () {
    var tr = $(this).closest('tr');
    var row = admin_table.row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        $(this).html('+');
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        // Open this row
        $(this).html('-');
        row.child( formatDetails( tr.attr('data-details') ) ).show();
        tr.addClass('shown');
    }
  });

  $('.filter-ucs-multiple').select2();

  tinymce.init({
    selector:'textarea.text-editor',
    menubar: false,
    toolbar_sticky: true,
    toolbar: 'undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | table codesample link | fullscreen',
    plugins: 'autolink link codesample advlist lists table fullscreen',
    style_formats: [
      { title: 'Headings', items: [
        { title: 'Heading 1', format: 'h1' },
        { title: 'Heading 2', format: 'h2' },
        { title: 'Heading 3', format: 'h3' },
        { title: 'Heading 4', format: 'h4' },
        { title: 'Heading 5', format: 'h5' },
        { title: 'Heading 6', format: 'h6' }
      ]},
      { title: 'Inline', items: [
        { title: 'Superscript', format: 'superscript' },
        { title: 'Subscript', format: 'subscript' },
        { title: 'Code', format: 'code' }
      ]},
      { title: 'Blocks', items: [
        { title: 'Paragraph', format: 'p' },
        { title: 'Blockquote', format: 'blockquote' }
      ]}
    ]
  });

  showRegisterFormFields();
});

$('#deleteUserModal a').on('click', function (event) {
  var $action = $(event.target);
  event.preventDefault();
  $(this).closest('.modal').on('hidden.bs.modal', function(ev) {
    var $href = $action.attr('href');
    window.location.href = $href;
  });
})

$("#user-profile-tabs.nav .nav-link").on("click", function() {
  $("#user-profile-tabs.nav").find(".active").removeClass("active");
  $(this).addClass("active");
});

window.setTimeout(function() {
  $(".alert").fadeTo(500, 0).slideUp(500, function() {
    this.remove();
  });
}, 4000);
