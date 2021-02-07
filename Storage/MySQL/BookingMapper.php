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
use Hotel\Storage\BookingMapperInterface;
use Hotel\Collection\BookingStatusCollection;

final class BookingMapper extends AbstractMapper implements BookingMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_hotel_rooms_booking');
    }

    /**
     * Confirms booking by a token
     * 
     * @param string $token
     * @return boolean Depending on success
     */
    public function confirmByToken($token)
    {
        $db = $this->db->update(self::getTableName(), ['status' => BookingStatusCollection::STATUS_CONFIRMED])
                       ->whereEquals('token', $token);

        return (bool) $db->execute(true);
    }

    /**
     * Fetch all booking entries
     * 
     * @return array
     */
    public function fetchAll()
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->orderBy('id')
                       ->desc();

        return $db->queryAll();
    }
}
