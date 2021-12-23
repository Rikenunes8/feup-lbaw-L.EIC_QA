<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Home
Route::get('/', 'Auth\LoginController@home');

// Cards
Route::get('cards', 'CardController@list');
Route::get('cards/{id}', 'CardController@show');

// API
Route::put('api/cards', 'CardController@create');
Route::delete('api/cards/{card_id}', 'CardController@delete');
Route::put('api/cards/{card_id}/', 'ItemController@create');
Route::post('api/item/{id}', 'ItemController@update');
Route::delete('api/item/{id}', 'ItemController@delete');

// Authentication
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');


// ------------ LEIC Q&A ---------------

// Static Pages
Route::get('home'    , function () { return view('pages.home'    ); });
Route::get('about'   , function () { return view('pages.about'   ); });
Route::get('faq'     , function () { return view('pages.faq'     ); });
Route::get('contact' , function () { return view('pages.contact' ); });


// Authentication
/*
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
*/
// Missing Password Recovery Routes

// API
Route::post('api/questions/{id}/vote'   , 'InterventionController@vote');
Route::post('api/questions/{id}/report' , 'InterventionController@report');
Route::post('api/answers/{id}/vote'     , 'InterventionController@vote');
Route::post('api/answers/{id}/report'   , 'InterventionController@report');
Route::post('api/answers/{id}/validate' , 'InterventionController@validate');
Route::post('api/comments/{id}/report'  , 'InterventionController@report');

Route::post('api/users/{id}/follow/{uc_id}', 'UserController@follow');

Route::post('api/users/{id}/notifications/{not_id}/read', 'NotificationController@read');


// Interventions - Questions
Route::get('questions'                , 'InterventionController@list');
Route::get('questions/{id}'           , 'InterventionController@show');
Route::get('questions/create'         , 'InterventionController@showCreateQuestionForm');
Route::put('questions/create'         , 'InterventionController@createQuestion');
Route::get('questions/{id}/edit'      , 'InterventionController@showUpdateQuestionForm');
Route::post('questions/{id}/edit'     , 'InterventionController@updateQuestion');
Route::delete('questions/{id}/delete' , 'InterventionController@deleteQuestion');

// Interventions - Answers
Route::get('answers/create'         , 'InterventionController@showCreateAnswerForm');
Route::put('answers/create'         , 'InterventionController@createAnswer');
Route::get('answers/{id}/edit'      , 'InterventionController@showUpdateAnswerForm');
Route::post('answers/{id}/edit'     , 'InterventionController@updateAnswer');
Route::delete('answers/{id}/delete' , 'InterventionController@deleteAnswer');

// Interventions - Comments
Route::get('comments/create'        , 'InterventionController@showCreateCommentForm');
Route::put('comments/create'        , 'InterventionController@createComment');
Route::get('comments/{id}/edit'     , 'InterventionController@showUpdateCommentForm');
Route::post('comments/{id}/edit'    , 'InterventionController@updateComment');
Route::delete('comments/{id}/delete', 'InterventionController@deleteComment');

// Users
Route::get('users'              , 'UserController@list');
Route::get('users/{id}'         , 'UserController@show');
Route::get('users/{id}/edit'    , 'UserController@showUpdateForm');
Route::post('users/{id}/edit'   , 'UserController@update');

// Notifications
Route::get('users/{id}/notifications'                   , 'NotificationController@list');
Route::get('users/{id}/notifications/{not_id}'          , 'NotificationController@show');
Route::delete('users/{id}/notifications/{not_id}/delete', 'NotificationController@delete');

// UCs
Route::get('ucs', 'UcController@list');

// Search
Route::get('search', 'InterventionController@list');


// Admin
Route::get('admin/users'                , 'AdminController@listUsers');
Route::post('admin/users/{id}/block'    , 'AdminController@blockUser');
Route::delete('admin/users/{id}/delete' , 'AdminController@deleteUser'); // ??
Route::get('admin/ucs'                  , 'AdminController@listUcs');
Route::get('admin/reports'              , 'AdminController@listReports');
// Falta ainda paginas que associem ucs a docentes

