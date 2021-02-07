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
                       ->orderBy('order');

        return $db->queryAll();
    }
}
