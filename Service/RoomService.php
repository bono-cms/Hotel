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

use Hotel\Storage\RoomMapperInterface;

final class RoomService
{
    /**
     * Any compliant room mapper
     * 
     * @var \Hotel\Storage\RoomMapperInterface
     */
    private $roomMapper;

    /**
     * State initialization
     * 
     * @param \Hotel\Storage\RoomMapperInterface $roomMapper
     * @return void
     */
    public function __construct(RoomMapperInterface $roomMapper)
    {
        $this->roomMapper = $roomMapper;
    }
}
