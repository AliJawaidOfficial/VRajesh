<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\PixelsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PixelsController extends Controller
{
    protected $pixelsService;

    public function __construct(private readonly PixelsService $importedPixelsService)
    {
        $this->pixelsService = $importedPixelsService;
    }

    public function search(String $type, String $q, Request $request)
    {
        $data = "";
        if ($type == 'photos') $data = $this->pixelsService->searchPhotos($q, $request->page, $request->per_page);
        if ($type == 'videos') $data = $this->pixelsService->searchVideo($q, $request->page, $request->per_page);

        return response()->json($data);
    }
}
