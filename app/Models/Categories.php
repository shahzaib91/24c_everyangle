<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class Categories extends Model
{
    use HasFactory;

    public static function __create(Request $request)
    {
        $obj = new Categories;
        $obj->cat_name  =   $request->cat_name;
        $obj->cat_owner =   Auth::user()->id;
        $obj->save();
    }

    public static function __update(Request $request)
    {
        $obj = Categories::find($request->id);
        if($obj && Auth::user()->id==$obj->cat_owner)
        {
            $obj->cat_name  =   $request->cat_name;
            $obj->save();
        }
    }

    public static function __delete($id)
    {
        $obj = MediaFiles::where('file_cat', $id)->get();
        if($obj && count($obj)>0)
        {
            return ['status'=>false, 'message'=>"Requested category can't be removed as it is associated with media items!"];
        }
        $obj = Categories::find($id);
        if($obj && Auth::user()->id==$obj->cat_owner)
        {
            $obj->delete();
        }
        return ['status'=>true, 'errors'=>[], 'message'=>"Record has been trashed!"];
    }

    // associated item relation function
    public function assocItems()
    {
        return $this->hasOne(MediaFiles::class,'file_cat', 'id')
            ->selectRaw('count(media_files.id) as total')
            ->groupByRaw('media_files.file_cat');
    }
}
