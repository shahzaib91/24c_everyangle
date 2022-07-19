<?php
use App\Helper\DataCollector;
use Illuminate\Support\Facades\DB;
use \App\Models\Categories;

/**
 * Class ModuleDataCollector
 * This class will get data of logged in user and pass it to view. This is being called in ViewsHandler controller and optional
 * to make dynamic view work only if you want to pass some data.
 */
class ModuleDataCollector implements DataCollector
{
    public function pre_process($id, $current_module, $current_view)
    {
        $data = new stdClass;

        // dynamic widgets query data will be used in dashboard
        $data->stats = Categories::select(DB::raw('categories.cat_name, count(media_files.id) as total'))
            ->join('media_files','categories.id','=','media_files.file_cat')
            ->where('cat_owner',Auth::user()->id)
            ->groupByRaw('categories.id')
            ->get();

        return $data;
    }
}
