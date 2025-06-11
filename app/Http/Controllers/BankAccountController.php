<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use TCPDF;
use TCPDF_FONTS;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class BankAccountController extends Controller
{

public function bankuser(Request $request)
{
    $search = $request->input('search');

    $users = BankAccount::with('user')
    ->whereHas('user') // only include if the user relation exists
    ->when($search, function($query, $search) {
        $query->whereHas('user', function($q) use ($search) {
            $q->where('name',  'like', "%{$search}%")
              ->orWhere('name_th', 'like', "%{$search}%")
              ->orWhere('surname_th', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    })
    ->orderBy('id', 'asc')
    ->paginate(20)
    ->withQueryString();

    return Inertia::render('Bank/User', [
        'users'   => $users,
        'filters' => ['search' => $search],
    ]);
}

    /**
     * แสดงบัญชีและรายการธุรกรรมของผู้ใช้ให้เจ้าของบัญชีดู
     */
    public function showme($student_id)
    {
        $user = User::where('student_id', $student_id)->firstOrFail();

        $account = $user->bankAccount->load('transactions');

        return Inertia::render('Bank/Accountme', [
            'account' => $account,
            'acc_name' => $user->name,
        ]);
    }

    /**
     * แสดงบัญชีและรายการธุรกรรมของผู้ใช้
     */
public function show($student_id)
{
    //$user = User::findOrFail($student_id);
    $user = User::where('student_id', $student_id)->firstOrFail();

    // Then grab their bank account and load transactions
    $account = $user->bankAccount     // or ->bankAccounts() if it's hasMany
                     ->load('transactions');

    return Inertia::render('Bank/Account', [
        'account' => $account,
        'acc_name' => $user->name,
    ]);
}

    /**
     * ทำรายการฝากเงิน
     */
    public function deposit(Request $request)
    {
        // 1) Validate
        $data = $request->validate([
            'student_id' => 'required|exists:users,student_id',
            'amount'     => 'required|numeric|min:0.01',
            'description'=> 'nullable|string',
        ]);

        // 2) Get account
        $account = BankAccount::where('student_id', $data['student_id'])->firstOrFail();

        // 3) Save transaction with staff_id
        DB::transaction(function () use ($account, $data) {
            $account->increment('balance', $data['amount']);
            $account->transactions()->create([
                'type'        => 'deposit',
                'amount'      => $data['amount'],
                'description' => $data['description'] ?? null,
                'staff_id'    => auth()->id(), // 👈 เพิ่มบรรทัดนี้
            ]);
        });

        return back()->with('success', 'ฝากเงินเรียบร้อยแล้ว');
    }


    /**
     * ทำรายการถอนเงิน
     */
    public function withdraw(Request $request)
    {
        $data_uid = $request->validate([
            'student_id' => 'required|exists:users,student_id',
        ]);

        $account = BankAccount::where('student_id', $data_uid['student_id'])->firstOrFail();

        $data = $request->validate([
            'amount'     => ['required', 'numeric', 'min:0.01', 'max:' . $account->balance],
            'description'=> 'nullable|string',
        ]);

        DB::transaction(function () use ($account, $data) {
            $account->decrement('balance', $data['amount']);

            $account->transactions()->create([
                'type'        => 'withdraw',
                'amount'      => $data['amount'],
                'description' => $data['description'] ?? null,
                'staff_id'    => auth()->id(), // 👈 เพิ่มบรรทัดนี้
            ]);
        });

        return back()->with('success', 'ถอนเงินเรียบร้อยแล้ว');
    }


    /**
     * สร้างและดาวน์โหลด PDF Passbook
     */

     public function passbookPdf($student_id)
     {
         $user = User::where('student_id', $student_id)->firstOrFail();

         $account = $user->bankAccount->load([
             'transactions' => function ($query) {
                 $query->whereNull('printed_at')->orderBy('created_at');
             }
         ]);

         if ($account->transactions->isEmpty()) {
             return redirect()->route('passbook.select', ['student_id' => $student_id])
                 ->with('info', 'ไม่มีรายการอัพเดทบัญชี');
         }

         $printedCount = $user->bankAccount->transactions()
             ->whereNotNull('printed_at')
             ->count();

         $pdf = new \TCPDF('L', 'mm', [170, 190], true, 'UTF-8', false);
         $pdf->SetMargins(10, 10, 10);
         $pdf->setPrintHeader(false);
         $pdf->setPrintFooter(false);

         $fontFile = base_path('resources/fonts/THSarabunNew.ttf');
         $fontName = \TCPDF_FONTS::addTTFfont($fontFile, 'TrueTypeUnicode', '', 32);
         $pdf->SetFont($fontName, '', 11);
         $pdf->setCellHeightRatio(1.0);

         $rowHeight = 4.2;

         // 🔢 ระบุว่าแต่ละหน้าได้กี่บรรทัด
         $linesPerOddPage  = 14; // หน้า 1,3,5,...
         $linesPerEvenPage = 17; // หน้า 2,4,6,...

         // ใช้สำหรับเลื่อนเริ่มพิมพ์ขึ้น 3 บรรทัด (เฉพาะหน้าคู่)
         $adjustTopLinesEvenPage = 3;

         $colWidths = [
             'date'     => 25,
             'withdraw' => 25,
             'deposit'  => 25,
             'balance'  => 35,
             'staff'    => 25,
         ];

         $printTime = now();
         $lineNo = $printedCount + 1;
         $printedLines = 0;
         $currentBalance = 0;

         // ✅ คำนวณยอดคงเหลือก่อนหน้า
         $allTx = $user->bankAccount->transactions()->orderBy('created_at')->get();
         foreach ($allTx as $tx) {
             if ($tx->printed_at) {
                 $currentBalance += $tx->type === 'deposit' ? $tx->amount : -$tx->amount;
             }
         }

         // ✅ แบ่ง transactions ทั้งหมดเป็นรายการแถวตามลำดับ
         $transactions = $account->transactions;
         $txChunks = [];

         // แบ่งเป็นหน้าๆ ตามลำดับหน้า
         $pageNum = 1;
         $pointer = 0;
         while ($pointer < $transactions->count()) {
             $linesPerPage = $pageNum % 2 === 0 ? $linesPerEvenPage : $linesPerOddPage;
             $txChunks[] = $transactions->slice($pointer, $linesPerPage);
             $pointer += $linesPerPage;
             $pageNum++;
         }

         $pageNum = 1;
         foreach ($txChunks as $chunk) {
             $pdf->AddPage();

             if ($pageNum === 1 && $printedCount > 0) {
                 $pdf->setXY(10,45);
                // $pdf->Ln($printedCount * $rowHeight);
             } elseif ($pageNum % 2 === 0) {
                 // หน้าเลขคู่ → พิมพ์สูงขึ้น 3 บรรทัด
                 $pdf->setXY(10,110);
                 //$pdf->Ln(-$adjustTopLinesEvenPage * $rowHeight);
             }

             foreach ($chunk as $tx) {
                 $type = strtolower($tx->type ?? '');
                 $isWithdraw = $type === 'withdraw';
                 $isDeposit  = $type === 'deposit';

                 $withdraw = $isWithdraw ? $tx->amount : 0;
                 $deposit  = $isDeposit ? $tx->amount : 0;
                 $currentBalance = $currentBalance - $withdraw + $deposit;

                 $carbon = \Carbon\Carbon::parse($tx->created_at);
                 $date = $carbon->format('d/m/') . ($carbon->year + 543);

                 $pdf->MultiCell($colWidths['date'],     $rowHeight, $date, 0, 'C', false, 0);
                 $pdf->MultiCell($colWidths['withdraw'], $rowHeight, $withdraw > 0 ? '-' . number_format($withdraw, 2) : '-', 0, 'C', false, 0);
                 $pdf->MultiCell($colWidths['deposit'],  $rowHeight, $deposit > 0 ? '+' . number_format($deposit, 2) : '-', 0, 'C', false, 0);
                 $pdf->MultiCell($colWidths['balance'],  $rowHeight, '*' . number_format($currentBalance, 2), 0, 'C', false, 0);
                 $pdf->MultiCell($colWidths['staff'],    $rowHeight, $tx->staff_id ?? '-', 0, 'C', false, 1);

                 $tx->update([
                     'printed_at' => $printTime,
                     'print_line_no' => $lineNo++,
                 ]);

                 $printedLines++;
             }

             $pageNum++;
         }

         $filename = "passbook_{$student_id}_" . $printTime->format('Y-m-d_H-i-s') . ".pdf";

         return response($pdf->Output($filename, 'S'))
             ->header('Content-Type', 'application/pdf')
             ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
     }

     public function selectPassbookLines($student_id)
     {
         $user = User::where('student_id', $student_id)->firstOrFail();

         $printed = $user->bankAccount->transactions()
             ->whereNotNull('printed_at')
             ->count();

         $pending = $user->bankAccount->transactions()
             ->whereNull('printed_at')
             ->count();

         $total = $printed + $pending;

         return Inertia::render('Bank/PassbookSelect', [
             'user' => $user,
             'printed' => $printed,
             'pending' => $pending,
             'total' => $total,
             'info' => session('info')
         ]);
     }

     public function customPassbookPdf(Request $request)
     {
         $student_id = $request->input('student_id');
         $start_line = (int) $request->input('start_line', 1);
         $line_count = (int) $request->input('line_count', 10);

         if (!$student_id || $start_line < 1 || $line_count < 1) {
             abort(400, 'ข้อมูลไม่ครบถ้วน');
         }

         $user = User::where('student_id', $student_id)->firstOrFail();

         // ดึงธุรกรรมทั้งหมด
         $allTransactions = $user->bankAccount->transactions()
             ->orderBy('created_at')
             ->get();

         $totalTx = $allTransactions->count();

         if ($totalTx === 0 || $start_line > $totalTx) {
             return back()->with('info', 'ไม่มีรายการที่เลือกสำหรับพิมพ์');
         }

         // ตัดช่วงที่ต้องการพิมพ์
         $transactions = $allTransactions->slice($start_line - 1, $line_count);

         if ($transactions->isEmpty()) {
             return back()->with('info', 'ไม่มีรายการที่เลือกสำหรับพิมพ์');
         }

         // ✅ เตรียม PDF
         $pdf = new \TCPDF('L', 'mm', [170, 190], true, 'UTF-8', false);
         $pdf->SetMargins(10, 10, 10);
         $pdf->setPrintHeader(false);
         $pdf->setPrintFooter(false);

         $fontFile = base_path('resources/fonts/THSarabunNew.ttf');
         $fontName = \TCPDF_FONTS::addTTFfont($fontFile, 'TrueTypeUnicode', '', 32);
         $pdf->SetFont($fontName, '', 11);
         $pdf->setCellHeightRatio(1.0);

         // ✅ คำนวณยอดก่อนหน้า
         $currentBalance = 0;
         foreach ($allTransactions->slice(0, $start_line - 1) as $tx) {
             $currentBalance += $tx->type === 'deposit' ? $tx->amount : -$tx->amount;
         }

         $rowHeight = 4.2;
         $colWidths = [
             'date'     => 25,
             'withdraw' => 25,
             'deposit'  => 25,
             'balance'  => 35,
             'staff'    => 25,
         ];

         $lineNo = $start_line;
         $printTime = now();

         // ✅ แบ่งรายการตามหน้าคี่/คู่: 14 หรือ 17 บรรทัด
         $chunks = [];
         $pointer = 0;
         $page = 1;
         while ($pointer < $transactions->count()) {
             $limit = ($page % 2 === 0) ? 17 : 14;
             $chunks[] = $transactions->slice($pointer, $limit);
             $pointer += $limit;
             $page++;
         }

         // ✅ พิมพ์ตามหน้า
         $page = 1;
         foreach ($chunks as $chunk) {
             $pdf->AddPage();

             // ย้ายหัวเริ่มต้นสำหรับหน้าคู่
             if ($page % 2 === 0) {
                $pdf->setXY(10,110);

             } else {
                $pdf->setXY(10,45);
             }

             foreach ($chunk as $tx) {
                 $type = strtolower($tx->type ?? '');
                 $isWithdraw = $type === 'withdraw';
                 $isDeposit  = $type === 'deposit';

                 $withdraw = $isWithdraw ? $tx->amount : 0;
                 $deposit  = $isDeposit ? $tx->amount : 0;

                 $currentBalance = $currentBalance - $withdraw + $deposit;

                 $carbon = \Carbon\Carbon::parse($tx->created_at);
                 $date = $carbon->format('d/m/') . ($carbon->year + 543);

                 $pdf->MultiCell($colWidths['date'],     $rowHeight, $date, 0, 'C', false, 0);
                 $pdf->MultiCell($colWidths['withdraw'], $rowHeight, $withdraw > 0 ? '-' . number_format($withdraw, 2) : '-', 0, 'C', false, 0);
                 $pdf->MultiCell($colWidths['deposit'],  $rowHeight, $deposit > 0 ? '+' . number_format($deposit, 2) : '-', 0, 'C', false, 0);
                 $pdf->MultiCell($colWidths['balance'],  $rowHeight, '*' . number_format($currentBalance, 2), 0, 'C', false, 0);
                 $pdf->MultiCell($colWidths['staff'],    $rowHeight, $tx->staff_id ?? '-', 0, 'C', false, 1);

                 $lineNo++;
             }

             $page++;
         }

         $filename = "passbook_{$student_id}_{$printTime->format('Y-m-d_H-i-s')}.pdf";

         return response($pdf->Output($filename, 'S'))
             ->header('Content-Type', 'application/pdf')
             ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
     }





///////// end Controller //////////////
}
