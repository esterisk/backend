<?php

return [

    /*
    |--------------------------------------------------------------------------
    | backendUrlPosition
    |--------------------------------------------------------------------------
    |
    | Position the backend tree in a subdir or in a subdomain
    | Accepted values: 'subdir', 'subdomain'
    |
    */

	'backendUrlPosition' => 'subdir',

    /*
    |--------------------------------------------------------------------------
    | backendUrlPrefix
    |--------------------------------------------------------------------------
    |
    | the prefix for the backend tree in your url, ie: mydomain.com/backend/[class]/[cmd]
    |
    */

    'backendUrlPrefix' => 'backend',

    /*
    |--------------------------------------------------------------------------
    | backendUrlDomain
    |--------------------------------------------------------------------------
    |
    | the subdomain for the backend tree in your url,  ie: backend.mydomain.com/[class]/[cmd]
    |
    */

    'backendUrlDomain' =>  preg_replace('|(https?://)|','\1'.'backend.',config('app.url')),

    /*
    |--------------------------------------------------------------------------
    | backendMiddleware
    |--------------------------------------------------------------------------
    |
    | the middleware for the backend tree
    |
    */

    'backendMiddleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | backendClassesNamespace
    |--------------------------------------------------------------------------
    |
    | where to find the Backend classes
    |
    */

    'backendClassesNamespace' => 'App\\Http\\Backends',

    /*
    |--------------------------------------------------------------------------
    | backendViewLayout
    |--------------------------------------------------------------------------
    |
    | the view extended by backend view
    |
    */

    'backendViewLayout' => 'backendLayout',

];
