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
use Krystal\Image\Tool\ImageManagerInterface;
use Hotel\Storage\RoomMapperInterface;
use Cms\Service\AbstractManager;
use Cms\Service\WebPageManagerInterface;

final class RoomService extends AbstractManager
{
    /**
     * Any compliant room mapper
     * 
     * @var \Hotel\Storage\RoomMapperInterface
     */
    private $roomMapper;

    /**
     * Any compliant image manager instance
     * 
     * @var \Krystal\Image\Tool\ImageManagerInterface
     */
    private $imageManager;

    /**
     * Web page manager to deal with slugs
     * 
     * @var \Cms\Service\WebPageManagerInterface
     */
    private $webPageManager;

    /**
     * State initialization
     * 
     * @param \Hotel\Storage\RoomMapperInterface $roomMapper
     * @param \Krystal\Image\Tool\ImageManagerInterface $imageManager
     * @param \Cms\Service\WebPageManagerInterface $webPageManager
     * @return void
     */
    public function __construct(RoomMapperInterface $roomMapper, ImageManagerInterface $imageManager, WebPageManagerInterface $webPageManager)
    {
        $this->roomMapper = $roomMapper;
        $this->imageManager = $imageManager;
        $this->webPageManager = $webPageManager;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $imageBag = clone ($this->imageManager->getImageBag());
        $imageBag->setId($row['id'])
                 ->setCover($row['cover']);

        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setLangId($row['lang_id'])
               ->setWebPageId($row['web_page_id'])
               ->setPrice($row['price'])
               ->setAdults($row['adults'])
               ->setChildren($row['children'])
               ->setCover($row['cover'])
               ->setName($row['name'])
               ->setDescription($row['description'])
               // SEO
               ->setMetaDescription($row['meta_description'])
               ->setKeywords($row['keywords'])
               ->setTitle($row['title'])
               ->setSlug($row['slug'])
               ->setUrl($this->webPageManager->surround($entity->getSlug(), $entity->getLangId()))
               ->setImageBag($imageBag);

        return $entity;
    }

    /**
     * Returns a collection of switching URLs
     * 
     * @param string $id Room ID
     * @return array
     */
    public function getSwitchUrls($id)
    {
        return $this->roomMapper->createSwitchUrls($id, 'Hotel', 'Hotel:Room@indexAction');
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
     * Search for available rooms
     * 
     * @param string $checkin Check-in date
     * @param string $checkout Check-out date
     * @param array $criteria Capacity criteria
     * @return array
     */
    public function search($checkin, $checkout, array $criteria)
    {
        return $this->roomMapper->search($checkin, $checkout, $criteria);
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
        return $this->roomMapper->deleteEntity($id) && $this->imageManager->delete($id);
    }

    /**
     * Saves a room
     * 
     * @param array $input Raw input data
     * @return boolean Depending on success
     */
    public function save(array $input)
    {
        $data = $input['data'];
        $file = isset($input['files']['room']['cover']) ? $input['files']['room']['cover'] : false;

        if (!$data['room']['id']) { // Creation
            // If there's a file, then it needs to uploaded as a cover
            $data['room']['cover'] = $file ? $file->getUniqueName() : '';

            // Do save
            $this->roomMapper->savePage('Hotel', 'Hotel:Room@indexAction', $data['room'], $data['translation']);

            if ($file) {
                $this->imageManager->upload($this->getLastId(), $file);
            }

            return true;

        } else { // Update
            if ($file) {
                // Remove previous one
                if (!empty($data['room']['cover'])) {
                    $this->imageManager->delete($data['room']['id'], $data['room']['cover']);
                }

                // Upload new file
                $data['room']['cover'] = $file->getUniqueName();
                $this->imageManager->upload($data['room']['id'], $file);
            }

            return $this->roomMapper->savePage('Hotel', 'Hotel:Room@indexAction', $data['room'], $data['translation']);
        }
    }
}
