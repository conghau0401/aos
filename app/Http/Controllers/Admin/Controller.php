<?php

namespace App\Http\Controllers\Admin;

use App\Models\Mapp;
use App\Models\MappOption;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * get meta key - value function
     *
     * @param integer $id
     * @param string $type
     * @return array
     */
    public function getMetaValue($id = 0, $type = '')
    {
        $mappingMeta = [];
        $mapp = Mapp::where(['type' => $type, 'cafe24_id' => $id])->first();
        if (!empty($mapp)) {
            $result = MappOption::where('map_id', $mapp->id)->get();
            foreach ($result as $value) {
                $mappingMeta[$value['meta_key']] = $value['meta_value'];
            }
        }
        return $mappingMeta;
    }
}
