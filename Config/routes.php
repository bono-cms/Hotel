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
    ]
];