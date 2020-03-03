<?php
namespace App\Services;

class GroupService
{
    public function getChildMaxOrder($pid)
    {
        $maxOrder=\DB::select('select max(`order`)+1 as maxOrder from groups where parent_id = ?', [$pid])[0]->maxOrder;
        return $maxOrder?$maxOrder:0;
    }
}
