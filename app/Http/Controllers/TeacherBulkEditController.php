<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB; // Import DB facade for transactions

class TeacherBulkEditController extends Controller
{
    public function edit()
    {
        // ดึง User ทั้งหมดที่มี role 'teacher'
        // ตรวจสอบให้แน่ใจว่า User model ของคุณมี relationship `roles` หรือ method `hasRole()`
        $teachers = User::role('teacher')->select('id', 'name', 'email')->get(); // เลือกเฉพาะ field ที่จำเป็น

        return Inertia::render('Teachers/BulkEditForm', [ // สร้าง Vue component นี้
            'teachers' => $teachers,
        ]);
    }

    public function update(Request $request)
    {
        // Validate ข้อมูลที่รับมา (array ของ teachers)
        // $request->input('teachers') ควรเป็น array เช่น:
        // [
        //   { "id": 1, "name": "New Name 1" },
        //   { "id": 2, "name": "New Name 2" }
        // ]
        $validator = Validator::make($request->all(), [
            'teachers' => 'required|array',
            'teachers.*.id' => 'required|integer|exists:users,id', // ตรวจสอบว่า id มีในตาราง users
            'teachers.*.name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $updatedTeachersData = $validator->validated()['teachers'];
        $updatedCount = 0;
        $errorMessages = [];

        DB::beginTransaction(); // เริ่ม Transaction

        try {
            foreach ($updatedTeachersData as $teacherData) {
                $teacher = User::find($teacherData['id']);
                if ($teacher && $teacher->hasRole('teacher')) { // ตรวจสอบอีกครั้งว่าเป็นครูจริง
                    $teacher->name = $teacherData['name'];
                    // คุณสามารถอัปเดต field อื่นๆ ได้ที่นี่ ถ้าต้องการ
                    $teacher->save();
                    $updatedCount++;
                } else {
                    $errorMessages[] = "User with ID {$teacherData['id']} not found or is not a teacher.";
                }
            }
            DB::commit(); // Commit transaction ถ้าทุกอย่างเรียบร้อย
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction ถ้ามี error
            // Log error $e->getMessage()
            return redirect()->back()->with('error', 'An error occurred while updating teachers. Please try again. (' . $e->getMessage() . ')');
        }

        $flashMessage = "Successfully updated {$updatedCount} teachers.";
        if (count($errorMessages) > 0) {
            $flashMessage .= " Some users could not be updated: " . implode(', ', $errorMessages);
            return redirect()->route('teachers.bulk-edit.form')->with('warning', $flashMessage); // หรือ 'error'
        }

        return redirect()->route('teachers.bulk-edit.form')->with('success', $flashMessage);
    }
}
