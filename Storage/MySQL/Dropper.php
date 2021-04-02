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

use Cms\Storage\MySQL\AbstractStorageDropper;

final class Dropper extends AbstractStorageDropper
{
    /**
     * {@inheritDoc}
     */
    protected function getTables()
    {
        return [
            RoomMapper::getTableName(),
            RoomTranslationMapper::getTableName(),
            BookingMapper::getTableName(),
            BookingGuestMapper::getTableName(),
            GalleryMapper::getTableName()
        ];
    }
}
