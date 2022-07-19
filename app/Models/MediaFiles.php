<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class MediaFiles extends Model
{
    use HasFactory;

    public static function __create(Request $request)
    {
        $obj = new MediaFiles;
        $obj->file_name     =   $request->file_name;
        $obj->file_owner    =   Auth::user()->id;
        $obj->file_cat      =   $request->file_cat;
        $obj->save();
    }

    public static function __update(Request $request)
    {
        $obj = MediaFiles::find($request->id);
        if($obj && Auth::user()->id==$obj->file_owner)
        {
            $obj->file_name     =   $request->file_name;
            $obj->file_cat      =   $request->file_cat;
            $obj->save();
        }
    }

    public static function __delete($id)
    {
        $obj = MediaFiles::find($id);
        if($obj && Auth::user()->id==$obj->file_owner)
        {
            $obj->delete();
        }
        return ['status'=>true, 'errors'=>[], 'message'=>"Record has been trashed!"];
    }

    // associated item relation function
    public function category()
    {
        return $this->hasOne(Categories::class,'id', 'file_cat');
    }
}
