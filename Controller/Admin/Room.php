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

final class Room extends AbstractController
{
    /**
     * Render all available rooms
     * 
     * @return string
     */
    public function indexAction()
    {
        // Append a breadcrumb
        $this->view->getBreadcrumbBag()->addOne('Hotel');

        return $this->view->render('index', [
            'rooms' => $this->getModuleService('roomService')->fetchAll()
        ]);
    }

    /**
     * Renders a form
     * 
     * @param array|\Krystal\Stdlib\VirtualEntity $room
     * @param string $title Page title
     * @return string
     */
    private function createForm($room, $title)
    {
        // Append a breadcrumb
        $this->view->getBreadcrumbBag()->addOne('Hotel', 'Hotel:Admin:Room@indexAction')
                                       ->addOne($title);

        return $this->view->render('form', [
            'room' => $room
        ]);
    }

    /**
     * Adds a room
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity(), 'Add new room');
    }

    /**
     * Edits a room
     * 
     * @param string $id Room id
     * @return strng
     */
    public function editAction($id)
    {
        $room = $this->getModuleService('roomService')->fetchById($id, true);

        if ($room) {
            return $this->createForm($room, 'Edit the room');
        } else {
            return false;
        }
    }

    /**
     * Deletes a room
     * 
     * @param int $id Room id
     * @return string
     */
    public function deleteAction($id)
    {
        $this->getModuleService('roomService')->deleteById($id);

        $this->flashBag->set('success', 'The room has been deleted successfully');
        return 1;
    }

    /**
     * Saves a room
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $input = $this->request->getPost();

        $isNew = empty($input['room']['id']);
        $roomService = $this->getModuleService('roomService');

        if ($roomService->save($input)) {
            // Flash message
            $this->flashBag->set('success', $isNew ? 'The room has been added successfully' : 'The room has been updated successfully');

            return $isNew ? $roomService->getLastId() : 1;
        }
    }
}
