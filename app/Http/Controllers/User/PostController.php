<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function create()
    {
        return view('user.post.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'media' => 'nullable',
                ],
                [
                    'title.required' => 'Title is required',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            if ($request->hasFile('media')) {
                $media = $request->file('media');
                $mediaName = time() . '.' . $media->getClientOriginalExtension();
                $media->move(public_path('temp'), $mediaName);
                $mediaPath = public_path('temp') . '/' . $mediaName;
            }

            return redirect()->back();
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back();
        }
    }
}
