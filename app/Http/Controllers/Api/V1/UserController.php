<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly UserService $userService) {}

    public function index(Request $request): JsonResponse
    {
        return $this->paginated($this->userService->paginate($request->query('search')), UserListResource::class);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create(
            $request->except('profile_photo', 'password_confirmation'),
            $request->file('profile_photo')
        );

        return $this->success(new UserResource($user), 'Usuario creado correctamente.', 201);
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $updated = $this->userService->update(
            $user,
            $request->except('profile_photo', 'password_confirmation'),
            $request->file('profile_photo')
        );

        return $this->success(new UserResource($updated), 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return $this->success(message: 'Usuario eliminado correctamente.');
    }

    public function exportPdf(): mixed
    {
        return $this->userService->exportPdf();
    }

    public function exportExcel(): mixed
    {
        return $this->userService->exportExcel();
    }
}
