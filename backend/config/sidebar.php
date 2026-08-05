<?php

return [
    [
        'type' => 'section',
        'name' => 'MAIN',
        'items' => [
            [
                'type' => 'accordion',
                'name' => 'Dashboard',
                'icon' => 'dashboard',
                'sub_items' => [
                    [
                        'name' => 'Ringkasan',
                        'url' => '/admin/dashboard',
                    ],
                ],
            ],
        ],
    ],
];
