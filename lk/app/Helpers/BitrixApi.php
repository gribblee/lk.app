<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class BitrixApi extends Helper
{
    protected $apiKey = '';
    protected $sendUrl = '';
    protected $action = '';

    protected $DATA_REQUEST = [];

    public $UTM_MARK = [];
    public $SOURCE_ID = 3;
    public $SOURCE_REGISTER_ID = 5;
    public $RESPONSIBLE_ID = 29;

    function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->sendUrl = "https://slm.bitrix24.ru/rest/{$this->RESPONSIBLE_ID}/{$apiKey}";
        // parent::__construct();
    }

    public function field($var, $val = '', $type = null)
    {
        if ($type) {
            $this->DATA_REQUEST['FIELDS'][$var][] = [
                'VALUE' => $val,
                'VALUE_TYPE' => $type
            ];
        } else {
            $this->DATA_REQUEST['FIELDS'][$var] = $val;
        }
        return $this;
    }

    public function utm(array $input)
    {
        $UTM_MARK = [];
		foreach ($input as $k_utm => $v_utm) {
                (strpos(strtoupper($k_utm), 'UTM_') !== false)
                    ? $UTM_MARK[strtoupper($k_utm)] = $v_utm
                    : false;
        }
        $this->DATA_REQUEST['FIELDS'] = array_merge($this->DATA_REQUEST['FIELDS'], $UTM_MARK);
        $this->UTM_MARK = $UTM_MARK;
        return $this;
    }

    public function req($var, $val)
    {
        $this->DATA_REQUEST[$var] = $val;
        return $this;
    }

    public function lead($type = 'default', string $source_url = '')
    {
        $this->action = 'crm.lead';
        if (empty($source_url) == false) {
            $this->field('UF_CRM_1602571646472', $source_url);
        }
        $this->field('ASSIGNED_BY_ID', 1);
        switch ($type) {
            case 'default':
                $this->field('SOURCE_ID', $this->SOURCE_ID);
                break;
            case 'register':
                $this->field('SOURCE_ID', $this->SOURCE_REGISTER_ID)
                     ->req("REGISTER_SONET_EVENT", "Y");
                break;
        }
        return $this;
    }

    public function task()
    {
        $this->action = 'task.item'; //В будущем Битрикс24 будет использовать метод tasks.task.add
        return $this;
    }

    public function add()
    {
        return Http::post($this->sendUrl . '/' . $this->action . '.add.json', $this->DATA_REQUEST);
    }

    public function getData()
    {
        return [
            'api_key' => $this->apiKey,
            'action' => $this->action,
            'data' => $this->DATA_REQUEST,
            'utm' => $this->UTM_MARK,
            'source_id' => $this->SOURCE_ID,
            'source_register_id' => $this->SOURCE_REGISTER_ID,
            'responsible_id' => $this->RESPONSIBLE_ID,
        ];
    }

    public function run()
    {
    }
}
