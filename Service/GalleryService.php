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
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setRoomId($row['room_id'])
               ->setOrder($row['order'])
               ->setFile($row['file']);

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
        return $this->galleryMapper->persist($input['data']['image']);
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
        return $this->prepareResult($this->galleryMapper->findByPk($id));
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
