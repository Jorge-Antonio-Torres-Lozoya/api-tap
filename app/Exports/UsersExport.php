<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return User::orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return ['Código', 'Usuario', 'Nombre', 'Teléfono', 'Fecha de Creación'];
    }

    public function map($user): array
    {
        $phone = $user->phone
            ? ($user->phone['country_code'] . ' ' . $user->phone['number'])
            : '';

        return [
            $user->code,
            $user->username,
            $user->name,
            $phone,
            $user->created_at?->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
