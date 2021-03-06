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

use DateTime;
use Hotel\Storage\BookingGuestMapperInterface;
use Hotel\Storage\BookingMapperInterface;
use Hotel\Collection\BookingStatusCollection;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Date\TimeHelper;
use Krystal\Text\TextUtils;
use Krystal\Stdlib\ArrayUtils;

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
     * Gets guest count from criteria
     * 
     * @param array $criteria
     * @return int
     */
    public static function getCriteriaCount(array $criteria)
    {
        return [
            'adults' => array_sum(array_column($criteria, 'adults')),
            'children' => array_sum(array_column($criteria, 'children'))
        ];
    }

    /**
     * Gets guest count from criteria
     * 
     * @param array $criteria
     * @return int
     */
    public static function getGuestCountFromCriteria(array $criteria)
    {
        $adults = array_column($criteria, 'adults');
        $children = array_column($criteria, 'children');

        return array_sum($adults) + array_sum($children);
    }

    /**
     * Helper method to count stay duration in days
     * 
     * @param string $checkin
     * @param string $checkout
     * @return int Number of days
     */
    public static function getDuration($checkin, $checkout)
    {
        $start = new DateTime($checkin);
        $start->setTime(0, 0, 0);

        $end = new DateTime($checkout);
        $end->setTime(0, 0, 0);

        $interval = $end->diff($start);

        return $interval->d;
    }

    /**
     * Returns arrival time
     * 
     * @return array
     */
    public static function getArrivalTime()
    {
        return ArrayUtils::valuefy([
            '14:00 - 15:00',
            '15:00 - 16:00',
            '16:00 - 17:00',
            '17:00 - 18:00',
            '18:00 - 19:00',
            '19:00 - 20:00',
            '20:00 - 21:00',
            '21:00 - 22:00',
            '22:00 - 23:00',
            '23:00 - 00:00'
        ]);
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
               ->setStatus($row['status'])
               ->setEmail($row['email'])
               ->setPhone($row['phone'])
               ->setComment($row['comment'])
               ->setToken($row['token'])
               ->setIndex($row['index'])
               ->setAddress($row['address'])
               ->setArrival($row['arrival']);

        if (isset($row['room'])) {
            $entity->setRoom($row['room']);
        }

        return $entity;
    }

    /**
     * Count all confirmed booking items
     * 
     * @return int
     */
    public function countAll()
    {
        return $this->bookingMapper->countAll();
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

        // Append date only for new records
        if ($isNew) {
            $token = TextUtils::uniqueString();
            $input['datetime'] = TimeHelper::getNow();
            $input['token'] = $token;
            $input['status'] = $status;
        } else {
            $token = $input['token'];
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
