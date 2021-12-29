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

// ------------ LEIC Q&A ---------------

// Home
Route::get('/', function () { return view('pages.static.home'); });

// Static Pages
Route::get('home'    , function () { return view('pages.static.home'    ); });
Route::get('about'   , function () { return view('pages.static.about'   ); });
Route::get('faq'     , function () { return view('pages.static.faq'     ); });
Route::get('contact' , function () { return view('pages.static.contact' ); });

// Authentication
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
// Missing Password Recovery Routes : recover

// API

Route::post('api/interventions/{id}/vote'     , 'InterventionController@vote');
Route::delete('api/interventions/{id}/delete' , 'InterventionController@delete');
Route::post('api/interventions/{id}/report'  , 'InterventionController@report');
Route::post('api/answers/{id}/validate' , 'InterventionController@validate');

Route::post('api/ucs/follow/{uc_id}', 'UcController@follow');

Route::post('api/notifications/read/{not_id}', 'NotificationController@read');


// Interventions - Questions
Route::get('questions'                , 'InterventionController@list');
Route::get('questions/create'         , 'InterventionController@showCreateQuestionForm');
Route::post('questions/create'         , 'InterventionController@createQuestion')->name('questions.create');
Route::get('questions/{id}'           , 'InterventionController@show');
Route::get('questions/{id}/edit'      , 'InterventionController@showEditQuestionForm');
Route::post('questions/{id}/edit'     , 'InterventionController@updateQuestion')->name('questions.edit');

// Interventions - Answers
Route::post('questions/{id}/answers/create', 'InterventionController@createAnswer')->name('answers.create');
Route::get('answers/{id}/edit'            , 'InterventionController@showEditAnswerForm');
Route::post('answers/{id}/edit'           , 'InterventionController@updateAnswer')->name('answers.edit');

// Interventions - Comments
Route::post('answers/{id}/comments/create' , 'InterventionController@createComment')->name('comments.create');
Route::get('comments/{id}/edit'           , 'InterventionController@showEditCommentForm');
Route::post('comments/{id}/edit'          , 'InterventionController@updateComment')->name('comments.edit');

// Users
Route::get('users'       , 'UserController@list');
Route::get('users/{id}'  , 'UserController@show');
Route::get('users/{id}/edit'  , 'UserController@showEditForm');
Route::post('users/{id}/edit' , 'UserController@update')->name('users.edit');

Route::post('api/users/{id}/block'    , 'UserController@block');
Route::delete('api/users/{id}/delete' , 'UserController@delete'); // ??

// Notifications
/*
Route::get('notifications'                   , 'NotificationController@list');
Route::get('notifications/{not_id}'          , 'NotificationController@show');
Route::delete('notifications/{not_id}/delete', 'NotificationController@delete');
*/

// UCs
Route::get('ucs'                , 'UcController@list');
Route::get('ucs/create'         , 'UcController@showCreateForm');
Route::post('ucs/create'        , 'UcController@create')->name('ucs.create');
Route::get('ucs/{id}'           , 'UcController@show');
Route::get('ucs/{id}/edit'      , 'UcController@showEditForm');
Route::post('ucs/{id}/edit'     , 'UcController@update')->name('ucs.edit');
Route::delete('api/ucs/{id}/delete' , 'UcController@delete');

Route::put('api/ucs/{uc_id}/teachers/{user_id}/add'       , 'UcController@addTeacher');
Route::delete('api/ucs/{uc_id}/teachers/{user_id}/delete' , 'UcController@deleteTeacher');

// Search
Route::get('search', 'InterventionController@list');


// Admin
Route::get('admin/users'                , 'AdminController@listUsers');
Route::get('admin/ucs'                  , 'AdminController@listUcs');
Route::get('admin/ucs/{id}/teachers'    , 'AdminController@listTeachers');
Route::get('admin/reports'              , 'AdminController@listReports');

