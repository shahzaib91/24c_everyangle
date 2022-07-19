<?php

namespace App\Http\Controllers;
use App\Models\Categories;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

/**
 * Class GetPostHandler
 * @package App\Http\Controllers
 * This class is responsible for handling get and post ajax requests of data
 */
class GetPostHandler extends Controller
{

    /**
     * FUNCTION EXECUTOR OF API
     * NOTE: Since these requests must be authenticated each request have to be execute after login
     */
    public function post(Request $request, $func)
    {
        // check user logged in
        if(!Auth::check()){
            return response()->json(['status'=>false, 'message'=>'Please login to perform this action!']);
        }

        // now check if method available
        if(!method_exists($this,$func)){
            return response()->json(['status'=>false, 'message'=>'Requested end-point is undefined!']);
        }

        // execute function
        return call_user_func_array([$this,$func],[$request]);
    }

    /**
     * @param null $func
     * @param null $id
     * @return \Illuminate\Http\JsonResponse|mixed
     * This function will be used for getting json data mainly post login
     */
    public function get($func = null, $id = null)
    {
        // check user logged in
        if(!Auth::check()){
            return response()->json(['status'=>false, 'message'=>'Please login to perform this action!']);
        }

        // now check if method available
        if(!method_exists($this,$func)){
            return response()->json(['status'=>false, 'message'=>'Requested end-point is undefined!']);
        }

        // execute function
        return call_user_func_array([$this,$func],[$id]);
    }
}
