<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Throwable;

class UserController extends Controller
{

    public function index()
    {
        $users = User::where('role', '!=', \App\Enums\UserRole::Student)
            ->orderBy('role')
            ->paginate(10);

        $users->getCollection()->transform(function ($user) {
            $user->role_key = $user->role->key(); // ✅ safe เพราะ role ถูก cast แล้ว
            return $user;
        });

        return inertia('Users/Index', [
            'users' => $users,
            'success' => session('success'),
            'error' => session('error'),
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => collect(UserRole::cases())->mapWithKeys(function ($role) {
                return [$role->value => $role->key()]; // เช่น 1 => 'superadmin'
            }),
        ]);
    }
    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => 'required|string|unique:users,student_id',
            'name_th'      => 'required|string|max:255',
            'surname_th'   => 'required|string|max:255',
            'name_en'      => 'nullable|string|max:255',
            'surname_en'   => 'nullable|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'gender'       => ['required', Rule::in(['male', 'female'])],
            'birthday'     => 'nullable|date',
            'room_id'      => 'nullable|exists:rooms,id',
            'password'     => 'required|string|min:6|confirmed',
            'role'         => ['required', Rule::enum(UserRole::class)],
            'status'       => 'required|boolean',
            'avatar'       => 'nullable|string', // You can handle file upload elsewhere
        ]);

        $validated['name'] = $validated['name_th'] . ' ' . $validated['surname_th'];
        $validated['password'] = Hash::make($validated['password']);
        $validated['avatar'] = $validated['avatar'] ?? 'images/default-avatar.png';

        $user = User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'student_id'   => ['nullable', 'string', Rule::unique('users', 'student_id')->ignore($user->id)],
        'name_th'      => 'required|string|max:255',
        'surname_th'   => 'required|string|max:255',
        'name_en'      => 'nullable|string|max:255',
        'surname_en'   => 'nullable|string|max:255',
        'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        'gender'       => ['required', Rule::in(['male', 'female'])],
        'birthday'     => 'nullable|date',
        'room_id'      => 'nullable|exists:rooms,id',
        'password'     => 'nullable|string|min:6|confirmed',
        'role'         => ['required', Rule::enum(UserRole::class)],
        'status'       => 'required|boolean',
        'avatar'       => 'nullable|string',
    ]);

    $validated['name'] = $validated['name_th'] . ' ' . $validated['surname_th'];

    if (!empty($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    } else {
        unset($validated['password']); // อย่าเปลี่ยน password ถ้าไม่ได้ตั้งใหม่
    }

    $user->update($validated);

    return redirect()->route('users.index')->with('success', 'แก้ไข ผู้ใช้สำเร็จเรียบร้อย.');
}

    /**
     * Delete the specified user.
     */

    /**
     * Remove the specified user from storage along with all related data.
     *
     * @param  \App\Models\User  $user The user instance to be deleted.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        try {
            // 3. ใช้ DB Transaction เพื่อให้แน่ใจว่าการลบข้อมูลทั้งหมดเกิดขึ้นอย่างสมบูรณ์
            // หากมีขั้นตอนใดล้มเหลว ระบบจะย้อนกลับ (rollback) การลบทั้งหมดที่ทำไปก่อนหน้า
            DB::transaction(function () use ($user) {

                // ลบความสัมพันธ์ในตาราง pivot 'room_student' (Many-to-Many)
                // Detaches all rooms from the user in the pivot table.
                $user->enrolledRooms()->detach();

                // ลบความสัมพันธ์ในตาราง pivot 'room_advisor' (Many-to-Many)
                // Detaches all advising rooms from the user.
                $user->advisingRooms()->detach();

                // ลบข้อมูลผู้ปกครองทั้งหมดที่ผูกกับผู้ใช้นี้ (One-to-Many)
                // Deletes all guardian records associated with this user.
                $user->guardians()->delete();

                // ลบข้อมูลบัญชีธนาคาร (One-to-One)
                // Eloquent is smart enough to handle cases where the bank account doesn't exist.
                // It will execute a DELETE query with a WHERE clause.
                if ($user->bankAccount) {
                    $user->bankAccount()->delete();
                }

                // เมื่อข้อมูลที่เกี่ยวข้องทั้งหมดถูกลบแล้ว จึงลบผู้ใช้หลัก
                // Finally, delete the user itself.
                $user->delete();

            });

        } catch (Throwable $e) {
            // 4. หากเกิดข้อผิดพลาดขึ้นใน Transaction ให้ส่งข้อความแจ้งเตือน
            // และสามารถ log error ไว้เพื่อตรวจสอบได้
            // Log::error('Failed to delete user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the user. Please try again.');
        }

        return redirect()->route('users.index')
                         ->with('success', 'User and all related data deleted successfully.');
    }
}