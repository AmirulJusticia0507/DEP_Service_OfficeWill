<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PublicController extends Controller
{
    public function welcome(): View
    {
        return view('public.welcome');
    }
}
