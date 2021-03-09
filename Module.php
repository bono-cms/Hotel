<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Hotel;

use Krystal\Image\Tool\ImageManager;
use Cms\AbstractCmsModule;
use Hotel\Service\RoomService;
use Hotel\Service\BookingService;
use Hotel\Service\GalleryService;

final class Module extends AbstractCmsModule
{
     /**
     * Builds gallery image manager service
     * 
     * @return \Krystal\Image\Tool\ImageManager
     */
    private function createGalleryImageManager()
    {
        $plugins = [
            'thumb' => [
                'quality' => 85,
                'dimensions' => [
                    // For administration panel
                    [400, 400],
                ]
            ]
        ];

        return new ImageManager(
            '/data/uploads/module/hotel/gallery/',
            $this->appConfig->getRootDir(),
            $this->appConfig->getRootUrl(),
            $plugins
        );
    }
    
     /**
     * Builds gallery image manager service
     * 
     * @return \Krystal\Image\Tool\ImageManager
     */
    private function createImageManager()
    {
        $plugins = [
            'thumb' => [
                'quality' => 85,
                'dimensions' => [
                    // For administration panel
                    [400, 400],
                ]
            ]
        ];

        return new ImageManager(
            '/data/uploads/module/hotel/rooms/',
            $this->appConfig->getRootDir(),
            $this->appConfig->getRootUrl(),
            $plugins
        );
    }

   /**
     * {@inheritDoc}
     */
    public function getServiceProviders()
    {
        $webPageManager = $this->getWebPageManager();

        // Mappers
        $roomMapper = $this->getMapper('/Hotel/Storage/MySQL/RoomMapper');
        $bookingMapper = $this->getMapper('/Hotel/Storage/MySQL/BookingMapper');
        $bookingGuestMapper = $this->getMapper('/Hotel/Storage/MySQL/BookingGuestMapper');
        $galleryMapper = $this->getMapper('/Hotel/Storage/MySQL/GalleryMapper');

        return [
            'bookingService' => new BookingService($bookingMapper, $bookingGuestMapper),
            'roomService' => new RoomService($roomMapper, $this->createImageManager(), $webPageManager),
            'galleryService' => new GalleryService($galleryMapper, $this->createGalleryImageManager())
        ];
    }
}
