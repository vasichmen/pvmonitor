<?php

return [
    'client' => [
        'hosts' => [
            env('ELASTIC_HOST', 'localhost:9200'),
        ],
    ],

    'prefix' => env('ELASTIC_INDEX_PREFIX', ''),

    'document_refresh' => env('ELASTIC_DOCUMENT_REFRESH'),

    'default_max_result_window' => env('ELASTIC_DEFAULT_MAX_RESULT_WINDOW', 10000),

    'indexes' => [
        // alias => indexClass
    ]
];
