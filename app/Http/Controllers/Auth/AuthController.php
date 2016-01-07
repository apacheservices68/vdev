<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Mail;
//use App\Lib\My_hash;
use App\Http\Controllers\HomeController;

class AuthController extends HomeController
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);        
    }

    public function showLoginForm(){
        $this->data['body'] = 'auth.admin.login';
        return $this->output('admin',true);
    }

/*    public function showDashboardLoginForm(){
        if(auth()->guard('admin')->check()){
            return redirect('/dashboard');
        }
        if(auth()->guard('web')->check()){
            return redirect('/');
        }-
        $this->data['body'] = 'auth.admin.login';
        return $this->output('admin' , true);
    }

    public function postDashboardLoginForm(Request $request){     
        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required',
        ]);
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);
        $credentials['active'] = 1;
        if (auth()->guard('admin')->attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request, $throttles);
        }
        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return redirect()->back()
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }

    public function admin_logout(){
        auth()->guard('admin')->logout();
        return redirect('dashboard/login');
    }*/

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [            
            'name' => 'required|max:255',            
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

/*    public function verify($confirmation_code){
        User::whereConfirmationCode($confirmation_code)->firstOrFail()->confirmEmail();
        flash()->success('Verifying success , Please login.');
        return redirect('/login');
    }*/

    public function authenticate()
    {
        if (Auth::attempt(['email' => $email, 'password' => $password,'active' => 1])) {
            // Authentication passed...
            return $this->handleUserWasAuthenticated($request, $throttles);
        }
    }

    /**
     * Show the register page.
     *
     * @return \Response
     */
    public function showRegistrationForm()
    {
        $this->data['body'] = 'auth.register';
        return $this->output();
    }

    public function register(Request $data)
    {
        $this->validate($data, [            
            'name' => 'required|max:255',            
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
        /*$title= "Verify account from BBC";
        $code = str_random(60);
        $activation = array(
            "title" => $title,
            "url" => url("register/verify/".$code),
        );
        Mail::send('auth.emails.verify', $activation, function($message) use($data , $title) {
            $message->to($data['email'],$data['name'])->subject($title);
        });*/

        $user = new User();
        $array = [            
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'active' => 1,
            'option' => NULL,
            'role_id' => 1,
            'display_name' => 'Super Admin',
        ];
        foreach($array as $key => $val){
            $user->{$key} = $val;
        }
        $user->save();
        flash()->info('Create user successful');
        return redirect($this->redirectPath());
    }
}
