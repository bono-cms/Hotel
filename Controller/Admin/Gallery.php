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

use Krystal\Stdlib\VirtualEntity;
use Cms\Controller\Admin\AbstractController;

final class Gallery extends AbstractController
{
    /**
     * Renders a gallery form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $image
     * @param string $title Page title
     * @return string
     */
    private function createForm(VirtualEntity $image, $title)
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Hotel', 'Hotel:Admin:Room@indexAction')
                                       ->addOne($this->translator->translate('Edit the room "%s"', $image->getRoom()), $this->createUrl('Hotel:Admin:Room@editAction', [$image->getRoomId()]))
                                       ->addOne($title);

        return $this->view->render('room/gallery', [
            'image' => $image
        ]);
    }

    /**
     * Renders adding form
     * 
     * @param int $roomId
     * @return string
     */
    public function addAction($roomId)
    {
        $room = $this->getModuleService('roomService')->fetchById($roomId, false);

        if ($room) {
            $image = new VirtualEntity();
            $image->setRoomId($roomId)
                  ->setRoom($room->getName());

            return $this->createForm($image, 'Upload new image');
        } else {
            return false;
        }
    }

    /**
     * Renders edit form
     * 
     * @param int $imageId Image id
     * @return string
     */
    public function editAction($imageId)
    {
        $image = $this->getModuleService('galleryService')->fetchById($imageId);

        if ($image !== false) {
            return $this->createForm($image, 'Edit the image');
        } else {
            return false;
        }
    }

    /**
     * Deletes an image
     * 
     * @param int $id
     * @return string
     */
    public function deleteAction($id)
    {
        if ($this->getModuleService('galleryService')->deleteById($id)) {
            $this->flashBag->set('success', 'Selected image has been removed successfully');
            return 1;
        }
    }

    /**
     * Save image
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $galleryService = $this->getModuleService('galleryService');
        
        $input = $this->request->getAll();
        $id = $input['data']['image']['id'];

        $galleryService->save($input);
        $this->flashBag->set('success', $id ? 'Image has been updated successfully' : 'Image has been uploaded successfully');

        return $id ? 1 : $galleryService->getLastId();
    }
}
