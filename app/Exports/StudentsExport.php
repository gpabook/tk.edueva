<?php

namespace App\Exports;

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentsExport implements FromCollection
{
    protected $filters;

    public function __construct(Request $request)
    {
        $this->filters = $request->only(['search', 'class_level_id', 'room_id']);
    }

    public function collection()
    {
        $query = User::where('role', 4)
            ->with('currentRoom.classLevel')
            ->select('student_id', 'name_th', 'surname_th', 'email', 'phone');

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
                $q->where('id', $roomId);
            });
        }

        return $query->get();
    }
}
