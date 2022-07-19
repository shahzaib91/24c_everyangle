<?php
namespace App\Helper;

/**
 * Interface DataCollector
 * @package App\Helper
 * This object will be responsible for collecting data and passing it to view via controller (Template Interface)
 */
interface DataCollector
{
    public function pre_process($id, $current_module, $current_view);
}
