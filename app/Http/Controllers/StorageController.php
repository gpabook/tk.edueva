<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class StorageController extends Controller
{
    public function link(Request $request)
    {
        // 🔐 Authorization check—only allow your admins!
        if ($request->user()->cannot('manage-storage')) {
            abort(403, 'Unauthorized.');
        }

        // ⚙️ Idempotency: only link if it doesn't exist
        if (! file_exists(public_path('storage'))) {
            Artisan::call('storage:link');
            $output = Artisan::output();
            return response("✅ storage link created:\n" . $output, 200)
                       ->header('Content-Type', 'text/plain');
        }

        return response("⚠️ storage link already exists.", 200)
                   ->header('Content-Type', 'text/plain');
    }
}
