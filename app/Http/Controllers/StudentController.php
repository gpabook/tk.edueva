<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\ClassLevel;
use App\Models\User;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Enums\UserRole;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = User::where('role', 4)
            ->with(['currentRoom.classLevel'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name_th', 'like', "%{$search}%")
                      ->orWhere('surname_th', 'like', "%{$search}%")
                      ->orWhere('student_id', 'like', "%{$search}%");
                });
            })
            ->when($request->class_level_id, function ($query, $classLevelId) {
                $query->whereHas('currentRoom', function ($q) use ($classLevelId) {
                    $q->where('rooms.class_level_id', $classLevelId);
                });
            })
            ->when($request->room_id, function ($query, $roomId) {
                $query->whereHas('currentRoom', function ($q) use ($roomId) {
                    $q->where('rooms.id', $roomId);
                });
            })
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
        // โหลด student พร้อม currentRoom, classLevel และ guardians
        $student = User::with([
            'currentRoom.classLevel',
            'guardian', // หากเป็น one-to-one (hasOne)
            // หรือ 'guardians' หากใช้ hasMany
        ])->findOrFail($id);
        // ตรวจสอบ role ว่าเป็นนักเรียน
        if ($student->role !== \App\Enums\UserRole::Student) {
            abort(403, 'Access denied');
        }

        // โหลดห้องเรียนทั้งหมดไว้สำหรับแสดงใน dropdown
        $rooms = Room::with('classLevel')->get();

        return Inertia::render('Students/Edit', [
            'student' => $student,
            'rooms' => $rooms,
            'guardian' => $student->guardian ?? new Guardian(), // 👈 ป้องกัน null
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
           'gender'       => 'nullable|in:male,female,other',
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
       // ดึงข้อมูลนักเรียนจากฐานข้อมูล
       $student = User::findOrFail($id);

       if ($student->role !== UserRole::Student) {
           abort(403, 'Access denied');
       }

       // 👉 ล้างขีดออกจาก national_id ก่อน validate
       $request->merge([
           'national_id' => $request->national_id
               ? str_replace('-', '', $request->national_id)
               : null,
       ]);

       // ✅ ตรวจสอบข้อมูลนักเรียน
       $validatedStudent = $request->validate([
           'student_id' => [
               'required',
               'string',
               Rule::unique('users', 'student_id')->ignore($student->id),
           ],
           'prefix_th' => ['nullable', Rule::in(['ด.ช.', 'ด.ญ.', 'นาย', 'นางสาว'])],
           'name_th' => 'required|string|max:100',
           'surname_th' => 'required|string|max:100',
           'name_en' => 'nullable|string|max:100',
           'surname_en' => 'nullable|string|max:100',
           'national_id' => [
               'nullable',
               'digits:13',
               Rule::unique('users', 'national_id')->ignore($student->id),
           ],
           'email' => [
               'nullable',
               'email',
               Rule::unique('users', 'email')->ignore($student->id),
           ],
           'gender' => 'nullable|in:male,female',
           'birthday' => 'nullable|date',
           'room_id' => 'nullable|exists:rooms,id',
       ]);

       // ✅ ตรวจสอบข้อมูลผู้ปกครอง
       $validatedGuardian = $request->validate([
           'guardian_prefix_th' => ['nullable', Rule::in(['นาย', 'นาง', 'นางสาว'])],
           'guardian_name_th' => 'nullable|string|max:100',
           'guardian_surname_th' => 'nullable|string|max:100',
           'guardian_phone_1' => 'nullable|string|max:20',
           'guardian_phone_2' => 'nullable|string|max:20',
           'guardian_relationship' => 'nullable|string|max:100',
       ]);

       DB::transaction(function () use ($student, $validatedStudent, $validatedGuardian) {
           // ⬆ บันทึกนักเรียน
           $student->update($validatedStudent);

           // ⬆ บันทึกหรืออัปเดตผู้ปกครอง (อิง user_id)
           if (
            $validatedGuardian['guardian_name_th'] ||
            $validatedGuardian['guardian_surname_th'] ||
            $validatedGuardian['guardian_phone_1']
        ) {
            Guardian::updateOrCreate(
                ['user_id' => $student->id],
                [
                    'prefix_th' => $validatedGuardian['guardian_prefix_th'] ?? null,
                    'name_th' => $validatedGuardian['guardian_name_th'] ?? null,
                    'surname_th' => $validatedGuardian['guardian_surname_th'] ?? null,
                    'phone_1' => $validatedGuardian['guardian_phone_1'] ?? null,
                    'phone_2' => $validatedGuardian['guardian_phone_2'] ?? null,
                    'relationship' => $validatedGuardian['guardian_relationship'] ?? null,
                ]
            );
        }
       });

       return redirect()->route('students.index')->with('success', 'บันทึกการแก้ไขแล้ว');
   }




}