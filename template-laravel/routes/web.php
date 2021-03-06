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
Route::post('contact', 'ContactController@send')->name('send-contact');

// Authentication - Basic Operations
Route::get('login'    , 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login'   , 'Auth\LoginController@login');
Route::get('logout'   , 'Auth\LoginController@logout')->name('logout');
Route::get('register' , 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Authentication - Verify Email
Route::get('/verify-email', 'Auth\EmailVerificationController@show')->middleware('auth')->name('verification.notice');
Route::post('/verify-email/request', 'Auth\EmailVerificationController@request')->middleware('auth')->name('verification.request');
Route::get('/verify-email/{id}/{hash}', 'Auth\EmailVerificationController@verify')->middleware(['auth', 'signed']) ->name('verification.verify'); 

// Authentication - Forgot Password
Route::get('forgot-password'        , 'Auth\ForgotPasswordController@showForgetPasswordForm')->middleware('guest')->name('password.request');
Route::post('forgot-password'       , 'Auth\ForgotPasswordController@submitForgetPasswordForm')->middleware('guest')->name('password.email'); 
Route::get('reset-password/{token}' , 'Auth\ForgotPasswordController@showResetPasswordForm')->middleware('guest')->name('password.reset');
Route::post('reset-password'        , 'Auth\ForgotPasswordController@submitResetPasswordForm')->middleware('guest')->name('password.update');

// Google Authentication
Route::prefix('google')->name('google.')->group( function(){
    Route::get('login', 'Auth\GoogleController@loginWithGoogle')->name('login');
    Route::any('callback', 'Auth\GoogleController@callbackFromGoogle')->name('callback');
});


// Interventions - API
Route::group(['middleware' => ['verified']], function() {
  Route::delete('api/interventions/{id}/delete' , 'InterventionController@delete');
  Route::post('api/interventions/{id}/vote'     , 'InterventionController@vote');
  Route::post('api/interventions/{id}/validate' , 'InterventionController@valid');
  Route::post('api/interventions/{id}/report'   , 'InterventionController@report');
});

// Interventions - Questions
Route::get('questions'                , 'InterventionController@list');
Route::group(['middleware' => ['verified']], function() {
  Route::get('questions/create'         , 'InterventionController@showCreateQuestionForm');
  Route::post('questions/create'        , 'InterventionController@createQuestion')->name('questions.create');
});
Route::get('questions/{id}'           , 'InterventionController@show');
Route::group(['middleware' => ['verified']], function() {
  Route::get('questions/{id}/edit'      , 'InterventionController@showEditQuestionForm');
  Route::post('questions/{id}/edit'     , 'InterventionController@updateQuestion')->name('questions.edit');
});

// Interventions - Answers
Route::get('answers'                       , function () { return redirect('/questions'); });
Route::group(['middleware' => ['verified']], function() {
  Route::post('questions/{id}/answers/create', 'InterventionController@createAnswer')->name('answers.create');
  Route::get('answers/{id}/edit'             , 'InterventionController@showEditAnswerForm');
  Route::post('answers/{id}/edit'            , 'InterventionController@updateAnswer')->name('answers.edit');
});

// Interventions - Comments
Route::get('comments'                     , function () { return redirect('/questions'); });
Route::group(['middleware' => ['verified']], function() {
  Route::post('answers/{id}/comments/create', 'InterventionController@createComment')->name('comments.create');
  Route::get('comments/{id}/edit'           , 'InterventionController@showEditCommentForm');
  Route::post('comments/{id}/edit'          , 'InterventionController@updateComment')->name('comments.edit');
});

// Users
Route::get('user' , function () {
  if (!Auth::check()) return redirect('/login');
  return redirect('/users'.'/'.Auth::user()->id);
});
Route::get('users'       , 'UserController@list');
Route::get('users/{id}'  , 'UserController@show');
Route::get('users/{id}/edit'  , 'UserController@showEditForm');
Route::post('users/{id}/edit' , 'UserController@update')->name('users.edit');
Route::get('users/{id}/delete', 'UserController@delete');

// Users - API
Route::group(['middleware' => ['verified']], function() {
  Route::post('api/users/{id}/active'    , 'UserController@active');
  Route::post('api/users/{id}/block'    , 'UserController@block');
  Route::delete('api/users/{id}/delete' , 'UserController@delete');
  Route::post('api/users/{id}/email'    , 'UserController@email');
  Route::post('api/users/{user_id}/follow/{uc_id}', 'UserController@follow');
});

// Notifications
Route::group(['middleware' => ['verified']], function() {
  Route::get('notifications'                    , 'NotificationController@list');
  Route::get('notifications/{id}'               , 'NotificationController@show');
  Route::get('notifications/{id}/read'          , 'NotificationController@read');

  // Notifications - API
  Route::post('api/notifications/{id}/read'     , 'NotificationController@apiRead');
  Route::post('api/notifications/{id}/remove'   , 'NotificationController@remove');
  Route::delete('api/notifications/{id}/delete' , 'NotificationController@delete');
});

// UCs
Route::get('ucs'                , 'UcController@list');
Route::group(['middleware' => ['verified']], function() {
  Route::get('ucs/create'         , 'UcController@showCreateForm');
  Route::post('ucs/create'        , 'UcController@create')->name('ucs.create');
});
Route::get('ucs/{id}'           , 'UcController@show');
Route::group(['middleware' => ['verified']], function() {
  Route::get('ucs/{id}/edit'      , 'UcController@showEditForm');
  Route::post('ucs/{id}/edit'     , 'UcController@update')->name('ucs.edit');

  // UCs - API
  Route::delete('api/ucs/{id}/delete' , 'UcController@delete');
  Route::put('api/ucs/{uc_id}/teachers/{user_id}/add'       , 'UcController@addTeacher');
  Route::delete('api/ucs/{uc_id}/teachers/{user_id}/remove' , 'UcController@deleteTeacher');
});

// Search
Route::get('search', 'InterventionController@searchList');


// Admin
Route::get('admin'                      , function () {
  if (!Auth::check()) return redirect('/login');
  return redirect('/users'.'/'.Auth::user()->id);
});

Route::group(['middleware' => ['verified']], function() {
  Route::get('admin/ucs/{id}'             , function ($id) { return redirect('/ucs'.'/'.$id); });

  Route::get('admin/users'                , 'AdminController@listUsers');
  Route::get('admin/ucs'                  , 'AdminController@listUcs');
  Route::get('admin/ucs/{id}/teachers'    , 'AdminController@listTeachers');
  Route::get('admin/reports'              , 'AdminController@listReports');
  Route::get('admin/requests'             , 'AdminController@listRequests');
});
