<?php

/**
 * Module configuration container
 */

return [
    'caption' => 'Hotel',
    'description' => 'Hotel module allows you to manage rooms and bookings on your site',
    'menu' => [
        'name' => 'Hotel',
        'icon' => 'fas fa-hotel fa-5x',
        'items' => [
            [
                'route' => 'Hotel:Admin:Room@indexAction',
                'name' => 'View all rooms'
            ]
        ]
    ]
];