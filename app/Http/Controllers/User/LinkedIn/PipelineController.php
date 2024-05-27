<?php

namespace App\Http\Controllers\User\LinkedIn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function index()
    {
        return view('user.linkedin.pipeline.index');
    }
}
