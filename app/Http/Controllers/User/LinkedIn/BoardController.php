<?php

namespace App\Http\Controllers\User\LinkedIn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index()
    {
        return view('user.linkedin.board.index');
    }
}
