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

  // Thingy examples
  let itemCheckers = document.querySelectorAll('article.card li.item input[type=checkbox]');
  [].forEach.call(itemCheckers, function(checker) {
    checker.addEventListener('change', sendItemUpdateRequest);
  });

  let itemCreators = document.querySelectorAll('article.card form.new_item');
  [].forEach.call(itemCreators, function(creator) {
    creator.addEventListener('submit', sendCreateItemRequest);
  });

  let itemDeleters = document.querySelectorAll('article.card li a.delete');
  [].forEach.call(itemDeleters, function(deleter) {
    deleter.addEventListener('click', sendDeleteItemRequest);
  });

  let cardDeleters = document.querySelectorAll('article.card header a.delete');
  [].forEach.call(cardDeleters, function(deleter) {
    deleter.addEventListener('click', sendDeleteCardRequest);
  });

  let cardCreator = document.querySelector('article.card form.new_card');
  if (cardCreator != null)
    cardCreator.addEventListener('submit', sendCreateCardRequest);
  // ----
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

function ucFollowHandler() {
  if (this.status != 200) window.location = '/';
  let uc = JSON.parse(this.responseText);
  let element = document.querySelector('div.uc-card[data-id="' + uc.id + '"] a.uc-card-icon-follow i');

  if (element.classList.contains('fas')) {
    element.classList.remove('fas');
    element.classList.add('far');
  } else {
    element.classList.remove('far');
    element.classList.add('fas');
  }
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

// Thingy examples
function sendItemUpdateRequest() {
  let item = this.closest('li.item');
  let id = item.getAttribute('data-id');
  let checked = item.querySelector('input[type=checkbox]').checked;

  sendAjaxRequest('post', '/api/item/' + id, {done: checked}, itemUpdatedHandler);
}

function sendDeleteItemRequest() {
  let id = this.closest('li.item').getAttribute('data-id');

  sendAjaxRequest('delete', '/api/item/' + id, null, itemDeletedHandler);
}

function sendCreateItemRequest(event) {
  let id = this.closest('article').getAttribute('data-id');
  let description = this.querySelector('input[name=description]').value;

  if (description != '')
    sendAjaxRequest('put', '/api/cards/' + id, {description: description}, itemAddedHandler);

  event.preventDefault();
}

function sendDeleteCardRequest(event) {
  let id = this.closest('article').getAttribute('data-id');

  sendAjaxRequest('delete', '/api/cards/' + id, null, cardDeletedHandler);
}

function sendCreateCardRequest(event) {
  let name = this.querySelector('input[name=name]').value;

  if (name != '')
    sendAjaxRequest('put', '/api/cards/', {name: name}, cardAddedHandler);

  event.preventDefault();
}

function itemUpdatedHandler() {
  let item = JSON.parse(this.responseText);
  let element = document.querySelector('li.item[data-id="' + item.id + '"]');
  let input = element.querySelector('input[type=checkbox]');
  element.checked = item.done == "true";
}

function itemAddedHandler() {
  if (this.status != 200) window.location = '/';
  let item = JSON.parse(this.responseText);

  // Create the new item
  let new_item = createItem(item);

  // Insert the new item
  let card = document.querySelector('article.card[data-id="' + item.card_id + '"]');
  let form = card.querySelector('form.new_item');
  form.previousElementSibling.append(new_item);

  // Reset the new item form
  form.querySelector('[type=text]').value="";
}

function itemDeletedHandler() {
  if (this.status != 200) window.location = '/';
  let item = JSON.parse(this.responseText);
  let element = document.querySelector('li.item[data-id="' + item.id + '"]');
  element.remove();
}

function cardDeletedHandler() {
  if (this.status != 200) window.location = '/';
  let card = JSON.parse(this.responseText);
  let article = document.querySelector('article.card[data-id="'+ card.id + '"]');
  article.remove();
}

function cardAddedHandler() {
  if (this.status != 200) window.location = '/';
  let card = JSON.parse(this.responseText);

  // Create the new card
  let new_card = createCard(card);

  // Reset the new card input
  let form = document.querySelector('article.card form.new_card');
  form.querySelector('[type=text]').value="";

  // Insert the new card
  let article = form.parentElement;
  let section = article.parentElement;
  section.insertBefore(new_card, article);

  // Focus on adding an item to the new card
  new_card.querySelector('[type=text]').focus();
}

function createCard(card) {
  let new_card = document.createElement('article');
  new_card.classList.add('card');
  new_card.setAttribute('data-id', card.id);
  new_card.innerHTML = `

  <header>
    <h2><a href="cards/${card.id}">${card.name}</a></h2>
    <a href="#" class="delete">&#10761;</a>
  </header>
  <ul></ul>
  <form class="new_item">
    <input name="description" type="text">
  </form>`;

  let creator = new_card.querySelector('form.new_item');
  creator.addEventListener('submit', sendCreateItemRequest);

  let deleter = new_card.querySelector('header a.delete');
  deleter.addEventListener('click', sendDeleteCardRequest);

  return new_card;
}

function createItem(item) {
  let new_item = document.createElement('li');
  new_item.classList.add('item');
  new_item.setAttribute('data-id', item.id);
  new_item.innerHTML = `
  <label>
    <input type="checkbox"> <span>${item.description}</span><a href="#" class="delete">&#10761;</a>
  </label>
  `;

  new_item.querySelector('input').addEventListener('change', sendItemUpdateRequest);
  new_item.querySelector('a.delete').addEventListener('click', sendDeleteItemRequest);

  return new_item;
}
// --- 

addEventListeners();

$(document).ready(function () {
  $('#admin-table').DataTable({
    "pagingType": "simple_numbers"
  });
  $('.dataTables_length').addClass('bs-select');
});
