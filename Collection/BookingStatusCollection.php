<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Hotel\Collection;

use Krystal\Stdlib\ArrayCollection;

final class BookingStatusCollection extends ArrayCollection
{
    const STATUS_TEMPORARY = -1;
    const STATUS_CONFIRMED = 1;

    /**
     * {@inheritDoc}
     */
    protected $collection = [
        self::STATUS_TEMPORARY => 'Temporary',
        self::STATUS_CONFIRMED => 'Confirmed'
    ];
}
