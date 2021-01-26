<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\Region;

class Geo extends Helper
{

    protected $remote_addr;
    protected $info;

    public $response;
    public $city;
    public $country;
    public $region;
    public $kladr_id;

    function __construct()
    {
        parent::__construct();
    }

    public function get(Request $request)
    {
        $this->remote_addr = $request->server('REMOTE_ADDR');
        $this->response = Http::get("http://api.sypexgeo.net/json/" . $this->remote_addr);
        $this->info = json_decode($this->response);

        $this->city = $this->info->city;
        $this->country = $this->info->country;

        if ($this->info->region) {
            $kladrs = explode(",", $this->info->region->auto);
            $kladrsId = [];
            foreach ($kladrs as $kladr) {
                $kladrsId[] = str_pad($kladr, 13, 0);
            }
            $this->region = Region::whereIn('kladr_id', $kladrsId)->first();
        } else {
            $this->region = Region::where('kladr_id', '5000000000000')->first();
        }
    }
}
