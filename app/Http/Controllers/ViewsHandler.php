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

    /**
     * @param $current_module
     * @param $current_view
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * This is our dynamic view loader function
     */
    public function loadView($current_module, $current_view, $id = null)
    {
        // check if user is not authenticated redirect
        if(!Auth::check())
        {
            return redirect("/");
        }

        // params receiving from url dynamically
        $current_module = strtolower($current_module);
        $current_view   = strtolower($current_view);

        // check if dynamic view being accessed is exists
        if(view()->exists($current_module.".".$current_view))
        {
            return view
            (
                $current_module.'.'.$current_view,
                [
                    'current_module'    =>  $current_module,
                    'current_view'      =>  $current_view
                ]
            );
        }

        // display 404 if arrives here
        return view("404");
    }
}
