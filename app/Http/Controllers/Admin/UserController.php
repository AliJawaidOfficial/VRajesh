<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $view = 'admin.user.';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $dataSet = User::query();
        if ($q) $dataSet = $dataSet->where(function ($query) use ($q) {
            $query->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%");
        });

        $dataSet = $dataSet->OrderBy('id', 'desc')
            ->paginate(10);

        return view($this->view . 'index', compact('dataSet'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $packages = Role::all();
        return view($this->view . 'create', compact('packages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8|max:20|confirmed',
                    'meta_email' => 'nullable|string|email|max:255',
                    'linkedin_email' => 'nullable|string|email|max:255',
                    'google_email' => 'nullable|string|email|max:255',
                    'package' => 'required|exists:roles,id',
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
                    'password.confirmed' => 'Confirm Password does not match',

                    'meta_email.email' => 'Meta Email is invalid',
                    'meta_email.max' => 'Meta Email is too long',

                    'linkedin_email.email' => 'Linkedin Email is invalid',
                    'linkedin_email.max' => 'Linkedin Email is too long',

                    'google_email.email' => 'Google Email is invalid',
                    'google_email.max' => 'Google Email is too long',

                    'package.required' => 'Package is required',
                    'package.exists' => 'Package not found',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $data = new User;
            $data->first_name = $request->first_name;
            $data->last_name = $request->last_name;
            $data->email = $request->email;
            $data->email_verified_at = now();
            $data->password = Hash::make($request->password);
            $data->meta_email = $request->meta_email;
            $data->linkedin_email = $request->linkedin_email;
            $data->google_email = $request->google_email;
            $data->save();

            $role = Role::where('id', $request->package)->first();
            $data->syncRoles($role);

            Session::flash('success', ['text' => 'User created successfully']);
            return redirect()->route('admin.user.index');
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = User::find($id);
            if (!$data) throw new Exception('User not found');

            $userRole = $data->getRoleNames()->first();
            $packages = Role::all();

            return view($this->view . 'edit', compact('data', 'packages'));
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($request->has('password')) {

                $validator = Validator::make(
                    $request->all(),
                    [
                        'password' => 'required|string|min:8|max:20|confirmed',
                    ],
                    [
                        'password.required' => 'Password is required',
                        'password.min' => 'Password must be at least 8 characters',
                        'password.max' => 'Password must be less than 20 characters',
                        'password.confirmed' => 'Confirm Password does not match',
                    ]
                );

                if ($validator->fails()) throw new Exception($validator->errors()->first());

                $data = User::find($id);
                $data->password = Hash::make($request->password);
                $data->save();

                Session::flash('success', ['text' => 'User password updated successfully']);
            } else {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                        'meta_email' => 'nullable|string|email|max:255',
                        'linkedin_email' => 'nullable|string|email|max:255',
                        'google_email' => 'nullable|string|email|max:255',
                        'package' => 'required|exists:roles,id',
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

                        'meta_email.email' => 'Meta Email is invalid',
                        'meta_email.max' => 'Meta Email is too long',

                        'linkedin_email.email' => 'Linkedin Email is invalid',
                        'linkedin_email.max' => 'Linkedin Email is too long',

                        'google_email.email' => 'Google Email is invalid',
                        'google_email.max' => 'Google Email is too long',

                        'package.required' => 'Package is required',
                        'package.exists' => 'Package not found',
                    ]
                );

                if ($validator->fails()) throw new Exception($validator->errors()->first());

                $data = User::find($id);
                $data->first_name = $request->first_name;
                $data->last_name = $request->last_name;
                $data->email = $request->email;
                $data->email_verified_at = now();
                $data->meta_email = $request->meta_email;
                $data->linkedin_email = $request->linkedin_email;
                $data->google_email = $request->google_email;
                $data->save();

                $role = Role::where('id', $request->package)->first();
                $data->syncRoles($role);

                Session::flash('success', ['text' => 'User updated successfully']);
            }

            return redirect()->route('admin.user.index');
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = User::find($id);
            if (!$data) throw new Exception('User not found');
            $data->delete();

            return response()->json([
                'status' => '200',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => '500',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function login(String $id)
    {
        try {
            $user = User::find($id);

            if (!$user) throw new Exception('User not found');

            Auth::login($user);
            
            return response()->json([
                'status' => '200',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => '500',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
