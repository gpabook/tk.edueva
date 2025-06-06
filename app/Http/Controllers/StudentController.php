<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\ClassLevel; // For fetching class levels for dropdowns
use App\Models\User;      // For fetching users (teachers/advisors) - will be used when advisor logic is added
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule; // For more complex validation rules

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = User::where('role', 4)
            ->with(['currentRoom.classLevel'])
            ->when($request->search, fn ($q) =>
                $q->where('name_th', 'like', "%{$request->search}%")
                  ->orWhere('student_id', 'like', "%{$request->search}%"))
            ->when($request->class_level_id, fn ($q) =>
                $q->whereHas('currentRoom', fn ($r) => $r->where('class_level_id', $request->class_level_id)))
            ->when($request->room_id, fn ($q) =>
                $q->whereHas('currentRoom', fn ($r) => $r->where('id', $request->room_id)))
            ->paginate(10)
            ->withQueryString();

        $rooms = Room::with('classLevel')->get();
        $classLevels = ClassLevel::all();

        return Inertia::render('Students/Index', [
            'students' => $students,
            'rooms' => $rooms,
            'classLevels' => $classLevels,
            'filters' => $request->only(['search', 'class_level_id', 'room_id']),
        ]);
    }

    public function edit($id)
   {
    $student = User::findOrFail($id);

    // ตรวจสอบว่าเป็น student จริง ๆ
    if ($student->role !== \App\Enums\UserRole::Student) {
        abort(403, 'Access denied');
    }

    return Inertia::render('Students/Edit', [
        'student' => $student,
    ]);
   }

   public function create()
   {
       $classLevels = ClassLevel::orderBy('code')->get();

       $rooms = Room::with('classLevel')
           ->orderBy('class_level_id')
           ->orderBy('name_room_th')
           ->get();

       return Inertia::render('Students/Create', [
           'classLevels' => $classLevels,
           'rooms' => $rooms,
       ]);
   }


   public function store(Request $request)
   {
       $validated = $request->validate([
           'student_id'   => 'required|string|unique:users,student_id',
           'name_th'      => 'required|string|max:100',
           'surname_th'   => 'required|string|max:100',
           'name_en'      => 'nullable|string|max:100',
           'surname_en'   => 'nullable|string|max:100',
           'email'        => 'nullable|email|unique:users,email',
           'gender'       => 'nullable|in:M,F',
           'birthday'     => 'nullable|date',
           'room_id'      => 'nullable|exists:rooms,id',
       ]);

       $student = User::create([
           ...$validated,
           'password' => bcrypt('secrete'), // ตั้งค่ารหัสผ่านเริ่มต้น
           'role'     => \App\Enums\UserRole::Student,
           'status'   => 1,
           'avatar'   => 'images/default-avatar.png', // ถ้ามี
       ]);

       // หากใช้ Spatie permission
       $student->syncRoles([\App\Enums\UserRole::Student->name]);

       return redirect()->route('students.index')->with('success', 'เพิ่มนักเรียนเรียบร้อยแล้ว');
   }


    public function update(Request $request, $id)
   {
       $student = User::findOrFail($id);

       if ($student->role !== \App\Enums\UserRole::Student) {
           abort(403, 'Access denied');
       }

       $validated = $request->validate([
           'student_id' => [
               'required', 'string',
               Rule::unique('users', 'student_id')->ignore($student->id)
           ],
           'name_th' => 'required|string|max:100',
           'surname_th' => 'required|string|max:100',
           'name_en' => 'nullable|string|max:100',
           'surname_en' => 'nullable|string|max:100',
           'email' => [
               'nullable', 'email',
               Rule::unique('users', 'email')->ignore($student->id)
           ],
           'gender' => 'nullable|in:M,F',
           'birthday' => 'nullable|date',
           'room_id' => 'nullable|exists:rooms,id',
       ]);

       $student->update($validated);

       return redirect()->route('students.index')->with('success', 'บันทึกการแก้ไขแล้ว');
   }

}