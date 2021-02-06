<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Hotel;

use Cms\AbstractCmsModule;
use Hotel\Service\RoomService;
use Hotel\Service\BookingService;

final class Module extends AbstractCmsModule
{
    /**
     * {@inheritDoc}
     */
    public function getServiceProviders()
    {
        $roomMapper = $this->getMapper('/Hotel/Storage/MySQL/RoomMapper');
        $bookingMapper = $this->getMapper('/Hotel/Storage/MySQL/BookingMapper');

        return [
            'bookingService' => new BookingService($bookingMapper),
            'roomService' => new RoomService($roomMapper)
        ];
    }
}
