<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

return [
    //Site
    '/module/hotel/search' => [
        'controller' => 'Room@searchAction'
    ],

    '/module/hotel/available' => [
        'controller' => 'Room@availableAction'
    ],
    
    // Rooms
    '/%s/module/hotel' => [
        'controller' => 'Admin:Room@indexAction'
    ],

    '/%s/module/hotel/add' => [
        'controller' => 'Admin:Room@addAction'
    ],

    '/%s/module/hotel/edit/(:var)' => [
        'controller' => 'Admin:Room@editAction'
    ],

    '/%s/module/hotel/delete/(:var)' => [
        'controller' => 'Admin:Room@deleteAction'
    ],

    '/%s/module/hotel/save' => [
        'controller' => 'Admin:Room@saveAction'
    ],

    // Room gallery
    '/%s/module/hotel/room/gallery/add/(:var)' => [
        'controller' => 'Admin:Gallery@addAction'
    ],

    '/%s/module/hotel/room/gallery/edit/(:var)' => [
        'controller' => 'Admin:Gallery@editAction'
    ],
    
    '/%s/module/hotel/room/gallery/delete/(:var)' => [
        'controller' => 'Admin:Gallery@deleteAction'
    ],

    '/%s/module/hotel/room/gallery/save' => [
        'controller' => 'Admin:Gallery@saveAction'
    ],

    // Booking
    '/%s/module/hotel/booking' => [
        'controller' => 'Admin:Booking@indexAction'
    ],

    '/%s/module/hotel/booking/add' => [
        'controller' => 'Admin:Booking@addAction'
    ],

    '/%s/module/hotel/booking/edit/(:var)' => [
        'controller' => 'Admin:Booking@editAction'
    ],

    '/%s/module/hotel/booking/delete/(:var)' => [
        'controller' => 'Admin:Booking@deleteAction'
    ],

    '/%s/module/hotel/booking/save' => [
        'controller' => 'Admin:Booking@saveAction'
    ]
];
