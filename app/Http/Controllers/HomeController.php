<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index() {
        return Inertia::render('Home'); // Home.vue
    }

    public function about() {
        return Inertia::render('About', [
            'message' => 'Inertia',
            'postcode' => 11000
        ]); // About.vue
    }

    public function user() {
        //$users = User::all();
        $users = User::paginate(10);
        //return $users;

         return Inertia::render('User', [
             'users' => $users
         ]); // User.vue
    }
}