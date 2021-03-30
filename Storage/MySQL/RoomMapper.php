<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Hotel\Storage\MySQL;

use Krystal\Db\Sql\QueryBuilder;
use Cms\Storage\MySQL\AbstractMapper;
use Cms\Storage\MySQL\WebPageMapper;
use Hotel\Storage\RoomMapperInterface;
use Hotel\Collection\BookingStatusCollection;

final class RoomMapper extends AbstractMapper implements RoomMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_hotel_rooms');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return RoomTranslationMapper::getTableName();
    }

    /**
     * Returns shared columns
     * 
     * @return array
     */
    private function getColumns()
    {
        return [
            self::column('id'),
            self::column('price'),
            self::column('adults'),
            self::column('children'),
            self::column('cover'),

            // Translations
            RoomTranslationMapper::column('web_page_id'),
            RoomTranslationMapper::column('lang_id'),
            RoomTranslationMapper::column('name'),
            RoomTranslationMapper::column('full'),
            RoomTranslationMapper::column('description'),
            RoomTranslationMapper::column('meta_description'),
            RoomTranslationMapper::column('keywords'),
            RoomTranslationMapper::column('title'),

            // Web page
            WebPageMapper::column('slug'),
            WebPageMapper::column('changefreq'),
            WebPageMapper::column('priority'),
        ];
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
        $langId = $this->getLangId();

        // Columns to be selected
        $columns = $this->getColumns();

        // Generate single SELECT query and returns query string
        $singleQuery = function($adults, $children) use ($checkin, $checkout, $langId, $columns){
            $qb = new QueryBuilder();
            $qb->select($columns)
            ->count(BookingMapper::column('room_id'), 'countReservation')
            ->from(RoomMapper::getTableName())
            // Room translations relation
            ->leftJoin(RoomTranslationMapper::getTableName(), [
                RoomTranslationMapper::column('id') => RoomMapper::column('id'),
                RoomTranslationMapper::column('lang_id') => $langId
            ])
            // Web page mapper
            ->leftJoin(WebPageMapper::getTableName(), [
                WebPageMapper::column('id') => RoomTranslationMapper::column('web_page_id'),
                WebPageMapper::column('lang_id') => RoomTranslationMapper::column('lang_id')
            ])
            // Booking
            ->leftJoin(BookingMapper::getTableName(), [
                BookingMapper::column('room_id') => RoomMapper::column('id')
                BookingMapper::column('status') => BookingStatusCollection::STATUS_CONFIRMED
            ])
            ->rawAnd()
            // Date filtering condition
            ->openBracket()
            ->compare(BookingMapper::column('checkin'), '<=', "'$checkin'")
            ->rawAnd()
            ->compare(BookingMapper::column('checkout'), '>=', "'$checkout'")
            ->closeBracket()
            // Capacity constraints
            ->where(RoomMapper::column('adults'), '>=', $adults);

            // Apply children count if provided
            if ($children) {
                $qb->andWhere(RoomMapper::column('children'), '>=', $children);
            }

            $qb->groupBy($columns)
               ->append(' HAVING countReservation = 0 ');

            return $qb->getQueryString();
        };

        $qb = new QueryBuilder();
        $qb->select('*')
           ->from()
           ->openBracket();

        // Amount of registered criterias we have
        $amount = count($criteria);

        // Iteration counter
        $i = 0;

        foreach ($criteria as $param) {
            $query = $singleQuery($param['adults'], isset($param['children']) ? $param['children'] : null);

            $qb->openBracket()
               ->append($query)
               ->closeBracket();
            
            ++$i;

            // Comparing iteration against number of mappers, tells whether this iteration is last
            $last = $i == $amount;

            // If we have more than one criteria, then we need to union results
            // And also, we should never append UNION in last iteration
            if ($amount > 1 && !$last) {
                $qb->union();
            }
        }

        $qb->closeBracket()
           ->asAlias('result');

        return $this->db->raw($qb->getQueryString())
                        ->queryAll();
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
        return $this->findWebPage($this->getColumns(), $id, $withTranslations);
    }

    /**
     * Fetch all available rooms
     * 
     * @return array
     */
    public function fetchAll()
    {
        $db = $this->createWebPageSelect($this->getColumns())
                    // Language ID constraint
                   ->whereEquals(RoomTranslationMapper::column('lang_id'), $this->getLangId());

        $db->orderBy(self::column('id'))
           ->desc();

        return $db->queryAll();
    }
}
