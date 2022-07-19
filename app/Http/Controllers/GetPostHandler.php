<?php

namespace App\Http\Controllers;
use App\Models\Categories;
use App\Models\Countries;
use App\Models\Products;
use App\Models\Roles;
use App\Models\Units;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class PostHandler extends Controller
{

    /**
     * FUNCTION EXECUTOR OF API
     * NOTE: Since these requests must be made by authorized personnel each request have to be execute after login
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
        return call_user_func_array(array($this,$func),[$request]);
    }

    public function get($func = null, $id = null, $ex = null)
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
        return call_user_func_array(array($this,$func),[$id, $ex]);
    }

    public function login(Request $request)
    {
        // fetch user details
        $user = User::where('email',$request->email) -> first();
        if($user!==null)
        {
            $auth_result = Auth::attempt
            ([
                "email"    =>  $request->email,
                "password"  =>  $request->password
            ]);

            if($auth_result)
            {
                $auth_result = Auth::attempt
                ([
                    "email"    =>  $request->email,
                    "password"  =>  $request->password,
                    "status"   =>  "e"
                ]);

                if($auth_result)
                {
                    $auth_role = Roles::find(Auth::user()->role_id);
                    if(!$auth_role)
                    {
                        return response()->json(["status"=>false,"message"=>"No role assigned to your account. Please contact system administrator.","errors"=>[],"redirect"=>""]);
                    }
                    else if($auth_role && isset($auth_role->status) && $auth_role->status!="e")
                    {
                        return response()->json(["status"=>false,"message"=>"Access denied! Either assigned role is disabled or trashed.","errors"=>[],"redirect"=>""]);
                    }
                    else
                    {
                        return response()->json(["status"=>true,"message"=>"success","errors"=>[],"redirect"=>URL::to("/dashboard/view")]);
                    }
                }
                else
                {
                    return response()->json(["status"=>false,"message"=>"Your account is disabled by administrator!","errors"=>[],"redirect"=>""]);
                }
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"Entered password is incorrect!","errors"=>[],"redirect"=>""]);

            }
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"Please enter valid e-mail address!","errors"=>[],"redirect"=>""]);
        }
    }


    /**
     * UNITS RELATED FUNCTIONS
     */
    public function unitsList()
    {
        $data       = ["data"=>[]];
        $q   = Units::where('status','<>','t')->get();
        $canEdit = true;
        $canDelete = true;

        for($i=0; $i<count($q); $i++)
        {
            $a = 0;
            $data['data'][$i][$a] = $q[$i]->id;
            $a++;
            $data['data'][$i][$a] = $q[$i]->unit_name;
            $a++;
            $data['data'][$i][$a] = ucwords($q[$i]->owner->name);
            $a++;
            $data['data'][$i][$a] = ($q[$i]->status=='e' ? '<span class="badge badge-success">Visible</span>' : '<span class="badge badge-warning">Hidden</span>');
            $a++;
            if($canEdit)
            {
                $data['data'][$i][$a] = '<a title="Edit" data-id="'.$q[$i]->id.'" data-name="'.$q[$i]->unit_name.'" data-status="'.$q[$i]->status.'" data-toggle="modal" data-target="#modal-unit-edit" data-backdrop="static" data-keyboard="false" href="javascript:void(0)" class="btn-edit-unit"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;';
            }
            if($canDelete)
            {
                $data['data'][$i][$a] .= '<a title="Delete" data-post="getDataTableData" class="btn_remove" href="javascript:void(0)" data-href="'.URL::to('get/api/unitRemove/'.base64_encode($q[$i]->id)).'"><i class="fa fa-trash-o fa-2x"></i></a>';
            }
            if(!isset($data['data'][$i][$a]) || $data['data'][$i][$a]=='')
            {
                $data['data'][$i][$a] = 'N/A';
            }
        }
        return response()->json($data);
    }

    public function unitEdit(Request $request)
    {
        // init validations attr
        $rules['unit_name']             =   "required|max:191";
        $rules['id']                    =   "required|max:191";

        $attr['unit_name']              =   "Name";
        $attr['id']                     =   "Unit Code";

        $isbeingupdate                  =   true;
        if($request->id=="")
        {
            unset($rules['id']);
            $isbeingupdate              =   false;
        }
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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights


        // if arrived here means validation check has passed do insert or update based on logic
        if(!$isbeingupdate)
        {
            Units::__create($request);
        }
        else
        {
            Units::__update($request);
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Data has been saved!", "redirect"=>""]);
    }

    public function unitRemove($id)
    {
        $id     = base64_decode($id);
        $data   = Units::find($id);
        if($data)
        {
            $data->status = 't';
            $data->save();
        }
        else
        {
            return response()->json(['status'=>false, 'message'=>'No record found!']);
        }
        return response()->json(['status'=>true, 'message'=>'Record has been trashed successfully!']);
    }

    /**
     * COUNTRIES RELATED FUNCTIONS
     */
    public function countriesList()
    {
        $data       = ["data"=>[]];
        $q   = Countries::where('status','<>','t')->get();
        $canEdit = true;
        $canDelete = true;

        for($i=0; $i<count($q); $i++)
        {
            $a = 0;
            $data['data'][$i][$a] = $q[$i]->id;
            $a++;
            $data['data'][$i][$a] = $q[$i]->country_name;
            $a++;
            $data['data'][$i][$a] = ucwords($q[$i]->owner->name);
            $a++;
            $data['data'][$i][$a] = ($q[$i]->status=='e' ? '<span class="badge badge-success">Visible</span>' : '<span class="badge badge-warning">Hidden</span>');
            $a++;
            if($canEdit)
            {
                $data['data'][$i][$a] = '<a title="Edit" data-id="'.$q[$i]->id.'" data-name="'.$q[$i]->country_name.'" data-status="'.$q[$i]->status.'" data-toggle="modal" data-target="#modal-unit-edit" data-backdrop="static" data-keyboard="false" href="javascript:void(0)" class="btn-edit-unit"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;';
            }
            if($canDelete)
            {
                $data['data'][$i][$a] .= '<a title="Delete" data-post="getDataTableData" class="btn_remove" href="javascript:void(0)" data-href="'.URL::to('get/api/countryRemove/'.base64_encode($q[$i]->id)).'"><i class="fa fa-trash-o fa-2x"></i></a>';
            }
            if(!isset($data['data'][$i][$a]) || $data['data'][$i][$a]=='')
            {
                $data['data'][$i][$a] = 'N/A';
            }
        }
        return response()->json($data);
    }

    public function countryEdit(Request $request)
    {
        // init validations attr
        $rules['country_name']          =   "required|max:191";
        $rules['id']                    =   "required|max:191";

        $attr['country_name']           =   "Name";
        $attr['id']                     =   "Unit Code";

        $isbeingupdate                  =   true;
        if($request->id=="")
        {
            unset($rules['id']);
            $isbeingupdate              =   false;
        }
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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights


        // if arrived here means validation check has passed do insert or update based on logic
        if(!$isbeingupdate)
        {
            Countries::__create($request);
        }
        else
        {
            Countries::__update($request);
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Data has been saved!", "redirect"=>""]);
    }

    public function countryRemove($id)
    {
        $id     = base64_decode($id);
        $data   = Countries::find($id);
        if($data)
        {
            $data->status = 't';
            $data->save();
        }
        else
        {
            return response()->json(['status'=>false, 'message'=>'No record found!']);
        }
        return response()->json(['status'=>true, 'message'=>'Record has been trashed successfully!']);
    }


    /**
     * CATEGORIES RELATED FUNCTIONS
     */
    public function categoriesList()
    {
        $data       = ["data"=>[]];
        $q   = Categories::where('status','<>','t')->get();
        $canEdit = true;
        $canDelete = true;

        for($i=0; $i<count($q); $i++)
        {
            $a = 0;
            $data['data'][$i][$a] = $q[$i]->id;
            $a++;
            $data['data'][$i][$a] = $q[$i]->category_name;
            $a++;
            $data['data'][$i][$a] = ($q[$i]->category_parent!=0 ? $q[$i]->parent_cat->category_name : "-");
            $a++;
            $data['data'][$i][$a] = ucwords($q[$i]->owner->name);
            $a++;
            $data['data'][$i][$a] = ($q[$i]->status=='e' ? '<span class="badge badge-success">Visible</span>' : '<span class="badge badge-warning">Hidden</span>');
            $a++;
            if($canEdit)
            {
                $data['data'][$i][$a] = '<a title="Edit" data-id="'.$q[$i]->id.'" data-name="'.$q[$i]->category_name.'" data-parent="'.$q[$i]->category_parent.'" data-status="'.$q[$i]->status.'" data-toggle="modal" data-target="#modal-unit-edit" data-backdrop="static" data-keyboard="false" href="javascript:void(0)" class="btn-edit-unit"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;';
            }
            if($canDelete)
            {
                $data['data'][$i][$a] .= '<a title="Delete" data-post="getDataTableData" class="btn_remove" href="javascript:void(0)" data-href="'.URL::to('get/api/categoryRemove/'.base64_encode($q[$i]->id)).'"><i class="fa fa-trash-o fa-2x"></i></a>';
            }
            if(!isset($data['data'][$i][$a]) || $data['data'][$i][$a]=='')
            {
                $data['data'][$i][$a] = 'N/A';
            }
        }
        return response()->json($data);
    }

    public function categoryEdit(Request $request)
    {
        // init validations attr
        $rules['category_name']         =   "required|max:191";
        $rules['id']                    =   "required|max:191";

        $attr['category_name']          =   "Name";
        $attr['id']                     =   "Unit Code";

        $isbeingupdate                  =   true;
        if($request->id=="")
        {
            unset($rules['id']);
            $isbeingupdate              =   false;
        }
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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights


        // if arrived here means validation check has passed do insert or update based on logic
        if(!$isbeingupdate)
        {
            Categories::__create($request);
        }
        else
        {
            Categories::__update($request);
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Data has been saved!", "redirect"=>""]);
    }

    public function categoryRemove($id)
    {
        $id     = base64_decode($id);

        $childs = Categories::where('category_parent',$id)->get();
        if(count($childs)>0)
        {
            return response()->json(['status'=>false, 'message'=>'This category can\'t be delete as records are associated, consider hiding this.']);
        }

        $data   = Categories::find($id);
        if($data)
        {
            $data->status = 't';
            $data->save();
        }
        else
        {
            return response()->json(['status'=>false, 'message'=>'No record found!']);
        }
        return response()->json(['status'=>true, 'message'=>'Record has been trashed successfully!']);
    }


    /**
     * PRODUCTS RELATED FUNCTIONS
     */
    public function productsList()
    {
        $data       = ["data"=>[]];
        $q   = Products::where('status','<>','t')->get();
        $canEdit = true;
        $canDelete = true;

        for($i=0; $i<count($q); $i++)
        {
            $a = 0;
            $data['data'][$i][$a] = $q[$i]->product_code;
            $a++;
            $data['data'][$i][$a] = $q[$i]->title;
            $a++;
            $data['data'][$i][$a] = $q[$i]->modelno;
            $a++;
            $data['data'][$i][$a] = $q[$i]->unitrate;
            $a++;
            $data['data'][$i][$a] = ucwords($q[$i]->owner->name);
            $a++;
            $data['data'][$i][$a] = ($q[$i]->status=='e' ? '<span class="badge badge-success">Visible</span>' : '<span class="badge badge-warning">Hidden</span>');
            $a++;


            $data['data'][$i][$a] = '<a title="View" href="'.URL::to('products/view/'.$q[$i]->id).'"><i class="fa fa-eye fa-2x"></i></a>&nbsp;&nbsp;';
            if($canEdit)
            {
                $data['data'][$i][$a] = '<a title="Edit" href="'.URL::to('products/view/'.$q[$i]->id).'"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;';
            }
            if($canDelete)
            {
                $data['data'][$i][$a] .= '<a title="Delete" data-post="getDataTableData" class="btn_remove" href="javascript:void(0)" data-href="'.URL::to('get/api/productRemove/'.base64_encode($q[$i]->id)).'"><i class="fa fa-trash-o fa-2x"></i></a>';
            }
            if(!isset($data['data'][$i][$a]) || $data['data'][$i][$a]=='')
            {
                $data['data'][$i][$a] .= 'N/A';
            }
        }
        return response()->json($data);
    }

    public function productEdit(Request $request)
    {
        // init validations attr
        $rules['category_name']         =   "required|max:191";
        $rules['id']                    =   "required|max:191";

        $attr['category_name']          =   "Name";
        $attr['id']                     =   "Unit Code";

        $isbeingupdate                  =   true;
        if($request->id=="")
        {
            unset($rules['id']);
            $isbeingupdate              =   false;
        }
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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights


        // if arrived here means validation check has passed do insert or update based on logic
        if(!$isbeingupdate)
        {
            Categories::__create($request);
        }
        else
        {
            Categories::__update($request);
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Data has been saved!", "redirect"=>""]);
    }

    public function productRemove($id)
    {
        $id     = base64_decode($id);

        $childs = Categories::where('category_parent',$id)->get();
        if(count($childs)>0)
        {
            return response()->json(['status'=>false, 'message'=>'This category can\'t be delete as records are associated, consider hiding this.']);
        }

        $data   = Categories::find($id);
        if($data)
        {
            $data->status = 't';
            $data->save();
        }
        else
        {
            return response()->json(['status'=>false, 'message'=>'No record found!']);
        }
        return response()->json(['status'=>true, 'message'=>'Record has been trashed successfully!']);
    }


















    /**
     * USERS RELATED FUNCTIONS
     */
    public function usersList()
    {
        $data       = ["data"=>[]];
        $fdata      = User::where('createdby','<>',0)->where('status','<>','trash')->get();
        $helper     = new \App\ILP\DBMSModuleHelper();
        if($helper->forceSelfCheck())
        {
            $fdata   = User::where('createdby',Auth::user()->id)->where('status','<>','trash')->get();
        }
        $canEdit    = false;
        $canDelete  = false;
        if($helper->thisUserCan("Users","edit"))
        {
            $canEdit = true;
        }
        if($helper->thisUserCan("Users","delete"))
        {
            $canDelete = true;
        }



        for($i=0; $i<count($fdata); $i++)
        {

            $data['data'][$i][0] = $fdata[$i]->name;
            $data['data'][$i][1] = $fdata[$i]->email;
            $data['data'][$i][2] = $fdata[$i]->mobile;
            $data['data'][$i][3] = $fdata[$i]->country.'/'.$fdata[$i]->relcity->label;
            $data['data'][$i][4] = $fdata[$i]->relarea->label;
            $data['data'][$i][5] = $fdata[$i]->role->name;
            $data['data'][$i][6] = $fdata[$i]->author->name;
            $data['data'][$i][7] = ($fdata[$i]->status=='active' ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Blocked</span>');
            if($canEdit)
            {
                $data['data'][$i][8] = '<a href="'.URL::to('users/edit/'.$fdata[$i]->id).'"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;';
            }
            if($canDelete)
            {
                $data['data'][$i][8] .= '<a data-post="getDataTableData" class="btn_remove" href="javascript:void(0)" data-href="'.URL::to('get/api/userRemove/'.base64_encode($fdata[$i]->id)).'"><i class="fa fa-trash-o fa-2x"></i></a>';
            }
            if(!isset($data['data'][$i][8]) || $data['data'][$i][8]=='')
            {
                $data['data'][$i][8] = 'N/A';
            }
        }
        return response()->json($data);
    }

    public function userStock(Request $request)
    {
        $data       = ["data"=>[]];
        $fdata      = TempStock::where('ts_spid','=',$request->uid)->where('ts_revert_by','=',0)->where('ts_qty','>',0)->get();
        $helper     = new \App\ILP\DBMSModuleHelper();
        if(!$helper->thisUserCan("Warehouse","create"))
        {
            return response()->json($data);
        }

        for($i=0; $i<count($fdata); $i++)
        {
            $a = 0;
            $data['data'][$i][$a] = $fdata[$i]->product->name;
            $a++;

            $data['data'][$i][$a] = number_format($fdata[$i]->ts_qty);
            $a++;

            $data['data'][$i][$a] = $fdata[$i]->assigned->name;
            $a++;

            $revert = ($fdata[$i]->ts_revert_by==0 ? "-" : $fdata[$i]->reverted->name);
            $data['data'][$i][$a] = $revert;
            $a++;
        }
        return response()->json($data);
    }

    public function userStockRevert(Request $request)
    {
        // init validations attr
        $rules['uid']                  =   "required|numeric";
        $attr['uid']                   =   "User";
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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights
        $helper = new \App\ILP\DBMSModuleHelper();
        if(!$helper->thisUserCan('Warehouse','create'))
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        // fetch data
        $fdata      = TempStock::where('ts_spid','=',$request->uid)->where('ts_revert_by','=',0)->where('ts_qty','>',0)->get();
        foreach($fdata as $row)
        {
            $p = Products::find($row->ts_pid);
            if($p)
            {
                $p->stock = $p->stock + $row->ts_qty;
                $p->save();
            }

            $s = TempStock::find($row->ts_id);
            $s->ts_revert_by = Auth::user()->id;
            $s->save();
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Re Stock has been completed successfully!", "redirect"=>""]);
    }

    public function usersEdit(Request $request)
    {
        // init validations attr
        $rules['name']                  =   "required|max:191";
        $rules['email']                 =   "required|email|unique:users,email";
        $rules['password']              =   "required|min:6|max:12";
        $rules['mobile']                =   "required|max:11";
        $rules['country']               =   "required|max:191";
        $rules['city']                  =   "required|max:191";
        $rules['area']                  =   "required|max:191";
        $rules['roleid']                =   "required|integer";
        $rules['status']                =   "required";

        $attr['name']                   =   "Name";
        $attr['email']                  =   "E-mail";
        $attr['password']               =   "Password";
        $attr['mobile']                 =   "Mobile";
        $attr['country']                =   "Country";
        $attr['city']                   =   "City";
        $attr['area']                   =   "Area";
        $attr['roleid']                 =   "Role";
        $attr['status']                 =   "Status";

        $isbeingupdate                  =   false;
        if($request->id!="")
        {
            $isbeingupdate              =   true;

            // validate old and new password logic
            if($request->password=="")
            {
                unset($rules['password']);
            }
            unset($rules['email']);
        }
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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights
        $helper = new \App\ILP\DBMSModuleHelper();
        if(!$helper->thisUserCan('Users','create') && !$isbeingupdate)
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        if(!$helper->thisUserCan('Users','edit') && $isbeingupdate)
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        // if arrived here means validation check has passed do insert or update based on logic
        if(!$isbeingupdate)
        {
            User::__create($request);
        }
        else
        {
            User::__update($request);
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Data has been saved!", "redirect"=>""]);
    }

    public function userRemove($id)
    {
        $helper = new \App\ILP\DBMSModuleHelper();
        if($helper->thisUserCan('Users','delete'))
        {
            $id     = base64_decode($id);
            $data   = User::find($id);
            if($data)
            {
                $data->status = 'trash';
                $data->save();
            }
            else
            {
                return response()->json(['status'=>false, 'message'=>'No record found!']);
            }
            return response()->json(['status'=>true, 'message'=>'']);
        }
        else
        {
            return response()->json(['status'=>false, 'message'=>'You are not authorized to perform this action!']);
        }
    }





    /**
     * ROLES RELATED FUNCTIONS
     */
    public function rolesList()
    {
        $data       = ["data"=>[]];
        $fdata      = Role::where('id','<>',1)->get();
        $helper     = new \App\ILP\DBMSModuleHelper();
        if($helper->forceSelfCheck())
        {
            $fdata   = Role::where('id','<>',1)->where('createdby',Auth::user()->id)->get();
        }
        $canEdit    = false;
        $canDelete  = false;
        if($helper->thisUserCan("Roles","edit"))
        {
            $canEdit = true;
        }

        for($i=0; $i<count($fdata); $i++)
        {
            $data['data'][$i][0] = $fdata[$i]->name;
            $data['data'][$i][1] = $fdata[$i]->author->name;
            $data['data'][$i][2] = ($fdata[$i]->status=='active' ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Disabled</span>');
            if($canEdit)
            {
                $data['data'][$i][3] = '<a href="'.URL::to('roles/edit/'.$fdata[$i]->id).'"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;';
            }
            if(!isset($data['data'][$i][3]) || $data['data'][$i][3]=='')
            {
                $data['data'][$i][3] = 'N/A';
            }
        }
        return response()->json($data);
    }

    public function rolesEdit(Request $request)
    {
        // init validations attr
        $rules['name']                  =   "required|max:191";
        $rules['status']                =   "required";
        $rules['permissions_schema']    =   "required";

        $attr['name']                   =   "Name";
        $attr['permissions_schema']     =   "Permissions";
        $attr['status']                 =   "Status";

        $isbeingupdate                  =   false;
        if($request->id!="")
        {
            $isbeingupdate              =   true;
        }
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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights
        $helper = new \App\ILP\DBMSModuleHelper();
        if(!$helper->thisUserCan('Roles','create') && !$isbeingupdate)
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        if(!$helper->thisUserCan('Roles','edit') && $isbeingupdate)
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        // if arrived here means validation check has passed do insert or update based on logic
        if(!$isbeingupdate)
        {
            Role::__create($request);
        }
        else
        {
            Role::__update($request);
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Data has been saved!", "redirect"=>""]);
    }





    /**
     * CITIES RELATED FUNCTIONS
     */
    public function citiesList()
    {
        $data       = ["data"=>[]];
        $fdata      = Cities::where('status','<>','trash')->get();
        $helper     = new \App\ILP\DBMSModuleHelper();
        if($helper->forceSelfCheck())
        {
            $fdata   = Cities::where('status','<>','trash')->where('createdby',Auth::user()->id)->get();
        }
        $canEdit    = false;
        $canDelete  = false;
        if($helper->thisUserCan("Cities","edit"))
        {
            $canEdit = true;
        }

        if($helper->thisUserCan("Cities","delete"))
        {
            $canDelete = true;
        }

        for($i=0; $i<count($fdata); $i++)
        {
            $data['data'][$i][0] = $fdata[$i]->country;
            $data['data'][$i][1] = $fdata[$i]->label;
            $data['data'][$i][2] = $fdata[$i]->author->name;
            $data['data'][$i][3] = ($fdata[$i]->status=='active' ? '<span class="badge badge-success">Visible</span>' : '<span class="badge badge-danger">Hidden</span>');

            if($canEdit)
            {
                $data['data'][$i][4] = '<a href="'.URL::to('cities/edit/'.$fdata[$i]->cityid).'"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;';
            }
            if($canDelete)
            {
                $data['data'][$i][4] .= '<a data-post="getDataTableData" class="btn_remove" href="javascript:void(0)" data-href="'.URL::to('get/api/cityRemove/'.base64_encode($fdata[$i]->cityid)).'"><i class="fa fa-trash-o fa-2x"></i></a>';
            }
            if(!isset($data['data'][$i][4]) || $data['data'][$i][4]=='')
            {
                $data['data'][$i][4] = 'N/A';
            }
        }
        return response()->json($data);
    }

    public function cityEdit(Request $request)
    {
        // init validations attr
        $rules['country']               =   "required|max:191";
        $rules['label']                 =   "required|max:191";

        $attr['country']                =   "Country";
        $attr['label']                  =   "City";
        $isbeingupdate                  =   false;
        if($request->id!="")
        {
            $isbeingupdate              =   true;
        }
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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights
        $helper = new \App\ILP\DBMSModuleHelper();
        if(!$helper->thisUserCan('Cities','create') && !$isbeingupdate)
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        if(!$helper->thisUserCan('Cities','edit') && $isbeingupdate)
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        // if arrived here means validation check has passed do insert or update based on logic
        if(!$isbeingupdate)
        {
            Cities::__create($request);
        }
        else
        {
            Cities::__update($request);
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Data has been saved!", "redirect"=>""]);
    }

    public function cityRemove($id)
    {
        $helper = new \App\ILP\DBMSModuleHelper();
        if($helper->thisUserCan('Cities','delete'))
        {
            $id     = base64_decode($id);
            $data   = Cities::find($id);
            if($data)
            {
                $data->status = 'trash';
                $data->save();
            }
            else
            {
                return response()->json(['status'=>false, 'message'=>'No record found!']);
            }
            return response()->json(['status'=>true, 'message'=>'']);
        }
        else
        {
            return response()->json(['status'=>false, 'message'=>'You are not authorized to perform this action!']);
        }
    }





    /**
     * AREAS RELATED FUNCTIONS
     */
    public function areasList()
    {
        $data       = ["data"=>[]];
        $fdata      = Areas::where('status','<>','trash')->get();
        $helper     = new \App\ILP\DBMSModuleHelper();
        if($helper->forceSelfCheck())
        {
            $fdata   = Areas::where('status','<>','trash')->where('createdby',Auth::user()->id)->get();
        }
        $canEdit    = false;
        $canDelete  = false;
        if($helper->thisUserCan("Areas","edit"))
        {
            $canEdit = true;
        }

        if($helper->thisUserCan("Areas","delete"))
        {
            $canDelete = true;
        }

        for($i=0; $i<count($fdata); $i++)
        {
            $data['data'][$i][0] = $fdata[$i]->city->country;
            $data['data'][$i][1] = $fdata[$i]->city->label;
            $data['data'][$i][2] = $fdata[$i]->label;
            $data['data'][$i][3] = $fdata[$i]->author->name;
            $data['data'][$i][4] = ($fdata[$i]->status=='active' ? '<span class="badge badge-success">Visible</span>' : '<span class="badge badge-danger">Hidden</span>');

            if($canEdit)
            {
                $data['data'][$i][5] = '<a href="'.URL::to('areas/edit/'.$fdata[$i]->areaid).'"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;';
            }
            if($canDelete)
            {
                $data['data'][$i][5] .= '<a data-post="getDataTableData" class="btn_remove" href="javascript:void(0)" data-href="'.URL::to('get/api/areaRemove/'.base64_encode($fdata[$i]->areaid)).'"><i class="fa fa-trash-o fa-2x"></i></a>';
            }
            if(!isset($data['data'][$i][5]) || $data['data'][$i][5]=='')
            {
                $data['data'][$i][5] = 'N/A';
            }
        }
        return response()->json($data);
    }

    public function areaEdit(Request $request)
    {
        // init validations attr
        $rules['country']               =   "required|max:191";
        $rules['cityid']                =   "required|max:191";
        $rules['label']                 =   "required|max:191";

        $attr['country']                =   "Country";
        $attr['label']                  =   "Area";
        $attr['cityid']                 =   "City";
        $isbeingupdate                  =   false;
        if($request->id!="")
        {
            $isbeingupdate              =   true;
        }
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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights
        $helper = new \App\ILP\DBMSModuleHelper();
        if(!$helper->thisUserCan('Areas','create') && !$isbeingupdate)
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        if(!$helper->thisUserCan('Areas','edit') && $isbeingupdate)
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        // if arrived here means validation check has passed do insert or update based on logic
        if(!$isbeingupdate)
        {
            Areas::__create($request);
        }
        else
        {
            Areas::__update($request);
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Data has been saved!", "redirect"=>""]);
    }

    public function areaRemove($id)
    {
        $helper = new \App\ILP\DBMSModuleHelper();
        if($helper->thisUserCan('Areas','delete'))
        {
            $id     = base64_decode($id);
            $data   = Areas::find($id);
            if($data)
            {
                $data->status = 'trash';
                $data->save();
            }
            else
            {
                return response()->json(['status'=>false, 'message'=>'No record found!']);
            }
            return response()->json(['status'=>true, 'message'=>'']);
        }
        else
        {
            return response()->json(['status'=>false, 'message'=>'You are not authorized to perform this action!']);
        }
    }





    /**
     * SHOPS RELATED FUNCTIONS
     */
    public function shopsList()
    {
        $data       = ["data"=>[]];
        $fdata      = Shops::all();
        $helper     = new \App\ILP\DBMSModuleHelper();
        if($helper->forceSelfCheck())
        {
            $fdata   = Shops::where('createdby',Auth::user()->id)->get();
        }
        $canEdit    = false;
        $canDelete  = false;
        if($helper->thisUserCan("Shops","edit"))
        {
            $canEdit = true;
        }

        for($i=0; $i<count($fdata); $i++)
        {
            $data['data'][$i][0] = "&nbsp;&nbsp;".$fdata[$i]->name;
            $data['data'][$i][1] = $fdata[$i]->poc_name;
            $data['data'][$i][2] = '<img class="lazyload" src="'.URL::to('public/uploads/thumbs/thumb_'.$fdata[$i]->image).'" style="max-width:100px;" />';
            $data['data'][$i][3] = $fdata[$i]->poc_mobile;
            $data['data'][$i][4] = $fdata[$i]->country;
            $data['data'][$i][5] = $fdata[$i]->relcity->label;
            $data['data'][$i][6] = $fdata[$i]->relarea->label;
            $data['data'][$i][7] = $fdata[$i]->address;
            $data['data'][$i][8] = $fdata[$i]->author->name;
            $data['data'][$i][9] = '<a target="_blank" href="https://maps.google.com/maps?q='.$fdata[$i]->latlng.'&hl=es;z=14&amp;output=embed"><i class="fa fa-map-marker fa-2x"></i></a>&nbsp;&nbsp;';
            if($canEdit)
            {
                $data['data'][$i][9] .= '<a href="'.URL::to('shops/edit/'.$fdata[$i]->id).'"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;';
            }
            if(!isset($data['data'][$i][9]) || $data['data'][$i][9]=='')
            {
                $data['data'][$i][9] = 'N/A';
            }
        }
        return response()->json($data);
    }

    public function shopEdit(Request $request)
    {
        // init validations attr
        $rules['name']                  =   "required|max:191";
        $rules['poc_name']              =   "required|max:191";
        $rules['poc_mobile']            =   "required|max:11";
        $rules['country']               =   "required|max:191";
        $rules['city']                  =   "required|max:191";
        $rules['area']                  =   "required|max:191";
        $rules['address']               =   "required|max:191";
        $rules['file']                  =   "required|max:10000|mimes:jpg,jpeg,png";
        $rules['latlng']                =   "required|max:191";

        $attr['name']                   =   "Name";
        $attr['poc_name']               =   "Person Name";
        $attr['poc_mobile']             =   "Person Mobile";
        $attr['country']                =   "Country";
        $attr['city']                   =   "City";
        $attr['area']                   =   "Area";
        $attr['address']                =   "Address";
        $attr['file']                   =   "Shop Image";
        $attr['latlng']                 =   "Location";

        $isbeingupdate                  =   false;
        if($request->id!="")
        {
            $isbeingupdate              =   true;
        }
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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights
        $helper = new \App\ILP\DBMSModuleHelper();
        if(!$helper->thisUserCan('Shops','create') && !$isbeingupdate)
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        if(!$helper->thisUserCan('Shops','edit') && $isbeingupdate)
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        // if arrived here means validation check has passed do insert or update based on logic
        if(!$isbeingupdate)
        {
            Shops::__create($request);
        }
        else
        {
            Shops::__update($request);
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Data has been saved!", "redirect"=>""]);
    }





    /**
     * ORDERS RELATED FUNCTIONS
     */
    public function ordersList()
    {
        $data       = ["data"=>[]];
        $fdata      = Orders::all();

        // check rights
        $helper     = new \App\ILP\DBMSModuleHelper();
        if($helper->forceSelfCheck())
        {
            $fdata   = Orders::where('createdby',Auth::user()->id)->get();
        }
        if(!$helper->thisUserCan('sales','view'))
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        $can_cancel = $helper->thisUserCan("Sales","delete");

        for($i=0; $i<count($fdata); $i++)
        {
            $data['data'][$i][0] = "&nbsp;&nbsp;".$fdata[$i]->id;
            $data['data'][$i][1] = $fdata[$i]->shop->name;
            $saleType = "Cash";
            if($fdata[$i]->sale_type=="pdc")
            {
                $saleType = "Cheque<br/>".$fdata[$i]->chequeno;
            }
            else if($fdata[$i]->sale_type=="credit")
            {
                $saleType = $fdata[$i]->credit_days." Day Credit";
            }
            $data['data'][$i][2] = $saleType;


            $status = '<span class="badge badge-danger">Pending Payment</span>';
            if($fdata[$i]->status=="cash-in-hand")
            {
                $status = '<span class="badge badge-warning">Cash Received</span>';
            }
            else if($fdata[$i]->status=="cheque-in-hand")
            {
                $status = '<span class="badge badge-warning">Cheque Received</span>';
            }
            else if($fdata[$i]->status=="cheque-deposited")
            {
                $status = '<span class="badge badge-info">Cheque Deposited</span>';
            }
            else if($fdata[$i]->status=="completed")
            {
                $status = '<span class="badge badge-success">Completed</span>';
            }

            $data['data'][$i][3] = $status;
            $data['data'][$i][4] = $fdata[$i]->shop->country.', '.$fdata[$i]->shop->relcity->label.',<br/>'.$fdata[$i]->shop->relarea->label.',<br/>'.$fdata[$i]->shop->address;
            $data['data'][$i][5] = $fdata[$i]->author->name;

            $data['data'][$i][6] = $fdata[$i]->created_at.'<br/><small>'.$fdata[$i]->created_at->diffForHumans().'</small>';

            $data['data'][$i][7] = $fdata[$i]->updated_at.'<br/><small>'.$fdata[$i]->updated_at->diffForHumans().'</small>';


            $isPrint = false;
            if($helper->thisUserCan('sales','view') || $helper->thisUserCan('epos','view'))
            {
                $isPrint = true;
                $data['data'][$i][8] = '<a target="_blank" href="'.URL::to('/print/'.base64_encode($fdata[$i]->id)).'"><i class="fa fa-print"></i> Print</a>&nbsp;&nbsp;';
            }

            if
            (
                ($can_cancel && ($fdata[$i]->status=="pending-payment" || $fdata[$i]->status=="cash-in-hand" || $fdata[$i]->status=="cheque-in-hand")) ||
                (Auth::user()->id==1)
            )
            {
                if ($isPrint)
                {
                    $data['data'][$i][8] .= '<a data-post="getDataTableData" class="btn_remove" href="javascript:void(0)" data-href="'.URL::to('get/api/orderRemove/'.base64_encode($fdata[$i]->id.':'.$fdata[$i]->createdby)).'"><i class="fa fa-window-close"></i> Delete Order</a>';
                }
                else
                {
                    $data['data'][$i][8] = '<a data-post="getDataTableData" class="btn_remove" href="javascript:void(0)" data-href="'.URL::to('get/api/orderRemove/'.base64_encode($fdata[$i]->id.':'.$fdata[$i]->createdby)).'"><i class="fa fa-window-close"></i> Delete Order</a>';
                }
            }
            else
            {
                $data['data'][$i][8] = 'N/A';
            }
        }
        return response()->json($data);
    }

    public function pendingCreditOrders()
    {
        $data       = ["data"=>[]];
        $helper     = new \App\ILP\DBMSModuleHelper();
        if(!$helper->thisUserCan('sales','view'))
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        $min_dt = today()->addDays(-2)->format('Y-m-d');
        $max_dt = today()->addDays(2)->format('Y-m-d');
        $fdata  =   \App\Orders::where('due_at','<=',$max_dt)
            ->where('sale_type','credit')
            ->where('status','pending-payment')
            ->where('createdby',Auth::user()->id)
            ->orderby('shopid')
            ->orderby('due_at')
            // ->limit(30)
            ->get();

        for($i=0; $i<count($fdata); $i++)
        {
            $data['data'][$i][0] = "&nbsp;&nbsp;".$fdata[$i]->id;
            $data['data'][$i][1] = number_format($fdata[$i]->orderTotal($fdata[$i]->id)->tot, 2);
            $data['data'][$i][2] = $fdata[$i]->shop->name.'<br/>'.$fdata[$i]->shop->country.', '.$fdata[$i]->shop->relcity->label.',<br/>'.$fdata[$i]->shop->relarea->label.',<br/>'.$fdata[$i]->shop->address;
            $data['data'][$i][3] = $fdata[$i]->shop->poc_name.'<br/><a href="tel:'.$fdata[$i]->shop->poc_mobile.'">'.$fdata[$i]->shop->poc_mobile.'</a>';
            $data['data'][$i][4] = '<a class="btn btn-success btn-payment-received" href="javacript:void(0)" data-href="'.URL::to('get/api/paymentReceived/'.base64_encode($fdata[$i]->id)).'"><i class="fa fa-check"></i></a>';

        }
        return response()->json($data);
    }

    public function getCollectionDetails($what)
    {
        $data       = ["data"=>[]];
        $helper     = new \App\ILP\DBMSModuleHelper();
        if(!$helper->thisUserCan('sales','view'))
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        // start performed shops counter
        /*$a = DB::table('users AS u')
            ->join('orders AS o','o.createdby','=','u.id')
            ->join('order_metas AS om','o.id','=','om.order_id')
            ->where('o.status','cash-in-hand')
            ->where('o.sale_type','cash')
            ->select(DB::raw('u.id, u.name, u.mobile, SUM(om.qty * om.sale_price) As cash, 0 As cheque, SUM(om.qty * om.sale_price) As total'))
            ->groupby('o.id');

        $b = DB::table('users AS u')
            ->join('orders AS o','o.createdby','=','u.id')
            ->join('order_metas AS om','o.id','=','om.order_id')
            ->where('o.status','cheque-in-hand')
            ->where('o.sale_type','pdc')
            ->select(DB::raw('u.id, u.name, u.mobile, 0 As cash, chequeno As cheque, SUM(om.qty * om.sale_price) As total'))
            ->groupby('o.id');
        $c = $a->union($b);

        $fdata = DB::query()
            ->fromSub($c,'a')
            ->select(DB::raw("a.id, a.name, a.mobile, SUM(a.cash) As cash, count(CASE WHEN a.cheque!=0 THEN a.cheque END) As cheque, SUM(a.total) As total"))
            ->groupby('a.id')
            ->get();

        for($i=0; $i<count($fdata); $i++)
        {
            $data['data'][$i][0] = "&nbsp;&nbsp;".$fdata[$i]->id;
            $data['data'][$i][1] = $fdata[$i]->name.'<br/><a href="tel:'.$fdata[$i]->mobile.'">'.$fdata[$i]->mobile.'</a>';
            $data['data'][$i][2] = number_format($fdata[$i]->cheque);
            $data['data'][$i][3] = 'Rs. '.number_format($fdata[$i]->cash,2);
            $data['data'][$i][4] = 'Rs. '.number_format($fdata[$i]->total,2);
            $data['data'][$i][5] = '<a class="btn btn-success btn-payment-received" href="javacript:void(0)" data-href="'.URL::to('get/api/paymentReceived/'.base64_encode($fdata[$i]->id)).'"><i class="fa fa-check"></i></a>';

        }*/

        $fdata = DB::table('users AS u')
            ->join('orders AS o','o.createdby','=','u.id')
            ->join('order_metas AS om','o.id','=','om.order_id')
            ->whereIn('o.status',['cash-in-hand','cheque-in-hand'])
            ->whereIn('o.sale_type',['cash','pdc','credit'])
            ->select(DB::raw('u.id, o.status as order_status, o.id as order_id, u.name, u.mobile, SUM(om.qty * om.sale_price) As amount, o.sale_type, o.chequeno'))
            ->groupby('o.id')
            ->orderby('u.id')
            ->get();

        if($what=="cheque-in-finance")
        {
            $fdata = DB::table('users AS u')
                ->join('orders AS o','o.createdby','=','u.id')
                ->join('order_metas AS om','o.id','=','om.order_id')
                ->whereIn('o.status',['cheque-in-finance'])
                ->whereIn('o.sale_type',['pdc'])
                ->select(DB::raw('u.id, o.status as order_status, o.id as order_id, u.name, u.mobile, SUM(om.qty * om.sale_price) As amount, o.sale_type, o.chequeno'))
                ->groupby('o.id')
                ->orderby('u.id')
                ->get();
        }
        else if($what=="cheque-deposited")
        {
            $fdata = DB::table('users AS u')
                ->join('orders AS o','o.createdby','=','u.id')
                ->join('order_metas AS om','o.id','=','om.order_id')
                ->whereIn('o.status',['cheque-deposited'])
                ->whereIn('o.sale_type',['pdc'])
                ->select(DB::raw('u.id, o.status as order_status, o.id as order_id, u.name, u.mobile, SUM(om.qty * om.sale_price) As amount, o.sale_type, o.chequeno'))
                ->groupby('o.id')
                ->orderby('u.id')
                ->get();
        }

        for($i=0; $i<count($fdata); $i++)
        {
            $data['data'][$i][0] = "&nbsp;&nbsp;".$fdata[$i]->id;
            $data['data'][$i][1] = $fdata[$i]->name.'<br/><a href="tel:'.$fdata[$i]->mobile.'">'.$fdata[$i]->mobile.'</a>';
            $data['data'][$i][2] = 'Rs. '.number_format($fdata[$i]->amount,2);
            $data['data'][$i][3] = strtoupper($fdata[$i]->sale_type);
            $data['data'][$i][4] = ($fdata[$i]->chequeno!="" ? $fdata[$i]->chequeno : "N/A");
            $data['data'][$i][5] = '<a class="btn btn-success btn-payment-received" href="javacript:void(0)" data-href="'.URL::to('get/api/paymentReceived/'.base64_encode($fdata[$i]->order_id)).'"><i class="fa fa-check"></i></a>';
        }

        return response()->json($data);
    }

    public function orderEdit(Request $request)
    {
        // init validations attr
        $rules['shopid']                =   "required|max:191";
        $rules['sale_type']             =   "required|max:191";
        $rules['chequeno']              =   "required|max:191";
        $rules['credit_days']           =   "required|numeric";
        $rules['order']                 =   "required";

        $attr['shopid']                 =   "Shop";
        $attr['sale_type']              =   "Sale Type";
        $attr['chequeno']               =   "Cheque No";
        $attr['credit_days']            =   "Credit Days";
        $attr['order']                  =   "Order Data";

        if($request->sale_type=="credit")
        {
            unset($rules['chequeno']);
        }

        else if($request->sale_type=="pdc")
        {
            unset($rules['credit_days']);
        }
        else if($request->sale_type=="cash")
        {
            unset($rules['chequeno']);
            unset($rules['credit_days']);
        }
        else if($request->sale_type=="")
        {
            unset($rules['chequeno']);
            unset($rules['credit_days']);
        }

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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights
        $helper = new \App\ILP\DBMSModuleHelper();
        if(!$helper->thisUserCan('epos','view'))
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }

        // if arrived here means validation check has passed do insert or update based on logic
        // parse order data
        $request->order = json_decode(base64_decode($request->order), true);

        // create parent order
        $oid = Orders::__create($request);

        // create order meta
        foreach($request->order as $k => $li)
        {
            $li["order_id"] = $oid;
            OrderMeta::__create($li);
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Order has been created successfully!"]);
    }

    public function orderRemove($id)
    {
        $helper = new \App\ILP\DBMSModuleHelper();
        if($helper->thisUserCan('Sales','delete'))
        {
            // decode id
            $id     = base64_decode($id);

            // cut to separate user and order id 0 = order id, 1 = creator id
            $id     = explode(":",$id);

            // fetch order
            $order  = Orders::whereNotIn('status',['cheque-deposited','completed'])->where('createdby',$id[1])->first();

            // check order
            if($order)
            {
                // fetch meta
                $order_meta_query = OrderMeta::where('order_id', $order->id);
                $order_meta = $order_meta_query->get();

                // check meta
                if($order_meta)
                {
                    // loop through meta and stock back
                    foreach($order_meta as $meta)
                    {
                        Products::__alterStock($meta->product_id, $meta->qty, "add");
                    }
                }

                // delete order and its meta after stock back
                $order_meta_query->delete();
                $order->delete();
            }
            else
            {
                return response()->json(['status'=>false, 'message'=>'No record found!']);
            }

            return response()->json(['status'=>true, 'message'=>'']);
        }
        else
        {
            return response()->json(['status'=>false, 'message'=>'You are not authorized to perform this action!']);
        }
    }

    public function paymentReceived($id)
    {
        $helper = new \App\ILP\DBMSModuleHelper();
        if($helper->thisUserCan('sales','view'))
        {
            // decode id
            $id     = base64_decode($id);

            // fetch order
            $order  = Orders::find($id);

            // check order
            if($order)
            {


                // fetch meta
                if($order->sale_type=='pdc' && $order->status=='cheque-in-hand')
                {
                    $order->status = 'cheque-in-finance';
                }
                else if($order->sale_type=='pdc' && $order->status=='cheque-in-finance')
                {
                    $order->status = 'cheque-deposited';
                }
                else if($order->sale_type=='pdc' && $order->status=='cheque-deposited')
                {
                    $order->status = 'completed';
                }
                else if($order->sale_type=='cash' && $order->status=='cash-in-hand')
                {
                    $order->status = 'completed';
                }
                // below action is related to sales team rest are all related to finance
                else if($order->sale_type=='credit' && $order->status=='pending-payment')
                {
                    $order->status = 'cash-in-hand';
                }
                else if($order->sale_type=='credit' && $order->status=='cash-in-hand')
                {
                    $order->status = 'completed';
                }
                $order->save();

                $message = 'Sale is marked as completed!';
                if($order->status=='cheque-in-finance')
                {
                    $message = 'Cheque status updated to: In-Finance';
                }
                else if($order->status=='cheque-deposited')
                {
                    $message = 'Cheque status updated to: Deposited';
                }
                else if($order->status=='cash-in-hand')
                {
                    $message = 'Please submit collected cash in finance dept';
                }

                return response()->json(['status'=>true, 'message'=>$message]);
            }
            else
            {
                return response()->json(['status'=>false, 'message'=>'No record found!']);
            }

            return response()->json(['status'=>true, 'message'=>'']);
        }
        else
        {
            return response()->json(['status'=>false, 'message'=>'You are not authorized to perform this action!']);
        }
    }

    public function transferStock(Request $request)
    {
        // init validations attr
        $rules['ts_spid']               =   "required|max:191";
        $rules['order']                 =   "required";

        $attr['ts_spid']                =   "Sales Person";
        $attr['order']                  =   "Order Data";

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
                return response()->json( ['status'=>false, 'errors'=>$errors, 'message'=>"There is a problem with your form submission. Please refer to errors!"]);
            }
        }

        // check rights
        $helper = new \App\ILP\DBMSModuleHelper();
        if(!$helper->thisUserCan('warehouse','view'))
        {
            return response()->json(['status'=>false, 'errors'=>[], 'message'=>"You are not authorized to perform this action!", "redirect"=>""]);
        }


        // if arrived here means validation check has passed do insert or update based on logic
        // parse order data
        $request->order = json_decode(base64_decode($request->order), true);

        // create order meta
        foreach($request->order as $k => $li)
        {
            $li["ts_spid"] = $request->ts_spid;
            TempStock::__create($li);
        }

        return response()->json(['status'=>true, 'errors'=>[], 'message'=>"Stock has been transferred successfully!"]);
    }





    /**
     * REPORTS RELATED FUNCTIONS
     */
    public function reportGeneralsales(Request $request)
    {
        // init data holder
        $d["data"] = [];
        $d["total"] = 0;

        // build query
        if
        (
            isset($request->from) && $request->from!="" &&
            isset($request->to) && $request->to!=""
        )
        {
            $data = Orders::where('status',$request->status)->whereBetween('updated_at',[$request->from, $request->to])->get();
        }
        else
        {
            $data = Orders::where('status',$request->status)->get();
        }

        // loop
        for($i=0; $i<count($data); $i++)
        {
            // init col value holder
            $c = 0;

            // order id
            $d["data"][$i][$c] = $data[$i]['id'];
            $c++;

            // sale type
            $saleType = "Cash";
            if($data[$i]->sale_type=="pdc")
            {
                $saleType = "Cheque<br/>".$data[$i]->chequeno;
            }
            $d['data'][$i][$c] = $saleType;
            $c++;

            // status

            $status = '<span class="badge badge-danger">Pending Payment</span>';
            if($data[$i]->status=="cash-in-hand")
            {
                $status = '<span class="badge badge-warning">Cash Received</span>';
            }
            else if($data[$i]->status=="cheque-in-hand")
            {
                $status = '<span class="badge badge-warning">Cheque Received</span>';
            }
            else if($data[$i]->status=="cheque-deposited")
            {
                $status = '<span class="badge badge-info">Cheque Deposited</span>';
            }
            else if($data[$i]->status=="completed")
            {
                $status = '<span class="badge badge-success">Completed</span>';
            }

            $d['data'][$i][$c] = $status;
            $c++;

            // sub total
            $total_sales = $data[$i]->orderTotal($data[$i]['id'])->tot;
            $d['data'][$i][$c] = "PKR. ".number_format($total_sales, 2);
            $d["total"] = $d["total"] + $total_sales;
            $c++;

            // sale create date
            $cDate = Carbon::parse($data[$i]->created_at);

            $d['data'][$i][$c] = $cDate->format("Y-m-d h:i:s A");
            $c++;

            // sale complete date
            $uDate = Carbon::parse($data[$i]->updated_at);
            $d['data'][$i][$c] = $uDate->format("Y-m-d h:i:s A");
            $c++;

            // sale complete interval
            $d['data'][$i][$c] = $cDate->diffInDays($uDate);
            $c++;
        }

        $d["total"] = "PKR. ".number_format($d["total"],2);

        // return data
        return response()->json($d);
    }

    public function reportPersonsales(Request $request)
    {
        // init data holder
        $d["data"] = [];
        $d["total"] = 0;

        // build query
        $data = Orders::where('status',$request->status);
        if
        (
            isset($request->from) && $request->from!="" &&
            isset($request->to) && $request->to!=""
        )
        {
            $data = $data->whereBetween('updated_at',[$request->from, $request->to]);
        }

        if (isset($request->user_id) && $request->user_id!="")
        {
            $data = $data->where('createdby',$request->user_id);
        }

        $data = $data->get();

        // loop
        for($i=0; $i<count($data); $i++)
        {
            // init col value holder
            $c = 0;

            // order id
            $d["data"][$i][$c] = $data[$i]['id'];
            $c++;

            // sale type
            $saleType = "Cash";
            if($data[$i]->sale_type=="pdc")
            {
                $saleType = "Cheque<br/>".$data[$i]->chequeno;
            }
            $d['data'][$i][$c] = $saleType;
            $c++;

            // status
            $status = '<span class="badge badge-danger">Pending Payment</span>';
            if($data[$i]->status=="cash-in-hand")
            {
                $status = '<span class="badge badge-warning">Cash Received</span>';
            }
            else if($data[$i]->status=="cheque-in-hand")
            {
                $status = '<span class="badge badge-warning">Cheque Received</span>';
            }
            else if($data[$i]->status=="cheque-deposited")
            {
                $status = '<span class="badge badge-info">Cheque Deposited</span>';
            }
            else if($data[$i]->status=="completed")
            {
                $status = '<span class="badge badge-success">Completed</span>';
            }
            $d['data'][$i][$c] = $status;
            $c++;

            // sub total
            $total_sales = $data[$i]->orderTotal($data[$i]['id'])->tot;
            $d['data'][$i][$c] = "PKR. ".number_format($total_sales, 2);
            $d["total"] = $d["total"] + $total_sales;
            $c++;

            // sale create date
            $cDate = Carbon::parse($data[$i]->created_at);

            $d['data'][$i][$c] = $cDate->format("Y-m-d h:i:s A");
            $c++;

            // sale complete date
            $uDate = Carbon::parse($data[$i]->updated_at);
            $d['data'][$i][$c] = $uDate->format("Y-m-d h:i:s A");
            $c++;

            // sale complete interval
            $d['data'][$i][$c] = $cDate->diffInDays($uDate);
            $c++;

            // sold by name
            $d['data'][$i][$c] = $data[$i]->author->name;
            $c++;
        }

        $d["total"] = "PKR. ".number_format($d["total"],2);

        // return data
        return response()->json($d);
    }

    public function reportShopsales(Request $request)
    {
        // init data holder
        $d["data"] = [];
        $d["total"] = 0;

        // build query
        $data = Orders::where('status','completed');
        if
        (
            isset($request->from) && $request->from!="" &&
            isset($request->to) && $request->to!=""
        )
        {
            $data = $data->whereBetween('updated_at',[$request->from, $request->to]);
        }

        if (isset($request->user_id) && $request->user_id!="")
        {
            $data = $data->where('shopid',$request->user_id);
        }

        $data = $data->get();

        // loop
        for($i=0; $i<count($data); $i++)
        {
            // init col value holder
            $c = 0;

            // order id
            $d["data"][$i][$c] = $data[$i]['id'];
            $c++;

            // sale type
            $saleType = "Cash";
            if($data[$i]->sale_type=="pdc")
            {
                $saleType = "Cheque<br/>".$data[$i]->chequeno;
            }
            $d['data'][$i][$c] = $saleType;
            $c++;

            // status
            $d['data'][$i][$c] = '<span class="badge badge-success">Completed</span>';
            $c++;

            // sub total
            $total_sales = $data[$i]->orderTotal($data[$i]['id'])->tot;
            $d['data'][$i][$c] = "PKR. ".number_format($total_sales, 2);
            $d["total"] = $d["total"] + $total_sales;
            $c++;

            // sale create date
            $cDate = Carbon::parse($data[$i]->created_at);

            $d['data'][$i][$c] = $cDate->format("Y-m-d h:i:s A");
            $c++;

            // sale complete date
            $uDate = Carbon::parse($data[$i]->updated_at);
            $d['data'][$i][$c] = $uDate->format("Y-m-d h:i:s A");
            $c++;

            // sale complete interval
            $d['data'][$i][$c] = $cDate->diffInDays($uDate);
            $c++;

            // sold by name
            $d['data'][$i][$c] = $data[$i]->shop->name;
            $c++;
        }

        $d["total"] = "PKR. ".number_format($d["total"],2);

        // return data
        return response()->json($d);
    }

    public function reportAreasales(Request $request)
    {
        // init data holder
        $d["data"] = [];
        $d["total"] = 0;

        // build query
        $data = Orders::where('status','completed');
        if
        (
            isset($request->from) && $request->from!="" &&
            isset($request->to) && $request->to!=""
        )
        {
            $data = $data->whereBetween('updated_at',[$request->from, $request->to]);
        }

        if (isset($request->area_id) && $request->area_id!="")
        {
            $id = $request->area_id;
            $data = $data->whereHas('author',function($query) use ($id){
                $query->where('areaid', $id);
            });
        }
        $data = $data->get();

        // loop
        for($i=0; $i<count($data); $i++)
        {
            // init col value holder
            $c = 0;

            // order id
            $d["data"][$i][$c] = $data[$i]['id'];
            $c++;

            // sale type
            $saleType = "Cash";
            if($data[$i]->sale_type=="pdc")
            {
                $saleType = "Cheque<br/>".$data[$i]->chequeno;
            }
            $d['data'][$i][$c] = $saleType;
            $c++;

            // status
            $d['data'][$i][$c] = '<span class="badge badge-success">Completed</span>';
            $c++;

            // sub total
            $total_sales = $data[$i]->orderTotal($data[$i]['id'])->tot;
            $d['data'][$i][$c] = "PKR. ".number_format($total_sales, 2);
            $d["total"] = $d["total"] + $total_sales;
            $c++;

            // sale complete date
            $uDate = Carbon::parse($data[$i]->updated_at);
            $d['data'][$i][$c] = $uDate->format("Y-m-d h:i:s A");
            $c++;

            // area name
            $d['data'][$i][$c] = $data[$i]->author->relcity->label.', '.$data[$i]->author->relarea->label;
            $c++;
        }

        $d["total"] = "PKR. ".number_format($d["total"],2);

        // return data
        return response()->json($d);
    }

    public function reportTypesales(Request $request)
    {
        // init data holder
        $d["data"] = [];
        $d["total"] = 0;

        // build query
        $data = Orders::where('status','completed');
        if
        (
            isset($request->from) && $request->from!="" &&
            isset($request->to) && $request->to!=""
        )
        {
            $data = $data->whereBetween('updated_at',[$request->from, $request->to]);
        }

        if (isset($request->type) && $request->type!="")
        {
            $data = $data->where('sale_type', $request->type);
        }
        $data = $data->get();

        // loop
        for($i=0; $i<count($data); $i++)
        {
            // init col value holder
            $c = 0;

            // order id
            $d["data"][$i][$c] = $data[$i]['id'];
            $c++;

            // sale type
            $saleType = "Cash";
            if($data[$i]->sale_type=="pdc")
            {
                $saleType = "Cheque<br/>".$data[$i]->chequeno;
            }
            $d['data'][$i][$c] = $saleType;
            $c++;

            // status
            $d['data'][$i][$c] = '<span class="badge badge-success">Completed</span>';
            $c++;

            // sub total
            $total_sales = $data[$i]->orderTotal($data[$i]['id'])->tot;
            $d['data'][$i][$c] = "PKR. ".number_format($total_sales, 2);
            $d["total"] = $d["total"] + $total_sales;
            $c++;

            // sale complete date
            $uDate = Carbon::parse($data[$i]->updated_at);
            $d['data'][$i][$c] = $uDate->format("Y-m-d h:i:s A");
            $c++;
        }

        $d["total"] = "PKR. ".number_format($d["total"],2);

        // return data
        return response()->json($d);
    }

    public function reportProductsales(Request $request)
    {
        // init data holder
        $d["data"] = [];
        $d["total"] = 0;

        // build query
        $data = DB::table('products')
            ->select(DB::raw(' products.id, products.name, SUM(order_metas.qty * order_metas.sale_price) as sub_total, SUM(order_metas.qty) as qty'))
            ->join('order_metas', 'products.id', '=', 'order_metas.product_id')
            ->join('orders', 'orders.id', '=', 'order_metas.order_id')
            ->groupby('products.id')
            ->whereIn('orders.status',['completed']);
        if
        (
            isset($request->from) && $request->from!="" &&
            isset($request->to) && $request->to!=""
        )
        {
            $data = $data->whereBetween('orders.updated_at',[$request->from, $request->to]);
        }

        if (isset($request->product_id) && $request->product_id!="")
        {
            $data = $data->where('products.id', $request->product_id);
        }
        $data = $data->get();

        // loop
        for($i=0; $i<count($data); $i++)
        {
            // init col value holder
            $c = 0;

            // id
            $d["data"][$i][$c] = $data[$i]->id;
            $c++;

            // name
            $d["data"][$i][$c] = $data[$i]->name;
            $c++;

            // qty
            $d["data"][$i][$c] = number_format($data[$i]->qty);
            $c++;

            // sub total
            $d["data"][$i][$c] = "PKR. ".number_format($data[$i]->sub_total, 2);
            $c++;

            $d["total"] = $d["total"] + $data[$i]->sub_total;
        }
        $d["total"] = "PKR. ".number_format($d["total"],2);

        // return data
        return response()->json($d);
    }
}
