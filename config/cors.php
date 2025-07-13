<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Ensure your API paths are covered
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',      // For when your React app runs on typical localhost:3000
        'http://127.0.0.1:3000',      // Another common localhost alias
        'http://192.168.0.110:8080',  // <--- THIS IS THE CRUCIAL ONE for your current setup
        'http://localhost:8080',      // If your React app also runs on localhost with port 8080
        'http://127.0.0.1:8080',      // Another common alias for 127.0.0.1 with port 8080
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],

];