<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UserExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    public function query()
    {
        return User::role('Public')->select(
            'first_name',
            'last_name',
            'display_name',
            'email',
            'phone',
            'status',
            'address',
            'postal_code',
            'dob'
        );
    }

    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Nickname',
            'Email',
            'Contact',
            'Status',
            'Address',
            'Postal Code',
            'Date of Birth',
        ];
    }

    public function map($user): array
    {
        return [
            $user->first_name,
            $user->last_name,
            $user->display_name,
            $user->email,
            $user->phone,
            $user->status_text,   // accessor still works
            $user->address,
            $user->postal_code,
            $user->dob,
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // process 1000 rows per chunk
    }
}
