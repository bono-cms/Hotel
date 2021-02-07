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
use Hotel\Storage\RoomMapperInterface;

final class RoomMapper extends AbstractMapper implements RoomMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_hotel_rooms');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return RoomTranslationMapper::getTableName();
    }

    /**
     * Returns shared columns
     * 
     * @return array
     */
    private function getColumns()
    {
        return [
            self::column('id'),
            self::column('price'),
            self::column('adults'),
            self::column('children'),
            RoomTranslationMapper::column('lang_id'),
            RoomTranslationMapper::column('name'),
            RoomTranslationMapper::column('description')
        ];
    }

    /**
     * Fetches room by its id
     * 
     * @param int $id Room id
     * @param boolean $withTranslations Whether to fetch translations
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        return $this->findEntity($this->getColumns(), $id, $withTranslations);
    }

    /**
     * Fetch all available rooms
     * 
     * @return array
     */
    public function fetchAll()
    {
        $db = $this->createEntitySelect($this->getColumns())
                    // Language ID constraint
                   ->whereEquals(RoomTranslationMapper::column('lang_id'), $this->getLangId());

        $db->orderBy(self::column('id'))
           ->desc();

        return $db->queryAll();
    }
}
