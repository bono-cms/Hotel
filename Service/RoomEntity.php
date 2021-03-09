<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Hotel\Service;

use Krystal\Stdlib\VirtualEntity;

final class RoomEntity extends VirtualEntity
{
    /**
     * Returns next URL on current room entity
     * 
     * @param string $checkin
     * @param string $checkout
     * @param array $criteria
     * @return string
     */
    public function getNextUrl($checkin, $checkout, array $criteria = [])
    {
        // Query parameters
        $query = [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'criteria' => $criteria
        ];

        return $this->getUrl() . '?' . http_build_query($query);
    }
}
