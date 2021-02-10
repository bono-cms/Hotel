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

use Krystal\Image\Tool\ImageManagerInterface;
use Krystal\Stdlib\VirtualEntity;
use Hotel\Storage\GalleryMapperInterface;
use Cms\Service\AbstractManager;

final class GalleryService extends AbstractManager
{
    /**
     * Any-compliant gallery mapper
     * 
     * @var \Hotel\Storage\GalleryMapperInterface
     */
    private $galleryMapper;

    /**
     * Any compliant image manager instance
     * 
     * @var \Krystal\Image\Tool\ImageManagerInterface
     */
    private $imageManager;

    /**
     * State initialization
     * 
     * @param \Hotel\Storage\GalleryMapperInterface $galleryMapper
     * @param \Krystal\Image\Tool\ImageManagerInterface $imageManager
     * @return void
     */
    public function __construct(GalleryMapperInterface $galleryMapper, ImageManagerInterface $imageManager)
    {
        $this->galleryMapper = $galleryMapper;
        $this->imageManager = $imageManager;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $imageBag = clone ($this->imageManager->getImageBag());
        $imageBag->setId($row['id'])
                 ->setCover($row['file']);

        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setRoomId($row['room_id'])
               ->setOrder($row['order'])
               ->setFile($row['file'])
               ->setImageBag($imageBag);

        if (isset($row['room'])) {
            $entity->setRoom($row['room']);
        }

        return $entity;
    }

    /**
     * Returns last image id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->galleryMapper->getMaxId();
    }

    /**
     * Saves an image
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function save(array $input)
    {
        // References
        $image =& $input['data']['image'];
        $file = isset($input['files']['image']['file']) ? $input['files']['image']['file'] : false;

        if (!$image['id']) { // Creation
            // If there's a file, then it needs to uploaded as a cover
            $image['file'] = $file ? $file->getUniqueName() : '';

            if ($file && $this->galleryMapper->persist($image)) {
                return $this->imageManager->upload($this->getLastId(), $file);
            }

        } else { // Update
            if ($file) {
                // Remove previous one
                if (!empty($image['file'])) {
                    if (!$this->imageManager->delete($image['id'], $image['file'])) {
                        // If failed, then exit this method immediately
                        return false;
                    }
                }

                // Upload new file
                $image['file'] = $file->getUniqueName();
                $this->imageManager->upload($image['id'], $file);
            }

            return $this->galleryMapper->persist($image);
        }
    }

    /**
     * Deletes an image by its id
     * 
     * @param int $id Image id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->galleryMapper->deleteByPk($id);
    }

    /**
     * Fetch image entity by its id
     * 
     * @param int $id Image id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->galleryMapper->fetchById($id));
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
