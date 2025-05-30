<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class UsersImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure
{
    private $importedCount = 0;
    private $skippedCount = 0; // This will now be a total of validation failures and other errors
    private $errors = [];      // For errors caught by onError
    private $failures = [];    // For validation failures caught by onFailure

    public function model(array $row)
    {
        // สมมติว่าใช้ WithHeadingRow:
        $student_id = $row['student_id'] ?? null;    // ดึงข้อมูลจากคอลัมน์ 'name' (หรือชื่อ header ที่คุณใช้)
        $name = $row['name'] ?? null;    // ดึงข้อมูลจากคอลัมน์ 'name' (หรือชื่อ header ที่คุณใช้)
        $email = $row['email'] ?? null;  // ดึงข้อมูลจากคอลัมน์ 'email'
        $role = $row['role'] ?? '4'; // ดึงข้อมูลจากคอลัมน์ 'role', ถ้าไม่มีให้เป็น 'student : 4'
        $status = $row['status'] ?? '1'; // ดึงข้อมูลจากคอลัมน์ 'status', ถ้าไม่มีให้เป็น '1'

        // --- ส่วนจัดการการข้ามแถว (Skip Rows) โดยตรง ---
        // ตัวอย่าง: ข้ามแถวถ้าข้อมูลสำคัญ (ชื่อหรืออีเมล) หายไป
        // หมายเหตุ: WithValidation concern ควรจะจัดการ rule 'required' ก่อนถึงจุดนี้
        // การเช็คตรงนี้เหมาะสำหรับ logic การข้ามแถวตามเงื่อนไขทางธุรกิจเพิ่มเติม
        if (empty($name) || empty($email)) {
            // ถ้าคุณมี $this->skippedCount และต้องการนับการข้ามแบบนี้โดยเฉพาะ:
            // $this->skippedCount++; (แต่ปกติ SkipsOnFailure จะจัดการการข้ามจาก validation)
            return null; // คืนค่า null เพื่อข้ามแถวนี้อย่างชัดเจน
        }
        // --- สิ้นสุดส่วนจัดการการข้ามแถว ---

        try {
            // สร้าง User ใหม่
            $user = User::create([
                'student_id'     => $student_id,
                'name'     => $name,
                'email'    => $email,
                'status'    => $status,
                'role'    => $role,
               // 'password' => Hash::make(Str::random(10)), // สร้างรหัสผ่านเริ่มต้นแบบสุ่ม
                'password' => Hash::make('secret'), // สร้างรหัสผ่านเริ่มต้นแบบสุ่ม
                'email_verified_at' => now(), // (เลือกได้) กำหนดให้ยืนยันอีเมลแล้ว
            ]);

            // ถ้าสร้าง User สำเร็จ กำหนด Role
            if ($user && !empty($role)) {
                // ตรวจสอบว่า Role ที่ระบุมาใน CSV มีอยู่จริงหรือไม่ (ป้องกัน error)
                if (\Spatie\Permission\Models\Role::where('id', $role)->exists()) {
                    $user->assignRole($role);
                } else {
                    // กรณี Role ไม่มีอยู่: อาจจะกำหนด Role เริ่มต้น, บันทึก error, หรือข้ามไป
                    $user->assignRole('4'); // กำหนด Role เริ่มต้นให้
                    // อาจจะเก็บ error/warning นี้ไว้แจ้งผู้ใช้:
                    // $this->roleAssignmentWarnings[] = "Role '{$roleName}' ของอีเมล {$email} ไม่พบ, กำหนด Role เริ่มต้นให้แทน";
                }
            }

            $this->importedCount++; // นับจำนวนที่ import สำเร็จ
            return $user; // คืนค่า Model User ที่สร้างใหม่

        } catch (\Illuminate\Database\QueryException $e) {
            // จัดการ error ที่อาจเกิดจากฐานข้อมูล เช่น unique constraint (ถ้า WithValidation ไม่ได้ดักจับไว้ก่อน)
            \Illuminate\Support\Facades\Log::error("UserImport - Database error สำหรับอีเมล {$email}: " . $e->getMessage());
            // ถ้าคุณใช้ SkipsOnError, exception นี้จะถูกจับโดยเมธอด onError
            // ถ้าไม่เช่นนั้น การ throw exception จะหยุดการ import หรือคุณสามารถ return null เพื่อข้ามแถวนี้
            // throw $e; // ปล่อยให้ SkipsOnError จัดการ (ถ้าใช้)
            return null; // หรือถ้าต้องการจัดการเองและข้ามแถว
        } catch (\Exception $e) {
            // จัดการ error อื่นๆ ที่ไม่คาดคิดระหว่างการสร้าง User หรือกำหนด Role
            \Illuminate\Support\Facades\Log::error("UserImport - General error สำหรับอีเมล {$email}: " . $e->getMessage());
            // throw $e; // ปล่อยให้ SkipsOnError จัดการ (ถ้าใช้)
            return null; // หรือถ้าต้องการจัดการเองและข้ามแถว
        }

        // ส่วนนี้อาจจะไม่จำเป็นถ้า try-catch ครอบคลุมทุกเส้นทางแล้ว
        // แต่ใส่ไว้เพื่อให้ Intelephense มั่นใจว่ามีการคืนค่าเสมอ (ถ้ามันยังแจ้งเตือน)
        // return null;
    }

    public function rules(): array
    {
        // Assuming you are using WithHeadingRow and your CSV headers are 'name', 'email', 'role'
        return [
            'student_id' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email', // Ensures email is unique in the 'users' table
            'role' => 'nullable|integer|exists:roles,id', // Optional: validates role name exists in 'roles' table if provided
            'status' => 'nullable|integer', // Optional: validates role name exists in 'roles' table if provided
            // Add other validation rules for other columns as needed
            // For example, if you have a 'phone_number' column:
            // 'phone_number' => 'nullable|string|regex:/^[0-9]{10}$/',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $this->skippedCount += count($failures); // Increment skipped count for validation failures
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }

    public function onError(Throwable $e)
    {
        $this->skippedCount++; // Increment skipped count for other errors
        $this->errors[] = "Error during import: " . $e->getMessage();
     //   \Illuminate\Support\Facades\Log::error("UserImport Error: " . $e->getMessage(), [
     //       'trace' => $e->getTraceAsString()
     //   ]);
    }

    // --- THESE ARE THE METHODS THE ERROR IS ABOUT ---
    public function getImportedCount(): int
    {
     //   dd($this->importedCount);
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getFailures(): array
    {
        return $this->failures;
    }

    public function getErrors(): array // Renamed from getOtherImportErrors for consistency
    {
        return $this->errors;
    }
}