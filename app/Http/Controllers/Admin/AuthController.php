<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\User\ForgetPasswordMail;
use App\Models\PasswordResetToken;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;


class AuthController extends Controller
{
    protected $view = "admin.auth.";
    /**
     * Login
     */
    public function login()
    {
        return view($this->view . '.login');
    }

    public function loginStore(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email|exists:admins,email',
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

            $data = Admin::where('email', $request->email)->first();
            if (!Auth::guard('admin')->attempt($request->only('email', 'password'))) throw new Exception('Invalid email or password');

            $request->authenticate();
            $request->session()->regenerate();

            Session::flash('success', ['text' => 'Login successfully']);

            return redirect()->route('user.dashboard');
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
        return view($this->view . '.forget-password');
    }

    public function forgetPasswordStore(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'required|string|email|max:255|exists:admins,email',
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

            $admin = Admin::where('email', $request->email)->first();
            Mail::to($request->email)->send(new ForgetPasswordMail([
                'name' => $admin->first_name . ' ' . $admin->last_name,
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
            $admin = PasswordResetToken::where('token', $token)->first();
            if (!$admin) throw new Exception('Invalid Request');

            return view($this->view . '.reset-password', compact('token'));
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

            $admin = Admin::where('email', $passwordResetToken->email)->first();
            $admin->password = Hash::make($request->password);
            $admin->save();

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
        Auth::guard('admin')->logout();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
