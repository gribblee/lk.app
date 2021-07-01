<?php

return [
    /*
     * This is user login. You can use any cloud storage user
     */
    'authUser' => env('163622_devers'),

    /*
     * Password for cloud storage service.
     * Note: it's different with account password
     */
    'authKey' => env('7FG73@qiG9Ugx5v'),

    /*
     * API url
     */
    'apiUrl' => 'https://auth.selcdn.ru/',

    /*
     * Default value for request timeout
     */
    'timeout' => 10,

    /*
     * Default storage url
     */
    'storageUrl' => env('SELECTEL_STORAGE_URL', 'https://api.selcdn.ru'),

    /*
     * Response view
     * Can be in json or xml
     */
    'returnView' => env('SELECTEL_RETURN_VIEW', 'json'),


];