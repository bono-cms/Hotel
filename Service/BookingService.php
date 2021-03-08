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

use Hotel\Storage\BookingGuestMapperInterface;
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
     * Guest mapper
     * 
     * @var \Hotel\Storage\BookingGuestMapperInterface
     */
    private $bookingGuestMapper;

    /**
     * State initialization
     * 
     * @param \Hotel\Storage\BookingMapperInterface $bookingMapper
     * @param \Hotel\Storage\BookingGuestMapperInterface $bookingGuestMapper
     * @return void
     */
    public function __construct(BookingMapperInterface $bookingMapper, BookingGuestMapperInterface $bookingGuestMapper)
    {
        $this->bookingMapper = $bookingMapper;
        $this->bookingGuestMapper = $bookingGuestMapper;
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

        if (isset($row['room'])) {
            $entity->setRoom($row['room']);
        }

        return $entity;
    }

    /**
     * Checks whether a single room is available at given dates
     * 
     * @param int $roomId
     * @param string $checkin
     * @param string $checkout
     * @return boolean
     */
    public function isAvailable($roomId, $checkin, $checkout)
    {
        return $this->bookingMapper->isAvailable($roomId, $checkin, $checkout);
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
     * Fetches booking entry by its token
     * 
     * @param string $token
     * @return string
     */
    public function fetchByToken($token)
    {
        return $this->prepareResult($this->bookingMapper->fetchByToken($token));
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
     * Fetch guests by booking ID
     * 
     * @param int $bookingId
     * @return array
     */
    public function fetchGuestsByBookingId($bookingId)
    {
        return $this->bookingGuestMapper->fetchGuestsByBookingId($bookingId);
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
     * Save guests
     * 
     * @param int $bookingId Attached booking ID
     * @param array $guests
     * @return boolean
     */
    private function saveGuests($bookingId, array $guests)
    {
        $values = [];

        foreach ($guests as $guest) {
            $values[] = [
                $bookingId,
                $guest['client'],
                $guest['email']
            ];
        }

        return $this->bookingGuestMapper->saveMany($values);
    }

    /**
     * Persists a booking entry
     * 
     * @param array $input Raw input data
     * @param int $status Status constant
     * @param array $guests Optional guests
     * @return string Token string on success
     */
    public function save(array $input, $status = BookingStatusCollection::STATUS_CONFIRMED, array $guests = [])
    {
        // Whether this record is new
        $isNew = empty($input['id']);
        
        $token = TextUtils::uniqueString();

        // Append date only for new records
        if ($isNew) {
            $input['datetime'] = TimeHelper::getNow();
            $input['token'] = $token;
            $input['status'] = $status;
        }

        $this->bookingMapper->persist($input);

        // Append guests if available
        if ($isNew && !empty($guests)) {
            $bookingId = $this->bookingMapper->getMaxId();
            $this->saveGuests($bookingId, $guests);
        }

        return $token;
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
