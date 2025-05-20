<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <style>
    @font-face {
      font-family: 'THSarabunNew';
      font-style: normal;
      font-weight: normal;
      src: url("file://{{ base_path('resources/fonts/THSarabunNew.ttf') }}") format('truetype');
    }
    body {
      font-family: 'THSarabunNew', sans-serif;
      font-size: 14px;
    }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #333; padding: 4px; }
    th.balance, td.balance { text-align: right; }
  </style>
</head>
<body>
  <h1>สมุดบัญชีออมทรัพย์ของ {{ $account->user->name }}</h1>

  <p><strong>ยอดคงเหลือตอนนี้: </strong>{{ number_format($account->balance, 2) }} บาท</p>

  <table>
    <thead>
      <tr>
        <th>วันที่</th>
        <th>ประเภท</th>
        <th>จำนวนเงิน (บาท)</th>
        <th class="balance">ยอดคงเหลือ (บาท)</th>
        <th>รายละเอียด</th>
      </tr>
    </thead>
    <tbody>
      @php $running = 0; @endphp
      @foreach($account->transactions as $tx)
        @php
          $running += $tx->type === 'deposit'
            ? $tx->amount
            : -$tx->amount;
        @endphp
        <tr>
          <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
          <td>{{ $tx->type === 'deposit' ? 'ฝาก' : 'ถอน' }}</td>
          <td>{{ number_format($tx->amount, 2) }}</td>
          <td class="balance">{{ number_format($running, 2) }}</td>
          <td>{{ $tx->description }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
