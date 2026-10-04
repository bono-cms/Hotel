<?php

/**
 * This file is part of the Bono CMS
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

        return $this->view->render('room/index', [
            'bookingCount' => $this->getModuleService('bookingService')->countAll(),
            'currency' => 'USD',
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
        $new = !is_array($room);

        // Append a breadcrumb
        $this->view->getBreadcrumbBag()->addOne('Hotel', 'Hotel:Admin:Room@indexAction')
                                       ->addOne($title);

        return $this->view->render('room/form', [
            'room' => $room,
            'new' => $new,
            'images' => $new ? [] : $this->getModuleService('galleryService')->fetchAll($room[0]->getId())
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
            $name = $this->getCurrentProperty($room, 'name');
            return $this->createForm($room, $this->translator->translate('Edit the room "%s"', $name));
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
        return $this->json([
            'refresh' => true
        ]);
    }

    /**
     * Saves a room
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $validator = $this->createValidation();

        $validator->field('room.price')
                  ->required()
                  ->addRule('numeric')
                  ->addRule('greaterthan', null, ['min' => 0]);

        $validator->field('room.adults')
                  ->required()
                  ->addRule('integer')
                  ->addRule('greaterthan', null, ['min' => 0]);

        $validator->field('room.children')
                  ->required()
                  ->addRule('integer')
                  ->addRule('lessorequal', null, ['max' => 100]);

        // Every translation's name is required
        $validator->field('translation.*.name')
                  ->required()
                  ->addRule('minlength', null, ['min' => 2]);

        // Every translation's description is required
        $validator->field('translation.*.description')
                  ->required();

        // Cover file is required only on creation (when id is empty)
        $validator->file('room.cover')
                  ->required(null, empty($this->request->getPost('room')['id']))
                  ->addRule('image');

        if ($validator->isPassed()) {
            $input = $this->request->getAll();

            $isNew = empty($input['data']['room']['id']);
            $roomService = $this->getModuleService('roomService');

            if ($roomService->save($input)) {
                // Flash message
                $this->flashBag->set('success', $isNew ? 'The room has been added successfully' : 'The room has been updated successfully');

                return $isNew ? $this->json([
                    'redirect' => $this->createUrl('Hotel:Admin:Room@editAction', [$roomService->getLastId()]),
                ]) : $this->json([
                    'refresh' => true
                ]);
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }
    }
}
