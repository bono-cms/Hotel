<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Hotel\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;
use Hotel\Storage\BookingGuestMapperInterface;

final class BookingGuestMapper extends AbstractMapper implements BookingGuestMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_hotel_booking_guest');
    }
}
