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


class BankAccountController extends Controller
{

public function bankuser(Request $request)
{
    $search = $request->input('search');

    $users = BankAccount::with('user')
        ->when($search, function($query, $search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->orderBy('id', 'asc')
        ->paginate(20)
        ->withQueryString(); // เก็บ ?search=… ไว้ในลิงก์ pagination

    return Inertia::render('Bank/User', [
        'users'   => $users,
        'filters' => ['search' => $search],
    ]);
}

    /**
     * แสดงบัญชีและรายการธุรกรรมของผู้ใช้ให้เจ้าของบัญชีดู
     */
public function showme($user_id)
{
    $user = User::findOrFail($user_id);

    // Then grab their bank account and load transactions
    $account = $user->bankAccount     // or ->bankAccounts() if it's hasMany
                     ->load('transactions');

    return Inertia::render('Bank/Accountme', [
        'account' => $account,
        'acc_name' => $user->name,
    ]);
}
    /**
     * แสดงบัญชีและรายการธุรกรรมของผู้ใช้
     */
public function show($user_id)
{
    $user = User::findOrFail($user_id);

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
        // 1) Validate amount, description, AND the user_id from your hidden input
        $data = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        // 2) Lookup that user's bank account
      //$account = Auth::user()->bankAccount;
        $account = BankAccount::where('user_id', $data['user_id'])
                ->firstOrFail();

        // 3) Perform your transaction
/////////////////////////////////////////////////
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
        $data_uid = $request->validate([
            'user_id'     => 'required|exists:users,id',
        ]);
       // $account = Auth::user()->bankAccount;

       $account = BankAccount::where('user_id', $data_uid['user_id'])
       ->firstOrFail();

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

public function passbookPdf($user_id)
{
    $user = User::findOrFail($user_id);

    // Then grab their bank account and load transactions
    $account = $user->bankAccount     // or ->bankAccounts() if it's hasMany
                     ->load('transactions');

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
