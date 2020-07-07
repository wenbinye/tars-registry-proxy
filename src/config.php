<?php

use wenbinye\tars\registry\RegistryProxyService;

return [
    'application' => [
        'tars' => [
            'servants' => [
                'tars.tarsregistry.QueryObj' => RegistryProxyService::class
            ]
        ],
        'registry' => [
            'host_mapping' => array_merge(...array_map(static function(string $pair) {
                $part = explode(":", $pair, 2);
                return count($part) === 2 ? [$part[0] => $part[1]] : [];
            }, explode('|', $_ENV['HOST_MAPPING'] ?? ''))),

            'route_list' => array_values(array_filter(array_map(
                'trim', explode('|', $_ENV['TARS_ROUTE_LIST'] ?? ''))))
        ]
    ]
];