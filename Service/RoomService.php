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

use Hotel\Storage\RoomMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;

final class RoomService extends AbstractManager
{
    /**
     * Any compliant room mapper
     * 
     * @var \Hotel\Storage\RoomMapperInterface
     */
    private $roomMapper;

    /**
     * State initialization
     * 
     * @param \Hotel\Storage\RoomMapperInterface $roomMapper
     * @return void
     */
    public function __construct(RoomMapperInterface $roomMapper)
    {
        $this->roomMapper = $roomMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setLangId($row['lang_id'])
               ->setPrice($row['price'])
               ->setName($row['name'])
               ->setDescription($row['description']);

        return $entity;
    }

    /**
     * Returns last room id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->roomMapper->getMaxId();
    }

    /**
     * Fetches room by its id
     * 
     * @param int $id Room id
     * @param boolean $withTranslations Whether to fetch translations
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations == true) {
            return $this->prepareResults($this->roomMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->roomMapper->fetchById($id, false));
        }
    }

    /**
     * Fetch all rooms
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->roomMapper->fetchAll());
    }

    /**
     * Deletes a room by its id
     * 
     * @param int $id Room id
     * @return int
     */
    public function deleteById($id)
    {
        return $this->roomMapper->deleteEntity($id);
    }

    /**
     * Saves a room
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->roomMapper->saveEntity($input['room'], $input['translation']);
    }
}
