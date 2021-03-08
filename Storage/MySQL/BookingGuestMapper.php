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

    /**
     * Insert many guests at once (in one query)
     * 
     * @param array $guests
     * @return boolean
     */
    public function saveMany(array $values)
    {
        // Target columns
        $columns = [
            'booking_id',
            'client',
            'email'
        ];

        $db = $this->db->insertMany(self::getTableName(), $columns, $values);

        return $db->execute();
    }

    /**
     * Fetch guests by booking id
     * 
     * @param int $bookingId
     * @return array
     */
    public function fetchGuestsByBookingId($bookingId)
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->whereEquals('booking_id', $bookingId);

        return $db->queryAll();
    }
}
