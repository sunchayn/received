<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

class Home extends Controller
{
    /**
     * GET /settings.
     *
     * Settings home page where all the interaction is handled.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('pages.settings.index');
    }
}
