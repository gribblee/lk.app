<?php

namespace App\Helpers;

use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;

/**
 * Читать документацию тут
 * https://github.com/sendpulse/sendpulse-rest-api-php
 */

class SendPulse extends ApiClient
{
    function __construct()
    {
        parent::__construct(config('sendpulse.SENDPULSE_API_ID'), config('sendpulse.SENDPULSE_API_SECRET'), new FileStorage());
    }
}
