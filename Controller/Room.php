<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Hotel\Controller;

use Hotel\Service\BookingService;
use Site\Controller\AbstractController;
use Krystal\Stdlib\VirtualEntity;

final class Room extends AbstractController
{
    /**
     * Renders room by its id
     * 
     * @param int $id Room id
     * @return string
     */
    public function indexAction($id)
    {
        $roomService = $this->getModuleService('roomService');
        $room = $roomService->fetchById($id, false);

        if ($room) {
            // Load site plugins
            $this->loadSitePlugins();

            // Append gallery
            $room->setGallery($this->getModuleService('galleryService')->fetchAll($id));

            // Query parameters
            $checkin = $this->request->getQuery('checkin');
            $checkout = $this->request->getQuery('checkout');
            $criteria = $this->request->getQuery('criteria', [['adults' => 1]]);

            return $this->view->render('hotel-room', [
                // Query parameters
                'checkin' => $checkin,
                'checkout' => $checkout,
                'criteria' => $criteria,
                'page' => $room,
                'room' => $room,
                'languages' => $roomService->getSwitchUrls($id)
            ]);

        } else {
            return false;
        }
    }

    /**
     * Checks whether a room is available on given dates
     * 
     * @return string
     */
    public function availableAction()
    {
        $result = $this->getModuleService('bookingService')->isAvailable(
            $this->request->getQuery('room_id'),
            $this->request->getQuery('checkin'),
            $this->request->getQuery('checkout')
        );

        return $this->json([
            'available' => $result
        ]);
    }

    /**
     * Performs a search
     * 
     * @return string
     */
    public function searchAction()
    {
        if ($this->request->hasQuery('checkin', 'checkout')) {
            // Query parameters
            $checkin = $this->request->getQuery('checkin');
            $checkout = $this->request->getQuery('checkout');
            $criteria = $this->request->getQuery('criteria', [['adults' => 1]]);

            $this->loadSitePlugins();
            $this->view->getBreadcrumbBag()
                       ->addOne($this->translator->translate('Search'));

            $title = $this->translator->translate('Search results');

            // Dummy page
            $page = new VirtualEntity();
            $page->setTitle($title)
                 ->setName($title)
                 ->setMetaDescription(null);

            return $this->view->render('hotel-rooms', [
                // Query parameters
                'checkin' => $checkin,
                'checkout' => $checkout,
                'criteria' => $criteria,
                'duration' => BookingService::getDuration($checkin, $checkout),
                'languages' => $this->getService('Cms', 'languageManager')->fetchAll(true),
                'rooms' => $this->getModuleService('roomService')->search($checkin, $checkout, $criteria),
                'page' => $page
            ]);

        } else {
            return false;
        }
    }
}
