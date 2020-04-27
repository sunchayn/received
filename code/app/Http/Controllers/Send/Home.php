<?php

namespace App\Http\Controllers\Send;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class Home extends Controller
{
    public function index($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return view('pages.send.index', ['username' => $username]);
    }
}
