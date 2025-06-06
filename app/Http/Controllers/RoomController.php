<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\ClassLevel; // For fetching class levels for dropdowns
use App\Models\User;      // For fetching users (teachers/advisors) - will be used when advisor logic is added
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule; // For more complex validation rules

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('classLevel', 'advisors') // Eager load the classLevel relationship
            ->orderBy('class_level_id')
            ->orderBy('name_room_en') // Optional: good for consistent ordering
            ->paginate(20);

        return Inertia::render('Rooms/Index', [
            'rooms' => $rooms,
        ]);
    }

    public function create()
    {
        // Fetch class levels for the select dropdown in the form
        //$classLevels = ClassLevel::orderBy('name_en')->get(['id', 'name_en', 'code']);
        $classLevels = ClassLevel::orderBy('id')->get(['id', 'name_en', 'code']);

        // ครูมี role 'teacher' หรือมีวิธีอื่นในการระบุว่าเป็นครู
        $potentialAdvisors = User::where('role', 'teacher') // ปรับเงื่อนไขตามระบบของคุณ
                                 ->orderBy('name')
                                 ->get(['id', 'name']);

        return Inertia::render('Rooms/Form', [
            'mode'        => 'create',
            'classLevels' => $classLevels,
            'potentialAdvisors' => $potentialAdvisors,
            'room'              => null, // ส่ง room เป็น null สำหรับโหมด create
            'currentAdvisorIds' => [], // ไม่มี advisor ที่ถูกเลือกสำหรับห้องใหม่
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_level_id' => 'required|exists:class_levels,id',
            'name_room_en'   => [
                'required',
                'string',
                'max:20',
                // Assumes 'name_room_en' should be unique within the scope of 'class_level_id'
                Rule::unique('rooms')->where(function ($query) use ($request) {
                    return $query->where('class_level_id', $request->class_level_id);
                }),
            ],
            'name_room_th'   => 'required|string|max:20',
            'advisor_ids'    => 'nullable|array', // Array ของ ID ครูที่ปรึกษา
            'advisor_ids.*'  => 'exists:users,id', // ตรวจสอบว่า ID แต่ละอันมีในตาราง users
        ]);

        // If you have an 'advisor_id' field from the form for a single advisor:
        // $room = Room::create($data);

        // If you are ONLY creating the room without advisor logic yet:
        $room = Room::create([
            'class_level_id' => $data['class_level_id'],
            'name_room_en'   => $data['name_room_en'],
            'name_room_th'   => $data['name_room_th'],
            // 'advisor_id'  => $data['advisor_id'] ?? null, // if managing single advisor
        ]);

        // บันทึกครูที่ปรึกษา
        if (isset($data['advisor_ids'])) {
            $room->advisors()->sync($data['advisor_ids']);
        } else {
            $room->advisors()->detach(); // ถ้าไม่มีการส่ง advisor_ids มา ให้ลบทั้งหมดที่มีอยู่ (ถ้าต้องการ)
        }

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function edit(Room $room)
    {
        $room->load('advisors'); // โหลด advisors ที่ผูกกับห้องนี้
        $classLevels = ClassLevel::orderBy('id')->get(['id', 'name_en', 'code']);
        $potentialAdvisors = User::role('teacher') // 'teacher' คือ name จากตาราง roles
                                 ->orderBy('name')
                                 ->get(['id', 'name']); // เพิ่ม 'email' หรือ field อื่นๆ ที่ต้องการแสดง
       $currentAdvisorIds = $room->advisors->pluck('id')->toArray();

         return Inertia::render('Rooms/Form', [
            'mode'        => 'edit',
             'room'        => $room, // This is the crucial part
             'classLevels' => $classLevels,
             'potentialAdvisors' => $potentialAdvisors,
             'currentAdvisorIds' => $currentAdvisorIds, // ส่ง ID ของ advisor ปัจจุบัน
         ]);
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'class_level_id' => 'required|exists:class_levels,id',
            'name_room_en'   => [
                'required',
                'string',
                'max:20',
                Rule::unique('rooms')->where(function ($query) use ($request) {
                    return $query->where('class_level_id', $request->class_level_id);
                })->ignore($room->id), // Ignore the current room's ID on update
            ],
            'name_room_th'   => 'required|string|max:20',
            'advisor_ids'    => 'nullable|array',
            'advisor_ids.*'  => 'exists:users,id',
        ]);


        $room->update([
            'class_level_id' => $data['class_level_id'],
            'name_room_en'   => $data['name_room_en'],
            'name_room_th'   => $data['name_room_th'],
            // 'advisor_id'  => $data['advisor_id'] ?? null, // if managing single advisor
        ]);

        if (isset($data['advisor_ids'])) {
            $room->advisors()->sync($data['advisor_ids']);
        } else {
            // หากไม่ต้องการให้ advisor_ids เป็น nullable และต้องมีอย่างน้อยหนึ่งคน
            // หรือต้องการลบทั้งหมดถ้าส่ง array ว่างมา ก็ใช้ detach()
            $room->advisors()->detach(); // ถ้าส่ง advisor_ids เป็น array ว่าง หรือไม่ได้ส่งมาเลย
        }

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        // advisors จะถูก detach อัตโนมัติถ้า onDelete('cascade') ถูกตั้งค่าใน migration ของ pivot table
        // หรือถ้าไม่แน่ใจ สามารถ detach() เองก่อนได้
        // $room->advisors()->detach();
        $room->delete();

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}