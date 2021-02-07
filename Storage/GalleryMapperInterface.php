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

interface GalleryMapperInterface
{
    /**
     * Fetch all images by room id
     * 
     * @param int $roomId
     * @return array
     */
    public function fetchAll($roomId);
}
