<?php

namespace App\Http\Controllers;

use TCPDF;
use App\Models\User;
use Illuminate\Http\Request;

use App\Exports\StudentsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Room;

class StudentExportController extends Controller
{
    public function exportPdf(Request $request)
    {
        $students = User::query()
            ->where('role', 4)
            ->with('currentRoom.classLevel')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('name_th', 'like', '%' . $request->search . '%')
                        ->orWhere('student_id', 'like', '%' . $request->search . '%');
                });
            })
            ->get();

        // Create new PDF
        $pdf = new TCPDF();
        $pdf->SetCreator('YourApp');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Students PDF Export');
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        // Content
        $html = '<h3>รายชื่อนักเรียน</h3>';
        $html .= '
        <table border="1" cellpadding="4">
            <thead>
                <tr style="font-weight: bold; background-color: #f5f5f5;">
                    <th width="15%">รหัสนักเรียน</th>
                    <th width="25%">ชื่อ-นามสกุล</th>
                    <th width="25%">Email</th>
                    <th width="15%">ระดับชั้น</th>
                    <th width="20%">ห้อง</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($students as $student) {
            $fullName = $student->name_th . ' ' . $student->surname_th;
            $classLevel = optional($student->currentRoom?->classLevel)->name_th ?? '-';
            $roomName = optional($student->currentRoom)->name_room_th ?? '-';

            $html .= "
                <tr>
                    <td>{$student->student_id}</td>
                    <td>{$fullName}</td>
                    <td>{$student->email}</td>
                    <td>{$classLevel}</td>
                    <td>{$roomName}</td>
                </tr>";
        }

        $html .= '</tbody></table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        return response($pdf->Output('students.pdf', 'S'))->header('Content-Type', 'application/pdf');
    }

    public function exportExcelGuardians(Request $request)
    {
        $timestamp = now()->format('Ymd_His');

        $filename = "students_export_{$timestamp}.xlsx";

        // หากเลือกห้องเรียน → เพิ่มชื่อห้องลงในชื่อไฟล์
        if ($roomId = $request->input('room_id')) {
            $room = Room::find($roomId);
            if ($room) {
                $roomName = preg_replace('/[\/\\\\]/', '_', $room->name_room_th);
                $filename = "students_{$roomName}_{$timestamp}.xlsx";
            }
        }

        return Excel::download(new StudentsExport($request), $filename);
    }
}
