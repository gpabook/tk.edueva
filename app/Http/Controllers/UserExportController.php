<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport; // Assuming you have created this Export class
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserExportController extends Controller
{
    public function exportExcel(Request $request)
    {

        return Excel::download(new UsersExport, 'users.xlsx');

        // For CSV:
        // return Excel::download(new UsersExport, 'users.csv', \Maatwebsite\Excel\Excel::CSV);

    }
}
