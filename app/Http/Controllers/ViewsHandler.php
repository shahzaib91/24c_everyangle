<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class ViewsHandler
 * @package App\Http\Controllers
 * This class is intended to be used as Dynamic Views Renderer
 */
class ViewsHandler extends Controller
{
    /*
     * Login view function
     */
    public function login()
    {
        if(Auth::check())
        {
            return redirect("/dashboard/view");
        }
        return view('login');
    }

    /*
     * Logout action function
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
