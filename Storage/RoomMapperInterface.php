<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Hotel\Storage;

interface RoomMapperInterface
{
    /**
     * Search for available rooms
     * 
     * @param string $checkin Check-in date
     * @param string $checkout Check-out date
     * @param array $criteria Capacity criteria
     * @return array
     */
    public function search($checkin, $checkout, array $criteria);

    /**
     * Fetch all available rooms
     * 
     * @return array
     */
    public function fetchAll();
}
