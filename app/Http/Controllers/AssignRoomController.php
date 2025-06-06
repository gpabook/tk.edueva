<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Room;
use App\Models\ClassLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AssignRoomController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', '4') // student
            ->with([
                'currentRoom.classLevel',
                'enrolledRooms.classLevel'
            ]);

        // Search by name or student_id
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->Where('name_th', 'like', "%{$search}%")
                  ->orWhere('surname_th', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('surname_en', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        // Filter by class level
        if ($level = $request->input('class_level_id')) {
            $query->whereHas('currentRoom', function ($q) use ($level) {
                $q->where('class_level_id', $level);
            });
        }

        // Sort by name_th (you can change as needed)
       // $students = $query->orderBy('name_th')->paginate(10)->withQueryString();
        $students = $query->orderBy('student_id')->paginate(30)->withQueryString();

        // Get all rooms and class levels for dropdowns
        $rooms = Room::with('classLevel')->get();
        $classLevels = ClassLevel::all();

        return Inertia::render('AssignRoom/Index', [
            'students'     => $students,
            'rooms'        => $rooms,
            'classLevels'  => $classLevels,
            'filters'      => $request->only('search', 'class_level_id'),
        ]);
    }


    public function assign(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
        ]);

        DB::transaction(function () use ($request) {
            // ปิด room เดิม
            DB::table('room_student')
                ->where('user_id', $request->user_id)
                ->whereNull('left_at')
                ->update(['left_at' => now()]);

            // สร้างการ assign ใหม่
            DB::table('room_student')->insert([
                'user_id'     => $request->user_id,
                'room_id'     => $request->room_id,
                'assigned_at' => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Room assigned successfully.');
    }
}