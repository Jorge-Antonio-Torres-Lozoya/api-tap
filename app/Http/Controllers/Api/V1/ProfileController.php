<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreProfileRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Traits\ApiResponse;
use App\Models\Profile;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ProfileService $profileService) {}

    public function index(): JsonResponse
    {
        $profiles = $this->profileService->paginate();

        return $this->success(ProfileResource::collection($profiles)->response()->getData(true));
    }

    public function store(StoreProfileRequest $request): JsonResponse
    {
        $profile = $this->profileService->create($request->validated());

        return $this->success(new ProfileResource($profile), 'Perfil creado correctamente.', 201);
    }

    public function show(Profile $profile): JsonResponse
    {
        return $this->success(new ProfileResource($profile));
    }

    public function update(UpdateProfileRequest $request, Profile $profile): JsonResponse
    {
        $updated = $this->profileService->update($profile, $request->validated());

        return $this->success(new ProfileResource($updated), 'Perfil actualizado correctamente.');
    }

    public function destroy(Profile $profile): JsonResponse
    {
        $this->profileService->delete($profile);

        return $this->success(message: 'Perfil eliminado correctamente.');
    }

    public function exportPdf(): mixed
    {
        return $this->profileService->exportPdf();
    }

    public function exportExcel(): mixed
    {
        return $this->profileService->exportExcel();
    }
}
