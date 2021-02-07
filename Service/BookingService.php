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
use Hotel\Collection\BookingStatusCollection;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Date\TimeHelper;
use Krystal\Text\TextUtils;

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
               ->setCheckout($row['checkout'])
               ->setStatus($row['status']);

        return $entity;
    }

    /**
     * Adds new booking entry
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input)
    {
        return $this->save($input, BookingStatusCollection::STATUS_TEMPORARY);
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
     * Confirms booking by a token
     * 
     * @param string $token
     * @return boolean Depending on success
     */
    public function confirmByToken($token)
    {
        return $this->bookingMapper->confirmByToken($token);
    }

    /**
     * Persists a booking entry
     * 
     * @param array $input
     * @param int $status Status constant
     * @return boolean
     */
    public function save(array $input, $status = BookingStatusCollection::STATUS_CONFIRMED)
    {
        // Append date only for new records
        if (empty($input['id'])) {
            $input['date'] = TimeHelper::getNow();
            $input['token'] = TextUtils::uniqueString();
            $input['status'] = $status;
        }

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
