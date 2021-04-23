<?php
return [
    'routes' => [
        # template endpoints
        ['name' => 'page#index',                'url' => '/',                       'verb' => 'GET'],
        ['name' => 'page#authorize',            'url' => '/authorize',              'verb' => 'GET'],
        ['name' => 'DescriboApi#informations',          'url' => '/api/v1/informations',    'verb' => 'GET'],
        ['name' => 'DescriboApi#publickey',             'url' => '/api/v1/publickey',       'verb' => 'GET'],
        [
            'name' => 'DescriboApi#preflighted_cors',
            'url' => '/api/v1/{path}',
            'verb' => 'OPTIONS',
            'requirements' => array('path' => '.+')
        ],
    ]
];
