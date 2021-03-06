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

interface BookingMapperInterface
{
    /**
     * Count all confirmed booking items
     * 
     * @return int
     */
    public function countAll();

    /**
     * Checks whether a single room is available at given dates
     * 
     * @param int $roomId
     * @param string $checkin
     * @param string $checkout
     * @return boolean
     */
    public function isAvailable($roomId, $checkin, $checkout);

    /**
     * Confirms booking by a token
     * 
     * @param string $token
     * @return boolean Depending on success
     */
    public function confirmByToken($token);

    /**
     * Fetch booking entry by its token
     * 
     * @param string $token
     * @return array
     */
    public function fetchByToken($token);

    /**
     * Fetch list of rooms
     * 
     * @return array
     */
    public function fetchList();

    /**
     * Fetch all booking entries
     * 
     * @return array
     */
    public function fetchAll();
}
