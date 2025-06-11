<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(Request $request)
    {
        $this->filters = $request->only(['search', 'class_level_id', 'room_id']);
    }

    public function collection()
    {
        $query = User::where('role', 4)
            ->with(['currentRoom', 'guardian'])
            ->select('id', 'prefix_th', 'name_th', 'surname_th', 'student_id', 'national_id');

        if ($search = $this->filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('name_th', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($classLevel = $this->filters['class_level_id'] ?? null) {
            $query->whereHas('currentRoom', function ($q) use ($classLevel) {
                $q->where('class_level_id', $classLevel);
            });
        }

        if ($roomId = $this->filters['room_id'] ?? null) {
            $query->whereHas('currentRoom', function ($q) use ($roomId) {
                $q->where('rooms.id', $roomId);
            });
        }

        return $query->get();
    }

    public function map($student): array
    {
        $room = $student->currentRoom->first();
        $guardian = $student->guardian;

        return [
            $student->prefix_th,
            $student->name_th,
            $student->surname_th,
            $student->student_id,
            " " . $student->national_id, // ✅ Force Excel to treat as text with single quote
            $room?->name_room_th ?? '',
            $guardian?->prefix_th ?? '',
            $guardian?->name_th ?? '',
            $guardian?->surname_th ?? '',
            $guardian?->phone_1 ?? '',
            $guardian?->phone_2 ?? '',
            $guardian?->relationship ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            'คำนำหน้า (นักเรียน)',
            'ชื่อ (นักเรียน)',
            'นามสกุล (นักเรียน)',
            'รหัสนักเรียน',
            'เลขบัตรประชาชน',
            'ห้องเรียน',
            'คำนำหน้า (ผู้ปกครอง)',
            'ชื่อ (ผู้ปกครอง)',
            'นามสกุล (ผู้ปกครอง)',
            'เบอร์โทร 1',
            'เบอร์โทร 2',
            'ความสัมพันธ์',
        ];
    }
}
