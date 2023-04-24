<?php

return [


    'paths' => ['api/*','admin/*', 'api/auth/',  '*', 'auth/*',
        'api/admin/*',
        'api/training-school-admin/*',
        'training-school-admin/*'
        ,'school-admin/*' ,
        'sanctum/csrf-cookie'
    ],

    'allowed_methods' => ['POST', 'GET', 'DELETE', 'PUT', '*'],

    'allowed_origins' => ['https://staging-api.wahebonline.org/api/*','*',
        'https://nyc3.digitaloceanspaces.com', 'https://nyc3.digitaloceanspaces.com/*',
        'https://staging-api.wahebonline.org/api/',],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['X-Custom-Header', 'Upgrade-Insecure-Requests', 'x-requested-with',
        'Content-Type', 'origin', 'authorization' ,'*', 'Access-Control-Allow-Origin'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
