<?php

namespace App\Services;

use App\Exports\UsersExport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserService
{
    public function __construct(private readonly AdministratorGuard $guard) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data, UploadedFile $photo): User
    {
        $data['profile_photo'] = $photo->store('photos', 'public');

        return User::create($data);
    }

    public function update(User $user, array $data, ?UploadedFile $photo): User
    {
        if (array_key_exists('profile_ids', $data)) {
            $this->guard->assertUserUpdatable($user, $data['profile_ids']);
        }

        if ($photo) {
            $this->deletePhoto($user->profile_photo);
            $data['profile_photo'] = $photo->store('photos', 'public');
        }

        $user->update($data);

        return $user->fresh();
    }

    public function delete(User $user): void
    {
        $this->guard->assertUserDeletable($user);

        $user->delete();
    }

    public function exportPdf(): mixed
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return Pdf::loadView('exports.users-pdf', compact('users'))
            ->download('usuarios.pdf');
    }

    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new UsersExport, 'usuarios.xlsx');
    }

    private function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
