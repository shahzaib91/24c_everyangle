<?php
use App\Helper\DataCollector;

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
        $data->stats = \App\Models\Categories::where('cat_owner',Auth::user()->id);
        return $data;
    }
}
