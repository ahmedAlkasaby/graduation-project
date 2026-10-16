<?php

return [

    'api_key' => env('VONAGE_API_KEY'),

    'api_secret' => env('VONAGE_API_SECRET'),

    'from_name' => env('VONAGE_FROM_NAME', config('app.name')),

];
