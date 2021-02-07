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

use Hotel\Storage\GalleryMapperInterface;

final class GalleryService
{
    /**
     * Any-compliant gallery mapper
     * 
     * @var \Hotel\Storage\GalleryMapperInterface
     */
    private $galleryMapper;

    /**
     * State initialization
     * 
     * @param \Hotel\Storage\GalleryMapperInterface $galleryMapper
     * @return void
     */
    public function __construct(GalleryMapperInterface $galleryMapper)
    {
        $this->galleryMapper = $galleryMapper;
    }

    /**
     * Fetch all images by room id
     * 
     * @param int $roomId
     * @return array
     */
    public function fetchAll($roomId)
    {
        return $this->galleryMapper->fetchAll($roomId);
    }
}
