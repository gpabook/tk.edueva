<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AvatarController extends Controller
{
    public function create(Request $request)
    {
        $lavyuser = $request->user();

        return Inertia::render('AvatarUpload', [
            // 3️ Pass just the bits your Vue page needs
        'user' => [
        'name'       => $lavyuser->name,
        'email'       => $lavyuser->email,
        'avatar_url' => $lavyuser->avatar_url, // from your accessor
        ],
    ]);
    //    return Inertia::render('AvatarUpload');
    }
//===============================
public function store(Request $request)
{
    $user = $request->user();
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // รับไฟล์และสร้างชื่อไม่ซ้ำ
    $file     = $request->file('avatar');
    $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
    // ย่อรูปเป็น 300×300 แล้วบันทึกไป storage/app/public/avatars
    $image = Image::read($file)
                  ->scale(300, 300)
                  ->save(storage_path('app/public/avatars/'.$filename));


    $oldAvatar = $user->avatar;
    // อัปเดตข้อมูลผู้ใช้
    $request->user()->update([
        'avatar' => 'avatars/'.$filename,
    ]);
    ////////////
    if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
        Storage::disk('public')->delete($oldAvatar);
    }
    ////////////
    return back()->with('success', 'อัปโหลดสำเร็จ');
    //return Redirect::to('avatar.update');

}
}
