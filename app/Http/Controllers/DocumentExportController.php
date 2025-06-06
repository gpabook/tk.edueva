<?php

namespace App\Http\Controllers;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // ตัวอย่าง Model

class DocumentExportController extends Controller
{
    public function exportUsersToWord()
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Tahoma'); // หรือฟอนต์ไทยที่รองรับ เช่น TH SarabunPSK
        $phpWord->setDefaultFontSize(11);

        // สร้าง Section ใหม่
        $section = $phpWord->addSection();

        // เพิ่มหัวข้อ
        $section->addTitle('User List', 1); // Level 1 heading

        // กำหนดฟอนต์และขนาดโดยตรง
        $section->addText(
            'ข้อความนี้ใช้ฟอนต์ TH SarabunPSK ขนาด 18 และเป็นตัวหนา',
            ['name' => 'TH SarabunPSK', 'size' => 18, 'bold' => true]
        );

        // เพิ่มตาราง
        $tableStyle = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80];
        $phpWord->addTableStyle('UserTable', $tableStyle);
        $table = $section->addTable('UserTable');

        // เพิ่มแถวหัวตาราง
        $table->addRow();
        $table->addCell(2000)->addText('ID', ['bold' => true]);
        $table->addCell(4000)->addText('Name', ['bold' => true]);
        $table->addCell(4000)->addText('Email', ['bold' => true]);

        // ดึงข้อมูล User (ตัวอย่าง)
        $users = User::select('id', 'name', 'email')->get();
        foreach ($users as $user) {
            $table->addRow();
            $table->addCell(2000)->addText($user->id);
            $table->addCell(4000)->addText($user->name);
            $table->addCell(4000)->addText($user->email);
        }

        // เพิ่มข้อความธรรมดา
        $section->addTextBreak(1); // เว้นบรรทัด
        $section->addText('Generated on: ' . date('Y-m-d H:i:s'));

        // บันทึกไฟล์และส่งให้ดาวน์โหลด
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007'); // Word2007 คือ .docx
        $fileName = "exported_users_" . Auth::user()->id . "_" . now()->format('Ymd_His') . ".docx";
       // $fileName = 'users_export.docx';

        // วิธีที่ 1: บันทึกลง Server ชั่วคราวแล้วดาวน์โหลด (ถ้าจำเป็น)
        // $objWriter->save(storage_path('app/' . $fileName));
        // return response()->download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);

        // วิธีที่ 2: Stream ไฟล์โดยตรง (แนะนำ)
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment;filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit; // จำเป็นต้องมี exit หลังจาก save('php://output')
    }
}