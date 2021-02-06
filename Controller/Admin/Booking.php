<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Hotel\Controller\Admin;

use Cms\Controller\Admin\AbstractController;
use Krystal\Stdlib\VirtualEntity;

final class Booking extends AbstractController
{
    /**
     * Render all bokings
     * 
     * @return string
     */
    public function indexAction()
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Hotel', 'Hotel:Admin:Room@indexAction')
                                       ->addOne('Bookings');

        return $this->view->render('booking/index', [
            'bookings' => $this->getModuleService('bookingService')->fetchAll()
        ]);
    }

    /**
     * Creates shared form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $booking
     * @param string $title Page title
     * @return string
     */
    private function createForm(VirtualEntity $booking, $title)
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Hotel', 'Hotel:Admin:Room@indexAction')
                                       ->addOne('Bookings', 'Hotel:Admin:Booking@indexAction')
                                       ->addOne($title);

        return $this->view->render('booking/form', [
            'booking' => $booking
        ]);
    }

    /**
     * Renders adding form
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity, 'Add new booking');
    }

    /**
     * Renders edit form
     * 
     * @param int $id
     * @return string
     */
    public function editAction($id)
    {
        $booking = $this->getModuleService('bookingService')->fetchById($id);

        if ($booking) {
            $title = $this->translator->translate('Edit the booking of "%s" from "%s"', $booking->getClient(), $booking->getDatetime());
            return $this->createForm($booking, $title);
        } else {
            return false;
        }
    }

    /**
     * Deletes a booking
     * 
     * @param int $id
     * @return string
     */
    public function deleteAction($id)
    {
        $this->getModuleService('bookingService')->deleteById($id);

        $this->flashBag->set('success', 'Selected booking entry has been removed');
        return 1;
    }

    /**
     * Saves a booking
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('booking');
        $isNew = empty($input['id']);

        $bookingService = $this->getModuleService('bookingService');
        $bookingService->save($input);

        $this->flashBag->set('success', $isNew ? 'Booking entry has been created successfully' : 'Booking entry has been updated successfully');

        return $isNew ? 1 : $bookingService->getLastId();
    }
}
