<?php

namespace App\Services;

use App\Exports\ProfilesExport;
use App\Models\Profile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileService
{
    public function __construct(private readonly AdministratorGuard $guard) {}

    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return Profile::query()
            ->when($search, fn ($query, $term) => $query->where(function ($query) use ($term) {
                $query->orWhere('code', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%");
            }))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Profile
    {
        return Profile::create($data);
    }

    public function update(Profile $profile, array $data): Profile
    {
        if (array_key_exists('sections', $data)) {
            $this->guard->assertProfileUpdatable($profile, $data['sections']);
        }

        $profile->update($data);

        return $profile->fresh();
    }

    public function delete(Profile $profile): void
    {
        $this->guard->assertProfileDeletable($profile);

        $profile->delete();
    }

    public function exportPdf(): mixed
    {
        $profiles = Profile::orderBy('created_at', 'desc')->get();

        return Pdf::loadView('exports.profiles-pdf', compact('profiles'))
            ->download('perfiles.pdf');
    }

    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new ProfilesExport, 'perfiles.xlsx');
    }
}
