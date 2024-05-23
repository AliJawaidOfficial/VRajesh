<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login
     */
    public function login()
    {
        return view('user.auth.login');
    }

    public function loginStore(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ],
                [
                    'email.required' => 'Email is required',
                    'password.required' => 'Password is required',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            if (!Auth::attempt($request->only('email', 'password'))) throw new Exception('Invalid email or password');

            Session::flash('success', ['text' => 'Login successfully']);
            return redirect()->intended('dashboard');
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }


    /**
     * Register
     */
    public function register()
    {
        return view('user.auth.register');
    }

    public function registerStore(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8|max:20|confirmed',
                ],
                [
                    'first_name.required' => 'First Name is required',
                    'first_name.max' => 'First Name is too long',

                    'last_name.required' => 'Last Name is required',
                    'last_name.max' => 'Last Name is too long',

                    'email.required' => 'Email is required',
                    'email.email' => 'Email is invalid',
                    'email.max' => 'Email is too long',
                    'email.unique' => 'Email already exists',

                    'password.required' => 'Password is required',
                    'password.min' => 'Password must be at least 8 characters',
                    'password.max' => 'Password must be less than 20 characters',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);


            Session::flash('success', ['text' => 'Account registered successfully']);
            return redirect()->route('user.login');
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }


    /**
     * Forget Password
     */
    public function forgetPassword()
    {
        return view('user.auth.forget-password');
    }

    public function forgetPasswordStore(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }


    /**
     * Resert Password
     */
    public function showResetForm($token)
    {
        return view('user.auth.passwords.reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }


    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
