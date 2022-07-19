<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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

    /**
     * Login action function
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function do_login(Request $request)
    {
        // call auth
        $auth_result = Auth::attempt
        ([
            "email"    =>  $request->email,
            "password"  =>  $request->password
        ]);

        // when success
        if($auth_result)
        {
            return response()->json(["status"=>true,"message"=>"success","errors"=>[],"redirect"=>URL::to("/dashboard/view")]);
        }

        // when failed
        return response()->json(["status"=>false,"message"=>"Incorrect e-mail address or password!","errors"=>[],"redirect"=>""]);
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
     * This is our dynamic view loader function
     * @param $current_module
     * @param $current_view
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
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

        // data collector class check within module
        // since we are using dynamic view logic we need some data adapter which will be passed to view when the form is being edit and
        // this should be module based this is where DataCollector becomes handy
        $class  = 'ModuleDataCollector';
        $path   = base_path()."/resources/views/".$current_module."/dispatcher/".$class.".php";
        $linker = null;
        if(file_exists($path))
        {
            include_once $path;
            if(class_exists($class))
            {
                $class      = new $class();
                $linker     = $class->pre_process($id, $current_module, $current_view);
            }
        }

        // check if dynamic view being accessed is exists
        if(view()->exists($current_module.".".$current_view))
        {
            return view
            (
                $current_module.'.'.$current_view,
                [
                    'current_module'    =>  $current_module,
                    'current_view'      =>  $current_view,
                    'data'              =>  $linker // assume this has required data if no data it will null
                ]
            );
        }

        // display 404 if arrives here
        return view("404");
    }
}
