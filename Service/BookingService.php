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

use Hotel\Storage\BookingMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;

final class BookingService extends AbstractManager
{
    /**
     * Booking mapper
     * 
     * @var \Hotel\Storage\BookingMapperInterface
     */
    private $bookingMapper;

    /**
     * State initialization
     * 
     * @param \Hotel\Storage\BookingMapperInterface $bookingMapper
     * @return void
     */
    public function __construct(BookingMapperInterface $bookingMapper)
    {
        $this->bookingMapper = $bookingMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setRoomId($row['room_id'])
               ->setDatetime($row['datetime'])
               ->setClient($row['client'])
               ->setAmount($row['amount'])
               ->setCheckin($row['checkin'])
               ->setCheckout($row['checkout']);

        return $entity;
    }

    /**
     * Deletes booking entry by its id
     * 
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->bookingMapper->deleteByPk($id);
    }

    /**
     * Fetch booking entry by its id
     * 
     * @param int $id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->bookingMapper->findByPk($id));
    }

    /**
     * Fetch all bookings
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->bookingMapper->fetchAll());
    }

    /**
     * Persists a booking entry
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->bookingMapper->persist($input);
    }

    /**
     * Returns last booking id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->bookingMapper->getMaxId();
    }
}
