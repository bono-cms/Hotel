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

use Krystal\Db\Sql\RawSqlFragment;
use Cms\Storage\MySQL\AbstractMapper;
use Hotel\Storage\GalleryMapperInterface;

final class GalleryMapper extends AbstractMapper implements GalleryMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_hotel_rooms_gallery');
    }

    /**
     * Fetch image by its id
     * 
     * @param int $id Image id
     * @return array
     */
    public function fetchById($id)
    {
        // Columns to be selected
        $columns = [
            self::column('id'),
            self::column('room_id'),
            self::column('order'),
            self::column('file'),
            RoomTranslationMapper::column('name') => 'room'
        ];

        $db = $this->db->select($columns)
                       ->from(self::getTableName())
                       // Room relation
                       ->innerJoin(RoomMapper::getTableName(), [
                            RoomMapper::column('id') => self::getRawColumn('room_id')
                       ])
                       // Room translation relation
                       ->leftJoin(RoomTranslationMapper::getTableName(), [
                            RoomTranslationMapper::column('id') => RoomMapper::getRawColumn('id'),
                            RoomTranslationMapper::column('lang_id') => $this->getLangId()
                       ])
                       ->whereEquals(self::column('id'), $id);

        return $db->query();
    }

    /**
     * Fetch all images by room id
     * 
     * @param int $roomId
     * @return array
     */
    public function fetchAll($roomId)
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->whereEquals('room_id', $roomId)
                       ->orderBy(new RawSqlFragment(sprintf('`order`, CASE WHEN `order` = 0 THEN %s END DESC', self::column('id'))));

        return $db->queryAll();
    }
}
