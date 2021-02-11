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
        // Columns to be selected
        $columns = [
            self::column('id'),
            self::column('room_id'),
            self::column('datetime'),
            self::column('client'),
            self::column('amount'),
            self::column('checkin'),
            self::column('checkout'),
            self::column('status'),
            self::column('token'),
            RoomTranslationMapper::column('name') => 'room'
        ];

        $db = $this->db->select($columns)
                       ->from(self::getTableName())
                       // Room relation
                       ->leftJoin(RoomMapper::getTableName(), [
                            RoomMapper::column('id') => self::getRawColumn('room_id')
                       ])
                       // Room translation
                       ->leftJoin(RoomTranslationMapper::getTableName(), [
                            RoomTranslationMapper::column('id') => RoomMapper::getRawColumn('id'),
                            RoomTranslationMapper::column('lang_id') => $this->getLangId()
                        ])
                       ->orderBy(self::column('id'))
                       ->desc();

        return $db->queryAll();
    }
}
