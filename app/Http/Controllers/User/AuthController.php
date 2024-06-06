<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\User\ForgetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\User\RegisterMail;
use App\Models\PasswordResetToken;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

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
                    'email' => 'required|email|exists:users,email',
                    'password' => 'required',
                ],
                [
                    'email.required' => 'Email is required',
                    'email.email' => 'Email is invalid',
                    'email.exists' => 'Email does\'t have an account',

                    'password.required' => 'Password is required',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            if (User::where('email', $request->email)->first()->email_verified_at == null) throw new Exception('Please verify your email');

            if (!Auth::attempt($request->only('email', 'password'))) throw new Exception('Invalid email or password');

            Session::flash('success', ['text' => 'Login successfully']);
            return redirect()->route('user.dashboard');
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
        $packages = Role::where('is_visible', 1)->get();
        return view('user.auth.register', compact('packages'));
    }

    public function registerStore(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'package' => 'required|exists:roles,id',
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

                    'package.required' => 'Package is required',
                    'package.exists' => 'Package not found',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $token = Str::random(60);
            $data = new User;
            $data->first_name = $request->first_name;
            $data->last_name = $request->last_name;
            $data->email = $request->email;
            $data->password = Hash::make($request->password);
            $data->meta_email = $request->meta_email;
            $data->linkedin_email = $request->linkedin_email;
            $data->remember_token = $token;
            $data->save();

            $role = Role::where('id', $request->package)->first();
            $data->syncRoles($role);

            Mail::to($data->email)->send(new RegisterMail([
                'name' => $data->first_name . ' ' . $data->last_name,
                'url' => route('user.register.verify', [
                    'token' => $token,
                    'email' => $data->email
                ]),
            ]));

            DB::commit();

            Session::flash('success', ['text' => 'Account registered successfully']);
            return redirect()->route('user.login');
        } catch (Exception $e) {
            DB::rollBack();

            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }

    public function registerVerify(String $token, String $email)
    {
        try {
            DB::beginTransaction();

            $user = User::where('remember_token', $token)->where('email', $email)->first();
            if (!$user) throw new Exception('Invalid Request');

            $user->email_verified_at = now();
            $user->save();

            DB::commit();

            Session::flash('success', ['text' => 'Your Account verified successfully']);
            return redirect()->route('user.login');
        } catch (Exception $e) {
            DB::rollBack();

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
        try {
            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'required|string|email|max:255|exists:users,email',
                ],
                [
                    'email.required' => 'Email is required',
                    'email.email' => 'Email is invalid',
                    'email.max' => 'Email is too long',
                    'email.exists' => 'Email does\'t have an account',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $token = Str::random(60);

            $data = PasswordResetToken::updateOrCreate(
                ['email' => $request->email],
                ['token' => $token]
            );

            $user = User::where('email', $request->email)->first();
            Mail::to($request->email)->send(new ForgetPasswordMail([
                'name' => $user->first_name . ' ' . $user->last_name,
                'url' => route('user.password.reset', $data->token),
            ]));

            DB::commit();

            Session::flash('success', ['text' => 'Password reset link has been sent to your email']);
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();

            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }


    /**
     * Resert Password
     */
    public function resetPassword(String $token)
    {
        try {
            $user = PasswordResetToken::where('token', $token)->first();
            if (!$user) throw new Exception('Invalid Request');

            return view('user.auth.reset-password', compact('token'));
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->route('user.login');
        }
    }

    public function resetPasswordStore(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                [
                    'token' => 'required|string|exists:password_reset_tokens,token',
                    'password' => 'required|string|min:8|max:20|confirmed',
                ],
                [
                    'password.required' => 'Password is required',
                    'password.min' => 'Password must be at least 8 characters',
                    'password.max' => 'Password must be less than 20 characters',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $passwordResetToken = PasswordResetToken::where('token', $request->token)->first();
            if (!$passwordResetToken) throw new Exception('Invalid Request');

            $user = User::where('email', $passwordResetToken->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            $passwordResetToken->delete();

            DB::commit();

            Session::flash('success', ['text' => 'Password reset successfully']);
            return redirect()->route('user.login');
        } catch (Exception $e) {
            DB::rollBack();

            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }


    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->regenerateToken();
        return redirect()->route('user.login');
    }
}
