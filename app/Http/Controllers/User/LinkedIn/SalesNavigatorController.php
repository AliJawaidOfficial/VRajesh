<?php

namespace App\Http\Controllers\User\LinkedIn;

use App\Http\Controllers\Controller;
use App\Services\LinkedInService;
use Illuminate\Http\Request;

class SalesNavigatorController extends Controller
{
    protected $linkedinService;

    public function __construct(private readonly LinkedInService $importLinkedinService,) {
        $this->linkedinService = $importLinkedinService;
    }

    /**
     * Display a frontend of the resource.
     */
    public function index()
    {
        return view('user.linkedin.leads.sales-navigator.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function all()
    {
        $connections = $this->linkedinService->connections();
        return $connections;
        $leads = $this->linkedinService->salesNavigatorLeads();
        return $leads;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
