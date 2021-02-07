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
     * Confirms booking by a token
     * 
     * @param string $token
     * @return boolean Depending on success
     */
    public function confirmByToken($token);

    /**
     * Fetch all booking entries
     * 
     * @return array
     */
    public function fetchAll();
}
