<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        // TODO middleware vai ser o "email verification"
    }

    /**
     * Handle a registration Request.
     *
     * @param \Illuminate\Http\Request $request
     * @return 
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // $this->guard()->login($user);

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        if ($data['usertype'] == "Admin") {
            return Validator::make($data, [
                'email' => 'required|string|email|max:255|unique:users,email',
                'username' => 'required|string|alpha_dash|max:20|unique:users,username',
                'password' => 'required|string|min:6|confirmed',
            ]);
        } else if ($data['usertype'] == "Teacher") {
            return Validator::make($data, [
                'email' => 'required|string|email|max:255|unique:users,email',
                'username' => 'required|string|alpha_dash|max:20|unique:users,username',
                'password' => 'required|string|min:6|confirmed',
                'name' => 'required|string|max:255',
                'about' => 'nullable|string|max:500',
                'photo' => 'nullable|image|mimes:jpeg,jpg,png,bmp,tiff,gif|max:4096',
                'birthdate' => 'nullable|date_format:Y-m-d',
            ]);
        } else { // ($data['usertype'] == "Student")
            return Validator::make($data, [
                'email' => 'required|string|email|max:255|unique:users,email',
                'username' => 'required|string|alpha_dash|max:20|unique:users,username',
                'password' => 'required|string|min:6|confirmed',
                'name' => 'required|string|max:255',
                'about' => 'nullable|string|max:500',
                'photo' => 'nullable|image|mimes:jpeg,jpg,png,bmp,tiff,gif|max:4096',
                'birthdate' => 'nullable|date_format:Y-m-d',
                'entryyear' => 'required|numeric|min:0',
            ]);
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        if ($data['usertype'] == "Admin") {
            return User::create([
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => bcrypt($data['password']),
                'score' => null,
                'blocked' => null,
                'type' => "Admin",
            ]);
        } else {
            $filename = null;
            if (!empty($_FILES['photo']['name'])) {
                $file = $data['photo'];
                $filename = '_'.time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
                $data['photo']->storeAs('users', $filename, 'images_uploads');
            }

            if ($data['usertype'] == "Teacher") {
                return User::create([
                    'email' => $data['email'],
                    'username' => $data['username'],
                    'password' => bcrypt($data['password']),
                    'name' => $data['name'],
                    'about' => $data['about'],
                    'photo' => $filename,
                    'birthdate' => $data['birthdate'],
                    'type' => "Teacher",
                ]);
            } else { // ($data['usertype'] == "Student")
                return User::create([
                    'email' => $data['email'],
                    'username' => $data['username'],
                    'password' => bcrypt($data['password']),
                    'name' => $data['name'],
                    'about' => $data['about'],
                    'photo' => $filename,
                    'birthdate' => $data['birthdate'],
                    'entry_year' => $data['entryyear'],
                    'type' => "Student",
                ]);
            }
        }
    }
}
