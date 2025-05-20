<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use TCPDF;
use TCPDF_FONTS;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class BankAccountController extends Controller
{
    /**
     * แสดงบัญชีและรายการธุรกรรมของผู้ใช้
     */
public function show()
{
    $user = Auth::user();

    // fetch or create the bank account in one go
    $account = BankAccount::firstOrCreate(
        ['user_id' => $user->id],
        ['balance' => 0]
    );

    // now $account is never null
    $account->load('transactions');

    return Inertia::render('Bank/Account', [
        'account' => $account,
    ]);
}

    /**
     * ทำรายการฝากเงิน
     */
    public function deposit(Request $request)
    {
        $data = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $account = Auth::user()->bankAccount;

        DB::transaction(function () use ($account, $data) {
            $account->increment('balance', $data['amount']);
            $account->transactions()->create([
                'type'        => 'deposit',
                'amount'      => $data['amount'],
                'description' => $data['description'] ?? null,
            ]);
        });

        return back()->with('success', 'ฝากเงินเรียบร้อยแล้ว');
    }

    /**
     * ทำรายการถอนเงิน
     */
    public function withdraw(Request $request)
    {
        $account = Auth::user()->bankAccount;

        $data = $request->validate([
            'amount'      => ['required','numeric','min:0.01','max:' . $account->balance],
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($account, $data) {
            $account->decrement('balance', $data['amount']);
            $account->transactions()->create([
                'type'        => 'withdraw',
                'amount'      => $data['amount'],
                'description' => $data['description'] ?? null,
            ]);
        });

        return back()->with('success', 'ถอนเงินเรียบร้อยแล้ว');
    }

    /**
     * สร้างและดาวน์โหลด PDF Passbook
     */

public function passbookPdf()
{
    $account = Auth::user()->bankAccount->load('transactions');

    // 1) instantiate TCPDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // 2) register your Thai TTF and get back its internal name
    $fontFile = base_path('resources/fonts/THSarabunNew.ttf');
    // parameters: (filepath, fonttype, encoding, flags, outpath='')
    $fontName = TCPDF_FONTS::addTTFfont($fontFile, 'TrueTypeUnicode', '', 32);

    // 3) now set that font
    $pdf->SetFont($fontName, '', 14);

    // 4) add a page & write your HTML
    $pdf->AddPage();
    $html = view('bank.passbook', compact('account'))->render();
    $pdf->writeHTML($html, true, false, true, false, '');

    // 5) output PDF to browser
    return response($pdf->Output('passbook.pdf', 'S'))
        ->header('Content-Type', 'application/pdf');
}

}