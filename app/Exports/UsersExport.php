<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::select('id', 'student_id', 'name', 'email', 'created_at')->get(); // หรือ query ที่คุณต้องการ
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student_ID',
            'Name',
            'Email',
            'Registered At',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->student_id,
            $user->name,
            $user->email,
            $user->created_at->toDateTimeString(), // Format วันที่
        ];
    }
}
