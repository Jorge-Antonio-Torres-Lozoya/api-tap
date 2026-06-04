<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Http\Traits\ApiResponse;
use App\Models\Section;
use Illuminate\Http\JsonResponse;

class SectionController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $sections = Section::orderBy('code')->get();

        return $this->success(SectionResource::collection($sections));
    }
}
