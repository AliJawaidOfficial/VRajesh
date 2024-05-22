<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    
    public function create() {
        return view('user.post.create');
    }

    public function store() {

    }
}
