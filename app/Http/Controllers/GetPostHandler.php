<?php

namespace App\Http\Controllers;
use App\Helper\Icon;
use App\Models\Categories;
use App\Models\MediaFiles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    /**
     * -----------------------------------------------
     * Category modules functions are written below
     * -----------------------------------------------
     */
    public function categoryEdit(Request $request)
    {
        // prepare validation rules and labels
        $rules['cat_name']              =   "required|max:191";
        $rules['id']                    =   "required|numeric";
        $attr['cat_name']               =   "Category Name";
        $attr['id']                     =   "Identity";

        // let's check whether data is being added or updated and delete id key from validation
        $isbeingupdate                  =   true;
        if($request->id=="")
        {
            unset($rules['id']);
            $isbeingupdate              =   false;
        }

        // run validation and grab errors
        $validateData = Validator::make($request->all(), $rules,[],$attr);// $this->validate($request, $rules, [], $attr);
        if($validateData->fails())
        {
            $errors = array();
            foreach($validateData->errors()->messages() as $field=>$eMsgs)
            {
                for($i=0; $i<count($eMsgs); $i++)
                {
                    $errors[] = $eMsgs[$i];
                }
            }
            // check if validation failed
            if($validateData)
            {
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"One or more validation error(s) occured!"]);
            }
        }

        // if arrived here means validation is passed let's create or update data accordingly
        if($isbeingupdate){
            Categories::__update($request);
        } else {
            Categories::__create($request);
        }

        // return final response
        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Record has been saved!", "redirect"=>""]);
    }

    public function categoryList()
    {
        // let's prepare datatable accepted format
        $data       = ["data"=>[]];

        // query
        $dt         = Categories::where('cat_owner',Auth::user()->id)->get();

        // loop through data
        for($i=0; $i<count($dt); $i++)
        {
            $a = 0;
            $data['data'][$i][$a] = $dt[$i]->id;
            $a++;
            $data['data'][$i][$a] = $dt[$i]->cat_name;
            $a++;
            $data['data'][$i][$a] = Carbon::parse($dt[$i]->created_at)->diffForHumans();
            $a++;
            $data['data'][$i][$a] = ($dt[$i]->assocItems ? number_format($dt[$i]->assocItems->total) : 0);
            $a++;
            $data['data'][$i][$a] = '<a title="Edit" data-id="'.$dt[$i]->id.'" data-name="'.$dt[$i]->cat_name.'" data-toggle="modal" data-target="#modal-edit" data-backdrop="static" data-keyboard="false" href="javascript:void(0)" class="btn-edit"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;';
            $data['data'][$i][$a] .= '<a title="Delete" data-post="getDataTableData" class="btn_remove" href="javascript:void(0)" data-href="'.URL::to('/api/categoryRemove/'.base64_encode($dt[$i]->id)).'"><i class="fa fa-trash-o fa-2x"></i></a>';
        }
        return response()->json($data);

    }

    public function categoryRemove($id)
    {
        $id = base64_decode($id);
        return response()->json(Categories::__delete($id));
    }


    /**
     * -----------------------------------------------
     * Category modules functions are written below
     * -----------------------------------------------
     */
    public function mediaEdit(Request $request)
    {
        // prepare validation rules and labels
        $rules['file_name']             =   "required|max:191";
        $rules['file_cat']              =   "required|numeric";
        $rules['id']                    =   "required|numeric";
        $attr['file_name']              =   "File Name";
        $attr['file_cat']               =   "Category";
        $attr['id']                     =   "Identity";

        // let's check whether data is being added or updated and delete id key from validation
        $isbeingupdate                  =   true;
        if($request->id=="")
        {
            unset($rules['id']);
            $isbeingupdate              =   false;
        }

        // run validation and grab errors
        $validateData = Validator::make($request->all(), $rules,[],$attr);// $this->validate($request, $rules, [], $attr);
        if($validateData->fails())
        {
            $errors = array();
            foreach($validateData->errors()->messages() as $field=>$eMsgs)
            {
                for($i=0; $i<count($eMsgs); $i++)
                {
                    $errors[] = $eMsgs[$i];
                }
            }
            // check if validation failed
            if($validateData)
            {
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"One or more validation error(s) occured!"]);
            }
        }

        // if arrived here means validation is passed let's create or update data accordingly
        if($isbeingupdate){
            MediaFiles::__update($request);
        } else {
            MediaFiles::__create($request);
        }

        // return final response
        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Record has been saved!", "redirect"=>""]);
    }

    public function mediaList()
    {
        // let's prepare datatable accepted format
        $data       = ["data"=>[], "json"=>[]];

        // query
        $dt         = MediaFiles::where('file_owner',Auth::user()->id)->get();

        // loop through data
        for($i=0; $i<count($dt); $i++)
        {
            // store plain object will be used as edit dataset in view
            $data['json'][$i] = $dt[$i];

            $a = 0;
            $data['data'][$i][$a] = $dt[$i]->id;
            $a++;
            $data['data'][$i][$a] = '<div class="text-center"><img src="'.Icon::get($dt[$i]->file_name).'" style="width:50px;" /><br/>'.$dt[$i]->file_name.'</div>';
            $a++;
            $data['data'][$i][$a] = Carbon::parse($dt[$i]->created_at)->diffForHumans();
            $a++;
            $data['data'][$i][$a] = ucwords($dt[$i]->category->cat_name);
            $a++;
            $data['data'][$i][$a] = '<a data-ind="'.$i.'" title="Edit" data-toggle="modal" data-target="#modal-edit" data-backdrop="static" data-keyboard="false" href="javascript:void(0)" class="btn-edit"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;';
            $data['data'][$i][$a] .= '<a title="Delete" data-post="getDataTableData" class="btn_remove" href="javascript:void(0)" data-href="'.URL::to('/api/mediaRemove/'.base64_encode($dt[$i]->id)).'"><i class="fa fa-trash-o fa-2x"></i></a>';
        }
        return response()->json($data);

    }

    public function mediaRemove($id)
    {
        $id = base64_decode($id);
        return response()->json(MediaFiles::__delete($id));
    }
}
