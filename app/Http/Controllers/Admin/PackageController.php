<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PackageController extends Controller
{
    protected $view = 'admin.package.';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $dataSet = Role::query();
        if ($q) $dataSet = $dataSet->where('name', 'like', "%$q%");

        $dataSet = $dataSet->OrderBy('id', 'desc')->paginate(10);

        return view($this->view . 'index', compact('dataSet'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view($this->view . 'create', compact('permissions'));
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
                    'name' => 'required|string|max:255|unique:roles,name',
                    'permissions' => 'required|array',
                ],
                [
                    'name.required' => 'Package Name required',
                    'name.max' => 'Package Name must be less than 255 characters',
                    'name.unique' => 'Package Name already exists',

                    'permissions.required' => 'Permissions required',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $role = new Role;
            $role->name = $request->name;
            $role->guard_name = 'web';
            $role->save();
            $role->givePermissionTo($request->permissions);

            Session::flash('success', ['text' => 'Package created successfully']);
            return redirect()->route('admin.package.index');
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
            $data = Role::find($id);
            if (!$data) throw new Exception('Package not found');

            $permissions = Permission::all();
            $rolePermissions = $data->permissions->pluck('id')->toArray();

            return view($this->view . 'edit', compact('data', 'permissions', 'rolePermissions'));
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
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|max:255|unique:roles,name,' . $id,
                    'permissions' => 'required|array',
                ],
                [
                    'name.required' => 'Package Name required',
                    'name.max' => 'Package Name must be less than 255 characters',
                    'name.unique' => 'Package Name already exists',

                    'permissions.required' => 'Permissions required',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $data = Role::find($id);
            if (!$data) throw new Exception('Package not found');

            $data->name = $request->name;
            $data->guard_name = 'web';
            $data->save();

            $data->syncPermissions($request->permissions);

            Session::flash('success', ['text' => 'Package updated successfully']);

            return redirect()->route('admin.package.index');
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Update visibility of the specified resource in storage.
     */
    public function visibility(String $id, Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'visibility' => 'required|boolean',
                ],
                [
                    'visibility.required' => 'Invalid visibility',
                    'visibility.boolean' => 'Invalid visibility',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $data = Role::find($id)->update(['is_visible' => $request->visibility]);

            if ($request->visibility == 1) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Package visible successfully'
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Package hidden successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = Role::find($id);
            if (!$data) throw new Exception('User not found');

            if ($data->users->count() > 0) throw new Exception('Users is assigned to this role');
            $data->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Package deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }
}
