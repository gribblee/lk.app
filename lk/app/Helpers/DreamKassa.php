<?php

namespace App\Helpers;
// Нужно ещё добавить обработку ошибок
// Так-как текущая взята с тинкова
class DreamKassa extends Helper
{
    protected $token = ''; // Токен
    protected $taxMode = 'PATENT'; // Налоговая ставка
    protected $deviceId = 129174; // ID устройства
    protected $timeout = 5; // Таймаут

    function __construct()
    {
        $this->token = config('dreamkassa.login');
        $this->url_init = config('dreamkassa.receipts_url');
    }

    /**
     * Фискализация чеков
     */
    public function receipts($positions = [], $attributes = [], $type = 'CASHLESS')
    {
        $params = [];

        $params["deviceId"] = $this->deviceId;
        $params["type"] = "SALE";
        $params["timeout"] = $this->timeout;
        $params["taxMode"] = $this->taxMode;


        $totalSum = 0;
        foreach ($positions as $position) {
            $totalSum = $totalSum + $position['price'];
        }

        $params['payments'] = [
            'sum' => $totalSum,
            'type' => $type
        ];
        $params['attributes'] = $attributes;
        $params['total'] = [
            'priceSum' => $totalSum
        ];
        $params["tags"] = [
            [
                "tag" => 1212,
                "value" => 12
            ]
        ];

        if ($this->sendRequest($this->url_init, $params)) {
            return $this->payment_url;
        }
        return false;
    }

    /**
     * 
     */
    protected function sendRequest($path, array $args)
    {

        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $path);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            $this->response = $response;
            $json = json_decode($response);

            if ($json) {
                if ($this->errorsFound()) {
                    return FALSE;
                } else {
                    $this->payment_id       = @$json->PaymentId;
                    $this->payment_url      = @$json->PaymentURL;
                    $this->payment_status   = @$json->Status;

                    return TRUE;
                }
            }
            $this->error .= "Can't create connection to: $path | with args: $args";
            return FALSE;
        } else {
            $this->error .= "CURL init filed: $path | with args: $args";
            return FALSE;
        }
    }

    /**
     * Finding all possible errors
     * @return bool
     */
    private function errorsFound(): bool
    {
        $response = json_decode($this->response, TRUE);

        if (isset($response['ErrorCode'])) {
            $error_code = (int) $response['ErrorCode'];
        } else {
            $error_code = 0;
        }

        if (isset($response['Message'])) {
            $error_msg = $response['Message'];
        } else {
            $error_msg = 'Unknown error.';
        }

        if (isset($response['Details'])) {
            $error_message = $response['Details'];
        } else {
            $error_message = 'Unknown error.';
        }

        if ($error_code !== 0) {
            $this->error = 'Error code: ' . $error_code .
                ' | Msg: ' . $error_msg .
                ' | Message: ' . $error_message;
            return TRUE;
        }
        return FALSE;
    }
}
